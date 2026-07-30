<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Missing = 'missing';
    case Disposed = 'disposed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Maintenance => 'Maintenance',
            self::Missing => 'Hilang',
            self::Disposed => 'Dilepas',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'pill--success',
            self::Maintenance => 'pill--warning',
            self::Missing, self::Disposed => 'pill--danger',
        };
    }
}
