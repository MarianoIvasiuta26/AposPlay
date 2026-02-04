<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'court_id',
        'user_id',
        'schedule_id',
        'reservation_date',
        'start_time',
        'duration_hours',
        'status',
        'payment_status',
        'payment_id',
        'amount_paid',
        'total_price',
        'notes'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'total_price' => 'decimal:2'
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
