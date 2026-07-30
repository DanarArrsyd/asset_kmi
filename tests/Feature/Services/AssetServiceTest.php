<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Services\AssetService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * AssetService owns the two things nothing else may decide: the asset number and
 * the QR that gets printed onto a label. Both are irreversible in practice — a
 * sticker is already on the rack — so they are tested at the service rather than
 * through a controller.
 *
 * These boot the framework because the service talks to the database and the
 * disk. tests/Unit stays framework-free by design; see tests/Pest.php.
 */
beforeEach(function () {
    Storage::fake('public');
});

function assetPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Rak Server',
        'category_id' => Category::firstOrCreate(['code' => 'ITE'], ['name' => 'IT Equipment'])->id,
        'brand_id' => Brand::firstOrCreate(['name' => 'Dell'])->id,
        'department_id' => department()->id,
        'location_id' => Location::firstOrCreate(['code' => 'HQ'], ['name' => 'Kantor Pusat'])->id,
        'status' => AssetStatus::Active->value,
        'condition' => AssetCondition::Good->value,
    ], $overrides);
}

it('numbers the first asset AST-KMI-0001', function () {
    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->asset_number)->toBe('AST-KMI-0001');
});

it('numbers each asset one above the last', function () {
    $service = app(AssetService::class);

    $first = $service->create(assetPayload(), null);
    $second = $service->create(assetPayload(['name' => 'Laptop']), null);

    expect($first->asset_number)->toBe('AST-KMI-0001');
    expect($second->asset_number)->toBe('AST-KMI-0002');
});

it('continues from the highest existing number rather than from the row count', function () {
    assetIn(department(), 'AST-KMI-0009');

    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->asset_number)->toBe('AST-KMI-0010');
});

it('grows past four digits rather than repeating a number', function () {
    assetIn(department(), 'AST-KMI-9999');

    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->asset_number)->toBe('AST-KMI-10000');
});

it('continues the sequence from a number in the pre-2026-07 format', function () {
    assetIn(department(), 'AST000007');

    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->asset_number)->toBe('AST-KMI-0008');
});

it('reads the sequence out of either format', function () {
    expect(AssetService::sequenceOf('AST-KMI-0001'))->toBe(1);
    expect(AssetService::sequenceOf('AST-KMI-10000'))->toBe(10000);
    expect(AssetService::sequenceOf('AST000042'))->toBe(42);
});

it('generates a QR code file for a new asset', function () {
    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->qr_path)->toBe('qrcodes/AST-KMI-0001.png');
    Storage::disk('public')->assertExists($asset->qr_path);
});

it('stores a photo when one is supplied', function () {
    $asset = app(AssetService::class)->create(
        assetPayload(),
        UploadedFile::fake()->image('rak.jpg')
    );

    expect($asset->photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($asset->photo_path);
});

it('leaves photo_path null when no photo is supplied', function () {
    $asset = app(AssetService::class)->create(assetPayload(), null);

    expect($asset->photo_path)->toBeNull();
});

it('replaces the old photo on update rather than orphaning it', function () {
    $service = app(AssetService::class);

    $asset = $service->create(assetPayload(), UploadedFile::fake()->image('lama.jpg'));
    $old = $asset->photo_path;

    $service->update($asset, assetPayload(['name' => 'Rak Server 4U']), UploadedFile::fake()->image('baru.jpg'));

    expect($asset->photo_path)->not->toBe($old);
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($asset->photo_path);
});

it('keeps the existing photo when an update supplies none', function () {
    $service = app(AssetService::class);

    $asset = $service->create(assetPayload(), UploadedFile::fake()->image('tetap.jpg'));
    $photo = $asset->photo_path;

    $service->update($asset, assetPayload(['name' => 'Nama Baru']), null);

    expect($asset->fresh()->photo_path)->toBe($photo);
    Storage::disk('public')->assertExists($photo);
});

it('never renumbers an asset on update', function () {
    $service = app(AssetService::class);

    $asset = $service->create(assetPayload(), null);
    $service->update($asset, assetPayload(['name' => 'Nama Baru']), null);

    expect($asset->fresh()->asset_number)->toBe('AST-KMI-0001');
});

it('removes the photo and the QR file when an asset is deleted', function () {
    $service = app(AssetService::class);

    $asset = $service->create(assetPayload(), UploadedFile::fake()->image('hapus.jpg'));
    $photo = $asset->photo_path;
    $qr = $asset->qr_path;

    $service->delete($asset);

    expect(Asset::count())->toBe(0);
    Storage::disk('public')->assertMissing($photo);
    Storage::disk('public')->assertMissing($qr);
});

it('regenerates the QR code on demand', function () {
    $service = app(AssetService::class);

    $asset = $service->create(assetPayload(), null);
    $before = Storage::disk('public')->get($asset->qr_path);

    Storage::disk('public')->delete($asset->qr_path);
    $service->regenerateQrCode($asset);

    Storage::disk('public')->assertExists($asset->qr_path);
    expect(Storage::disk('public')->get($asset->qr_path))->toBe($before);
});
