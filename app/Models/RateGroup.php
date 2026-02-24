<?php

namespace App\Models;

/**
 * RateGroup Model
 *
 * Represents a grouping of rates (each group becomes a card).
 */
class RateGroup extends Model
{
    protected string $table = 'rate_groups';

    protected array $fillable = [
        'slug',
        'title',
        'subtitle',
        'note',
        'sort_order',
        'active',
    ];

    protected bool $timestamps = true;

    /**
     * Get all groups, optionally only active ones
     */
    public static function allGroups(bool $onlyActive = false): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        if ($onlyActive) {
            $sql .= " WHERE active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        return $instance->getDatabase()->fetchAll($sql);
    }

    /**
     * Find a group and load its related rate items
     */
    public static function findWithRates(int $id): ?array
    {
        $group = static::find($id);
        if ($group) {
            $group['rates'] = Rate::where(['group_id' => $id]);
        }
        return $group;
    }
}
