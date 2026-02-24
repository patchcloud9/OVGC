<?php

namespace App\Models;

class MembershipItem extends Model
{
    protected string $table = 'membership_items';
    protected array $fillable = [
        'group_id',
        'sort_order',
        'name',
        'price',
        'notes',
    ];
    protected bool $timestamps = true;
}
