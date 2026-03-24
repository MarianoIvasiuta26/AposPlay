<?php

namespace App\Enums;

enum TournamentTeamPaymentStatus: string
{
    case PENDING  = 'pending';
    case PAID     = 'paid';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING  => 'Pendiente',
            self::PAID     => 'Pagado',
            self::REFUNDED => 'Reembolsado',
        };
    }
}
