<?php

use App\Enums\UserRole;
use App\Models\Category;

/**
 * The list pages gained a shared toolbar — search, sort, pagination, export —
 * on top of controllers that previously returned an unbounded, unsorted
 * collection. These cover the four actions that behaviour rests on.
 */
beforeEach(function () {
    $this->admin = userOfRole(UserRole::SuperAdmin);
});

it('paginates master data instead of returning every row', function () {
    foreach (range(1, 20) as $i) {
        Category::create(['name' => "Kategori {$i}", 'code' => sprintf('K%03d', $i)]);
    }

    $this->actingAs($this->admin)
        ->get('/categories')
        ->assertOk()
        ->assertViewHas('items', fn ($items) => $items->perPage() === 15
            && $items->count() === 15
            && $items->total() === 20);
});

it('sorts master data by an allowed column', function () {
    Category::create(['name' => 'Zebra', 'code' => 'ZBR']);
    Category::create(['name' => 'Alpha', 'code' => 'ALP']);

    $this->actingAs($this->admin)
        ->get('/categories?sort=name&direction=desc')
        ->assertOk()
        ->assertViewHas('items', fn ($items) => $items->first()->name === 'Zebra');
});

it('ignores a sort column that is not allowlisted', function () {
    Category::create(['name' => 'Zebra', 'code' => 'ZBR']);
    Category::create(['name' => 'Alpha', 'code' => 'ALP']);

    // The column falls back to `name`; only the direction is taken from input.
    $this->actingAs($this->admin)
        ->get('/categories?sort=id);drop--&direction=asc')
        ->assertOk()
        ->assertViewHas('items', fn ($items) => $items->first()->name === 'Alpha');
});

it('searches master data by name and code', function () {
    Category::create(['name' => 'Perkakas', 'code' => 'PKS']);
    Category::create(['name' => 'Kendaraan', 'code' => 'KDR']);

    $this->actingAs($this->admin)
        ->get('/categories?q=PKS')
        ->assertOk()
        ->assertViewHas('items', fn ($items) => $items->count() === 1 && $items->first()->name === 'Perkakas');
});

it('exports master data as csv honouring the active filter', function () {
    Category::create(['name' => 'Perkakas', 'code' => 'PKS']);
    Category::create(['name' => 'Kendaraan', 'code' => 'KDR']);

    $response = $this->actingAs($this->admin)->get('/categories/export?q=Perkakas');

    $response->assertOk()->assertHeader('content-type', 'text/csv; charset=utf-8');

    $csv = $response->streamedContent();

    expect($csv)->toContain('Perkakas')
        ->and($csv)->not->toContain('Kendaraan');
});

it('exports users as csv', function () {
    $response = $this->actingAs($this->admin)->get('/users/export');

    $response->assertOk();

    expect($response->streamedContent())
        ->toContain('Nama,Email,Role,Departemen,Dibuat')
        ->and($response->streamedContent())->toContain($this->admin->email);
});

it('exports stock opname history as csv', function () {
    $this->actingAs($this->admin)
        ->get('/stock-opname/export')
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=utf-8');
});

it('refuses master data export to a role without read access', function () {
    $this->actingAs(userOfRole(UserRole::User, department()))
        ->get('/categories/export')
        ->assertForbidden();
});
