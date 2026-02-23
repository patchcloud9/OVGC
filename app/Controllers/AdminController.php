<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Log;

/**
 * Admin Controller
 * 
 * Handles admin panel and admin-only functionality.
 * All routes protected by role:admin middleware.
 */
class AdminController extends Controller
{
    /**
     * Show admin dashboard
     * Route: GET /admin
     * Middleware: auth, role:admin
     */
    public function index(): void
    {
        // Get recent logs if available
        $recentLogs = [];
        try {
            $recentLogs = Log::recent(5);
        } catch (\Exception $e) {
            // Logs might not be available
        }

        $this->view('admin/index', [
            'title' => 'Admin Panel',
            'recentLogs' => $recentLogs,
        ]);
    }
}
