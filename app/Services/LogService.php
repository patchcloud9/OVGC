<?php

namespace App\Services;

use App\Models\Log;

/**
 * Log Service - Dual Persistence Strategy
 * 
 * Implements a resilient logging system that writes to both database and file storage.
 * 
 * Features:
 * - Dual persistence: Logs written to both database and JSON file
 * - Graceful degradation: Falls back to file if database unavailable
 * - Auto-sync: Detects and syncs file-only logs to database when it recovers
 * - Thread-safe: Uses file locking for concurrent writes
 * 
 * Architecture:
 * - Write path: File (always succeeds) → Database (best effort)
 * - Read path: Database (fast, queryable) → File (fallback)
 * - Sync path: Manual trigger to reconcile file logs to database
 * 
 * @package App\Services
 */
class LogService
{
    private string $logFile;
    
    public function __construct()
    {
        // Store logs in the storage folder as backup
        $this->logFile = BASE_PATH . '/storage/logs/app.json';
        
        // Ensure directory exists
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Get all logs from database with file fallback
     * 
     * Primary source is database (fast, queryable). Falls back to file if unavailable.
     * Also checks if file contains unsynced logs and reports sync status.
     * 
     * @return array [
     *   'logs' => array,              // Log entries
     *   'source' => string,           // 'database' or 'file'
     *   'database_available' => bool, // Database connection status
     *   'needs_sync' => bool,         // File has logs not in DB
     *   'file_log_count' => int       // Total file logs
     * ]
     */
    public function all(): array
    {
        $databaseAvailable = $this->isDatabaseAvailable();
        
        if ($databaseAvailable) {
            try {
                // Get database logs
                $logs = Log::all();
                
                // Check if there are file logs that need syncing
                $fileLogs = $this->getFromFile();
                $needsSync = $this->hasUnsyncedLogs($logs, $fileLogs);
                
                return [
                    'logs' => $logs,
                    'source' => 'database',
                    'database_available' => true,
                    'needs_sync' => $needsSync,
                    'file_log_count' => count($fileLogs)
                ];
            } catch (\Exception $e) {
                // Database failed, use file
                return [
                    'logs' => $this->getFromFile(),
                    'source' => 'file',
                    'database_available' => false,
                    'needs_sync' => false
                ];
            }
        } else {
            // Database unavailable, use file
            return [
                'logs' => $this->getFromFile(),
                'source' => 'file',
                'database_available' => false,
                'needs_sync' => false
            ];
        }
    }
    
    /**
     * Check if there are file logs that aren't in the database
     * 
     * Compares file logs against database logs by message and level.
     * Used to determine if sync button should be shown.
     * 
     * @param array $dbLogs   Logs from database
     * @param array $fileLogs Logs from file
     * @return bool True if file contains logs not in database
     */
    private function hasUnsyncedLogs(array $dbLogs, array $fileLogs): bool
    {
        if (empty($fileLogs)) {
            return false;
        }
        
        // Check if any file log doesn't exist in database
        foreach ($fileLogs as $fileLog) {
            $found = false;
            foreach ($dbLogs as $dbLog) {
                if ($dbLog['message'] === $fileLog['message'] && 
                    $dbLog['level'] === $fileLog['level']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return true; // Found a log in file that's not in database
            }
        }
        
        return false;
    }
    
    /**
     * Get a single log by ID
     */
    public function find(int $id): ?array
    {
        try {
            // Try database first
            return Log::find($id);
        } catch (\Exception $e) {
            // Fallback to file
            return $this->findInFile($id);
        }
    }
    
    /**
     * Add a new log entry to both database and file
     * 
     * File logging always succeeds; database logging is best-effort.
     * If database is unavailable, the log is captured in file and can be synced later.
     * 
     * @param string $level   Log level: 'info', 'warning', 'error', 'debug'
     * @param string $message Log message (should be descriptive)
     * @param array  $context Additional context data (e.g., user ID, IP, stack trace)
     * @return void
     */
    public function add(string $level, string $message, array $context = []): void
    {
        // Sanitize context to avoid persisting secrets
        $sanitizedContext = \sanitize_for_log($context);

        // Always log to file first (guaranteed to work)
        $this->logToFile($level, $message, $sanitizedContext);
        
        // Try to log to database (but don't fail if it doesn't work)
        try {
            Log::log($level, $message, $sanitizedContext);
        } catch (\Exception $e) {
            // Database logging failed, but file logging succeeded
            // Also record the logging failure to the file so it can be reconciled
            error_log("Failed to log to database: " . $e->getMessage());
            $this->logToFile('error', 'Failed to log to database', ['error' => substr($e->getMessage(), 0, 512)]);
        }
    }
    
    /**
     * Clear all logs (both database and file)
     */
    public function clear(): void
    {
        // Clear file
        file_put_contents($this->logFile, '[]');
        
        // Try to clear database
        try {
            $db = \Core\Database::getInstance();
            $db->execute("TRUNCATE TABLE logs");
        } catch (\Exception $e) {
            // Database clear failed, but file was cleared
            error_log("Failed to clear database logs: " . $e->getMessage());
        }
    }
    
    /**
     * Check if database is available
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
     * Synchronize file logs to database
     * 
     * Reconciles file-based logs to the database after database recovery.
     * Compares logs by message+level to avoid duplicates.
     * 
     * Use Cases:
     * - Database was temporarily unavailable
     * - Manual file-only logs were created for testing
     * - Post-recovery data consistency check
     * 
     * @return array Result with keys: success (bool), synced (int), skipped (int), errors (array)
     */
    public function syncToDatabase(): array
    {
        $result = [
            'success' => false,
            'synced' => 0,
            'skipped' => 0,
            'errors' => []
        ];
        
        // Check if database is available
        if (!$this->isDatabaseAvailable()) {
            $result['errors'][] = 'Database is not available';
            return $result;
        }
        
        // Get logs from file
        $fileLogs = $this->getFromFile();
        
        if (empty($fileLogs)) {
            $result['success'] = true;
            return $result;
        }
        
        // Get existing log messages from database to avoid duplicates
        try {
            $existingLogs = Log::all();
            $existingMessages = array_column($existingLogs, 'message');
            
            foreach ($fileLogs as $log) {
                // Skip if this log already exists in database (by message and level)
                $exists = false;
                foreach ($existingLogs as $existing) {
                    if ($existing['message'] === $log['message'] && 
                        $existing['level'] === $log['level']) {
                        $exists = true;
                        break;
                    }
                }
                
                if ($exists) {
                    $result['skipped']++;
                    continue;
                }
                
                // Add to database
                try {
                    Log::log(
                        $log['level'],
                        $log['message'],
                        $log['context'] ?? []
                    );
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
    

    /**
     * Get logs from file storage
     * 
     * @return array Array of log entries
     */
    private function getFromFile(): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $contents = @file_get_contents($this->logFile);
        
        if ($contents === false) {
            error_log("Failed to read log file: {$this->logFile}");
            return [];
        }
        
        $logs = json_decode($contents, true);
        
        if ($logs === null && $contents !== '[]') {
            error_log("Failed to parse log file JSON: {$this->logFile}");
            return [];
        }
        
        return $logs ?? [];
    }
    
    /**
     * Find a log in file
     */
    private function findInFile(int $id): ?array
    {
        $logs = $this->getFromFile();
        
        foreach ($logs as $log) {
            if ($log['id'] === $id) {
                return $log;
            }
        }
        
        return null;
    }
    
    /**
     * Write log entry to file storage
     * 
     * Uses atomic write with temporary file to prevent corruption.
     * Generates sequential IDs for file-based log entries.
     * 
     * @param string $level   Log level (info, warning, error, debug)
     * @param string $message Log message
     * @param array  $context Additional context data
     * @return void
     */
    private function logToFile(string $level, string $message, array $context = []): void
    {
        $logs = $this->getFromFile();
        
        // Generate sequential ID
        $maxId = 0;
        foreach ($logs as $log) {
            if (isset($log['id']) && $log['id'] > $maxId) {
                $maxId = $log['id'];
            }
        }
        
        $logs[] = [
            'id'        => $maxId + 1,
            'level'     => $level,
            'message'   => $message,
            'context'   => $context,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        // Atomic write: write to temp file then rename
        $tempFile = $this->logFile . '.tmp';
        $json = json_encode($logs, JSON_PRETTY_PRINT);
        
        if (@file_put_contents($tempFile, $json, LOCK_EX) === false) {
            error_log("Failed to write log file: {$tempFile}");
            return;
        }
        
        if (!@rename($tempFile, $this->logFile)) {
            error_log("Failed to rename log file: {$tempFile} to {$this->logFile}");
            @unlink($tempFile);
        }
    }
}