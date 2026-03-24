<?php

namespace App\Enums;

enum TournamentMatchStatus: string
{
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => 'Pendiente',
            self::COMPLETED => 'Completado',
            self::CANCELLED => 'Cancelado',
        };
    }
}
