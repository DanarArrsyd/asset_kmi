<?php

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;

/**
 * Brand, Category, Department and Location all share ManagesMasterData, so the
 * matrix is asserted once per role against every one of them. A policy that
 * quietly stops using the trait — or a trait edit that widens one role — fails
 * here rather than in production.
 *
 * The matrix from CLAUDE.md: Super Admin and Admin write; Auditor reads;
 * Department and User cannot reach master data at all.
 */
dataset('master data models', [
    'brand' => [Brand::class],
    'category' => [Category::class],
    'department' => [Department::class],
    'location' => [Location::class],
]);

dataset('roles that may write master data', [
    'super admin' => [UserRole::SuperAdmin],
    'admin' => [UserRole::Admin],
]);

dataset('roles that may only read master data', [
    'auditor' => [UserRole::Auditor],
]);

dataset('roles shut out of master data', [
    'department' => [UserRole::Department],
    'user' => [UserRole::User],
]);

it('lets a writer read and change master data', function (string $model, UserRole $role) {
    $user = userOfRole($role, department());

    expect($user->can('viewAny', $model))->toBeTrue();
    expect($user->can('create', $model))->toBeTrue();
    expect($user->can('update', new $model))->toBeTrue();
    expect($user->can('delete', new $model))->toBeTrue();
})->with('master data models')->with('roles that may write master data');

it('lets a reader read master data but not change it', function (string $model, UserRole $role) {
    $user = userOfRole($role, department());

    expect($user->can('viewAny', $model))->toBeTrue();
    expect($user->can('create', $model))->toBeFalse();
    expect($user->can('update', new $model))->toBeFalse();
    expect($user->can('delete', new $model))->toBeFalse();
})->with('master data models')->with('roles that may only read master data');

it('keeps scoped roles out of master data entirely', function (string $model, UserRole $role) {
    $user = userOfRole($role, department());

    expect($user->can('viewAny', $model))->toBeFalse();
    expect($user->can('create', $model))->toBeFalse();
    expect($user->can('update', new $model))->toBeFalse();
    expect($user->can('delete', new $model))->toBeFalse();
})->with('master data models')->with('roles shut out of master data');

it('refuses a scoped role over HTTP as well as at the gate', function () {
    $department = userOfRole(UserRole::Department, department());

    $this->actingAs($department)->get('/categories')->assertForbidden();
    $this->actingAs($department)->post('/categories', ['name' => 'Baru', 'code' => 'BRU'])->assertForbidden();
});

it('lets an auditor read the list but refuses the write', function () {
    $auditor = userOfRole(UserRole::Auditor);

    $this->actingAs($auditor)->get('/brands')->assertOk();
    $this->actingAs($auditor)->post('/brands', ['name' => 'Baru'])->assertForbidden();
});
