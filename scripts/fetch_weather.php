<?php
// CLI script to update weather cache. Run from the workspace root:
//   php scripts/fetch_weather.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

require BASE_PATH . '/core/Autoloader.php';
\Core\Autoloader::register();

$result = \App\Services\WeatherService::updateCache();
if ($result) {
    echo "Weather cache updated successfully.\n";
    exit(0);
} else {
    echo "Failed to update weather cache.\n";
    $logfile = BASE_PATH . '/storage/logs/cron-error.log';
    if (is_file($logfile)) {
        echo "--- recent log entries ---\n";
        $lines = array_slice(file($logfile), -20);
        foreach ($lines as $line) {
            echo $line;
        }
    }
    exit(1);
}
