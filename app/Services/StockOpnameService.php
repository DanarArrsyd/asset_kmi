<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function record(Asset $asset, User $user, array $data, ?UploadedFile $photo): StockOpname
    {
        return DB::transaction(function () use ($asset, $user, $data, $photo) {
            $photoPath = $photo ? $photo->store('stock-opname', 'public') : null;

            $sto = StockOpname::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'checked_by_name' => $user->name,
                'condition' => $data['condition'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'photo_path' => $photoPath,
                'checked_at' => now(),
            ]);

            $asset->update([
                'condition' => $data['condition'],
                'status' => $data['status'],
            ]);

            return $sto;
        });
    }
}
