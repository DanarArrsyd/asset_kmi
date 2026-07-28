<?php

use App\Enums\UserRole;

/**
 * Create and edit pages put the fields in one column and the context that used
 * to have nowhere to go — the photo, what gets generated on save, what a role
 * can do — in the column beside them. These assert the aside is actually
 * populated, since an empty one is the whole bug being fixed.
 */
it('shows the photo panel and what gets generated beside the asset form', function () {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/assets/create');

    $response->assertOk();
    $response->assertSee('form-layout__aside', false);
    $response->assertSee('Foto Asset');
    $response->assertSee('Dibuat Otomatis');
    $response->assertSee('AST000001');
});

it('shows the asset identity beside the edit form', function () {
    $asset = assetIn(department());

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/assets/{$asset->asset_number}/edit");

    $response->assertOk();
    $response->assertSee('Info Asset');
    $response->assertSee($asset->asset_number);
    $response->assertDontSee('Dibuat Otomatis');
});

it('explains every role beside the user form', function () {
    $response = $this->actingAs(userOfRole(UserRole::SuperAdmin))->get('/users/create');

    $response->assertOk();
    $response->assertSee('Hak Akses per Role');

    foreach (UserRole::cases() as $role) {
        $response->assertSee($role->description());
    }
});

it('marks the role the form has selected', function () {
    $target = userOfRole(UserRole::Auditor);

    $response = $this->actingAs(userOfRole(UserRole::SuperAdmin))->get("/users/{$target->id}/edit");

    $response->assertOk();
    $response->assertSee('data-role="auditor" class="role-list__item is-active"', false);
});
