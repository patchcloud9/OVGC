<?php

namespace Core;

/**
 * Middleware Base Class
 * 
 * All middleware must extend this class and implement the handle() method.
 * 
 * Middleware runs between the router and controller, allowing you to:
 * - Inspect/modify the request
 * - Authenticate users
 * - Validate tokens
 * - Log requests
 * - Rate limit
 * - Short-circuit the response
 * 
 * Usage:
 *   class MyMiddleware extends Middleware {
 *       public function handle(array $params = []): bool {
 *           // Do your checks
 *           return true;  // Continue to next middleware/controller
 *           return false; // Stop processing (already sent response)
 *       }
 *   }
 */
abstract class Middleware
{
    /**
     * Handle the middleware logic
     * 
     * @param array $params Optional parameters from route definition
     * @return bool True to continue to next middleware/controller, false to stop
     */
    abstract public function handle(array $params = []): bool;
    
    /**
     * Redirect helper
     */
    protected function redirect(string $url): void
    {
        redirect($url);
    }
    
    /**
     * JSON response helper
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
