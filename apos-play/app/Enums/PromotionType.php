<?php

namespace App\Enums;

enum PromotionType: string
{
    case COMBO = 'combo';
    case COUPON = 'coupon';
    case EXTRA_POINTS = 'extra_points';

    public function label(): string
    {
        return match ($this) {
            self::COMBO => 'Combo',
            self::COUPON => 'Cupón',
            self::EXTRA_POINTS => 'Puntos Extra',
        };
    }
}
