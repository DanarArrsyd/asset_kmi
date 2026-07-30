<?php

use App\Enums\UserRole;
use App\Models\StockOpname;

/**
 * StockOpnamePolicy only answers viewAny, and it answers true for everyone —
 * StockOpnameController scopes the rows instead. That is a deliberate split, so
 * it is asserted rather than assumed: the gate is open, and the scoping is what
 * keeps a department from reading another department's checks.
 *
 * Permission to record a check lives on AssetPolicy::recordStockOpname, because
 * it has to be answered against a specific asset.
 */
dataset('every role', [
    'super admin' => [UserRole::SuperAdmin],
    'admin' => [UserRole::Admin],
    'auditor' => [UserRole::Auditor],
    'department' => [UserRole::Department],
    'user' => [UserRole::User],
]);

it('opens the stock opname list to every role', function (UserRole $role) {
    expect(userOfRole($role, department())->can('viewAny', StockOpname::class))->toBeTrue();
})->with('every role');

it('reaches the list over HTTP for every role', function (UserRole $role) {
    $this->actingAs(userOfRole($role, department()))
        ->get('/stock-opname')
        ->assertOk();
})->with('every role');

it('shows a scoped role only its own department history', function () {
    $mine = department('Produksi', 'PRD');
    $theirs = department('Finance', 'FIN');

    $auditor = userOfRole(UserRole::Auditor);

    $mineAsset = assetIn($mine, 'AST-KMI-0001');
    $theirsAsset = assetIn($theirs, 'AST-KMI-0002');

    foreach ([$mineAsset, $theirsAsset] as $asset) {
        $this->actingAs($auditor)->post("/assets/{$asset->asset_number}/stock-opname", [
            'condition' => 'good',
            'status' => 'active',
        ])->assertRedirect();
    }

    $response = $this->actingAs(userOfRole(UserRole::Department, $mine))->get('/stock-opname');

    $response->assertOk();
    $response->assertSee('AST-KMI-0001');
    $response->assertDontSee('AST-KMI-0002');
});

it('refuses a scoped role the stock take of another department', function () {
    $theirs = assetIn(department('Finance', 'FIN'), 'AST-KMI-0002');
    $outsider = userOfRole(UserRole::Department, department('Produksi', 'PRD'));

    $this->actingAs($outsider)
        ->post("/assets/{$theirs->asset_number}/stock-opname", [
            'condition' => 'good',
            'status' => 'active',
        ])
        ->assertForbidden();

    expect(StockOpname::count())->toBe(0);
});
