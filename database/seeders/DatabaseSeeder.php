<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $departments = collect([
            ['name' => 'Produksi', 'code' => 'PRD'],
            ['name' => 'Warehouse', 'code' => 'WH'],
            ['name' => 'IT & Support', 'code' => 'IT'],
            ['name' => 'Finance', 'code' => 'FIN'],
        ])->map(fn ($d) => Department::create($d));

        collect([
            ['name' => 'Machinery', 'code' => 'MCH'],
            ['name' => 'IT Equipment', 'code' => 'ITE'],
            ['name' => 'Furniture', 'code' => 'FUR'],
        ])->each(fn ($c) => Category::create($c));

        collect([
            ['name' => 'Kantor Pusat', 'code' => 'LOC-HQ'],
            ['name' => 'Gudang A', 'code' => 'LOC-WHA'],
            ['name' => 'Gudang B', 'code' => 'LOC-WHB'],
        ])->each(fn ($l) => Location::create($l));

        collect(['Dell', 'HP', 'Toyota', 'Generic'])
            ->each(fn ($b) => Brand::create(['name' => $b]));

        User::query()->update(['role' => UserRole::SuperAdmin]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => UserRole::SuperAdmin,
        ]);
    }
}
