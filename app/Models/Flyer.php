<?php

namespace App\Models;

class Flyer extends Model
{
    protected string $table = 'flyers';

    protected array $fillable = [
        'title',
        'description',
        'filename',
        'file_path',
        'mime_type',
        'expires_at',
        'display_order',
        'uploaded_by',
    ];

    protected bool $timestamps = true;

    /**
     * All active (non-expired) flyers ordered by display_order, then expiry ascending.
     */
    public static function getActive(): array
    {
        $instance = new static();
        $sql = "SELECT f.*, u.name AS uploader_name
                FROM {$instance->table} f
                LEFT JOIN users u ON u.id = f.uploaded_by
                WHERE f.expires_at >= CURDATE()
                ORDER BY f.display_order ASC, f.expires_at ASC";
        return $instance->getDatabase()->fetchAll($sql);
    }

    /**
     * All flyers (including expired) for admin, newest first.
     */
    public static function allForAdmin(): array
    {
        $instance = new static();
        $sql = "SELECT f.*, u.name AS uploader_name,
                       (f.expires_at < CURDATE()) AS is_expired
                FROM {$instance->table} f
                LEFT JOIN users u ON u.id = f.uploaded_by
                ORDER BY f.created_at DESC";
        return $instance->getDatabase()->fetchAll($sql);
    }

    /**
     * Next display_order value.
     */
    public static function getNextDisplayOrder(): int
    {
        $instance = new static();
        $row = $instance->getDatabase()->fetch(
            "SELECT COALESCE(MAX(display_order), 0) + 1 AS next_order FROM {$instance->table}"
        );
        return (int) $row['next_order'];
    }
}
