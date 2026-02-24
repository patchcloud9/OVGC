<?php

namespace App\Models;

class MembershipGroup extends Model
{
    protected string $table = 'membership_groups';
    protected array $fillable = [
        'slug',
        'title',
        'subtitle',
        'note',
        'sort_order',
        'active',
    ];
    protected bool $timestamps = true;

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

    public static function findWithItems(int $id): ?array
    {
        $group = static::find($id);
        if ($group) {
            $group['items'] = MembershipItem::where(['group_id' => $id]);
        }
        return $group;
    }
}
