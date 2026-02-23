<?php

namespace App\Models;

/**
 * AboutContent Model
 *
 * Singleton model for managing About page content.
 * Only one record should exist in the database.
 */
class AboutContent extends Model
{
    protected string $table = 'about_content';

    protected array $fillable = [
        'page_title',
        'page_subtitle',
        'section1_image',
        'section1_text',
        'section1_image_position',
        'section1_text_align_h',
        'section1_text_align_v',
        'section2_image',
        'section2_text',
        'section2_image_position',
        'section2_text_align_h',
        'section2_text_align_v',
        'artist_signature',
    ];

    protected bool $timestamps = true;

    /**
     * Get the about content (singleton)
     * Creates default content if none exists
     */
    public static function getContent(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        $content = $instance->getDatabase()->fetch($sql);

        // If no content exists, create default
        if (!$content) {
            $content = static::create([
                'page_title' => 'About the Artist',
                'page_subtitle' => '',
                'section1_text' => 'Add your about content here.',
                'section1_image_position' => 'left',
                'section2_text' => 'Add more content here.',
                'section2_image_position' => 'left',
                'artist_signature' => '',
            ]);
        }

        return $content;
    }

    /**
     * Update about content
     * Updates existing record or creates new one
     */
    public static function updateContent(array $data): bool
    {
        $instance = new static();
        $existing = static::getContent();

        if ($existing && isset($existing['id'])) {
            return static::update($existing['id'], $data) !== false;
        } else {
            static::create($data);
            return true;
        }
    }

    /**
     * Get a specific setting value
     */
    public static function get(string $key, $default = null)
    {
        $content = static::getContent();
        return $content[$key] ?? $default;
    }
}
