<?php

use App\Enums\UserRole;
use App\Models\Asset;
use Illuminate\Support\Facades\Gate;

/**
 * The role x action matrix for assets.
 *
 * Each spec asserts the whole map at once rather than one role per test, so a
 * policy change shows every role it moved, not just the first one to fail.
 */

/** Every role, each holding the asset's own department where that matters. */
function abilityMap(string $ability, bool $sameDepartment = true): array
{
    $owning = department();
    $other = department('Finance', 'FIN');
    $asset = assetIn($owning);

    $scopedDepartment = $sameDepartment ? $owning : $other;

    $map = [];

    foreach (UserRole::cases() as $role) {
        $user = userOfRole($role, in_array($role, [UserRole::Department, UserRole::User], true)
            ? $scopedDepartment
            : null);

        $map[$role->value] = Gate::forUser($user)->allows($ability, $asset);
    }

    return $map;
}

it('gates viewing an asset', function () {
    expect(abilityMap('view'))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => true,
        'department' => true,
        'user' => true,
    ]);
});

it('gates viewing an asset belonging to another department', function () {
    expect(abilityMap('view', sameDepartment: false))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => true,
        'department' => false,
        'user' => false,
    ]);
});

it('gates editing the asset record', function () {
    expect(abilityMap('update'))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => false,
        'department' => true,
        'user' => false,
    ]);
});

it('gates editing an asset belonging to another department', function () {
    expect(abilityMap('update', sameDepartment: false))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => false,
        'department' => false,
        'user' => false,
    ]);
});

it('gates deleting an asset', function () {
    expect(abilityMap('delete'))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => false,
        'department' => false,
        'user' => false,
    ]);
});

it('gates recording a stock take, which auditors may do but update denies them', function () {
    expect(abilityMap('recordStockOpname'))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => true,
        'department' => false,
        'user' => false,
    ]);
});

it('gates moving an asset to another department', function () {
    expect(abilityMap('reassignDepartment'))->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => false,
        'department' => false,
        'user' => false,
    ]);
});

it('gates creating assets', function () {
    $map = [];

    foreach (UserRole::cases() as $role) {
        $map[$role->value] = Gate::forUser(userOfRole($role, department()))
            ->allows('create', Asset::class);
    }

    expect($map)->toBe([
        'super_admin' => true,
        'admin' => true,
        'auditor' => false,
        'department' => false,
        'user' => false,
    ]);
});
