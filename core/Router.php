<?php

namespace Core;

/**
 * Router Class
 * 
 * Matches incoming URLs to controller methods defined in config/routes.php
 * 
 * How it works:
 * 1. Load route definitions from config
 * 2. Get the current HTTP method and URI
 * 3. Loop through routes looking for a pattern match
 * 4. If found, instantiate the controller and call the method
 * 5. If not found, show 404 error
 */
class Router
{
    /**
     * All registered routes, organized by HTTP method
     * @var array
     */
    protected array $routes;
    
    /**
     * Middleware aliases for cleaner route definitions
     * @var array
     */
    protected array $middlewareAliases = [
        'csrf' => \App\Middleware\CsrfMiddleware::class,
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'role' => \App\Middleware\RoleMiddleware::class,
        'log-request' => \App\Middleware\LogRequestMiddleware::class,
    ];
    
    /**
     * Constructor - loads routes from config file
     */
    public function __construct()
    {
        $this->routes = require BASE_PATH . '/config/routes.php';
    }
    
    /**
     * Dispatch the request to the appropriate controller
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $uri    The request URI path
     */
    public function dispatch(string $method, string $uri): void
    {
        // Handle method override for DELETE/PUT requests via POST
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Normalize the URI
        // - Remove trailing slash (so /about and /about/ both work)
        // - But keep '/' for the home page
        $uri = $this->normalizeUri($uri);
        
        // Log for debugging (you can see this in your browser's network tab or server logs)
        if (APP_DEBUG) {
            error_log("Router: {$method} {$uri}");
        }
        
        // Check if we have any routes for this HTTP method
        if (!isset($this->routes[$method])) {
            throw new \Core\Exceptions\NotFoundHttpException("No routes defined for {$method} method");
        }
        
        // Loop through each route pattern for this HTTP method
        foreach ($this->routes[$method] as $pattern => $handler) {
            // Try to match the URI against this pattern
            $params = $this->matchRoute($pattern, $uri);
            
            // If we got a match (returns array, even if empty)
            if ($params !== false) {
                $this->callController($handler, $params);
                return;
            }
        }
        
        // No route matched - throw 404
        throw new \Core\Exceptions\NotFoundHttpException("No route matches {$method} {$uri}");
    }
    
    /**
     * Normalize the URI for consistent matching
     * 
     * @param string $uri
     * @return string
     */
    protected function normalizeUri(string $uri): string
    {
        // Remove trailing slash, but keep '/' for root
        $uri = rtrim($uri, '/') ?: '/';
        
        // Remove query string if present (shouldn't be, but just in case)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return $uri;
    }
    
    /**
     * Try to match a route pattern against the URI
     * 
     * @param string $pattern The route pattern (e.g., '/users/(\d+)')
     * @param string $uri     The actual URI (e.g., '/users/42')
     * @return array|false    Array of captured parameters, or false if no match
     */
    protected function matchRoute(string $pattern, string $uri): array|false
    {
        // Convert route pattern to a regex
        // - Escape forward slashes
        // - Wrap in delimiters and anchors
        // 
        // Example: '/users/(\d+)' becomes '#^/users/(\d+)$#'
        $regex = '#^' . $pattern . '$#';
        
        // Try to match
        if (preg_match($regex, $uri, $matches)) {
            // $matches[0] is the full match (the whole URI)
            // $matches[1], [2], etc. are the captured groups (the parameters)
            
            // Remove the full match, keep only captured groups
            array_shift($matches);
            
            return $matches;
        }
        
        return false;
    }
    
    /**
     * Instantiate a controller and call a method
     * 
     * @param array|string $handler [ControllerName, methodName] or [ControllerName, methodName, [middleware]]
     * @param array $params  Parameters to pass to the method
     */
    protected function callController(array $handler, array $params): void
    {
        // Extract controller, method, and optional middleware
        $controllerName = $handler[0];
        $methodName = $handler[1];
        $middleware = $handler[2] ?? [];
        
        // Execute middleware pipeline
        if (!$this->runMiddleware($middleware)) {
            // Middleware stopped the request
            return;
        }
        
        // Build the fully qualified class name
        // 'HomeController' becomes 'App\Controllers\HomeController'
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        // Check if the controller class exists
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller not found: {$controllerClass}");
        }
        
        // Create an instance of the controller
        $controller = new $controllerClass();
        
        // Check if the method exists
        if (!method_exists($controller, $methodName)) {
            throw new \Exception("Method not found: {$controllerClass}::{$methodName}");
        }
        
        // Call the controller method with the parameters
        // call_user_func_array allows us to pass an array as individual arguments
        // So if $params = ['42', '23'], it calls: $controller->method('42', '23')
        call_user_func_array([$controller, $methodName], $params);
    }
    
    /**
     * Run middleware pipeline
     * 
     * @param array $middlewareList Array of middleware names/classes
     * @return bool True if all middleware passed, false if any stopped the request
     */
    protected function runMiddleware(array $middlewareList): bool
    {
        foreach ($middlewareList as $middlewareDefinition) {
            // Parse middleware definition
            // Can be: 'csrf', 'auth', or 'rate-limit:contact-form,5,60'
            $parts = explode(':', $middlewareDefinition, 2);
            $middlewareName = $parts[0];
            $params = isset($parts[1]) ? explode(',', $parts[1]) : [];
            
            // Resolve middleware class
            $middlewareClass = $this->resolveMiddleware($middlewareName);
            
            if (!$middlewareClass) {
                if (APP_DEBUG) {
                    error_log("Router: Unknown middleware '{$middlewareName}'");
                }
                continue;
            }
            
            // Instantiate and run middleware
            try {
                $middleware = new $middlewareClass();
                
                if (!$middleware->handle($params)) {
                    // Middleware returned false - stop processing
                    return false;
                }
            } catch (\Exception $e) {
                if (APP_DEBUG) {
                    error_log("Router: Middleware error: " . $e->getMessage());
                }
                throw $e;
            }
        }
        
        return true;
    }
    
    /**
     * Resolve middleware name to class name
     * 
     * @param string $name Middleware alias or fully qualified class name
     * @return string|null
     */
    protected function resolveMiddleware(string $name): ?string
    {
        // Check if it's an alias
        if (isset($this->middlewareAliases[$name])) {
            return $this->middlewareAliases[$name];
        }
        
        // Check if it's already a full class name
        if (class_exists($name)) {
            return $name;
        }
        
        // Try to find it in App\Middleware
        $className = "App\\Middleware\\{$name}";
        if (class_exists($className)) {
            return $className;
        }
        
        return null;
    }
}
