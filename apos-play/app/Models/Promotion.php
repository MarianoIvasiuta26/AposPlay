<?php

namespace App\Models;

use App\Enums\PromotionType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'type',
        'discount_value',
        'points_bonus',
        'conditions',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'type' => PromotionType::class,
        'conditions' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function conflictsWith(self $other): bool
    {
        if ($this->type !== $other->type) {
            return false;
        }

        if ($this->id && $this->id === $other->id) {
            return false;
        }

        return $this->starts_at <= $other->ends_at && $this->ends_at >= $other->starts_at;
    }
}
