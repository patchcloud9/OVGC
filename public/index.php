<?php
/**
 * Front Controller
 *
 * This is the single entry point for all requests.
 * Apache's .htaccess redirects everything here.
 */

// Define the base path first so everything else can reference it
define('BASE_PATH', dirname(__DIR__));

// Pre-config safety net: if config.php itself fails, errors still reach a log somewhere
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Load the configuration (plain defines — safe to run before anything else)
require_once BASE_PATH . '/config/config.php';

// Now APP_DEBUG is defined — enable display only in dev
if (defined('APP_DEBUG') && APP_DEBUG) {
    ini_set('display_errors', '1');
}

// Route PHP errors to a fixed file; cPanel host locks the error_log field in PHP Options
// so this must be set at runtime. Covers parse errors, notices, warnings, fatals.
ini_set('error_log', BASE_PATH . '/storage/logs/php_errors.log');

// Load the autoloader
require_once BASE_PATH . '/core/Autoloader.php';

// Load helper functions
require_once BASE_PATH . '/core/helpers.php';

// Initialize debug bar as early as possible so it can time the full request
if (is_debug()) {
    \Core\DebugBar::getInstance();
}

// Start session with secure settings
session_start([
    'cookie_httponly' => true,
    // Use secure cookies when not in debug mode (i.e., production)
    'cookie_secure' => !is_debug(),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// Set up global exception handler
set_exception_handler(function ($exception) {
    if (is_debug() && class_exists('Core\DebugBar')) {
        \Core\DebugBar::getInstance()->recordException($exception);
    }

    // 404s are normal traffic — log at info. Everything else is an error.
    $is404  = $exception instanceof \Core\Exceptions\NotFoundHttpException;
    $level  = $is404 ? 'info' : 'error';

    try {
        $logService = new \App\Services\LogService();
        $logService->add($level, 'Uncaught Exception: ' . $exception->getMessage(), [
            'exception_class' => get_class($exception),
            'message'         => $exception->getMessage(),
            'file'            => $exception->getFile(),
            'line'            => $exception->getLine(),
            'uri'             => $_SERVER['REQUEST_URI'] ?? null,
            'method'          => $_SERVER['REQUEST_METHOD'] ?? null,
            'ip'              => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'trace'           => $exception->getTraceAsString(),
        ]);
    } catch (\Throwable $e) {
        // LogService unavailable — fall back to php_errors.log
        error_log('Failed to write exception to LogService: ' . $e->getMessage());
        error_log('Uncaught Exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
    }

    // HTTP exceptions carry their own status code
    if ($exception instanceof \Core\Exceptions\HttpException) {
        http_response_code($exception->getStatusCode());

        $message = APP_DEBUG ? $exception->getMessage() : null;
        $trace   = APP_DEBUG ? $exception->getTraceAsString() : null;

        if ($exception->getStatusCode() === 404) {
            require BASE_PATH . '/app/Views/errors/404.php';
        } else {
            require BASE_PATH . '/app/Views/errors/500.php';
        }
    } else {
        http_response_code(500);

        $message = APP_DEBUG ? $exception->getMessage() : null;
        $trace   = APP_DEBUG ? $exception->getTraceAsString() : null;

        require BASE_PATH . '/app/Views/errors/500.php';
    }

    exit;
});

// Catch fatal errors (E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR) that bypass the exception handler
register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        return;
    }

    error_log("Fatal error: [{$error['type']}] {$error['message']} in {$error['file']} on line {$error['line']}");

    try {
        $logService = new \App\Services\LogService();
        $logService->add('error', 'Fatal PHP error', [
            'type'    => $error['type'],
            'message' => $error['message'],
            'file'    => $error['file'],
            'line'    => $error['line'],
            'uri'     => $_SERVER['REQUEST_URI'] ?? null,
            'method'  => $_SERVER['REQUEST_METHOD'] ?? null,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);
    } catch (\Throwable $e) {
        error_log('Failed to write fatal error to LogService: ' . $e->getMessage());
    }

    if (!headers_sent()) {
        http_response_code(500);
    }

    $message = (defined('APP_DEBUG') && APP_DEBUG) ? $error['message'] : null;
    $trace    = null;
    require BASE_PATH . '/app/Views/errors/500.php';
});

// Get the request URI and method
// parse_url extracts just the path, ignoring query strings
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Create router and dispatch the request
$router = new Core\Router();
$router->dispatch($method, $uri);
