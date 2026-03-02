<?php

namespace App\Models;

class BoardMember extends Model
{
    protected string $table = 'board_members';

    protected array $fillable = [
        'name',
        'title',
        'email',
        'photo_path',
        'sort_order',
    ];

    protected bool $timestamps = true;

    /**
     * Get all members ordered by sort_order then name
     *
     * @return array
     */
    public static function allOrdered(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} ORDER BY sort_order ASC, name ASC";
        return $instance->getDatabase()->fetchAll($sql);
    }
}
