<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Fingerprints public assets with their last-modified time.
 *
 * Static files are served with a long max-age, so a plain /css/app.css URL keeps
 * resolving to whatever the CDN cached at deploy time minus one. Appending the
 * mtime changes the URL whenever the file changes, which is what actually busts
 * the cache.
 */
class AssetVersion
{
    /** @var array<string, string> Fingerprints resolved so far this request. */
    private static array $fingerprints = [];

    public static function url(string $path): string
    {
        return asset($path).'?v='.self::fingerprint($path);
    }

    /** Forget cached fingerprints. Only useful between tests. */
    public static function flush(): void
    {
        self::$fingerprints = [];
    }

    private static function fingerprint(string $path): string
    {
        return self::$fingerprints[$path] ??= self::readModifiedTime($path);
    }

    private static function readModifiedTime(string $path): string
    {
        $file = public_path(ltrim($path, '/'));

        return File::exists($file) ? (string) File::lastModified($file) : '0';
    }
}
