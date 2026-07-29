<?php

use App\Enums\UserRole;
use App\Models\User;

/**
 * The user list already refuses to delete the last Super Admin. This page could,
 * and the outcome is worse — the account deletes itself, so there is nobody left
 * with the rights to create a replacement.
 */
it('refuses to delete the only Super Admin', function () {
    $only = userOfRole(UserRole::SuperAdmin);

    $response = $this->actingAs($only)->delete('/profile', ['password' => 'password']);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('error');

    expect(User::find($only->id))->not->toBeNull();
    $this->assertAuthenticatedAs($only);
});

it('lets a Super Admin leave once another one exists', function () {
    $leaving = userOfRole(UserRole::SuperAdmin);
    userOfRole(UserRole::SuperAdmin);

    $this->actingAs($leaving)->delete('/profile', ['password' => 'password']);

    expect(User::find($leaving->id))->toBeNull();
    $this->assertGuest();
});

it('still deletes an account that is not the last Super Admin', function () {
    $auditor = userOfRole(UserRole::Auditor);
    userOfRole(UserRole::SuperAdmin);

    $this->actingAs($auditor)->delete('/profile', ['password' => 'password']);

    expect(User::find($auditor->id))->toBeNull();
});

it('shows the account facts a user cannot edit here', function () {
    $user = userOfRole(UserRole::Auditor, department());

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    $response->assertSee('Akun Anda');
    $response->assertSee(UserRole::Auditor->label());
    $response->assertSee(UserRole::Auditor->description());
    $response->assertSee($user->department->name);
});

it('speaks Indonesian like the rest of the app', function () {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/profile');

    $response->assertOk();
    $response->assertSee('Informasi Akun');
    $response->assertSee('Ubah Password');
    $response->assertSee('Hapus Akun');
    $response->assertDontSee('Profile Information');
    $response->assertDontSee('Update Password');
    $response->assertDontSee('Delete Account');
    $response->assertDontSee("Update your account's profile information", false);
});
