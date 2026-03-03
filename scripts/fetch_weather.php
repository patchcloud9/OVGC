<?php
// CLI script to update weather snapshot. Run from the workspace root:
//   php scripts/fetch_weather.php

require __DIR__ . '/../app/Services/WeatherService.php';

if (WeatherService::updateSnapshot()) {
    echo "Weather snapshot updated successfully.\n";
    exit(0);
} else {
    echo "Failed to update weather snapshot.\n";
    exit(1);
}
