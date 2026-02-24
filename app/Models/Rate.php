<?php

namespace App\Models;

/**
 * Rate Model
 *
 * Individual pricing line item attached to a RateGroup.
 */
class Rate extends Model
{
    protected string $table = 'rates';

    protected array $fillable = [
        'group_id',
        'sort_order',
        'description',
        'price',
        'notes',
    ];

    protected bool $timestamps = true;
}
