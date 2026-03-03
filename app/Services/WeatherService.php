<?php

namespace App\Services;

/**
 * WeatherService
 *
 * Fetches weather data from the free NWS (api.weather.gov) API and caches
 * it to storage/cache/weather-data.json.  A cron job should call
 * updateCache() every 15–30 minutes so the homepage widget loads instantly.
 */
class WeatherService
{
    // Omak, WA
    private const POINTS_URL  = 'https://api.weather.gov/points/48.4104,-119.5296';
    private const USER_AGENT  = 'OVGC/1.0 (okanoganvalleygolf.com)';
    private const CACHE_TTL   = 1800;  // 30 minutes
    private const API_TIMEOUT = 10;    // seconds per request

    // -------------------------------------------------------------------------
    // Path helpers
    // -------------------------------------------------------------------------

    private static function cacheFile(): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : realpath(__DIR__ . '/../../');
        return rtrim($base, '/\\') . '/storage/cache/weather-data.json';
    }

    private static function logFile(): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : realpath(__DIR__ . '/../../');
        return rtrim($base, '/\\') . '/storage/logs/cron-error.log';
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Fetch fresh data from the NWS API and write to cache.
     * Returns true on success.
     */
    public static function updateCache(): bool
    {
        // Step 1: grid metadata
        $points = self::fetchJson(self::POINTS_URL);
        if (!$points) {
            self::log('points API call failed');
            return false;
        }

        $forecastUrl = $points['properties']['forecast']            ?? null;
        $stationsUrl = $points['properties']['observationStations'] ?? null;

        if (!$forecastUrl) {
            self::log('missing forecast URL in points response');
            return false;
        }

        // Step 2: 7-day forecast periods
        $forecastData = self::fetchJson($forecastUrl);
        if (!$forecastData) {
            self::log('forecast API call failed: ' . $forecastUrl);
            return false;
        }
        $periods = $forecastData['properties']['periods'] ?? [];

        // Step 3: current observations from nearest station (best-effort)
        $obsProps = null;
        if ($stationsUrl) {
            $stations  = self::fetchJson($stationsUrl . '?limit=1');
            $stationId = $stations['features'][0]['properties']['stationIdentifier'] ?? null;
            if ($stationId) {
                $obs = self::fetchJson(
                    "https://api.weather.gov/stations/{$stationId}/observations/latest"
                );
                if ($obs) {
                    $obsProps = $obs['properties'];
                }
            }
        }

        $data = self::buildData($periods, $obsProps);

        $file = self::cacheFile();
        $dir  = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            self::log('failed to write cache file');
            return false;
        }

        return true;
    }

    /**
     * Return cached widget data array, or null if unavailable.
     * Accepts data up to 2× TTL old so a brief cron failure doesn't blank the widget.
     */
    public static function getWidgetData(): ?array
    {
        $file = self::cacheFile();
        if (!is_file($file)) {
            return null;
        }

        $raw  = file_get_contents($file);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data)) {
            return null;
        }

        if ((time() - ($data['updated_at'] ?? 0)) > self::CACHE_TTL * 2) {
            return null;
        }

        return $data;
    }

    /**
     * Return the Weather Icons (wi-*) CSS class for a NWS condition code.
     *
     * @param string $condition  NWS condition code, e.g. "ovc", "rain", "tsra"
     * @param bool   $isDaytime  Whether to use day/night variant icons
     */
    public static function iconClass(string $condition, bool $isDaytime = true): string
    {
        if (str_starts_with($condition, 'wind_')) {
            $condition = substr($condition, 5);
        }

        $map = [
            'skc'             => $isDaytime ? 'wi-day-sunny'          : 'wi-night-clear',
            'few'             => $isDaytime ? 'wi-day-cloudy'         : 'wi-night-alt-cloudy',
            'sct'             => $isDaytime ? 'wi-day-cloudy'         : 'wi-night-alt-cloudy',
            'bkn'             => 'wi-cloudy',
            'ovc'             => 'wi-cloud',
            'rain'            => 'wi-rain',
            'rain_showers'    => 'wi-showers',
            'rain_showers_hi' => 'wi-showers',
            'rain_snow'       => 'wi-rain-mix',
            'snow'            => 'wi-snow',
            'blizzard'        => 'wi-snow-wind',
            'fzra'            => 'wi-sleet',
            'rain_fzra'       => 'wi-sleet',
            'sleet'           => 'wi-sleet',
            'tsra'            => 'wi-thunderstorm',
            'tsra_sct'        => 'wi-storm-showers',
            'tsra_hi'         => 'wi-storm-showers',
            'fog'             => 'wi-fog',
            'haze'            => $isDaytime ? 'wi-day-haze'           : 'wi-night-fog',
            'smoke'           => 'wi-smoke',
            'dust'            => 'wi-dust',
            'hot'             => 'wi-hot',
            'cold'            => 'wi-snowflake-cold',
            'tornado'         => 'wi-tornado',
        ];

        return $map[$condition] ?? 'wi-cloud';
    }

    /**
     * Diagnostics for the cron endpoint.
     *
     * @return array<string,string>
     */
    public static function getFetchDiagnostics(): array
    {
        return [
            'allow_url_fopen' => ini_get('allow_url_fopen') ? '1' : '0',
            'curl_available'  => function_exists('curl_version') ? '1' : '0',
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private static function buildData(array $periods, ?array $obs): array
    {
        // Current conditions
        $tempF     = null;
        $desc      = 'unknown';
        $condition = 'ovc';
        $daytime   = true;

        if ($obs) {
            $tempC = $obs['temperature']['value'] ?? null;
            if ($tempC !== null) {
                $tempF = self::c2f((float) $tempC);
            }
            $desc      = strtolower($obs['textDescription'] ?? 'unknown');
            $iconUrl   = $obs['icon'] ?? '';
            $condition = self::codeFromIcon($iconUrl);
            $daytime   = !str_contains($iconUrl, '/night/');
        }

        // Fallback: first daytime forecast period
        if ($tempF === null) {
            foreach ($periods as $p) {
                if ($p['isDaytime'] ?? false) {
                    $tempF     = (int) ($p['temperature'] ?? 0);
                    $desc      = strtolower($p['shortForecast'] ?? 'unknown');
                    $condition = self::codeFromIcon($p['icon'] ?? '');
                    $daytime   = true;
                    break;
                }
            }
        }

        // 3-day forecast: skip partial "today" periods, pair day+night for hi/lo
        $forecast  = [];
        $skipNames = ['Today', 'This Afternoon', 'This Morning'];

        foreach ($periods as $i => $period) {
            if (!($period['isDaytime'] ?? false)) {
                continue;
            }
            if (in_array($period['name'] ?? '', $skipNames, true)) {
                continue;
            }
            if (count($forecast) >= 3) {
                break;
            }

            $low = null;
            if (isset($periods[$i + 1]) && !($periods[$i + 1]['isDaytime'] ?? true)) {
                $low = (int) ($periods[$i + 1]['temperature'] ?? 0);
            }

            $forecast[] = [
                'day'       => $period['name'] ?? '',
                'high_f'    => (int) ($period['temperature'] ?? 0),
                'low_f'     => $low,
                'condition' => self::codeFromIcon($period['icon'] ?? ''),
            ];
        }

        return [
            'updated_at' => time(),
            'current'    => [
                'temp_f'    => $tempF,
                'desc'      => $desc,
                'condition' => $condition,
                'daytime'   => $daytime,
            ],
            'forecast'   => $forecast,
        ];
    }

    /**
     * Extract the NWS condition code from an icon URL.
     * "https://api.weather.gov/icons/land/day/ovc?size=medium"      → "ovc"
     * "https://api.weather.gov/icons/land/day/rain,40?size=medium"  → "rain"
     */
    private static function codeFromIcon(string $url): string
    {
        if (preg_match('|/icons/land/(?:day|night)/([a-z_]+)|', $url, $m)) {
            return $m[1];
        }
        return 'ovc';
    }

    private static function c2f(float $c): int
    {
        return (int) round($c * 9 / 5 + 32);
    }

    /**
     * Fetch a URL and decode JSON. Returns null on any failure.
     */
    private static function fetchJson(string $url): ?array
    {
        $headers = [
            'User-Agent: ' . self::USER_AGENT,
            'Accept: application/geo+json',
        ];

        $body = null;

        if (function_exists('curl_version')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => self::API_TIMEOUT,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $body = curl_exec($ch);
            if (curl_errno($ch)) {
                self::log('curl error [' . $url . ']: ' . curl_error($ch));
                $body = null;
            }
            curl_close($ch);
        } elseif (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => self::API_TIMEOUT,
                    'header'  => implode("\r\n", $headers),
                ],
            ]);
            $body = @file_get_contents($url, false, $context);
        }

        if (!$body) {
            return null;
        }

        $data = json_decode($body, true);
        return is_array($data) ? $data : null;
    }

    private static function log(string $msg): void
    {
        $f   = self::logFile();
        $dir = dirname($f);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($f, date('c') . " WeatherService: $msg\n", FILE_APPEND);
    }
}
