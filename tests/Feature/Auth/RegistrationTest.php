<?php

use App\Models\User;

it('does not expose a public registration page', function () {
    $this->get('/register')->assertNotFound();
});

it('does not accept a posted registration', function () {
    $this->post('/register', [
        'name' => 'Someone Uninvited',
        'email' => 'uninvited@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

it('still serves the login page', function () {
    $this->get('/login')->assertOk();
});
