<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\StockOpname;
use App\Models\User;
use App\Services\StockOpnameService;

/**
 * The stock take history is the audit trail of an asset audit system. It used to
 * cascade away with the account that recorded it, so an auditor leaving the
 * company silently deleted every check they had ever made.
 */
function recordCheck(Asset $asset, User $user): StockOpname
{
    return app(StockOpnameService::class)->record($asset, $user, [
        'condition' => AssetCondition::Good->value,
        'status' => AssetStatus::Active->value,
        'notes' => 'diperiksa di rak',
    ], null);
}

it('keeps the stock take when the auditor is deleted', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    $check = recordCheck($asset, $auditor);
    $auditor->delete();

    $check->refresh();

    expect(StockOpname::count())->toBe(1);
    expect($check->user_id)->toBeNull();
    expect($check->notes)->toBe('diperiksa di rak');
});

it('still names who did the check after the account is gone', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);
    $name = $auditor->name;

    $check = recordCheck($asset, $auditor);
    $auditor->delete();

    expect($check->fresh()->auditorName())->toBe($name);
});

it('names the live account rather than the snapshot while it exists', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    $check = recordCheck($asset, $auditor);
    $auditor->update(['name' => 'Nama Baru']);

    expect($check->fresh()->auditorName())->toBe('Nama Baru');
});

it('says so when neither the account nor the snapshot survives', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    $check = recordCheck($asset, $auditor);
    // A row written before checked_by_name existed.
    $check->forceFill(['checked_by_name' => null])->save();
    $auditor->delete();

    expect($check->fresh()->auditorName())->toBe('Pengguna dihapus');
});

it('renders the asset history after the auditor is deleted', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);
    $name = $auditor->name;

    recordCheck($asset, $auditor);
    $auditor->delete();

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('Riwayat Stock Opname');
    $response->assertSee($name);
});

it('renders the stock opname list and export after the auditor is deleted', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);
    $name = $auditor->name;

    recordCheck($asset, $auditor);
    $auditor->delete();

    $admin = userOfRole(UserRole::Admin);

    $this->actingAs($admin)->get('/stock-opname')->assertOk()->assertSee($name);

    $csv = $this->actingAs($admin)->get('/stock-opname/export')->streamedContent();

    expect($csv)->toContain($name);
});

it('renders the dashboard activity feed after the auditor is deleted', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);
    $name = $auditor->name;

    recordCheck($asset, $auditor);
    $auditor->delete();

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get('/dashboard')
        ->assertOk()
        ->assertSee("diverifikasi STO oleh {$name}", false);
});
