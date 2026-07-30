<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;

/**
 * The interface is Indonesian. It drifted because Breeze ships English and each
 * new screen copied whatever the last one did, so the labels are pinned here.
 *
 * Role names (Super Admin, Admin, Auditor, Department, User) stay English on
 * purpose — they are identifiers from the access matrix, not prose.
 */
it('reports validation failures in Indonesian', function () {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->post('/assets', []);

    $response->assertSessionHasErrors([
        'name' => 'Nama wajib diisi.',
        'category_id' => 'Kategori wajib diisi.',
        'department_id' => 'Departemen wajib diisi.',
        'location_id' => 'Lokasi wajib diisi.',
    ]);
});

it('names the field, not the database column, when validation fails', function () {
    $asset = assetIn(department());

    $response = $this->actingAs(userOfRole(UserRole::Admin))
        ->put("/assets/{$asset->asset_number}", validPayload($asset, ['purchase_date' => 'bukan-tanggal']));

    $response->assertSessionHasErrors([
        'purchase_date' => 'Tanggal Pembelian harus berupa tanggal yang benar.',
    ]);
});

it('labels the sidebar in Indonesian', function () {
    $response = $this->actingAs(userOfRole(UserRole::SuperAdmin))->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Kategori');
    $response->assertSee('Departemen');
    $response->assertSee('Lokasi');
    $response->assertSee('Pengguna');
    $response->assertSee('Profil');
    $response->assertDontSee('>Category<', false);
    $response->assertDontSee('>Location<', false);
});

it('labels the dashboard summary in Indonesian', function () {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Total Aset');
    $response->assertSee('Aktif');
    $response->assertSee('Hilang');
    $response->assertSee('Progres STO');
    $response->assertSee('Aset per Departemen');
    $response->assertSee('Aktivitas Terakhir');
    $response->assertDontSee('Total Assets');
    $response->assertDontSee('Recent Activities');
});

it('labels the asset detail in Indonesian', function () {
    $asset = assetIn(department());
    // The specification row only renders when the field has a value.
    $asset->update(['specification' => 'Rak Server 4U']);

    $response = $this->actingAs(userOfRole(UserRole::Admin))->get("/asset/{$asset->asset_number}");

    $response->assertOk();
    $response->assertSee('Tanggal Pembelian');
    $response->assertSee('Kondisi');
    $response->assertSee('Spesifikasi');
    $response->assertSee('Riwayat Stock Opname');
    $response->assertSee('Mulai STO');
    $response->assertDontSee('Purchase Date');
    $response->assertDontSee('History Stock Opname');
});

it('labels the asset form in Indonesian', function () {
    $response = $this->actingAs(userOfRole(UserRole::Admin))->get('/assets/create');

    $response->assertOk();
    $response->assertSee('Spesifikasi');
    $response->assertSee('Tanggal Pembelian');
    $response->assertSee('Pilih departemen');
    $response->assertSee('Pilih lokasi');
    $response->assertDontSee('Purchase Date');
});

it('labels asset status and condition in Indonesian', function () {
    expect(AssetStatus::Active->label())->toBe('Aktif');
    expect(AssetStatus::Missing->label())->toBe('Hilang');
    expect(AssetCondition::Good->label())->toBe('Baik');
    expect(AssetCondition::Damaged->label())->toBe('Rusak');

    $asset = assetIn(department());

    $this->actingAs(userOfRole(UserRole::Admin))
        ->get("/asset/{$asset->asset_number}")
        ->assertOk()
        ->assertSee('Aktif')
        ->assertSee('Baik')
        ->assertDontSee('>Active<', false)
        ->assertDontSee('>Good<', false);
});

it('titles the master data screens in Indonesian', function () {
    $admin = userOfRole(UserRole::Admin);

    $this->actingAs($admin)->get('/categories')->assertOk()->assertSee('Tambah Kategori');
    $this->actingAs($admin)->get('/departments')->assertOk()->assertSee('Tambah Departemen');
    $this->actingAs($admin)->get('/locations')->assertOk()->assertSee('Tambah Lokasi');
});
