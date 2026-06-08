<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'location',
        'price_per_hour',
        'status',
        'open_time',
        'close_time',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
        ];
    }
}
