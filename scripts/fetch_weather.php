<?php
// CLI script to update weather snapshot. Run from the workspace root:
//   php scripts/fetch_weather.php

// bootstrap config so BASE_PATH, etc. are defined
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/Services/WeatherService.php';

$result = WeatherService::updateSnapshot();
if ($result) {
    echo "Weather snapshot updated successfully.\n";
    exit(0);
} else {
    echo "Failed to update weather snapshot.\n";
    // log failure for diagnostics
    $logfile = __DIR__ . '/../storage/logs/cron.log';
    file_put_contents($logfile, date('Y-m-d H:i:s') . " weather snapshot update failed\n", FILE_APPEND);
    exit(1);
}
