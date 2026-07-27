# Deployment — Hostinger (FTP, no SSH)

Host is Hostinger/hPanel, not cPanel. There is no SSH, so nothing can be built
or migrated on the server itself — GitHub Actions does both remotely.

## Layout

Hostinger gives no way to move the subdomain's document root, so the app root
**is** the web root:

```
/                       FTP root, not web-served, holds Hostinger's DO_NOT_UPLOAD_HERE marker
└── public_html/        document root AND Laravel app root
    ├── .env            never in git, never overwritten by deploy
    ├── .htaccess       the only thing keeping .env off the public internet
    ├── app/ vendor/ storage/ ...
    └── public/         Laravel's front controller
```

This is weaker than keeping the app above the web root. `.htaccess` does two
things: denies `.env`, `composer.*`, `artisan`, `*.log` and `*.zip` by filename,
and rewrites every request into `public/` so nothing else has a reachable path.
A `RedirectMatch` covers `app/`, `config/`, `routes/`, `vendor/` and friends for
the case where `mod_rewrite` is unavailable and the rewrite silently stops.

Because that protection is configuration rather than filesystem layout, the
deploy workflow re-proves it on every run — see step 5 below. In July 2026 this
exact layout leaked `.env` (`APP_KEY`, DB credentials, `DEPLOY_TOKEN`) and a full
source zip for two days before anyone noticed.

If Hostinger ever allows changing the document root, point it at
`public_html/public` and the `.htaccess` becomes redundant. Nothing else changes.

## 1. One-time server setup

1. **Database** — hPanel → Databases: create database + user, grant all
   privileges. Note name, user, password, host (`localhost`).
2. **PHP version** — hPanel → PHP Configuration: 8.3 or newer.
3. **Upload `.env` manually, once** — gitignored on purpose, and the deploy
   never touches it. Put it at `public_html/.env`:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://asset.kencomanufactur.co.id`
   - `APP_KEY=` — generate locally with `php artisan key:generate --show`
   - `DB_CONNECTION=mysql` plus `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `DEPLOY_TOKEN=` — long random string, e.g. `php -r "echo bin2hex(random_bytes(32));"`

`SESSION_DRIVER=file` and `CACHE_STORE=file` are the safer starting point: the
login page then renders even if the DB credentials are wrong, which makes a bad
deploy far easier to diagnose. Switch to `database` once things are stable.

## 2. GitHub repo secrets

Settings → Secrets and variables → Actions:

| Secret | Value |
|---|---|
| `FTP_SERVER` | FTP host from hPanel |
| `FTP_USERNAME` | FTP username |
| `FTP_PASSWORD` | FTP password |
| `FTP_SERVER_DIR` | `public_html/` — **must end with `/`** |
| `APP_URL` | `https://asset.kencomanufactur.co.id` — no trailing slash, no `/public` |
| `DEPLOY_TOKEN` | identical to `DEPLOY_TOKEN` in the server `.env` |

`FTP_SERVER_DIR` is concatenated directly (`${FTP_SERVER_DIR}public/`), so a
missing slash silently uploads to a folder named `public_htmlpublic/`.

## 3. How deploy works

Push to `main` → `.github/workflows/deploy.yml`. It first calls the reusable
`tests.yml` workflow (Pint + Pest + asset build); **if that is red the deploy
job never starts**. Then:

1. `composer install --no-dev` + `npm run build` on the runner
2. Zips the build into one `deploy.zip` — uploading thousands of `vendor/` files
   over FTP is what made the first attempt time out and get disconnected after
   an hour
3. Uploads two files: `deploy/unpack.php` → `public_html/public/unpack.php`, and
   `deploy.zip` → `public_html/`
4. `POST /unpack.php?token=…` — extracts server-side, deletes the zip, chmods
   `storage/` and `bootstrap/cache` to 775, and clears any stale config/route
   cache carried over from the previous release
5. Probes `/.env`, `/config/app.php`, `/vendor/autoload.php` and others — **any
   200 fails the run**
6. `POST /deploy/migrate?token=…` — `migrate --force`, `storage:link`, and warms
   config/route/view caches

`unpack.php` reads `DEPLOY_TOKEN` straight out of the server's `.env` with a
plain-text regex, since no Laravel bootstrap exists before the app is unpacked.

Deploys are serialized by a `deploy-production` concurrency group — the unpack
step extracts in place, and a second upload landing mid-extract corrupts the app.

The build writes `.deploy-build` (commit SHA + run ID) into the zip so two
deploys of the same commit still differ. `FTP-Deploy-Action` skips uploads whose
hash matches its server-side state file, which would otherwise leave `unpack.php`
with no zip to extract.

## 4. CI

`.github/workflows/tests.yml` runs on pull requests, on pushes to non-`main`
branches, and as a gate inside deploy:

1. `./vendor/bin/pint --test`
2. `php artisan test` — Pest against in-memory SQLite
3. `npm ci && npm run build`

Locally: `./vendor/bin/pint && php artisan test`

Pest runs core-only; `pest-plugin-laravel` caps at Laravel `^12.25` and this is
Laravel 13. `Tests\TestCase` covers the helpers, and `tests/Pest.php` binds it
plus `RefreshDatabase` to `tests/Feature`.

## 5. First deploy checklist

- [ ] Database created, credentials in `public_html/.env`
- [ ] `.env` uploaded with real `APP_KEY` and `DEPLOY_TOKEN`
- [ ] All 6 GitHub Secrets set, `FTP_SERVER_DIR` ending in `/`
- [ ] Push to `main`, watch the Actions tab
- [ ] Visit the domain — should land on `/login`
- [ ] Confirm `https://asset.kencomanufactur.co.id/.env` returns 403

## 6. Notes

- No SSH means no `php artisan tinker`. Add a one-off command as a temporary
  route guarded by `DEPLOY_TOKEN`, run it once, then remove it.
- Uploads go to `storage/app/public`, served through the `storage:link` symlink
  the migrate endpoint creates. The `.htaccess` deliberately does not block
  `/storage/` wholesale, only `storage/framework`, `storage/logs` and
  `storage/app/private`, so uploaded photos and QR codes keep working.
- A blank 500 with no `storage/logs/laravel.log` almost always means `storage/`
  is not writable. `unpack.php` chmods it on every deploy, so this should not
  recur; if it does, check the host did not reset ownership.
