<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetService
{
    public function create(array $data, ?UploadedFile $photo): Asset
    {
        return DB::transaction(function () use ($data, $photo) {
            $data['asset_number'] = $this->nextAssetNumber();

            $asset = Asset::create($data);

            if ($photo) {
                $asset->photo_path = $this->storePhoto($photo);
            }

            $asset->qr_path = $this->generateQrCode($asset);
            $asset->save();

            return $asset;
        });
    }

    public function update(Asset $asset, array $data, ?UploadedFile $photo): Asset
    {
        $asset->fill($data);

        if ($photo) {
            $this->deletePhoto($asset);
            $asset->photo_path = $this->storePhoto($photo);
        }

        $asset->save();

        return $asset;
    }

    public function delete(Asset $asset): void
    {
        $this->deletePhoto($asset);
        $this->deleteQrCode($asset);
        $asset->delete();
    }

    /** Printed on every physical label, so the shape is fixed: AST-KMI-0001. */
    public const NUMBER_PREFIX = 'AST-KMI-';

    /** Padded to four digits. The 10,000th asset simply becomes five. */
    public const NUMBER_PAD = 4;

    protected function nextAssetNumber(): string
    {
        $last = Asset::query()->lockForUpdate()->orderByDesc('id')->value('asset_number');

        return self::formatNumber($last ? self::sequenceOf($last) + 1 : 1);
    }

    public static function formatNumber(int $sequence): string
    {
        return self::NUMBER_PREFIX.str_pad((string) $sequence, self::NUMBER_PAD, '0', STR_PAD_LEFT);
    }

    /**
     * The trailing digits of an asset number.
     *
     * Reads the tail rather than slicing at a fixed offset, so it survives both
     * the pre-2026-07 format (AST000001) and a number that has outgrown its
     * padding (AST-KMI-10000).
     */
    public static function sequenceOf(string $assetNumber): int
    {
        preg_match('/(\d+)$/', $assetNumber, $matches);

        return (int) ($matches[1] ?? 0);
    }

    protected function storePhoto(UploadedFile $photo): string
    {
        return $photo->store('assets', 'public');
    }

    protected function deletePhoto(Asset $asset): void
    {
        if ($asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
        }
    }

    protected function generateQrCode(Asset $asset): string
    {
        $url = route('asset.public', $asset->asset_number);
        $path = 'qrcodes/'.$asset->asset_number.'.png';

        Storage::disk('public')->put($path, QrCode::format('png')->size(400)->generate($url));

        return $path;
    }

    protected function deleteQrCode(Asset $asset): void
    {
        if ($asset->qr_path) {
            Storage::disk('public')->delete($asset->qr_path);
        }
    }

    public function regenerateQrCode(Asset $asset): Asset
    {
        $this->deleteQrCode($asset);
        $asset->qr_path = $this->generateQrCode($asset);
        $asset->save();

        return $asset;
    }
}
