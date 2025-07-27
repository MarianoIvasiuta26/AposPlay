<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchedulesXCourt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'schedule_id',
        'court_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
