<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'asset_number',
        'name',
        'category_id',
        'brand_id',
        'department_id',
        'location_id',
        'model',
        'specification',
        'pic',
        'status',
        'condition',
        'purchase_date',
        'photo_path',
        'qr_path',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
            'condition' => AssetCondition::class,
            'purchase_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'asset_number';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class)->latest('checked_at');
    }
}
