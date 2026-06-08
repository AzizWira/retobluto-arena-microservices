<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }
}
