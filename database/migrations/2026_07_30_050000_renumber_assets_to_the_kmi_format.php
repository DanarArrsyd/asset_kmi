<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Renumbers AST000001 to AST-KMI-0001.
 *
 * The QR code encodes the asset URL, and the URL contains the asset number, so
 * renumbering invalidates every sticker already printed. The QR files are
 * regenerated here — but the labels stuck on the racks still carry the old URL
 * and must be reprinted. That was accepted while the register held two assets.
 *
 * QR generation is inlined rather than called through AssetService on purpose: a
 * dated migration should not change behaviour when that service is refactored.
 */
return new class extends Migration
{
    private const PREFIX = 'AST-KMI-';

    public function up(): void
    {
        foreach (DB::table('assets')->select('id', 'asset_number', 'qr_path')->get() as $asset) {
            if (str_starts_with($asset->asset_number, self::PREFIX)) {
                continue;
            }

            $number = self::PREFIX.str_pad(
                (string) self::sequenceOf($asset->asset_number), 4, '0', STR_PAD_LEFT
            );

            if ($asset->qr_path) {
                Storage::disk('public')->delete($asset->qr_path);
            }

            DB::table('assets')->where('id', $asset->id)->update([
                'asset_number' => $number,
                'qr_path' => self::writeQrCode($number),
            ]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('assets')->select('id', 'asset_number', 'qr_path')->get() as $asset) {
            if (! str_starts_with($asset->asset_number, self::PREFIX)) {
                continue;
            }

            $number = 'AST'.str_pad((string) self::sequenceOf($asset->asset_number), 6, '0', STR_PAD_LEFT);

            if ($asset->qr_path) {
                Storage::disk('public')->delete($asset->qr_path);
            }

            DB::table('assets')->where('id', $asset->id)->update([
                'asset_number' => $number,
                'qr_path' => self::writeQrCode($number),
            ]);
        }
    }

    private static function sequenceOf(string $assetNumber): int
    {
        preg_match('/(\d+)$/', $assetNumber, $matches);

        return (int) ($matches[1] ?? 0);
    }

    private static function writeQrCode(string $number): string
    {
        $path = 'qrcodes/'.$number.'.png';

        Storage::disk('public')->put(
            $path,
            QrCode::format('png')->size(400)->generate(url('/asset/'.$number))
        );

        return $path;
    }
};
