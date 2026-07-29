<?php

use App\Enums\UserRole;

/**
 * The sign-in screens are the first thing anyone sees, and until now they were
 * the last corner still speaking Breeze's English.
 */
it('shows the sign-in screen in Indonesian', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('Masuk');
    $response->assertSee('Gunakan akun kantor Anda.');
    $response->assertSee('Ingat saya');
    $response->assertSee('Lupa password?');
    $response->assertDontSee('Remember me');
    $response->assertDontSee('Forgot your password?');
});

it('reports a failed sign-in in Indonesian', function () {
    $user = userOfRole(UserRole::Admin);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'password-yang-salah',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email' => 'Email atau password salah.']);
});

it('frames the sign-in screen with the brand rail', function () {
    $response = $this->get('/login');

    $response->assertSee('auth-split__brand', false);
    $response->assertSee('Sistem Manajemen Aset');
    $response->assertSee('PT Kenco Manufacturing');
});

it('leaves the QR landing page as a plain centred card', function () {
    $asset = assetIn(department());

    $response = $this->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('auth-card', false);
    $response->assertDontSee('auth-split__brand', false);
});

it('keeps the other auth screens in Indonesian', function () {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertSee('Lupa Password')
        ->assertSee('Kirim Tautan Reset')
        ->assertDontSee('Email Password Reset Link');
});
