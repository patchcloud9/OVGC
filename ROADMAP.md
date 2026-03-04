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

### Phase 1 — Testing & Dev Tools

- [ ] Move inline `<script>` blocks out of view files into external JS assets to allow removing `'unsafe-inline'` from CSP `script-src` (affects: `main.php`, `home/index.php`, `gallery/`, `admin/`, `banners/`, `users/`, `menu/`, `logs/`, `rates/` views)
- [ ] text replacer, email, address, name, custom.  Such as {{email}}
- [ ] Debug toolbar (dev-only)
- [ ] Maybe add top menu and bottom menu to be seperate.
- [ ] Note about updating dependancies
- [ ] Bulma .9x to 1.x migration at some point.
- [ ] Create full documentation on how to use this in markdown files.

---

**Deploy checklist:** 
Set `APP_DEBUG=false` and `APP_ENV=production`; ensure `.env` is not committed; rotate DB credentials; verify upload directory permissions.
