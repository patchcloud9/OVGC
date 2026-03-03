<?php

namespace App\Services;

/**
 * WeatherService
 *
 * Provides a simple cache of the external weather widget HTML so that the
 * main site doesn't have to hit the third-party server on every request.
 * A cron job or scheduled task can call updateSnapshot() every 15 minutes.
 */
class WeatherService
{
    // location URL we pull from (forecast7 page for Omak, WA)
    private const SOURCE_URL = 'https://forecast7.com/en/48d41n119d53/omak/?unit=us';
    // previous constant replaced by a method to allow dynamic resolution of BASE_PATH
    // even when running from CLI. The storage/cache directory holds the snapshot.
    private static function cacheFile(): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : realpath(__DIR__ . '/../../');
        return rtrim($base, '/\\') . '/storage/cache/weather-snapshot.html';
    }

    /**
     * Fetch the source page, extract the widget markup, and save to cache file.
     * Returns true on success.
     */
    public static function updateSnapshot(): bool
    {
        // make sure cache directory exists
        $file = self::cacheFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $opts = [
            'http' => [
                'timeout' => 5, // seconds
                'user_agent' => 'OVGC/1.0 (+https://www.okanoganvalleygolf.com)',
            ],
        ];
        $context = stream_context_create($opts);

        $html = false;
        // try using file_get_contents if allowed
        if (ini_get('allow_url_fopen')) {
            $html = @file_get_contents(self::SOURCE_URL, false, $context);
        }

        // if we still have nothing and curl is available, try that as a fallback
        if (($html === false || $html === null) && function_exists('curl_version')) {
            $ch = curl_init(self::SOURCE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $opts['http']['timeout']);
            curl_setopt($ch, CURLOPT_USERAGENT, $opts['http']['user_agent']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $html = curl_exec($ch);
            $curlErr = curl_error($ch);
            curl_close($ch);
            if ($html === false || $html === null || $html === '') {
                // record curl error for logging below
                $curlErr = $curlErr ?: 'unknown';
            } else {
                $curlErr = null;
            }
        } else {
            $curlErr = null;
        }

        if ($html === false || $html === null) {
            // log failure details to cron-error.log if defined
            $log = defined('BASE_PATH') ? BASE_PATH . '/storage/logs/cron-error.log' : null;
            if ($log) {
                $msg = "fetch failed";
                $msg .= ' allow_url_fopen=' . (ini_get('allow_url_fopen') ? '1' : '0');
                $msg .= ' curl_available=' . (function_exists('curl_version') ? '1' : '0');
                if (isset($http_response_header)) {
                    $msg .= ' headers=' . implode(' | ', $http_response_header);
                }
                if (!empty($curlErr)) {
                    $msg .= ' curl_error=' . $curlErr;
                }
                $err = error_get_last();
                if ($err) {
                    $msg .= ' php_error=' . $err['message'];
                }
                file_put_contents($log, date('c') . " WeatherService: $msg\n", FILE_APPEND);
            }
            return false;
        }

        // parse out the <a class="weatherwidget-io" ...> element and script if DOM is available
        $widgetHtml = '';
        if (class_exists('DOMDocument')) {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            if ($dom->loadHTML($html)) {
                $xpath = new \DOMXPath($dom);
                $nodes = $xpath->query('//a[contains(@class,"weatherwidget-io")]');
                if ($nodes->length) {
                    $widgetHtml .= $dom->saveHTML($nodes->item(0));
                }
                // include the initialization script if present
                $scripts = $xpath->query('//script[contains(text(),"weatherwidget.io") or contains(@src,"weatherwidget.io")]);');
                foreach ($scripts as $script) {
                    $widgetHtml .= "\n" . $dom->saveHTML($script);
                }
            }
        }

        // if we couldn't parse anything, fall back to raw HTML
        if (trim($widgetHtml) === '') {
            $widgetHtml = $html;
        }

        $result = file_put_contents(self::cacheFile(), $widgetHtml);
        if ($result === false) {
            if (defined('BASE_PATH')) {
                file_put_contents(BASE_PATH . '/storage/logs/cron-error.log', date('c') . " WeatherService: failed to write cache\n", FILE_APPEND);
            }
            return false;
        }
        return true;
    }

    /**
     * Get the cached snapshot HTML (may contain script tag)
     *
     * @return string
     */
    public static function getSnapshot(): string
    {
        $path = self::cacheFile();
        if (is_file($path)) {
            return file_get_contents($path);
        }
        return '';
    }

    /**
     * Return useful environment diagnostics related to fetching
     *
     * @return array<string,string>
     */
    public static function getFetchDiagnostics(): array
    {
        return [
            'allow_url_fopen' => ini_get('allow_url_fopen') ? '1' : '0',
            'curl_available' => function_exists('curl_version') ? '1' : '0',
        ];
    }
}
