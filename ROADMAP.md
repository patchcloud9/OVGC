# OVGC Website — Roadmap

> For architecture, file references, and coding conventions see [CLAUDE.md](CLAUDE.md) and [.github/copilot-instructions.md](.github/copilot-instructions.md).

---

## Changelog

### 2026-03 Additions

- **Events system** — `events`, `event_exceptions`, `event_results` tables (migrations 016–018). Public calendar at `/events` (FullCalendar v6), event detail pages, admin CRUD at `/admin/events`, homepage upcoming-events widget. `Core\RRuleExpander` handles recurrence without Composer.
- **Weather widget** — NWS API cached widget (`api.weather.gov`; no key required), server-side render, cron refresh every 30 min (`GET /cron-weather.php?key=<WEATHER_KEY>`). Current conditions + 3-day forecast using Weather Icons font. Zero external HTTP per page load.
- **Camera widget** — FTP camera drops `public/uploads/camera1.jpg`; `CameraController` serves it via `GET /camera/live` with corruption-safe JPEG validation and stable-copy fallback. JS on homepage auto-refreshes every 17 s with a `naturalWidth` guard.

### Project Notes

- **Migrations:** This repo uses `database/initialize/` create scripts and `database/seed/` files for fresh installs. No incremental migration runner; add `database/migrations/` if you need upgrade scripts.
- **Placeholders:** The `{email}` token in hero/card/bottom text is replaced at render time with `ThemeSetting::get('contact_email')`. Use it in admin text fields to insert the site contact email.
- **Page subtitles:** `page_subtitle` is supported on About and Purchase pages (stored in their respective tables, editable in admin).
- **Purchase page:** Public route `/purchase`; editable at Admin → Pages → Purchase.
- **Footer menu:** Quick Links driven by `menu_items` DB table (same visibility rules as the main navbar).
- **Profile link:** Removed from nav — no active profile page in this project.
- **No Composer/vendor:** Never add third-party packages. Implement all dependencies as `Core\` classes.

---

## Planned Work

### Phase 1 — Production Hardening & Cleanup
- [x] Content Security Policy (CSP) headers
- [x] HSTS and X-Frame-Options via `.htaccess` (Apache, no reverse proxy)
- [x] Restrictive file permissions (755 dirs, 644 files)
- [x] Disable directory listing in web server config
- [x] Asset versioning and static caching strategy
- [x] Monitoring and alerting — UptimeRobot free tier (external setup only, no code); Sentry skipped as site is lightly used and errors surface via user reports
- [x] Email functionality

### Phase 2 — Testing & Dev Tools
- [ ] PHPUnit test suite (unit + feature) + GitHub Actions CI
- [ ] Test database (SQLite or separate MySQL instance) for CI runs
- [ ] Debug toolbar (dev-only)
- [ ] Move inline `<script>` blocks out of view files into external JS assets to allow removing `'unsafe-inline'` from CSP `script-src` (affects: `main.php`, `home/index.php`, `gallery/`, `admin/`, `banners/`, `users/`, `menu/`, `logs/`, `rates/` views)

---

## Optional Enhancements

- **API & Integrations** — RESTful controllers, token-based auth, versioning (`/api/v1/`), CORS policy.
- **Advanced Storage & Media** — SVG sanitizer, image optimization, optional S3/remote storage.
- **Enhanced Developer Tools** — CLI generators, code scaffolding, richer debug tooling.
- **Advanced Security & Policies** — Granular RBAC, audit logs, per-user rate limits.
- **Performance** — Redis caching, query optimization, advanced asset pipelines, CDN, OPcache.

---

## Security Checklist

- [x] `display_errors = 0` in production PHP config
- [x] HTTPS enforced (CPanel SSL via Apache)
- [x] CSRF protection on all state-changing routes (tokens + validation)
- [x] All user input validated and sanitized (`e()`, prepared statements, `Validator`)
- [x] Prepared statements for all database queries (PDO)
- [x] Secure session cookie flags: `httponly`, `secure`, `samesite` (configured in `index.php`)
- [x] Rate limiting on authentication endpoints (`RateLimiter`, applied to contact and user creation)
- [x] Proper file upload restrictions — type, size, dedicated directory (`ThemeController`, 2 MB limit)
- [x] SVG uploads excluded by default (SVG can contain executable content)
- [x] Proper error logging (dual database + file logging with graceful degradation)
- [x] Environment variables for sensitive config (`.env` loader; prefer secret store in CI/CD)
- [x] Content Security Policy headers (`default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'`)
- [ ] Remove or protect debug/test routes in production
- [x] Restrictive file permissions (755 dirs, 644 files)
- [x] Disable directory listing in web server config
- [ ] Keep framework dependencies updated

> **Deploy checklist:** Set `APP_DEBUG=false` and `APP_ENV=production`; ensure `.env` is not committed; rotate DB credentials; verify upload directory permissions.
