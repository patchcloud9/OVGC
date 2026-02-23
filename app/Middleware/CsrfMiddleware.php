<?php

namespace App\Middleware;

use Core\Middleware;

/**
 * CSRF Protection Middleware
 * 
 * Automatically validates CSRF tokens on state-changing requests (POST, PUT, DELETE).
 * This eliminates the need for manual verifyCsrf() calls in controllers.
 * 
 * Usage in routes:
 *   'POST' => [
 *       '/contact' => ['HomeController', 'contactSubmit', ['csrf']],
 *   ]
 */
class CsrfMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Only check CSRF on state-changing requests
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return true;
        }
        
        // Get token from POST or query string
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        
        // Verify the token
        if (!csrf_verify($token)) {
            if (is_debug()) {
                throw new \Exception('CSRF token validation failed');
            }
            
            \flash('error', 'Invalid security token. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return false;
        }
        
        return true;
    }
}
