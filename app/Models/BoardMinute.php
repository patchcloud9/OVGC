<?php

namespace App\Models;

class BoardMinute extends Model
{
    protected string $table = 'board_minutes';

    protected array $fillable = [
        'meeting_date',
        'filename',
        'file_path',
    ];

    protected bool $timestamps = false; // only created_at exists

    /**
     * Paginate minutes by meeting_date desc
     *
     * @param int $page
     * @param int $perPage
     * @return array ['minutes'=>array,'total'=>int,'page'=>int,'totalPages'=>int]
     */
    public static function paginate(int $page = 1, int $perPage = 10): array
    {
        $instance = new static();
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM {$instance->table}";
        $countResult = $instance->getDatabase()->fetch($countSql);
        $total = (int) $countResult['total'];
        $totalPages = (int) ceil($total / $perPage);

        $sql = "SELECT * FROM {$instance->table} ORDER BY meeting_date DESC LIMIT ? OFFSET ?";
        $minutes = $instance->getDatabase()->fetchAll($sql, [$perPage, $offset]);

        return [
            'minutes' => $minutes,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ];
    }

    /**
     * Return all minutes ordered by date desc (used in admin views)
     *
     * @return array
     */
    public static function allOrdered(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} ORDER BY meeting_date DESC";
        return $instance->getDatabase()->fetchAll($sql);
    }
}
