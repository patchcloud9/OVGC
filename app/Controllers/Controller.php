<?php

namespace App\Controllers;

/**
 * Base Controller
 * 
 * All controllers extend this class to get common functionality:
 * - View rendering
 * - Redirects
 * - JSON responses
 * - Access to request data
 * - CSRF protection
 */
class Controller
{
    /**
     * Verify CSRF token for state-changing requests
     * Call this at the beginning of POST/PUT/DELETE/PATCH methods
     * 
     * @throws \Exception If CSRF token is invalid
     */
    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;
        
        if (!csrf_verify($token)) {
            if (APP_DEBUG) {
                throw new \Exception('CSRF token validation failed');
            }
            
            $this->flash('error', 'Security token invalid. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
    /**
     * Render a view with optional data
     * 
     * @param string $viewPath Path relative to Views folder (e.g., 'home/index')
     * @param array  $data     Variables to make available in the view
     * @param string $layout   Layout to wrap the view (null for no layout)
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void
    {
        // Extract data array into individual variables
        // ['user' => $user, 'posts' => $posts] becomes $user and $posts
        extract($data);
        
        // Build the full path to the view file
        $viewFile = BASE_PATH . '/app/Views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: {$viewPath}");
        }
        
        if ($layout) {
            // Capture the view content into a variable
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Now render the layout, which will use $content
            $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout not found: {$layout}");
            }
            
            require $layoutFile;
        } else {
            // No layout - just render the view directly
            require $viewFile;
        }
    }
    
    /**
     * Render a view without a layout
     */
    protected function partial(string $viewPath, array $data = []): void
    {
        $this->view($viewPath, $data, null);
    }
    
    /**
     * Return a JSON response
     * 
     * @param mixed $data Data to encode as JSON
     * @param int   $statusCode HTTP status code
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Redirect to another URL
     * 
     * @param string $url  URL to redirect to
     * @param int    $code HTTP status code (302 = temporary, 301 = permanent)
     */
    protected function redirect(string $url, int $code = 302): void
    {
        http_response_code($code);
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Get a value from GET parameters
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Get a value from POST parameters
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get all POST data
     */
    protected function all(): array
    {
        return $_POST;
    }
    
    /**
     * Check if this is an AJAX request
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Set a flash message (shown once on next page load)
     */
    protected function flash(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        
        $_SESSION['flash'][] = [
            'type' => $type,      // 'success', 'error', 'warning', 'info'
            'message' => $message
        ];
    }
    
    /**
     * Get and clear the flash message
     */
    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
