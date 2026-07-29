<?php

use Illuminate\Support\Facades\File;

/**
 * The icon font is subsetted to the glyphs this app uses, so a class the
 * stylesheet does not define renders nothing at all — no error, no missing-glyph
 * box, just a gap where an icon should be. Nothing at runtime notices.
 *
 * This does. Add an icon to a template without rebuilding the subset and the
 * suite tells you, with the name to add.
 */
function referencedIcons(): array
{
    $names = [];

    foreach (['resources', 'app', 'routes', 'config'] as $dir) {
        foreach (File::allFiles(base_path($dir)) as $file) {
            preg_match_all('/\bbi-([a-z0-9-]+)/', $file->getContents(), $matches);
            $names = array_merge($names, $matches[1]);
        }
    }

    return array_values(array_unique($names));
}

function definedIcons(): array
{
    preg_match_all(
        '/^\.bi-([a-z0-9-]+)::before/m',
        File::get(public_path('css/app.css')),
        $matches
    );

    return $matches[1];
}

it('defines every icon the app references', function () {
    $missing = array_diff(referencedIcons(), definedIcons());

    expect($missing)->toBeEmpty(
        'Icons used but not in the subset: '.implode(', ', $missing).
        '. Rebuild the font — see FONTS.md.'
    );
});

it('carries no glyph rule the app never uses', function () {
    $unused = array_diff(definedIcons(), referencedIcons());

    expect($unused)->toBeEmpty(
        'Icon rules with no reference left in the app: '.implode(', ', $unused).
        '. Drop them and rebuild the subset.'
    );
});

it('ships the subsetted font, not the full one', function () {
    $fonts = File::glob(public_path('fonts/bootstrap-icons-subset.*.woff2'));

    expect($fonts)->toHaveCount(1);
    expect(File::size($fonts[0]))->toBeLessThan(20 * 1024);
});

it('points the stylesheet at the font file that is actually there', function () {
    $css = File::get(public_path('css/tokens.css'));

    foreach (File::glob(public_path('fonts/*.woff2')) as $font) {
        expect($css)->toContain(basename($font));
    }

    preg_match_all("/url\('\.\.\/fonts\/([^']+)'\)/", $css, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach ($matches[1] as $referenced) {
        expect(File::exists(public_path('fonts/'.$referenced)))->toBeTrue(
            "tokens.css points at fonts/{$referenced}, which does not exist."
        );
    }
});
