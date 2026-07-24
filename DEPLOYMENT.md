# Deployment — cPanel (FTP, no SSH)

## 1. cPanel setup (one-time, do this first)

1. **Database** — cPanel → MySQL Databases: create a database + user, grant ALL PRIVILEGES, attach user to database. Note the DB name, username, password, host (usually `localhost`).
2. **Document root** — cPanel → Domains → Manage (for your domain): set the document root to a `public` subfolder, e.g. `public_html/public`, **not** `public_html` itself. The rest of the Laravel app (app/, config/, storage/, vendor/…) must sit one level above the web root so it isn't directly browsable. If your plan doesn't allow a custom document root, ask host support — this step is what keeps `.env`, `app/`, etc. from being served as plain files.
3. **PHP version** — cPanel → MultiPHP Manager: set PHP 8.3+ for the domain.
4. **Upload `.env` manually, once** — this file is gitignored on purpose and is never touched by the deploy workflow. Use cPanel File Manager to create it in the app root (sibling of `public_html`, wherever the workflow uploads to — see `FTP_SERVER_DIR` below) with:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://yourdomain.com`
   - `APP_KEY=` — generate locally with `php artisan key:generate --show`, paste the output
   - `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from step 1
   - `DEPLOY_TOKEN=` — a long random string (e.g. `php -r "echo bin2hex(random_bytes(32));"`). This authorizes the no-SSH migration endpoint. Keep it secret.
5. Import the base schema once via phpMyAdmin **or** just let the first deploy's migration call create all tables (recommended — see below).

## 2. GitHub repo secrets (Settings → Secrets and variables → Actions)

| Secret | Value |
|---|---|
| `FTP_SERVER` | FTP host from cPanel (e.g. `ftp.yourdomain.com`) |
| `FTP_USERNAME` | cPanel FTP username |
| `FTP_PASSWORD` | cPanel FTP password |
| `FTP_SERVER_DIR` | Remote path the app deploys to, e.g. `/home/youruser/asset_kenco/` — must match where `.env` was placed in step 1.4, and `public_html/public` document root must point inside this same app folder's `public/` |
| `APP_URL` | `https://yourdomain.com` (used to call the migrate endpoint after deploy) |
| `DEPLOY_TOKEN` | Same value as `.env`'s `DEPLOY_TOKEN` on the server |

Never put any of these values directly in code or commit them — GitHub Secrets only.

## 3. How deploy works

Push to `main` → `.github/workflows/deploy.yml` runs:
1. `composer install --no-dev` + `npm run build` on the GitHub runner (host has no SSH to do this itself)
2. Uploads the built app over FTP to `FTP_SERVER_DIR` (excludes `.env`, `.git`, `node_modules`, `tests`, `design-preview`)
3. Calls `POST https://yourdomain.com/deploy/migrate?token=...` — runs `migrate --force`, `storage:link`, and warms config/route/view caches

`.env` on the server is never overwritten by deploy — edit it directly via cPanel File Manager when needed (e.g. rotating `DEPLOY_TOKEN`).

## 4. First deploy checklist

- [ ] cPanel DB created, credentials in server `.env`
- [ ] Document root points at `.../public`
- [ ] Server `.env` uploaded manually with real `APP_KEY` and `DEPLOY_TOKEN`
- [ ] All 6 GitHub Secrets set
- [ ] Push to `main`, watch the Actions tab
- [ ] Visit the domain — should hit `/login`
- [ ] Check `storage/logs/laravel.log` via File Manager if something 500s

## 5. Notes

- No SSH means no `php artisan tinker` or ad-hoc commands on the server. Add any one-off command as a temporary route guarded by `DEPLOY_TOKEN`, run it once, then remove it.
- Asset photo/QR uploads go to `storage/app/public`, served via the `storage:link` symlink the migrate endpoint creates. If uploads 404, re-trigger `/deploy/migrate` or check that PHP's `symlink()` isn't disabled by the host.
