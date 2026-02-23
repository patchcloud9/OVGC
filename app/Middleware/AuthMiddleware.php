<?php

namespace App\Middleware;

use Core\Middleware;
use App\Services\LogService;

/**
 * Authentication Middleware
 * 
 * Ensures user is logged in before accessing protected routes.
 * Redirects to login page if not authenticated.
 * 
 * Usage in routes:
 *   'GET' => [
 *       '/dashboard' => ['HomeController', 'dashboard', ['auth']],
 *       '/admin/users' => ['AdminController', 'index', ['auth']],
 *   ]
 */
class AuthMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id'])) {
            // Log unauthorized access attempt
            $logService = new LogService();
            $logService->add('warning', 'Unauthorized access attempt', [
                'uri' => $_SERVER['REQUEST_URI'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'reason' => 'Not authenticated'
            ]);
            
            // Store the intended destination
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            \flash('error', 'Please log in to access this page.');
            $this->redirect('/login');
            return false;
        }
        
        return true;
    }
}
