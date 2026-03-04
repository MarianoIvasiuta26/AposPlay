<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_uses',
        'times_used',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'type' => CouponType::class,
        'value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * The admin who created this coupon.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users assigned to this coupon.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot('used_at')
            ->withTimestamps();
    }

    /**
     * Check if the coupon is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->times_used >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted discount display value.
     */
    public function formattedValue(): string
    {
        return match ($this->type) {
            CouponType::PERCENTAGE => $this->value . '%',
            CouponType::FIXED_AMOUNT => '$' . number_format($this->value, 0),
        };
    }

    /**
     * Calculate the discount amount for a given price.
     */
    public function calculateDiscount(float $price): float
    {
        return match ($this->type) {
            CouponType::PERCENTAGE => round($price * ((float) $this->value / 100), 2),
            CouponType::FIXED_AMOUNT => min((float) $this->value, $price),
        };
    }

    /**
     * Generate a unique coupon code.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'APOS-' . strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
