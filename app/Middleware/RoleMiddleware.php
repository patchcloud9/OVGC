<?php

namespace App\Middleware;

use Core\Middleware;
use App\Services\LogService;

/**
 * Role Middleware
 * 
 * Ensures the authenticated user has a specific role.
 * Must be used after AuthMiddleware.
 * 
 * Usage in routes:
 *   'GET' => [
 *       '/admin/dashboard' => ['AdminController', 'index', ['auth', 'role:admin']],
 *       '/moderator/posts' => ['ModeratorController', 'index', ['auth', 'role:moderator']],
 *   ]
 */
class RoleMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        if (empty($params)) {
            if (APP_DEBUG) {
                error_log("RoleMiddleware: No role specified");
            }
            return true;
        }
        
        $requiredRole = $params[0];
        $userRole = $_SESSION['user_role'] ?? null;
        
        // Check if user has the required role
        if ($userRole !== $requiredRole) {
            // Log insufficient privileges attempt
            $logService = new LogService();
            $logService->add('warning', 'Insufficient privileges', sanitize_for_log([
                'uri' => $_SERVER['REQUEST_URI'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_id' => $_SESSION['user_id'] ?? null,
                'user_email' => $_SESSION['user_email'] ?? 'unknown',
                'user_role' => $userRole,
                'required_role' => $requiredRole,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]));
            
            \flash('error', 'You do not have permission to access this page.');
            $this->redirect('/');
            return false;
        }
        
        return true;
    }
}
