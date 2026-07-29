# Fonts and icons

Both are served from this origin. Nothing on any page reaches out to
`fonts.googleapis.com`, `fonts.gstatic.com` or `cdn.jsdelivr.net`, so no page
waits on a third-party DNS lookup and TLS handshake before it can paint.

| File | What it is | Size |
|---|---|---|
| `public/fonts/inter-latin-var.*.woff2` | Inter, variable, latin subset. One file covers weight 400–700. | 48 KB |
| `public/fonts/bootstrap-icons-subset.*.woff2` | bootstrap-icons cut down to the glyphs this app uses. | 4 KB |

The unsubsetted icon font is 130 KB and its stylesheet another 98 KB, for 42
icons. That is what the subset replaces.

`@font-face` lives at the top of `public/css/tokens.css`. The `.bi-*` rules live
in `public/css/app.css` under **Icons**.

## The hash in the filename is the cache key

These URLs sit inside CSS, where the `@assetUrl` fingerprint cannot reach them,
and the host serves static files with a week-long `max-age`. **Never overwrite a
font file in place** — a new font means a new filename, or browsers keep the old
one for a week. The rebuild below writes hashed names for exactly this reason.

## Adding an icon

The font only contains the glyphs listed in `app.css`. A `bi-` class that is not
listed there renders nothing — no error, just a blank space. `IconCoverageTest`
fails when a template references an icon the stylesheet does not define, so this
is caught before it ships.

There is no build step in this project and one icon is not worth inventing one,
so the rebuild is a handful of commands. Start here:

```bash
python3 -m venv /tmp/fontenv && /tmp/fontenv/bin/pip install 'fonttools[woff]' brotli
curl -sS -o /tmp/bi.css   https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css
curl -sS -o /tmp/bi.woff2 https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2
```

Then:

1. Collect every `bi-[a-z0-9-]+` in `resources/`, `app/`, `routes/` and `config/`.
   Icon names appear in Blade *and* in PHP arrays (the sidebar menu) *and* in the
   inline JS that swaps the collapse chevron — grep all four directories, not
   just the templates.
2. Read `content: "\fXXX"` for each name out of `/tmp/bi.css`.
3. Subset:
   ```bash
   /tmp/fontenv/bin/pyftsubset /tmp/bi.woff2 --unicodes=U+f124,U+f127,... \
     --flavor=woff2 --output-file=/tmp/subset.woff2 \
     --no-hinting --desubroutinize --layout-features='' --name-IDs=''
   ```
4. Name the output `bootstrap-icons-subset.<first 8 of its sha256>.woff2`, put it
   in `public/fonts/`, delete the previous one.
5. Update the `src:` URL in `tokens.css` and the `.bi-*` rules in `app.css`.
6. Run the tests. `IconCoverageTest` tells you if step 1 missed a name.

## Replacing Inter

Ask Google Fonts for the variable range rather than four static weights — it is
one file instead of four:

```
https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap
```

Fetch it with a browser User-Agent or you get the woff (not woff2) fallback.
Take the `latin` block: that subset covers Indonesian and English. Keep the
`unicode-range` from that block in `tokens.css` — without it the browser
downloads the font for pages that would render fine without it.
