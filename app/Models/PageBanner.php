<?php

namespace App\Models;

class PageBanner extends Model
{
    protected string $table = 'page_banners';

    protected array $fillable = [
        'page',
        'position',
        'text',
        'colour',
        'dismissable',
        'sort_order',
        'active',
        'start_at',
        'end_at',
    ];

    protected bool $timestamps = true;

    /**
     * Get active banners for given page, optionally limit position
     */
    public static function forPage(string $page, ?string $position = null): array
    {
        $instance = new static();
        $params = [$page];
        $sql = "SELECT * FROM {$instance->table} WHERE page = ? AND active = 1";

        if ($position) {
            $sql .= " AND position = ?";
            $params[] = $position;
        }

        $sql .= " AND (start_at IS NULL OR start_at <= NOW())"
              . " AND (end_at IS NULL OR end_at >= NOW())";
        $sql .= " ORDER BY sort_order ASC, id ASC";

        return $instance->getDatabase()->fetchAll($sql, $params);
    }
}
