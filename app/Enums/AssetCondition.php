<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Good = 'good';
    case Fair = 'fair';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Fair => 'Fair',
            self::Damaged => 'Damaged',
        };
    }
}
