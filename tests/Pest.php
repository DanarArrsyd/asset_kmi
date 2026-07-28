<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the full application and hit a fresh in-memory SQLite
| database (see phpunit.xml) so every test starts from a known schema.
| Unit tests stay framework-free — no TestCase, no database.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Add project-wide custom expectations here, e.g. ->toBeAssetNumber().
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Shared test helpers live here so specs stay readable.
|
*/

/**
 * A user of the given role, attached to $department when the role is scoped.
 */
function userOfRole(UserRole $role, ?Department $department = null): User
{
    return User::factory()->create([
        'role' => $role,
        'department_id' => $department?->id,
    ]);
}

/**
 * A saved asset in $department, with the master data it needs created inline.
 * Number is caller-supplied so a spec can hold several at once.
 */
function assetIn(Department $department, string $number = 'AST000001'): Asset
{
    return Asset::create([
        'asset_number' => $number,
        'name' => 'Laptop '.$number,
        'category_id' => Category::firstOrCreate(['code' => 'ITE'], ['name' => 'IT Equipment'])->id,
        'brand_id' => Brand::firstOrCreate(['name' => 'Dell'])->id,
        'department_id' => $department->id,
        'location_id' => Location::firstOrCreate(['code' => 'HQ'], ['name' => 'Kantor Pusat'])->id,
        'status' => AssetStatus::Active,
        'condition' => AssetCondition::Good,
    ]);
}

function department(string $name = 'Produksi', string $code = 'PRD'): Department
{
    return Department::firstOrCreate(['code' => $code], ['name' => $name]);
}
