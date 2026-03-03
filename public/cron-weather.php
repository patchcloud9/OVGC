<?php
// This script is intended to be invoked via HTTP by a cron job (wget/curl).
// It simply calls the WeatherService snapshot updater and outputs a status.

// wrap whole script so any fatal error is caught and logged
try {
    // bootstrap minimal environment
    if (!defined('BASE_PATH')) {
        define('BASE_PATH', realpath(__DIR__ . '/..'));
    }
    require BASE_PATH . '/core/Autoloader.php';
    \Core\Autoloader::register();
    require BASE_PATH . '/app/Services/WeatherService.php';

    // optionally protect by a simple key param, e.g. ?key=secret
    $expected = 'please_change_me';
    if (isset($_GET['key']) && $_GET['key'] === $expected) {
        try {
            $ok = \App\Services\WeatherService::updateSnapshot();
            if ($ok) {
                echo "OK";
                http_response_code(200);
            } else {
                echo "FAIL";
                http_response_code(500);
            }
        } catch (\Throwable $e) {
            // output exception text for debugging
            echo "EXCEPTION: " . $e->getMessage();
            file_put_contents(BASE_PATH . '/storage/logs/cron-error.log', date('c') . " Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            http_response_code(500);
        }
    } else {
        echo "Unauthorized";
        http_response_code(401);
    }
} catch (\Throwable $e) {
    file_put_contents(BASE_PATH . '/storage/logs/cron-error.log', date('c') . " Fatal: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "ERROR";
    http_response_code(500);
}
