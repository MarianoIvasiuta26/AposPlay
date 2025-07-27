<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'turn',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    public function schedulesXCourt()
    {
        return $this->hasMany(SchedulesXCourt::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scope para filtrar horarios de mañana
    public function scopeMorning($query)
    {
        return $query->where('turn', 'morning');
    }

    // Scope para filtrar horarios de tarde
    public function scopeAfternoon($query)
    {
        return $query->where('turn', 'afternoon');
    }
}
