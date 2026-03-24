<?php

namespace App\Enums;

enum TournamentFormat: string
{
    case ROUND_ROBIN = 'round_robin';
    case SINGLE_ELIMINATION = 'single_elimination';

    public function label(): string
    {
        return match($this) {
            self::ROUND_ROBIN        => 'Liga',
            self::SINGLE_ELIMINATION => 'Eliminación Directa',
        };
    }
}
