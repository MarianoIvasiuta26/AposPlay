<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case OWNER = 'owner';
    case STAFF = 'staff';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Superadmin',
            self::OWNER => 'Owner',
            self::STAFF => 'Staff',
            self::USER => 'Usuario',
        };
    }
}
