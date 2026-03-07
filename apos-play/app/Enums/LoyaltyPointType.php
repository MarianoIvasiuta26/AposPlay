<?php

namespace App\Enums;

enum LoyaltyPointType: string
{
    case EARNED = 'earned';
    case SPENT = 'spent';
    case REVERSED = 'reversed';
    case EXPIRED = 'expired';
}
