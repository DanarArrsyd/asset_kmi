<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Recording a stock take writes two things: the history row and the asset's
 * current condition. They must not be able to disagree — the service writes the
 * history first inside a transaction, so a rejected check leaves the asset
 * exactly as it was.
 */
beforeEach(function () {
    Storage::fake('public');
});

it('records the check against the asset and the auditor', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    $check = app(StockOpnameService::class)->record($asset, $auditor, [
        'condition' => AssetCondition::Fair->value,
        'status' => AssetStatus::Maintenance->value,
        'notes' => 'kipas berbunyi',
    ], null);

    expect($check->asset_id)->toBe($asset->id);
    expect($check->user_id)->toBe($auditor->id);
    expect($check->notes)->toBe('kipas berbunyi');
    expect($check->checked_at)->not->toBeNull();
});

it('moves the asset to the condition and status that were found', function () {
    $asset = assetIn(department());
    expect($asset->condition)->toBe(AssetCondition::Good);

    app(StockOpnameService::class)->record($asset, userOfRole(UserRole::Auditor), [
        'condition' => AssetCondition::Damaged->value,
        'status' => AssetStatus::Missing->value,
    ], null);

    $asset->refresh();

    expect($asset->condition)->toBe(AssetCondition::Damaged);
    expect($asset->status)->toBe(AssetStatus::Missing);
});

it('accepts a check with no notes', function () {
    $check = app(StockOpnameService::class)->record(
        assetIn(department()),
        userOfRole(UserRole::Auditor),
        ['condition' => AssetCondition::Good->value, 'status' => AssetStatus::Active->value],
        null
    );

    expect($check->notes)->toBeNull();
});

it('stores the evidence photo when one is supplied', function () {
    $check = app(StockOpnameService::class)->record(
        assetIn(department()),
        userOfRole(UserRole::Auditor),
        ['condition' => AssetCondition::Good->value, 'status' => AssetStatus::Active->value],
        UploadedFile::fake()->image('bukti.jpg')
    );

    expect($check->photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($check->photo_path);
});

it('keeps every check rather than overwriting the last one', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);
    $service = app(StockOpnameService::class);

    $service->record($asset, $auditor, [
        'condition' => AssetCondition::Good->value,
        'status' => AssetStatus::Active->value,
    ], null);

    $service->record($asset, $auditor, [
        'condition' => AssetCondition::Fair->value,
        'status' => AssetStatus::Active->value,
    ], null);

    expect(StockOpname::where('asset_id', $asset->id)->count())->toBe(2);
    expect($asset->fresh()->condition)->toBe(AssetCondition::Fair);
});

it('leaves the asset untouched when the check is rejected', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    expect(fn () => app(StockOpnameService::class)->record($asset, $auditor, [
        'condition' => 'tidak-ada-kondisi-ini',
        'status' => AssetStatus::Missing->value,
    ], null))->toThrow(ValueError::class);

    $asset->refresh();

    expect(StockOpname::count())->toBe(0);
    expect($asset->condition)->toBe(AssetCondition::Good);
    expect($asset->status)->toBe(AssetStatus::Active);
});
