<?php

namespace App\Models;

/**
 * Homepage Settings Model
 * 
 * Singleton model for homepage customization.
 */
class HomepageSetting extends Model
{
    protected string $table = 'homepage_settings';
    
    protected array $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_title_color',
        'hero_subtitle_color',
        'hero_background_color',
        'hero_background_image',
        'card1_icon',
        'card1_title',
        'card1_text',
        'card1_button_text',
        'card1_button_link',
        'card2_icon',
        'card2_title',
        'card2_text',
        'card2_button_text',
        'card2_button_link',
        'card3_icon',
        'card3_title',
        'card3_text',
        'card3_button_text',
        'card3_button_link',
        'bottom_section_layout',
        'bottom_section_title',
        'bottom_section_text',
        'bottom_section_image',
    ];
    
    protected bool $timestamps = true;
    
    /**
     * Get the homepage settings (singleton)
     * 
     * @return array|null
     */
    public static function getSettings(): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        return $instance->getDatabase()->fetch($sql);
    }
    
    /**
     * Update homepage settings
     * 
     * @param array $data
     * @return bool
     */
    public static function updateSettings(array $data): bool
    {
        $instance = new static();
        
        // Get existing settings
        $existing = static::getSettings();
        
        if ($existing) {
            // Update existing
            return static::update($existing['id'], $data);
        } else {
            // Create new
            static::create($data);
            return true;
        }
    }
}
