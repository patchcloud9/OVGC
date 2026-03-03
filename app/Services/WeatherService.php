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

        $html = @file_get_contents(self::SOURCE_URL, false, $context);
        if ($html === false) {
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

        file_put_contents(self::cacheFile(), $widgetHtml);
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
}
