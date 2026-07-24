<?php

namespace App\Http\Controllers;

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

        Artisan::call('storage:link');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        Log::info('Deploy migration run via /deploy/migrate');

        return response("Migrated.\n\n{$migrateOutput}", 200)->header('Content-Type', 'text/plain');
    }
}
