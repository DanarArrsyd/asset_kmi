<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Auditor = 'auditor';
    case Department = 'department';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Auditor => 'Auditor',
            self::Department => 'Department',
            self::User => 'User',
        };
    }

    /**
     * Roles that can write master data carry the accent pill; the rest stay
     * neutral. Green is reserved for asset health and must not be spent here.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::SuperAdmin, self::Admin => 'pill--info',
            self::Auditor, self::Department, self::User => 'pill--neutral',
        };
    }
}
