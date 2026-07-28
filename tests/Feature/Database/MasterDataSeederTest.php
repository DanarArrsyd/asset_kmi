<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\MasterDataSeeder;

it('fills the master data tables when they are empty', function () {
    $this->seed(MasterDataSeeder::class);

    expect(Department::query()->count())->toBe(4)
        ->and(Category::query()->count())->toBe(3)
        ->and(Location::query()->count())->toBe(3)
        ->and(Brand::query()->count())->toBe(4);
});

it('runs on every deploy, so it must be safe to repeat', function () {
    $this->seed(MasterDataSeeder::class);
    $this->seed(MasterDataSeeder::class);
    $this->seed(MasterDataSeeder::class);

    expect(Department::query()->count())->toBe(4)
        ->and(Brand::query()->count())->toBe(4);
});

it('never resurrects a row someone deleted on purpose', function () {
    $this->seed(MasterDataSeeder::class);

    Category::query()->where('code', 'FUR')->delete();
    $this->seed(MasterDataSeeder::class);

    expect(Category::query()->count())->toBe(2)
        ->and(Category::query()->where('code', 'FUR')->exists())->toBeFalse();
});

it('leaves a populated table alone but still fills the empty ones', function () {
    Category::query()->create(['name' => 'Sudah Ada', 'code' => 'ADA']);

    $this->seed(MasterDataSeeder::class);

    expect(Category::query()->count())->toBe(1)
        ->and(Department::query()->count())->toBe(4);
});

it('creates no accounts', function () {
    $this->seed(MasterDataSeeder::class);

    expect(User::query()->count())->toBe(0);
});
