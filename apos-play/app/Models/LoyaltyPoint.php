<?php

namespace App\Models;

use App\Enums\LoyaltyPointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyPoint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'reservation_id',
        'points',
        'type',
        'description',
        'expires_at',
    ];

    protected $casts = [
        'type' => LoyaltyPointType::class,
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
