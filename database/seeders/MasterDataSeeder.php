<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Starter master data, safe to run on production.
 *
 * The deploy endpoint runs this on every deploy, so it must never overwrite
 * or resurrect anything. Each table is filled only while it is still empty:
 * once a table has rows, whoever owns that data owns it, including having
 * deliberately deleted a row this seeder would otherwise put back.
 *
 * Creates no users — see FirstAdminSeeder for that.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->fillIfEmpty(Department::class, [
            ['name' => 'Produksi', 'code' => 'PRD'],
            ['name' => 'Warehouse', 'code' => 'WH'],
            ['name' => 'IT & Support', 'code' => 'IT'],
            ['name' => 'Finance', 'code' => 'FIN'],
        ]);

        $this->fillIfEmpty(Category::class, [
            ['name' => 'Machinery', 'code' => 'MCH'],
            ['name' => 'IT Equipment', 'code' => 'ITE'],
            ['name' => 'Furniture', 'code' => 'FUR'],
        ]);

        $this->fillIfEmpty(Location::class, [
            ['name' => 'Kantor Pusat', 'code' => 'LOC-HQ'],
            ['name' => 'Gudang A', 'code' => 'LOC-WHA'],
            ['name' => 'Gudang B', 'code' => 'LOC-WHB'],
        ]);

        $this->fillIfEmpty(Brand::class, [
            ['name' => 'Dell'],
            ['name' => 'HP'],
            ['name' => 'Toyota'],
            ['name' => 'Generic'],
        ]);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<int, array<string, string>>  $rows
     */
    private function fillIfEmpty(string $model, array $rows): void
    {
        if ($model::query()->exists()) {
            return;
        }

        foreach ($rows as $row) {
            $model::query()->create($row);
        }
    }
}
