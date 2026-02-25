<?php

namespace App\Models;

/**
 * Singleton model for rates page content (rules and scorecard path).
 */
class RatePageContent extends Model
{
    protected string $table = 'rates_page_content';

    protected array $fillable = [
        'rules_text',
        'scorecard_path',
    ];

    protected bool $timestamps = true;

    public static function getContent(): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} LIMIT 1";
        return $instance->getDatabase()->fetch($sql);
    }

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
