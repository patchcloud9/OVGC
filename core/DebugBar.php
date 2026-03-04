<?php

namespace Core;

/**
 * Dev-only debug toolbar data collector.
 *
 * Visibility: APP_DEBUG=true AND (admin session OR IP in DEBUG_ALLOWED_IPS).
 * Data collection: APP_DEBUG=true (always, so data is ready when visibility kicks in).
 */
class DebugBar
{
    private static ?self $instance = null;

    private float $startTime;
    private array $queries    = [];
    private array $views      = [];
    private ?array $route     = null;
    private array $exceptions = [];

    private function __construct()
    {
        $this->startTime = microtime(true);
    }

    // -------------------------------------------------------------------------
    // Static accessors
    // -------------------------------------------------------------------------

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Should hooks collect data this request? */
    public static function enabled(): bool
    {
        return defined('APP_DEBUG') && APP_DEBUG === true;
    }

    /**
     * Should the toolbar be rendered in the response?
     * Requires: enabled() AND (admin session OR allowed IP).
     */
    public static function isVisible(): bool
    {
        if (!self::enabled()) {
            return false;
        }

        // Admin session
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }

        // Allowed IP list
        $allowedRaw = defined('DEBUG_ALLOWED_IPS') ? DEBUG_ALLOWED_IPS : '127.0.0.1,::1';

        $allowed = array_map('trim', explode(',', $allowedRaw));
        $ip      = $_SERVER['REMOTE_ADDR'] ?? '';

        return in_array($ip, $allowed, true);
    }

    // -------------------------------------------------------------------------
    // Data recording
    // -------------------------------------------------------------------------

    public function recordQuery(string $sql, array $params, float $durationMs, int $rowCount = 0): void
    {
        $this->queries[] = [
            'sql'         => $sql,
            'params'      => $this->sanitizeParams($params),
            'duration_ms' => round($durationMs, 3),
            'rows'        => $rowCount,
        ];
    }

    public function recordView(string $path, float $durationMs): void
    {
        $this->views[] = [
            'path'        => $path,
            'duration_ms' => round($durationMs, 3),
        ];
    }

    public function recordRoute(
        string $method,
        string $uri,
        string $pattern,
        string $controller,
        string $action,
        array  $middleware = [],
        array  $params     = []
    ): void {
        $this->route = compact('method', 'uri', 'pattern', 'controller', 'action', 'middleware', 'params');
    }

    public function recordException(\Throwable $e): void
    {
        $this->exceptions[] = [
            'class'   => get_class($e),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ];
    }

    // -------------------------------------------------------------------------
    // Computed metrics
    // -------------------------------------------------------------------------

    public function getElapsedMs(): float
    {
        return round((microtime(true) - $this->startTime) * 1000, 2);
    }

    public function getTotalQueryMs(): float
    {
        return round(array_sum(array_column($this->queries, 'duration_ms')), 2);
    }

    public function getMemoryMb(): string
    {
        return number_format(memory_get_usage(true) / 1048576, 2);
    }

    public function getPeakMemoryMb(): string
    {
        return number_format(memory_get_peak_usage(true) / 1048576, 2);
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    public function getQueries(): array      { return $this->queries; }
    public function getViews(): array        { return $this->views; }
    public function getRoute(): ?array       { return $this->route; }
    public function getExceptions(): array   { return $this->exceptions; }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Mask sensitive parameter keys (password, token, key, secret, hash, etc.).
     */
    private function sanitizeParams(array $params): array
    {
        $sensitive = ['password', 'pass', 'token', 'key', 'secret', 'hash', 'auth', 'credential'];
        $out = [];
        foreach ($params as $k => $v) {
            $lk = strtolower((string) $k);
            $masked = false;
            foreach ($sensitive as $word) {
                if (str_contains($lk, $word)) {
                    $out[$k] = '***';
                    $masked = true;
                    break;
                }
            }
            if (!$masked) {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
