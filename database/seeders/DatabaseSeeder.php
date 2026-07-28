<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Local development seed.
 *
 * Production never runs this one — the deploy endpoint calls MasterDataSeeder
 * and FirstAdminSeeder directly, because the test account below has a known
 * password and has no business existing on a live server.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(MasterDataSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => UserRole::SuperAdmin,
        ]);
    }
}
