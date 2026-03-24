<?php

namespace App\Enums;

enum TournamentStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT       => 'Borrador',
            self::OPEN        => 'Inscripciones Abiertas',
            self::IN_PROGRESS => 'En Curso',
            self::FINISHED    => 'Finalizado',
            self::CANCELLED   => 'Cancelado',
        };
    }
}
