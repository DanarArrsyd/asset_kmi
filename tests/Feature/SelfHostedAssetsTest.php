<?php

use App\Enums\UserRole;

/**
 * Every byte a page needs comes from this origin. A stylesheet link to Google
 * Fonts or jsdelivr is easy to paste back in and costs a third-party DNS lookup
 * and TLS handshake before the page can paint, so it is asserted against rather
 * than left to review.
 */
$thirdParty = ['fonts.googleapis.com', 'fonts.gstatic.com', 'cdn.jsdelivr.net'];

it('serves the login page without reaching off-origin', function () use ($thirdParty) {
    $response = $this->get('/login');

    $response->assertOk();

    foreach ($thirdParty as $host) {
        $response->assertDontSee($host);
    }
});

it('serves the app shell without reaching off-origin', function () use ($thirdParty) {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/dashboard');

    $response->assertOk();

    foreach ($thirdParty as $host) {
        $response->assertDontSee($host);
    }
});

it('serves the QR landing page without reaching off-origin', function () use ($thirdParty) {
    $asset = assetIn(department());

    $response = $this->get("/asset/{$asset->asset_number}");

    $response->assertOk();

    foreach ($thirdParty as $host) {
        $response->assertDontSee($host);
    }
});
