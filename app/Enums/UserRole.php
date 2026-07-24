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
}
