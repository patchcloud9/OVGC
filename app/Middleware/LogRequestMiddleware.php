<?php

namespace App\Middleware;

use Core\Middleware;
use App\Services\LogService;

/**
 * Request Logging Middleware
 * 
 * Logs all incoming requests for debugging and monitoring.
 * 
 * Usage in routes:
 *   'GET' => [
 *       '/' => ['HomeController', 'index', ['log-request']],
 *   ]
 * 
 * Or apply globally in Router if you want to log everything.
 */
class LogRequestMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        $logService = new LogService();
        
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logService->add('info', "{$method} {$uri}", sanitize_for_log([
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? null,
        ]));
        
        return true;
    }
}
