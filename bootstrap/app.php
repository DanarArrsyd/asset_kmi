<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // POST /deploy/migrate is called by CI, which has no browser session
        // and therefore no CSRF token to send — it authenticates with
        // DEPLOY_TOKEN instead (see DeployController::migrate).
        //
        // Without this the route answers 419 to every deploy. That went
        // unnoticed for weeks because the workflow only echoed a warning on
        // failure, so runs stayed green while no migration ever ran.
        //
        // No test covers this: VerifyCsrfToken short-circuits whenever the app
        // is running tests, so a feature test passes with or without the
        // exemption. The deploy workflow is the real check — it fails hard on
        // a non-2xx now.
        $middleware->validateCsrfTokens(except: [
            'deploy/migrate',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
