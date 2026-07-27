<?php

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
