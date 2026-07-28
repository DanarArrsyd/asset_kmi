<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\FirstAdminSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    config([
        'app.first_admin.name' => 'Danar',
        'app.first_admin.email' => 'admin@example.com',
        'app.first_admin.password' => 'a-long-enough-password',
    ]);
});

it('creates one super admin when the users table is empty', function () {
    $this->seed(FirstAdminSeeder::class);

    $admin = User::query()->sole();

    expect($admin->name)->toBe('Danar')
        ->and($admin->email)->toBe('admin@example.com')
        ->and($admin->role)->toBe(UserRole::SuperAdmin)
        ->and($admin->email_verified_at)->not->toBeNull();
});

it('hashes the password rather than storing it as given', function () {
    $this->seed(FirstAdminSeeder::class);

    $admin = User::query()->sole();

    expect($admin->password)->not->toBe('a-long-enough-password')
        ->and(Hash::check('a-long-enough-password', $admin->password))->toBeTrue();
});

it('cannot mint a second admin once any account exists', function () {
    User::factory()->create(['role' => UserRole::User]);

    $this->seed(FirstAdminSeeder::class);

    expect(User::query()->count())->toBe(1)
        ->and(User::query()->where('email', 'admin@example.com')->exists())->toBeFalse();
});

it('cannot reset an existing administrator by re-running', function () {
    $this->seed(FirstAdminSeeder::class);
    $original = User::query()->sole()->password;

    config(['app.first_admin.password' => 'attacker-chosen-password']);
    $this->seed(FirstAdminSeeder::class);

    expect(User::query()->sole()->password)->toBe($original);
});

it('creates nothing when the credentials are not configured', function () {
    config(['app.first_admin.email' => null, 'app.first_admin.password' => null]);

    $this->seed(FirstAdminSeeder::class);

    expect(User::query()->count())->toBe(0);
});

it('creates nothing when only the email is configured', function () {
    config(['app.first_admin.password' => null]);

    $this->seed(FirstAdminSeeder::class);

    expect(User::query()->count())->toBe(0);
});
