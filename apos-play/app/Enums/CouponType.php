<?php

namespace App\Enums;

enum CouponType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Porcentaje',
            self::FIXED_AMOUNT => 'Monto Fijo',
        };
    }
}
