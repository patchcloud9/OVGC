<?php
// This script is intended to be invoked via HTTP by a cron job (wget/curl).
// It simply calls the WeatherService snapshot updater and outputs a status.

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
    $ok = \App\Services\WeatherService::updateSnapshot();
    if ($ok) {
        echo "OK";
        http_response_code(200);
    } else {
        echo "FAIL";
        http_response_code(500);
    }
} else {
    echo "Unauthorized";
    http_response_code(401);
}
