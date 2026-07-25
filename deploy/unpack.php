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

header('Content-Type: text/plain');
echo "Unpacked OK.\n";

function blank(?string $value): bool
{
    return $value === null || trim($value) === '';
}
