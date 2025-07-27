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
        'address_id',
        'number_players'
    ];

    public function address()
    {
        return $this->belongsTo(CourtAddress::class);
    }

    public function courtsXAdmin()
    {
        return $this->hasMany(CourtsXAdmin::class);
    }

    public function schedulesXCourt()
    {
        return $this->hasMany(SchedulesXCourt::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
