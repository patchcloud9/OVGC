<?php

namespace App\Models;

/**
 * PurchaseContent Model
 *
 * Singleton model for managing Purchase page content.
 * Only one record should exist in the database.
 */
class PurchaseContent extends Model
{
    protected string $table = 'purchase_content';

    protected array $fillable = [
        'page_title',
        'page_subtitle',
        'content_text',
        'contact_email',
        'button_text',
        'button_url',
    ];

    protected bool $timestamps = true;

    /**
     * Get the purchase content (singleton)
     * Creates default content if none exists
     */
    public static function getContent(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        $content = $instance->getDatabase()->fetch($sql);

        // If no content exists, create default
        if (!$content) {
            // We intentionally do not store a contact email here; the public page will use the theme_settings 'gallery_contact_email' when rendering {email}
            $content = static::create([
                'page_title' => 'Purchase',
                'page_subtitle' => '',
                'content_text' => 'Add your purchase page content here.',
                'contact_email' => null,
                'button_text' => 'Visit Store',
                'button_url' => '#',
            ]);
        }

        return $content;
    }

    /**
     * Update purchase content
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
