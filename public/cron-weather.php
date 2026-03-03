<?php
// This script is intended to be invoked via HTTP by a cron job (wget/curl).
// It simply calls the WeatherService snapshot updater and outputs a status.

// wrap whole script so any fatal error is caught and logged
try {
    // bootstrap minimal environment
    if (!defined('BASE_PATH')) {
        // public/cron-weather.php lives one level below the project root
        define('BASE_PATH', dirname(__DIR__));
    }
    // diagnostic log of base path and existence of files
    $debugLog = BASE_PATH . '/storage/logs/cron-error.log';
    file_put_contents($debugLog, date('c') . " BASE_PATH=" . BASE_PATH . "\n", FILE_APPEND);
    $autoloaderPath = BASE_PATH . '/core/Autoloader.php';
    file_put_contents($debugLog, date('c') . " autoloader exists?=" . (is_file($autoloaderPath) ? 'yes' : 'no') . " path={$autoloaderPath}\n", FILE_APPEND);
    if (is_file($autoloaderPath)) {
        // simply include the autoloader file (it registers itself)
        require $autoloaderPath;
    } else {
        throw new \RuntimeException("Autoloader file missing");
    }
    require BASE_PATH . '/app/Services/WeatherService.php';

    // optionally protect by a simple key param, e.g. ?key=secret
    // prefer environment variable so key isn't stored in source
    $expected = getenv('WEATHER_KEY') ?: '477kHwPEw6ZBSUbhEB';
    // version constant to help verify deployment
    define('CRON_WEATHER_VERSION', '20260302-1');
    if (isset($_GET['key']) && $_GET['key'] === $expected) {
        try {
            $ok = \App\Services\WeatherService::updateCache();
            if ($ok) {
                echo "OK";
                http_response_code(200);
            } else {
                echo "FAIL";
                http_response_code(500);
            }
        } catch (\Throwable $e) {
            echo "EXCEPTION: " . $e->getMessage();
            file_put_contents(BASE_PATH . '/storage/logs/cron-error.log', date('c') . " Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            http_response_code(500);
        }
        // show version and fetch diagnostics
        $diags = \App\Services\WeatherService::getFetchDiagnostics();
        echo "\nversion=" . CRON_WEATHER_VERSION . "\n";
        echo "allow_url_fopen=" . $diags['allow_url_fopen'] . " curl=" . $diags['curl_available'] . "\n";
        // always append last 20 lines of log to response
        $logfile = BASE_PATH . '/storage/logs/cron-error.log';
        if (is_file($logfile)) {
            echo "\n--- log ---\n";
            $lines = array_slice(file($logfile), -20);
            foreach ($lines as $line) {
                echo htmlspecialchars($line);
            }
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
