<?php

use App\Enums\UserRole;
use App\Models\Asset;

/**
 * Specifications are typed one item per line. HTML collapses newlines, so the
 * detail page used to render a laptop's whole spec sheet as a single wrapped
 * paragraph — technically all there, unreadable in practice.
 */
function assetWithSpec(string $spec): Asset
{
    $asset = assetIn(department());
    $asset->update(['specification' => $spec]);

    return $asset->fresh();
}

it('renders each specification line as its own labelled row', function () {
    $asset = assetWithSpec(
        "Processor : Intel Core i3-1215U\nMemory : 8 GB DDR4-3200\nHard Drive : 512 GB SSD PCIe"
    );

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('<dt class="spec-list__term">Processor</dt>', false);
    $response->assertSee('<dd class="spec-list__value">Intel Core i3-1215U</dd>', false);
    $response->assertSee('<dt class="spec-list__term">Memory</dt>', false);
    $response->assertSee('<dt class="spec-list__term">Hard Drive</dt>', false);
});

it('splits only on the first colon, so a value may contain one', function () {
    $asset = assetWithSpec('Operating System : Windows 11 Home Single Language 64(EN:English)');

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/asset/{$asset->asset_number}")
        ->assertOk()
        ->assertSee('<dt class="spec-list__term">Operating System</dt>', false)
        ->assertSee('Windows 11 Home Single Language 64(EN:English)');
});

it('keeps a line with no label as free text across both columns', function () {
    $asset = assetWithSpec("Processor : Intel Core i3\nGaransi habis Oktober 2026");

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('<dd class="spec-list__note">Garansi habis Oktober 2026</dd>', false);
});

it('drops blank lines rather than rendering empty rows', function () {
    $asset = assetWithSpec("Processor : Intel Core i3\n\n\nMemory : 8 GB");

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    expect(substr_count($response->getContent(), 'spec-list__term'))->toBe(2);
});

it('handles a specification written as a single line', function () {
    $asset = assetWithSpec('Laptop kantor, spesifikasi standar');

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/asset/{$asset->asset_number}")
        ->assertOk()
        ->assertSee('Laptop kantor, spesifikasi standar');
});

it('escapes markup inside a specification', function () {
    $asset = assetWithSpec('Monitor : 14" <script>alert(1)</script>');

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertDontSee('<script>alert(1)</script>', false);
    $response->assertSee('&lt;script&gt;', false);
});

it('leaves the specification off the public QR page', function () {
    $asset = assetWithSpec('Processor : Intel Core i3-1215U');

    $response = $this->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertDontSee('spec-list', false);
    $response->assertDontSee('Intel Core i3-1215U');
});
