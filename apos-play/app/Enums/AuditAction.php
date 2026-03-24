<?php

namespace App\Enums;

enum AuditAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case PAYMENT = 'payment';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Creación',
            self::UPDATED => 'Edición',
            self::DELETED => 'Eliminación',
            self::LOGIN => 'Login',
            self::LOGOUT => 'Logout',
            self::PAYMENT => 'Pago',
            self::CANCELLED => 'Cancelación',
            self::REFUNDED => 'Reembolso',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CREATED => 'green',
            self::UPDATED => 'blue',
            self::DELETED => 'red',
            self::LOGIN => 'gray',
            self::LOGOUT => 'gray',
            self::PAYMENT => 'yellow',
            self::CANCELLED => 'orange',
            self::REFUNDED => 'purple',
        };
    }
}
