<?php

namespace App\Controllers;

use App\Services\LogService;

/**
 * Log Controller
 * 
 * Manages application logs using the LogService (dual database + file logging).
 */
class LogController extends Controller
{
    private LogService $logService;
    
    public function __construct()
    {
        $this->logService = new LogService();
    }
    
    /**
     * List all logs
     * Route: GET /logs
     */
    public function index(): void
    {
        // Get all logs (from database with file fallback)
        $result = $this->logService->all();
        
        // Extract logs and metadata
        $allLogs = $result['logs'];
        $source = $result['source'];
        $databaseAvailable = $result['database_available'];
        $needsSync = $result['needs_sync'] ?? false;
        $fileLogCount = $result['file_log_count'] ?? 0;
        
        // Reverse so newest are first (for file-based logs)
        // Database logs are already ordered by created_at DESC
        if ($source === 'file' && !empty($allLogs)) {
            $allLogs = array_reverse($allLogs);
        }
        
        // Pagination
        $perPage = 10;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalLogs = count($allLogs);
        $totalPages = max(1, ceil($totalLogs / $perPage));
        $page = min($page, $totalPages); // Ensure page doesn't exceed total pages
        $offset = ($page - 1) * $perPage;
        
        $logs = array_slice($allLogs, $offset, $perPage);
        
        $this->view('logs/index', [
            'title' => 'Application Logs',
            'logs'  => $logs,
            'source' => $source,
            'databaseAvailable' => $databaseAvailable,
            'needsSync' => $needsSync,
            'fileLogCount' => $fileLogCount,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
            'perPage' => $perPage,
        ]);
    }
    
    /**
     * Show a single log entry
     * Route: GET /logs/(\d+)
     */
    public function show(string $id): void
    {
        $logId = (int) $id;
        $log = $this->logService->find($logId);
        
        if (!$log) {
            $this->flash('error', "Log entry #{$id} not found");
            $this->redirect('/logs');
            return;
        }
        
        $this->view('logs/show', [
            'title' => "Log #{$id}",
            'log'   => $log,
        ]);
    }
    

    
    /**
     * Clear all logs
     * Route: POST /logs/clear
     * Middleware: csrf
     */
    public function clear(): void
    {
        $this->logService->clear();

        // Log the action (who cleared logs)
        $logService = new \App\Services\LogService();
        $logService->add('info', 'Logs cleared by user', sanitize_for_log([
            'user_id' => auth_user()['id'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]));
        
        $this->flash('success', 'All logs cleared (both database and file)');
        $this->redirect('/logs');
    }
    
    /**
     * Sync file logs to database
     * Route: POST /logs/sync
     * Middleware: csrf
     */
    public function sync(): void
    {
        $result = $this->logService->syncToDatabase();

        // Record sync attempt
        $logService = new \App\Services\LogService();
        $logService->add('info', 'Log sync attempted', sanitize_for_log([
            'user_id' => auth_user()['id'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'synced' => $result['synced'] ?? 0,
            'skipped' => $result['skipped'] ?? 0,
            'errors' => $result['errors'] ?? []
        ]));
        
        if ($result['success']) {
            if ($result['synced'] > 0) {
                $this->flash('success', "Synced {$result['synced']} logs to database. Skipped {$result['skipped']} duplicates.");
            } else {
                $this->flash('info', 'No new logs to sync.');
            }
        } else {
            $errorMsg = 'Sync failed: ' . implode(', ', $result['errors']);
            $this->flash('error', $errorMsg);
        }
        
        $this->redirect('/logs');
    }
}