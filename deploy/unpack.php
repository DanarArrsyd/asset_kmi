<?php

/**
 * Standalone, framework-free unzip endpoint for FTP-only hosts.
 *
 * Lives at public/unpack.php (uploaded directly via FTP, outside the
 * zip). GitHub Actions ships the whole build as one deploy.zip (fast,
 * single file) instead of thousands of individual files over FTP —
 * this extracts it in place, then gets out of the way.
 *
 * Token is read straight out of the server's .env (no Laravel bootstrap
 * available yet on a fresh deploy), same DEPLOY_TOKEN used by
 * /deploy/migrate once the app itself is unpacked.
 */
$envPath = __DIR__.'/../.env';

if (! is_file($envPath)) {
    http_response_code(404);
    exit;
}

$expected = null;
foreach (file($envPath) as $line) {
    if (str_starts_with($line, 'DEPLOY_TOKEN=')) {
        $expected = trim(substr($line, strlen('DEPLOY_TOKEN=')));
        break;
    }
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (blank($expected ?? null) || ! hash_equals((string) $expected, (string) $token)) {
    http_response_code(404);
    exit;
}

$zipPath = __DIR__.'/../deploy.zip';

if (! is_file($zipPath)) {
    header('Content-Type: text/plain');
    exit("No deploy.zip found at {$zipPath}.\n");
}

$zip = new ZipArchive;

if ($zip->open($zipPath) !== true) {
    header('Content-Type: text/plain');
    http_response_code(500);
    exit("Failed to open deploy.zip.\n");
}

$zip->extractTo(__DIR__.'/..');
$zip->close();
unlink($zipPath);

// Safety net: these must exist + be writable for Laravel to boot at all.
// A zip tool can silently drop directories that end up with no files in
// them after exclude patterns are applied (bit us once already).
$root = __DIR__.'/..';
foreach ([
    'storage/logs',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/app/public',
    'storage/app/private',
    'bootstrap/cache',
] as $dir) {
    $path = "{$root}/{$dir}";
    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

// ZipArchive keeps whatever permissions the runner's zip recorded, which on a
// GitHub runner is not what this host's PHP process needs. If storage/ ends up
// unwritable Laravel dies before its logger exists — a blank 500 with nothing
// in laravel.log, which is exactly what this deploy produced on 2026-07-27.
$chmodded = 0;

foreach (['storage', 'bootstrap/cache'] as $dir) {
    $path = "{$root}/{$dir}";

    if (! is_dir($path)) {
        continue;
    }

    @chmod($path, 0775);
    $chmodded++;

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($items as $item) {
        @chmod($item->getPathname(), $item->isDir() ? 0775 : 0664);
        $chmodded++;
    }
}

// A config/route cache carried over from the previous release still points at
// the old release's values. Laravel never re-reads .env once config.php exists,
// so a stale one survives every later deploy until it is deleted.
$stale = 0;

foreach (glob("{$root}/bootstrap/cache/*.php") ?: [] as $cached) {
    if (in_array(basename($cached), ['packages.php', 'services.php'], true)) {
        continue;
    }

    unlink($cached);
    $stale++;
}

header('Content-Type: text/plain');
echo "Unpacked OK.\n";
echo "Permissions fixed on {$chmodded} paths.\n";
echo "Stale caches removed: {$stale}.\n";

function blank(?string $value): bool
{
    return $value === null || trim($value) === '';
}
