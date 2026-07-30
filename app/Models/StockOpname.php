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
        'checked_by_name',
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

    /**
     * Who performed this check.
     *
     * The account may be gone — the foreign key nulls rather than cascading, so
     * the history outlives the person. checked_by_name is the snapshot taken at
     * the time of the check; rows written before that column existed and whose
     * user has since been deleted have neither, and say so.
     */
    public function auditorName(): string
    {
        return $this->user?->name
            ?? $this->checked_by_name
            ?? 'Pengguna dihapus';
    }
}
