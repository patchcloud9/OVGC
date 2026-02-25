<?php

namespace App\Models;

/**
 * Singleton model for membership page content.
 */
class MembershipPageContent extends Model
{
    protected string $table = 'membership_page_content';

    protected array $fillable = [
        'top_text',
        'bullets',
        'bottom_text',
    ];

    protected bool $timestamps = true;

    /**
     * Retrieve the single content row (if any).
     *
     * @return array|null
     */
    public static function getContent(): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        return $instance->getDatabase()->fetch($sql);
    }

    /**
     * Update the singleton content record, creating it if necessary.
     *
     * @param array $data
     * @return bool
     */
    public static function updateContent(array $data): bool
    {
        $instance = new static();
        $existing = static::getContent();
        if ($existing) {
            return static::update($existing['id'], $data);
        } else {
            static::create($data);
            return true;
        }
    }
}
