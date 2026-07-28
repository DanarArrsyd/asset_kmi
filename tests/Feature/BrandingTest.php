<?php

use App\Enums\UserRole;

/**
 * The app name is configuration, not a string copied into six templates —
 * which is what it was until the rename from "STO Asset". These pin the
 * templates to config so the next rename is one line.
 */
it('takes the sidebar wordmark and page title from config', function () {
    config(['app.name' => 'Nama Percobaan']);

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Dashboard — Nama Percobaan');
    $response->assertSee('<span class="brand-text">Nama Percobaan</span>', false);
});

it('takes the login wordmark from config', function () {
    config(['app.name' => 'Nama Percobaan']);

    $this->get('/login')
        ->assertOk()
        ->assertSee('Nama Percobaan');
});

it('defaults to the Kenco name', function () {
    expect(config('app.name'))->toBe('SIMASET Kenco');
});
