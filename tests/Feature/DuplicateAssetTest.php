<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;

/**
 * Identical hardware arrives in batches — two of the same switch, ten of the same
 * laptop. Retyping a full specification for each is where wrong data comes from.
 *
 * Duplicating deliberately writes nothing. It prefills the create form and waits,
 * because matching hardware rarely matches completely and because a double-click
 * must not produce two assets nobody asked for.
 */
function sourceAsset(): Asset
{
    $asset = assetIn(department());

    $asset->update([
        'model' => 'Type 82TS',
        'specification' => "Processor : Intel Core i3-1215U\nMemory : 8 GB DDR4-3200",
        'pic' => 'Danar',
        'purchase_date' => '2023-10-22',
        'status' => AssetStatus::Maintenance,
        'condition' => AssetCondition::Fair,
    ]);

    return $asset->fresh();
}

it('sends the duplicate button to the create form', function () {
    $asset = sourceAsset();

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/assets/{$asset->asset_number}/duplicate")
        ->assertRedirect(route('assets.create'));
});

it('prefills the form with the source asset', function () {
    $asset = sourceAsset();

    $response = $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/assets/{$asset->asset_number}/duplicate");

    $response->assertSessionHasInput([
        'name' => $asset->name,
        'category_id' => $asset->category_id,
        'brand_id' => $asset->brand_id,
        'model' => 'Type 82TS',
        'department_id' => $asset->department_id,
        'location_id' => $asset->location_id,
        'pic' => 'Danar',
    ]);
});

it('prefills the date and the enums as values the inputs can use', function () {
    $asset = sourceAsset();

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/assets/{$asset->asset_number}/duplicate")
        ->assertSessionHasInput([
            'purchase_date' => '2023-10-22',
            'status' => 'maintenance',
            'condition' => 'fair',
        ]);
});

it('renders the prefilled values into the form', function () {
    $asset = sourceAsset();
    $admin = userOfRole(UserRole::Admin);

    $this->actingAs($admin)->get("/assets/{$asset->asset_number}/duplicate");

    $response = $this->actingAs($admin)->get('/assets/create');

    $response->assertOk();
    $response->assertSee('value="Type 82TS"', false);
    $response->assertSee('Processor : Intel Core i3-1215U');
    $response->assertSee('value="2023-10-22"', false);
    $response->assertSee("Disalin dari <strong>{$asset->asset_number}</strong>", false);
});

it('creates nothing until the form is submitted', function () {
    $asset = sourceAsset();

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/assets/{$asset->asset_number}/duplicate");

    expect(Asset::count())->toBe(1);
});

it('gives the saved duplicate its own asset number and QR', function () {
    $asset = sourceAsset();
    $admin = userOfRole(UserRole::Admin);

    $this->actingAs($admin)->get("/assets/{$asset->asset_number}/duplicate");

    $this->actingAs($admin)->post('/assets', [
        'name' => $asset->name,
        'category_id' => $asset->category_id,
        'brand_id' => $asset->brand_id,
        'department_id' => $asset->department_id,
        'location_id' => $asset->location_id,
        'status' => 'active',
        'condition' => 'good',
    ])->assertRedirect();

    $copy = Asset::latest('id')->first();

    expect(Asset::count())->toBe(2);
    expect($copy->asset_number)->not->toBe($asset->asset_number);
    expect($copy->qr_path)->toBe("qrcodes/{$copy->asset_number}.png");
});

it('does not carry the photo of the source asset', function () {
    $asset = sourceAsset();
    $asset->update(['photo_path' => 'assets/asli.jpg']);

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/assets/{$asset->asset_number}/duplicate")
        ->assertSessionMissing('_old_input.photo');
});

it('refuses a role that may not create assets', function () {
    $asset = sourceAsset();

    foreach ([UserRole::Auditor, UserRole::User] as $role) {
        $this->actingAs(userOfRole($role, department()))
            ->get("/assets/{$asset->asset_number}/duplicate")
            ->assertForbidden();
    }
});

it('refuses a scoped user duplicating another department asset', function () {
    $theirs = assetIn(department('Finance', 'FIN'), 'AST-KMI-0009');
    $outsider = userOfRole(UserRole::Department, department('Produksi', 'PRD'));

    $this->actingAs($outsider)
        ->get("/assets/{$theirs->asset_number}/duplicate")
        ->assertForbidden();
});

it('offers the button on the detail page only to a role that may create', function () {
    $asset = sourceAsset();

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/asset/{$asset->asset_number}")
        ->assertSee('Duplikat');

    $this->actingAs(userOfRole(UserRole::Auditor))
        ->get("/asset/{$asset->asset_number}")
        ->assertDontSee('Duplikat');
});
