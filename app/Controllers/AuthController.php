<?php

namespace App\Controllers;

use App\Services\AuthService;
use Core\Validator;

/**
 * Authentication Controller
 * 
 * Handles login, logout, and registration for users.
 */
class AuthController extends Controller
{
    private AuthService $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    /**
     * Show the login form
     * Route: GET /login
     * Middleware: guest
     */
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
        ]);
    }
    
    /**
     * Handle login form submission
     * Route: POST /login
     * Middleware: guest, csrf, rate-limit:login,5,300
     */
    public function login(): void
    {
        // Validate input
        $validator = new Validator($_POST, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            flash_old_input($_POST);

            // Log validation failure for login attempts
            $logService = new \App\Services\LogService();
            $logService->add('warning', 'Login validation failed', [
                'email' => sanitize_for_log(['email' => $this->input('email')])['email'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('error', 'Please provide valid credentials.');
            $this->redirect('/login');
            return;
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        
        // Attempt login
        $user = $this->authService->login($email, $password);
        
        if (!$user) {
            flash_old_input(['email' => $email]);
            $this->flash('error', 'Invalid email or password.');
            $this->redirect('/login');
            return;
        }
        
        clear_old_input();
        
        // Check for intended destination
        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);
        
        // If no intended URL, redirect based on role
        if (!$intended) {
            $intended = ($user['role'] === 'admin') ? '/admin' : '/';
        }
        
        $this->flash('success', "Welcome back, {$user['name']}!");
        $this->redirect($intended);
    }
    
    /**
     * Show the registration form
     * Route: GET /register
     * Middleware: guest
     */
    public function showRegister(): void
    {
        $this->view('auth/register', [
            'title' => 'Register',
        ]);
    }
    
    /**
     * Handle registration form submission
     * Route: POST /register
     * Middleware: guest, csrf, rate-limit:register,3,600
     */
    public function register(): void
    {
        // Validate input
        $validator = new Validator($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|max:255',
            'password_confirmation' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            flash_old_input($_POST);
            
            // Log registration validation failure (do not store raw passwords)
            $errors = $validator->errors();
            $firstError = reset($errors)[0];

            $logService = new \App\Services\LogService();
            $logService->add('warning', 'Registration validation failed', [
                'email' => sanitize_for_log(['email' => $this->input('email')])['email'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'error' => $firstError
            ]);

            // Show first error
            $this->flash('error', $firstError);
            $this->redirect('/register');
            return;
        }
        
        // Register the user
        $user = $this->authService->register([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
        ]);
        
        clear_old_input();
        
        $this->flash('success', "Welcome, {$user['name']}! Your account has been created.");
        $this->redirect('/');
    }
    
    /**
     * Handle logout
     * Route: POST /logout
     * Middleware: csrf, auth
     */
    public function logout(): void
    {
        $this->authService->logout();
        
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/');
    }
}
