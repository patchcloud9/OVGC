<?php

namespace App\Models;

/**
 * ThemeSetting Model
 * 
 * Manages site-wide theme configuration (colors, logo, favicon, layout).
 * This is a singleton table - only one theme configuration exists per site.
 * 
 * Usage:
 *   $theme = ThemeSetting::getSiteTheme();
 *   ThemeSetting::updateTheme([
 *       'primary_color' => '#ff6b6b',
 *       'secondary_color' => '#4ecdc4'
 *   ]);
 */
class ThemeSetting extends Model
{
    protected string $table = 'theme_settings';
    
    protected array $fillable = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'danger_color',
        'navbar_color',
        'navbar_hover_color',
        'navbar_text_color',
        'hero_background_color',
        'homepage_logo_path',
        'secondary_logo_path',
        'site_name',
        'contact_email',
        'phone_number',
        'address1',
        'address2',
        'city_state_zip',
        'footer_tagline',
        'favicon_path',
        'header_style',
        'card_style',
    ];
    
    protected bool $timestamps = true;
    
    /**
     * Get the site-wide theme configuration
     * Since this is a singleton table, always returns the first (and only) row
     * 
     * @return array|null
     */
    public static function getSiteTheme(): ?array
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        return $instance->getDatabase()->fetch($sql);
    }
    
    /**
     * Update the site theme
     * Since this is a singleton table, always updates the first row
     * 
     * @param array $data Theme settings to update
     * @return bool Success
     */
    public static function updateTheme(array $data): bool
    {
        $instance = new static();
        
        // Get the theme ID (should always be 1)
        $theme = static::getSiteTheme();
        
        if (!$theme) {
            // No theme exists, create initial one
            return static::createInitialTheme($data);
        }
        
        // Update existing theme
        return static::update($theme['id'], $data);
    }
    
    /**
     * Create the initial theme configuration
     * Should only be called once during setup
     * 
     * @param array $data Initial theme settings
     * @return bool Success
     */
    private static function createInitialTheme(array $data): bool
    {
        try {
            $defaults = [
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'accent_color' => '#48c78e',
                'header_style' => 'static',
                'card_style' => 'default',
            ];
            
            $themeData = array_merge($defaults, $data);
            static::create($themeData);
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to create initial theme: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a specific theme setting value
     * 
     * @param string $key Setting key (e.g., 'primary_color')
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $theme = static::getSiteTheme();
        
        if (!$theme) {
            return $default;
        }
        
        return $theme[$key] ?? $default;
    }
}
