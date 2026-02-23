<?php

namespace App\Services;

use App\Models\User;

/**
 * Authentication Service
 * 
 * Handles user authentication, registration, and session management.
 * 
 * Usage:
 *   $auth = new AuthService();
 *   $user = $auth->login('email@example.com', 'password');
 *   $auth->logout();
 *   $user = $auth->register(['name' => 'John', 'email' => '...', 'password' => '...']);
 */
class AuthService
{
    /**
     * Attempt to log in a user
     * 
     * @param string $email
     * @param string $password
     * @return array|null User data if successful, null otherwise
     */
    public function login(string $email, string $password): ?array
    {
        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user) {
            // Log failed login attempt (user not found)
            $logService = new LogService();
            $logService->add('warning', 'Failed login attempt - user not found', [
                'email' => sanitize_for_log(['email' => $email])['email'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return null;
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Log failed login attempt (incorrect password)
            $logService = new LogService();
            $logService->add('warning', 'Failed login attempt - incorrect password', [
                'user_id' => $user['id'],
                'email' => sanitize_for_log(['email' => $email])['email'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return null;
        }
        
        // Create session
        $this->createSession($user);
        
        // Log the login
        $logService = new LogService();
        $logService->add('info', 'User logged in', sanitize_for_log([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]));
        
        return $user;
    }
    
    /**
     * Register a new user
     * 
     * @param array $data ['name', 'email', 'password']
     * @return array Created user data
     */
    public function register(array $data): array
    {
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default role
        $data['role'] = $data['role'] ?? 'user';
        
        // Create the user
        $user = User::create($data);
        
        // Log the registration
        $logService = new LogService();
        $logService->add('info', 'New user registered', sanitize_for_log([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]));
        
        // Automatically log them in
        $this->createSession($user);
        
        return $user;
    }
    
    /**
     * Log out the current user
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        // Log the logout
        if ($userId) {
            $logService = new LogService();
            $logService->add('info', 'User logged out', sanitize_for_log([
                'user_id' => $userId,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
        }
        
        // Clear session data
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }
    
    /**
     * Get the currently authenticated user
     * 
     * @return array|null User data if authenticated, null otherwise
     */
    public function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return User::find((int) $_SESSION['user_id']);
    }
    
    /**
     * Check if a user is authenticated
     * 
     * @return bool
     */
    public function check(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if the authenticated user is a guest (not logged in)
     * 
     * @return bool
     */
    public function guest(): bool
    {
        return !$this->check();
    }
    
    /**
     * Get the authenticated user's ID
     * 
     * @return int|null
     */
    public function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Check if the authenticated user has a specific role
     * 
     * @param string $role Role to check (e.g., 'admin', 'user')
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (!$this->check()) {
            return false;
        }
        
        return ($_SESSION['user_role'] ?? '') === $role;
    }
    
    /**
     * Check if the authenticated user is an admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Create a session for the user
     * 
     * @param array $user User data
     */
    private function createSession(array $user): void
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
    }
}
