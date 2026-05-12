<?php

namespace App\Services;

use App\Models\Log;

/**
 * Log Service - Dual Persistence Strategy
 *
 * Writes to both database and a JSON Lines file (one JSON object per line).
 *
 * Architecture:
 * - Write path: File (O(1) append, always succeeds) → Database (best effort)
 * - Read path:  Database (fast, queryable) → File (fallback, streaming)
 * - Sync path:  Manual trigger to reconcile file logs to database
 *
 * File format: app.jsonl — one compact JSON object per line, no array wrapper.
 * Append-only writes eliminate the read-decode-encode-rewrite cycle that
 * caused OOM on high-volume 404 traffic with the previous app.json format.
 *
 * Rotation: file is archived to app.jsonl.1 when it exceeds 50 MB.
 */
class LogService
{
    private string $logFile;

    public function __construct()
    {
        $this->logFile = BASE_PATH . '/storage/logs/app.jsonl';

        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Get all logs from database with file fallback.
     *
     * @return array{
     *   logs: array,
     *   source: string,
     *   database_available: bool,
     *   needs_sync: bool,
     *   file_log_count: int
     * }
     */
    public function all(): array
    {
        $databaseAvailable = $this->isDatabaseAvailable();

        if ($databaseAvailable) {
            try {
                $logs     = Log::all();
                $fileLogs = $this->getFromFile();
                $needsSync = $this->hasUnsyncedLogs($logs, $fileLogs);

                return [
                    'logs'               => $logs,
                    'source'             => 'database',
                    'database_available' => true,
                    'needs_sync'         => $needsSync,
                    'file_log_count'     => count($fileLogs),
                ];
            } catch (\Exception $e) {
                return [
                    'logs'               => $this->getFromFile(),
                    'source'             => 'file',
                    'database_available' => false,
                    'needs_sync'         => false,
                    'file_log_count'     => 0,
                ];
            }
        }

        return [
            'logs'               => $this->getFromFile(),
            'source'             => 'file',
            'database_available' => false,
            'needs_sync'         => false,
            'file_log_count'     => 0,
        ];
    }

    /**
     * Get a single log entry by ID (database first, file fallback).
     */
    public function find(int $id): ?array
    {
        try {
            return Log::find($id);
        } catch (\Exception $e) {
            return $this->findInFile($id);
        }
    }

    /**
     * Append a log entry to both file and database.
     *
     * File write is guaranteed; database write is best-effort.
     *
     * @param string $level   'info' | 'warning' | 'error' | 'debug'
     * @param string $message Descriptive log message
     * @param array  $context Scalar/shallow-array context values only
     */
    public function add(string $level, string $message, array $context = []): void
    {
        $sanitizedContext = \sanitize_for_log($context);

        $this->logToFile($level, $message, $sanitizedContext);

        try {
            Log::log($level, $message, $sanitizedContext);
        } catch (\Exception $e) {
            error_log("Failed to log to database: " . $e->getMessage());
            $this->logToFile('error', 'Failed to log to database', ['error' => substr($e->getMessage(), 0, 512)]);
        }
    }

    /**
     * Clear all logs (file truncated, database table truncated).
     */
    public function clear(): void
    {
        @file_put_contents($this->logFile, '');

        try {
            $db = \Core\Database::getInstance();
            $db->execute("TRUNCATE TABLE logs");
        } catch (\Exception $e) {
            error_log("Failed to clear database logs: " . $e->getMessage());
        }
    }

    /**
     * Check if the database is reachable.
     */
    public function isDatabaseAvailable(): bool
    {
        try {
            $db = \Core\Database::getInstance();
            $db->query("SELECT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Synchronise file logs to the database after database recovery.
     *
     * Reads all file entries and inserts any not already present in the DB
     * (compared by message + level to avoid duplicates).
     *
     * @return array{success: bool, synced: int, skipped: int, errors: array}
     */
    public function syncToDatabase(): array
    {
        $result = ['success' => false, 'synced' => 0, 'skipped' => 0, 'errors' => []];

        if (!$this->isDatabaseAvailable()) {
            $result['errors'][] = 'Database is not available';
            return $result;
        }

        // Pass limit=0 to read all entries, not just the recent window
        $fileLogs = $this->getFromFile(0);

        if (empty($fileLogs)) {
            $result['success'] = true;
            return $result;
        }

        try {
            $existingLogs = Log::all();

            foreach ($fileLogs as $log) {
                $exists = false;
                foreach ($existingLogs as $existing) {
                    if ($existing['message'] === $log['message'] &&
                        $existing['level']   === $log['level']) {
                        $exists = true;
                        break;
                    }
                }

                if ($exists) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    Log::log($log['level'], $log['message'], $log['context'] ?? []);
                    $result['synced']++;
                } catch (\Exception $e) {
                    $result['errors'][] = "Failed to sync log #{$log['id']}: " . $e->getMessage();
                }
            }

            $result['success'] = true;
        } catch (\Exception $e) {
            $result['errors'][] = 'Failed to sync: ' . $e->getMessage();
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Read entries from the JSON Lines file.
     *
     * Streams line-by-line so large files do not require loading the entire
     * content into memory. Returns entries most-recent-first.
     *
     * @param int $limit Maximum entries to return (0 = all).
     */
    private function getFromFile(int $limit = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $entries = [];
        try {
            $file = new \SplFileObject($this->logFile, 'r');
            while (!$file->eof()) {
                $line = trim($file->fgets());
                if ($line === '') {
                    continue;
                }
                $decoded = json_decode($line, true);
                if ($decoded !== null) {
                    $entries[] = $decoded;
                }
            }
            unset($file); // release file handle
        } catch (\Throwable $e) {
            error_log("Failed to read log file {$this->logFile}: " . $e->getMessage());
            return [];
        }

        $entries = array_reverse($entries); // most-recent-first

        return ($limit > 0) ? array_slice($entries, 0, $limit) : $entries;
    }

    /**
     * Find a single entry in the file by its ID (streams; does not load all entries).
     */
    private function findInFile(int $id): ?array
    {
        if (!file_exists($this->logFile)) {
            return null;
        }

        try {
            $file = new \SplFileObject($this->logFile, 'r');
            while (!$file->eof()) {
                $line = trim($file->fgets());
                if ($line === '') {
                    continue;
                }
                $decoded = json_decode($line, true);
                if ($decoded !== null && isset($decoded['id']) && $decoded['id'] === $id) {
                    return $decoded;
                }
            }
            unset($file);
        } catch (\Throwable $e) {
            error_log("Failed to search log file {$this->logFile}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Archive the current log file if it exceeds 50 MB.
     *
     * Keeps one archive: app.jsonl.1 (overwritten each rotation).
     */
    private function rotateIfNeeded(): void
    {
        if (!file_exists($this->logFile)) {
            return;
        }
        if (filesize($this->logFile) < 50 * 1024 * 1024) {
            return;
        }

        $archive = $this->logFile . '.1';
        if (file_exists($archive)) {
            @unlink($archive);
        }
        @rename($this->logFile, $archive);
    }

    /**
     * Append one JSON Lines entry to the log file.
     *
     * O(1) write — no read, no re-encode of existing entries.
     * Uses FILE_APPEND | LOCK_EX for safe concurrent access.
     * Rotates the file first if it has grown beyond 50 MB.
     */
    private function logToFile(string $level, string $message, array $context = []): void
    {
        $this->rotateIfNeeded();

        $entry = [
            'id'        => intval(microtime(true) * 10000),
            'level'     => $level,
            'message'   => $message,
            'context'   => $context,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $line = json_encode($entry) . "\n";

        if (@file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX) === false) {
            error_log("Failed to write log file: {$this->logFile}");
        }
    }

    /**
     * Check whether any file entry is absent from the database (by message + level).
     */
    private function hasUnsyncedLogs(array $dbLogs, array $fileLogs): bool
    {
        if (empty($fileLogs)) {
            return false;
        }

        foreach ($fileLogs as $fileLog) {
            $found = false;
            foreach ($dbLogs as $dbLog) {
                if ($dbLog['message'] === $fileLog['message'] &&
                    $dbLog['level']   === $fileLog['level']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return true;
            }
        }

        return false;
    }
}
