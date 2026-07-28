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
     * What the role may actually do, phrased for whoever is picking one on the
     * user form. Mirrors the policies — change both together.
     */
    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Akses penuh, termasuk kelola user.',
            self::Admin => 'Kelola master data, transaksi, dan laporan. Tidak bisa kelola user.',
            self::Auditor => 'Lihat semua asset dan jalankan stock opname. Tidak bisa ubah master data.',
            self::Department => 'Lihat dan ubah asset departemennya sendiri.',
            self::User => 'Hanya lihat asset departemennya sendiri.',
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
