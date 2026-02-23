<?php

namespace App\Middleware;

use Core\Middleware;

/**
 * Guest Middleware
 * 
 * Ensures user is NOT logged in (for login/register pages).
 * Redirects authenticated users away from guest-only pages.
 * 
 * Usage in routes:
 *   'GET' => [
 *       '/login' => ['AuthController', 'showLogin', ['guest']],
 *       '/register' => ['AuthController', 'showRegister', ['guest']],
 *   ]
 */
class GuestMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        // If user is authenticated, redirect them away
        if (isset($_SESSION['user_id'])) {
            // Check if there's an intended destination, otherwise go to dashboard
            $destination = $_SESSION['intended_url'] ?? '/dashboard';
            unset($_SESSION['intended_url']);
            
            $this->redirect($destination);
            return false;
        }
        
        return true;
    }
}
