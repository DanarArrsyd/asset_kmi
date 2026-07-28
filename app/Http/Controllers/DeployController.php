<?php

namespace App\Http\Controllers;

use Database\Seeders\FirstAdminSeeder;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    /**
     * Run pending migrations + cache warmup on hosts with no SSH access.
     * Guarded by a random token set via DEPLOY_TOKEN in .env (never in git).
     * Call after every deploy: POST /deploy/migrate?token=...
     */
    public function migrate(Request $request): Response
    {
        $expected = config('app.deploy_token');

        if (blank($expected) || ! hash_equals($expected, (string) $request->query('token'))) {
            abort(404);
        }

        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        // Both seeders are no-ops once their tables have rows, so running them
        // on every deploy is safe. They are called by class rather than through
        // DatabaseSeeder because that one also creates a test account with a
        // known password.
        Artisan::call('db:seed', [
            '--class' => MasterDataSeeder::class,
            '--force' => true,
        ]);
        $seedOutput = Artisan::output();

        Artisan::call('db:seed', [
            '--class' => FirstAdminSeeder::class,
            '--force' => true,
        ]);
        $seedOutput .= Artisan::output();

        Artisan::call('storage:link');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        Log::info('Deploy migration run via /deploy/migrate');

        return response("Migrated.\n\n{$migrateOutput}\n{$seedOutput}", 200)
            ->header('Content-Type', 'text/plain');
    }
}
