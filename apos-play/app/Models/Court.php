<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'type',
        'court_address_id',
        'number_players'
    ];

    public function address()
    {
        return $this->belongsTo(CourtAddress::class, 'court_address_id');
    }

    public function courtsXAdmin()
    {
        return $this->hasMany(CourtsXAdmin::class);
    }

    // Deprecated? Keeping for backward compatibility if other code uses it, or removing. User asked to remove schedule logic before, but now restoring with new structure.
    public function schedulesXCourt()
    {
        return $this->hasMany(SchedulesXCourt::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // New relationship for Pivot
    public function schedules()
    {
        return $this->belongsToMany(Dia::class, 'court_schedules', 'court_id', 'day_id')
                    ->withPivot('start_time_1', 'end_time_1', 'start_time_2', 'end_time_2')
                    ->withTimestamps();
    }
}
