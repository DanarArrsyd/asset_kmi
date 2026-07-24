<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'condition',
        'status',
        'notes',
        'photo_path',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'condition' => AssetCondition::class,
            'status' => AssetStatus::class,
            'checked_at' => 'datetime',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
