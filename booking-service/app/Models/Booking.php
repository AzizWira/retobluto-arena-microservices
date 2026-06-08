<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'member_id',
        'member_user_id',
        'member_name',
        'member_email',
        'field_id',
        'field_name',
        'field_type',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'price_per_hour',
        'total_price',
        'status',
        'note',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        'canceled_at',
        'approved_by',
        'rejected_by',
        'canceled_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'duration_hours' => 'integer',
            'price_per_hour' => 'decimal:2',
            'total_price' => 'decimal:2',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }
}