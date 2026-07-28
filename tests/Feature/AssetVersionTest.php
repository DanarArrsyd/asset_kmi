<?php

use App\Support\AssetVersion;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    AssetVersion::flush();
});

it('appends the file modification time to the url', function () {
    $path = 'testing-asset-version.css';
    File::put(public_path($path), 'body{}');
    $mtime = File::lastModified(public_path($path));

    expect(AssetVersion::url($path))->toBe(asset($path).'?v='.$mtime);

    File::delete(public_path($path));
});

it('falls back to a zero version for a missing file', function () {
    expect(AssetVersion::url('does-not-exist.css'))->toBe(asset('does-not-exist.css').'?v=0');
});

it('versions the stylesheets rendered by the guest layout', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('css/tokens.css?v='.File::lastModified(public_path('css/tokens.css')), false);
    $response->assertSee('css/app.css?v='.File::lastModified(public_path('css/app.css')), false);
});
