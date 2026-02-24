<?php
/**
 * Helper Functions
 * 
 * Global utility functions available throughout the application.
 * Loaded automatically in public/index.php
 */

/**
 * Escape HTML output to prevent XSS attacks
 * Shorthand for htmlspecialchars()
 * 
 * @param string $value The string to escape
 * @return string Escaped string safe for HTML output
 */
function e(?string $value): string
{
    // Accept null and cast to empty string to avoid errors in views
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a CSRF token
 * Creates and stores a token in the session if one doesn't exist
 * 
 * @return string The CSRF token
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Generate a hidden CSRF token field for forms
 * 
 * @return string HTML input field with CSRF token
 */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Verify CSRF token from request
 * 
 * @param string|null $token The token to verify (from $_POST or $_GET)
 * @return bool True if token is valid
 */
function csrf_verify(?string $token): bool
{
    if (!isset($_SESSION['csrf_token']) || $token === null) {
        return false;
    }
    
    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get the current URL
 * 
 * @return string Current URL
 */
function current_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to a URL
 * 
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Get old input value (after validation failure)
 * 
 * @param string $key The input field name
 * @param mixed $default Default value if not found
 * @return mixed
 */
function old(string $key, $default = '')
{
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Store old input in session (for repopulating forms after errors)
 * 
 * @param array $data Input data to store
 * @return void
 */
function flash_old_input(array $data): void
{
    $_SESSION['old_input'] = $data;
}

/**
 * Clear old input from session
 * 
 * @return void
 */
function clear_old_input(): void
{
    unset($_SESSION['old_input']);
}

/**
 * Check if app is in debug mode
 * 
 * @return bool
 */
function is_debug(): bool
{
    return defined('APP_DEBUG') && APP_DEBUG === true;
}

/**
 * Mask a single sensitive value for logs
 * - Emails are partially masked: j***@domain.tld
 * - Other strings are replaced with '***' to avoid leaking secrets
 *
 * @param mixed $value
 * @return mixed
 */
function mask_value($value)
{
    if (is_null($value)) {
        return null;
    }

    if (is_bool($value) || is_int($value) || is_float($value)) {
        return $value;
    }

    $str = (string) $value;

    // Mask emails partially
    if (strpos($str, '@') !== false) {
        [$local, $domain] = explode('@', $str, 2);
        $local = strlen($local) > 1 ? substr($local, 0, 1) . str_repeat('*', max(1, strlen($local) - 1)) : '*';
        return $local . '@' . $domain;
    }

    // Don't reveal long values (tokens, secrets, etc.)
    if (strlen($str) <= 4) {
        return str_repeat('*', strlen($str));
    }

    return substr($str, 0, 2) . str_repeat('*', min(6, strlen($str) - 2)) . substr($str, -2);
}

/**
 * Recursively sanitize data for logging by masking sensitive keys/values
 * Keys that contain any of these fragments will be masked: password, pass, token,
 * secret, csrf, key, ssn, credit, card
 *
 * @param mixed $data
 * @return mixed
 */
function sanitize_for_log($data)
{
    if (is_array($data)) {
        $out = [];
        foreach ($data as $k => $v) {
            $lower = is_string($k) ? strtolower($k) : '';
            $sensitive = preg_match('/password|pass|token|secret|csrf|key|ssn|credit|card|cvv/i', $lower);

            if ($sensitive) {
                $out[$k] = mask_value($v);
            } else {
                $out[$k] = sanitize_for_log($v);
            }
        }
        return $out;
    }

    // Objects: convert to array then sanitize
    if (is_object($data)) {
        return sanitize_for_log((array) $data);
    }

    // Scalar values: mask only if looks like a secret (e.g., long random strings)
    if (is_string($data) && preg_match('/^[A-Za-z0-9_\-]{20,}$/', $data)) {
        return mask_value($data);
    }

    return $data;
}

/**
 * Dump and die - development-only helper.
 *
 * In production this function does not output sensitive data. Calling it
 * in non-debug environments will log an attempt instead of dumping.
 *
 * @param mixed ...$vars Variables to dump
 * @return void
 */
function dd(...$vars): void
{
    // Only allow interactive dumps when APP_DEBUG is enabled
    if (!is_debug()) {
        // Avoid leaking data in production; record the call and exit
        error_log('dd() called in non-debug environment; suppressing output.');
        http_response_code(500);
        exit;
    }

    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
} 

/**
 * Get the authenticated user from the session
 * 
 * @return array|null User data if authenticated, null otherwise
 */
function auth_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? '',
        'name' => $_SESSION['user_name'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
    ];
}

/**
 * Check if a user is authenticated
 * 
 * @return bool
 */
function is_authenticated(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if the authenticated user has a specific role
 * 
 * @param string $role Role to check (e.g., 'admin', 'user')
 * @return bool
 */
function has_role(string $role): bool
{
    if (!is_authenticated()) {
        return false;
    }
    
    return ($_SESSION['user_role'] ?? '') === $role;
}

/**
 * Check if the authenticated user is an admin
 * 
 * @return bool
 */
function is_admin(): bool
{
    return has_role('admin');
}

/**
 * Set a flash message (shown once on next page load)
 * 
 * @param string $type    Message type: 'success', 'error', 'warning', 'info'
 * @param string $message The message to display
 * @return void
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear the flash message
 * 
 * @return array|null Flash message array with 'type' and 'message' keys, or null
 */
function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Get site theme settings from database
 * Uses static cache to avoid multiple database queries per request
 * 
 * @return array Theme settings with default fallbacks
 */
function get_site_theme(): array
{
    static $theme = null;
    
    // Return cached theme if already loaded
    if ($theme !== null) {
        return $theme;
    }
    
    try {
        $theme = \App\Models\ThemeSetting::getSiteTheme();
    } catch (\Exception $e) {
        // If database unavailable, use default theme
        error_log("Failed to load theme settings: " . $e->getMessage());
        $theme = [
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'accent_color' => '#48c78e',
            'logo_path' => null,
            'favicon_path' => null,
            'header_style' => 'static',
            'card_style' => 'default',
        ];
    }
    
    return $theme;
}

/**
 * Get a specific theme setting value
 * 
 * @param string $key Theme setting key
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function theme_setting(string $key, $default = null)
{
    $theme = get_site_theme();
    return $theme[$key] ?? $default;
}
