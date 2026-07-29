<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\StockOpname;

/**
 * The QR printed on a physical label points here, so this URL is reachable
 * without a session. What a stranger gets back is the point of these tests:
 * enough to identify the asset and see when it was last counted, and nothing
 * that would help someone walk off with it.
 */
function labelled(): Asset
{
    $asset = assetIn(department());

    $asset->update([
        'pic' => 'Danar',
        'purchase_date' => '2026-05-16',
        'specification' => 'Rak Server Sinorack 4U',
    ]);

    return $asset->fresh();
}

it('identifies the asset to a visitor with no session', function () {
    $asset = labelled();

    $response = $this->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee($asset->asset_number);
    $response->assertSee($asset->name);
    $response->assertSee($asset->category->name);
    $response->assertSee(AssetStatus::Active->label());
    $response->assertSee(AssetCondition::Good->label());
});

it('withholds the fields that would help someone take the asset', function () {
    $asset = labelled();

    $response = $this->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertDontSee('Danar');
    $response->assertDontSee($asset->location->name);
    $response->assertDontSee($asset->department->name);
    $response->assertDontSee('Rak Server Sinorack 4U');
    $response->assertDontSee('16 May 2026');
});

it('tells a visitor the asset has never been counted', function () {
    $asset = labelled();

    $this->get("/asset/{$asset->asset_number}")
        ->assertOk()
        ->assertSee('Belum pernah di-STO');
});

it('shows a visitor the date of the last stock take', function () {
    $asset = labelled();

    StockOpname::create([
        'asset_id' => $asset->id,
        'user_id' => userOfRole(UserRole::Auditor)->id,
        'condition' => AssetCondition::Good,
        'status' => AssetStatus::Active,
        'checked_at' => now()->subDays(3),
    ]);

    $this->get("/asset/{$asset->asset_number}")
        ->assertOk()
        ->assertSee(now()->subDays(3)->format('d M Y'))
        ->assertDontSee('Belum pernah di-STO');
});

it('keeps the page out of search indexes', function () {
    $asset = labelled();

    $this->get("/asset/{$asset->asset_number}")
        ->assertSee('name="robots" content="noindex, nofollow"', false);
});

it('gives staff who may see the record the full detail page', function () {
    $asset = labelled();

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('Danar');
    $response->assertSee($asset->location->name);
    $response->assertSee('Rak Server Sinorack 4U');
});

it('falls back to the summary for staff scoped to another department', function () {
    $asset = labelled();
    $outsider = userOfRole(UserRole::Department, department('Finance', 'FIN'));

    $response = $this->actingAs($outsider)->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee($asset->asset_number);
    $response->assertDontSee('Danar');
    $response->assertDontSee('Rak Server Sinorack 4U');
});
