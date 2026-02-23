<?php
/**
 * Main Configuration File
 *
 * Loads values from environment variables when available. A minimal .env
 * loader is included for development convenience; DO NOT commit secrets.
 */

// Basic .env loader (development only). It reads lines of the format KEY=VALUE
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "'\"");
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function env(string $key, $default = null)
{
    $val = getenv($key);
    return $val !== false ? $val : $default;
}

// Application settings
define('APP_NAME', env('APP_NAME', 'Base Framework'));
define('APP_ENV', env('APP_ENV', 'development'));  // 'development' or 'production'
define('APP_DEBUG', filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));
define('APP_URL', env('APP_URL', 'http://localhost'));

// Database settings — prefer environment variables for credentials
define('DB_HOST', env('DB_HOST', 'maria_db_mvelopes'));
define('DB_NAME', env('DB_NAME', 'baseframework'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// Timezone
date_default_timezone_set(env('APP_TIMEZONE', 'America/Los_Angeles'));

// IMPORTANT: Do not commit a production .env file. Use .env.example as a template
// and set secrets via your deployment system or CI/CD secret store.

