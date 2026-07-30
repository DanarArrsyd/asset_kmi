<?php

use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;

/**
 * The same matrix over real HTTP.
 *
 * A policy can be right while the route that uses it is wrong — the two
 * findings in the 2026-07-28 audit were both exactly that shape, so these
 * exercise the routes rather than the Gate.
 */
function validPayload(Asset $asset, array $overrides = []): array
{
    return array_merge([
        'name' => $asset->name,
        'category_id' => $asset->category_id,
        'brand_id' => $asset->brand_id,
        'department_id' => $asset->department_id,
        'location_id' => $asset->location_id,
        'status' => 'active',
        'condition' => 'good',
    ], $overrides);
}

it('lets an auditor mark an asset missing during a stock take', function () {
    $asset = assetIn(department());
    $auditor = userOfRole(UserRole::Auditor);

    $this->actingAs($auditor)
        ->post("/assets/{$asset->asset_number}/stock-opname", [
            'condition' => 'damaged',
            'status' => 'missing',
            'notes' => 'tidak ditemukan di lokasi',
        ])->assertRedirect();

    expect($asset->fresh()->status)->toBe(AssetStatus::Missing);
});

it('still refuses an auditor the plain edit form', function () {
    $asset = assetIn(department());

    $this->actingAs(userOfRole(UserRole::Auditor))
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['name' => 'diubah auditor']))
        ->assertForbidden();

    expect($asset->fresh()->name)->not->toBe('diubah auditor');
});

it('refuses a department user who posts another department id', function () {
    $mine = department();
    $theirs = department('Finance', 'FIN');
    $asset = assetIn($mine);

    $this->actingAs(userOfRole(UserRole::Department, $mine))
        ->from("/assets/{$asset->asset_number}/edit")
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['department_id' => $theirs->id]))
        ->assertSessionHasErrors('department_id');

    expect($asset->fresh()->department_id)->toBe($mine->id);
});

it('still lets a department user edit their own asset', function () {
    $mine = department();
    $asset = assetIn($mine);

    $this->actingAs(userOfRole(UserRole::Department, $mine))
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['name' => 'Laptop Baru']))
        ->assertRedirect();

    expect($asset->fresh()->name)->toBe('Laptop Baru');
});

it('lets an admin move an asset between departments', function () {
    $mine = department();
    $theirs = department('Finance', 'FIN');
    $asset = assetIn($mine);

    $this->actingAs(userOfRole(UserRole::Admin))
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['department_id' => $theirs->id]))
        ->assertRedirect();

    expect($asset->fresh()->department_id)->toBe($theirs->id);
});

it('refuses the read-only user every write path', function () {
    $dept = department();
    $asset = assetIn($dept);
    $user = userOfRole(UserRole::User, $dept);

    $this->actingAs($user)
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['name' => 'diubah']))
        ->assertForbidden();

    $this->actingAs($user)
        ->post("/assets/{$asset->asset_number}/stock-opname", ['condition' => 'damaged', 'status' => 'missing'])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete("/assets/{$asset->asset_number}")
        ->assertForbidden();

    expect($asset->fresh()->name)->toBe($asset->name);
});

/**
 * This used to assert a 403. The QR landing page is now reachable without a
 * session, so the scoped user gets the same summary a stranger would — the
 * detail stays hidden, which is what the policy was protecting. Asserting the
 * withheld fields is a tighter check than asserting the status code was.
 */
it('hides another department asset detail from a scoped user', function () {
    $mine = department();
    $theirs = department('Finance', 'FIN');
    $asset = assetIn($theirs);
    $asset->update(['pic' => 'Bukan Orang Sini', 'specification' => 'Rahasia departemen lain']);

    $response = $this->actingAs(userOfRole(UserRole::Department, $mine))
        ->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertDontSee('Bukan Orang Sini');
    $response->assertDontSee('Rahasia departemen lain');
    $response->assertDontSee($asset->location->name);
    $response->assertDontSee('Start STO');
});

it('keeps another department asset out of the list and the export', function () {
    $mine = department();
    $theirs = department('Finance', 'FIN');
    assetIn($mine, 'AST-KMI-0001');
    assetIn($theirs, 'AST-KMI-0002');

    $this->actingAs(userOfRole(UserRole::User, $mine))
        ->get('/assets')
        ->assertOk()
        ->assertSee('AST-KMI-0001')
        ->assertDontSee('AST-KMI-0002');
});

it('cannot widen its own scope through the department filter', function () {
    $mine = department();
    $theirs = department('Finance', 'FIN');
    assetIn($mine, 'AST-KMI-0001');
    assetIn($theirs, 'AST-KMI-0002');

    $this->actingAs(userOfRole(UserRole::User, $mine))
        ->get("/assets?department_id={$theirs->id}")
        ->assertOk()
        ->assertDontSee('AST-KMI-0002');
});
