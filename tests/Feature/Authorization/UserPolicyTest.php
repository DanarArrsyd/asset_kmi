<?php

use App\Enums\UserRole;
use App\Models\User;

/**
 * User management belongs to Super Admin alone. The interesting rule is the last
 * one: a Super Admin may delete other Super Admins but never themselves, so the
 * account holding the rights cannot remove the rights.
 */
dataset('roles without user management', [
    'admin' => [UserRole::Admin],
    'auditor' => [UserRole::Auditor],
    'department' => [UserRole::Department],
    'user' => [UserRole::User],
]);

it('lets a Super Admin manage users', function () {
    $admin = userOfRole(UserRole::SuperAdmin);
    $other = userOfRole(UserRole::Admin);

    expect($admin->can('viewAny', User::class))->toBeTrue();
    expect($admin->can('view', $other))->toBeTrue();
    expect($admin->can('create', User::class))->toBeTrue();
    expect($admin->can('update', $other))->toBeTrue();
    expect($admin->can('delete', $other))->toBeTrue();
});

it('refuses user management to every other role', function (UserRole $role) {
    $user = userOfRole($role, department());
    $other = userOfRole(UserRole::User, department());

    expect($user->can('viewAny', User::class))->toBeFalse();
    expect($user->can('view', $other))->toBeFalse();
    expect($user->can('create', User::class))->toBeFalse();
    expect($user->can('update', $other))->toBeFalse();
    expect($user->can('delete', $other))->toBeFalse();
})->with('roles without user management');

it('refuses a Super Admin the deletion of their own account from the user list', function () {
    $admin = userOfRole(UserRole::SuperAdmin);

    expect($admin->can('delete', $admin))->toBeFalse();
    expect($admin->can('update', $admin))->toBeTrue();
});

it('lets a Super Admin delete another Super Admin', function () {
    $admin = userOfRole(UserRole::SuperAdmin);
    $peer = userOfRole(UserRole::SuperAdmin);

    expect($admin->can('delete', $peer))->toBeTrue();
});

it('enforces the same matrix over HTTP', function (UserRole $role) {
    $user = userOfRole($role, department());

    $this->actingAs($user)->get('/users')->assertForbidden();
    $this->actingAs($user)->get('/users/create')->assertForbidden();
})->with('roles without user management');

it('still refuses the last Super Admin deletion through the user list', function () {
    $only = userOfRole(UserRole::SuperAdmin);
    $target = userOfRole(UserRole::SuperAdmin);

    // Two exist, so the floor does not apply and the delete goes through.
    $this->actingAs($only)->delete("/users/{$target->id}")->assertRedirect();
    expect(User::find($target->id))->toBeNull();

    // One left, and it is the caller — refused by the policy, not the floor.
    $this->actingAs($only)->delete("/users/{$only->id}")->assertForbidden();
    expect(User::find($only->id))->not->toBeNull();
});
