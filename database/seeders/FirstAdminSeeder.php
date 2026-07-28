<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates the one Super Admin that opens a brand-new database.
 *
 * Public registration is closed and the host has no SSH, so without this a
 * fresh install has no way in short of hand-writing a bcrypt hash into
 * phpMyAdmin.
 *
 * Runs only while the users table is empty. Once any account exists this is a
 * no-op forever, so it cannot be used to mint a second admin, and it cannot
 * reset the password of an existing one.
 */
class FirstAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->exists()) {
            $this->command?->info('Users already exist — first admin not created.');

            return;
        }

        $email = config('app.first_admin.email');
        $password = config('app.first_admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn(
                'FIRST_ADMIN_EMAIL / FIRST_ADMIN_PASSWORD not set — no admin created.'
            );

            return;
        }

        $user = User::query()->create([
            'name' => config('app.first_admin.name'),
            'email' => $email,
            'password' => $password,
            'role' => UserRole::SuperAdmin,
        ]);

        // email_verified_at is not fillable, and User does not implement
        // MustVerifyEmail today — set it anyway so enabling verification later
        // does not lock the only administrator out of the dashboard.
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->command?->info("Super Admin created: {$email}");
    }
}
