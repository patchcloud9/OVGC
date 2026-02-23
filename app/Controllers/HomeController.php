<?php

namespace App\Controllers;

use App\Models\HomepageSetting;

/**
 * Home Controller
 * 
 * Handles the main pages of the site.
 */
class HomeController extends Controller
{
    /**
     * Home page
     * Route: GET /
     */
    public function index(): void
    {
        // Get homepage settings
        $settings = HomepageSetting::getSettings();
        
        $data = [
            'title' => 'Welcome Home',
            'settings' => $settings,
        ];
        
        $this->view('home/index', $data);
    }
    
    /**
     * About page
     * Route: GET /about
     */
    public function about(): void
    {
        $this->view('home/about', [
            'title' => 'About Us',
        ]);
    }
    
    /**
     * Contact page (GET - show form)
     * Route: GET /contact
     */
    public function contact(): void
    {
        $this->view('home/contact', [
            'title' => 'Contact Us',
        ]);
    }
    
    /**
     * Contact form submission (POST)
     * Route: POST /contact
     * Middleware: csrf, rate-limit:contact-form,5,60
     */
    public function contactSubmit(): void
    {
        // Validate input
        $validator = new \Core\Validator($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255',
            'message' => 'required|min:10|max:1000'
        ]);
        
        if ($validator->fails()) {
            // Store old input for repopulating form
            flash_old_input($_POST);
            
            // Show first error
            $errors = $validator->errors();
            $firstError = reset($errors)[0];
            $this->flash('error', $firstError);
            $this->redirect('/contact');
            return;
        }
        
        // Get validated data
        $name = $this->input('name');
        $email = $this->input('email');
        $message = $this->input('message');
        
        // In a real app, you'd save/send this
        // For now, log it
        $logService = new \App\Services\LogService();
        $logService->add('info', 'Contact form submitted', [
            'name' => $name,
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        // Clear old input on success
        clear_old_input();
        
        $this->flash('success', "Thanks {$name}! We received your message.");
        $this->redirect('/contact');
    }
    
    /**
     * Debug page - shows how routing works
     * Route: GET /debug
     */
    public function debug(): void
    {
        // Only expose debug info when APP_DEBUG is enabled
        if (!is_debug()) {
            throw new \Core\Exceptions\NotFoundHttpException('Not found');
        }

        // Gather debug info
        $debugInfo = [
            'Request' => [
                'Method' => $_SERVER['REQUEST_METHOD'],
                'URI' => $_SERVER['REQUEST_URI'],
                'Query String' => $_SERVER['QUERY_STRING'] ?? '(none)',
            ],
            'Server' => [
                'PHP Version' => phpversion(),
                'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'Document Root' => $_SERVER['DOCUMENT_ROOT'],
            ],
            'Paths' => [
                'BASE_PATH' => BASE_PATH,
                'Current File' => __FILE__,
            ],
            'Session' => $_SESSION,
            'GET Parameters' => $_GET ?: '(none)',
            'POST Parameters' => $_POST ?: '(none)',
        ];
        
        $this->view('home/debug', [
            'title' => 'Debug Info',
            'debugInfo' => $debugInfo,
        ]);
    }
    
    /**
     * Test 500 error page
     * Route: GET /test-500
     * 
     * Intentionally triggers a 500 error for testing
     */
    public function test500(): void
    {
        // Trigger a fatal error by calling a non-existent class method
        throw new \Exception('This is a test 500 error triggered intentionally from /test-500 route');
    }
}
