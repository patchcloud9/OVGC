# OVGC Website — Roadmap

> For architecture, file references, and coding conventions see [CLAUDE.md](CLAUDE.md) and [.github/copilot-instructions.md](.github/copilot-instructions.md).

---

## Changelog

### 2026-03 Additions (continued)

- **Debug toolbar** — `core/DebugBar.php` singleton collects queries/timing, matched route, rendered views, exceptions. Fixed bottom bar rendered by `app/Views/partials/debug-bar.php` when `APP_DEBUG=true` AND admin is logged in OR request IP is in `DEBUG_ALLOWED_IPS`. Tabs: Queries, Route, Views, Session, Request. Zero overhead in production.

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
- [ ] text replacer, email, address, name, custom.  Such as {{email}}
- [ ] Convert inline `onclick=`/`onchange=`/`onsubmit=` event handler attributes to `addEventListener` calls in external JS, then remove `'unsafe-inline'` from CSP `script-src` (~35 handlers across: `board_members/`, `board_minutes/`, `banners/admin.php`, `membership/`, `rates/admin.php`, `gallery/admin.php`, `menu/admin.php`, `users/edit.php`, `admin/events/`, `partials/messages.php`, `errors/`)
- [ ] Note about updating dependancies
- [ ] Bulma .9x to 1.x migration at some point.
- [ ] Create full documentation on how to use this in markdown files.
- [ ] Write novice admin documentation covering:
  - [x] Admin Login (accessing the admin panel, credentials, logging out)
  - [x] Board Members (adding, editing, reordering, removing members)
  - [x] Board Minutes (uploading, publishing, managing meeting minutes)
  - [x] Events (creating single and recurring events, editing, cancelling occurrences, adding results) — **needs update: results section added after initial write**
  - [x] Dashboard (navigation overview, what each section is for)
  - [x] Users (creating and editing admin accounts, changing passwords)
  - [ ] Theme Settings (colors, fonts, branding, template variables like `{{email}}`)
  - [ ] Homepage (editing hero content and featured sections)
  - [ ] Banners (creating and managing page banners)
  - [x] Menu (adding and reordering nav items, visibility rules)
  - [ ] Gallery (uploading and organizing photos)
  - [ ] Rates (rate groups and individual fee entries)
  - [ ] Membership (tiers and included items)
  - [ ] Logs (what they show, when to check them)
  - [ ] Test Email (verifying SMTP is working)
  - [ ] cPanel basics (logging in, file manager, databases, email accounts, error logs, backups)
  - [ ] Cloudflare basics (domain registration, DNS records, what to change if hosting moves)
  - [ ] RackNerd basics (logging into client area, managing the VPS/hosting plan, support tickets, renewal)

---

**Deploy checklist:** 
Set `APP_DEBUG=false` and `APP_ENV=production`; ensure `.env` is not committed; rotate DB credentials; verify upload directory permissions.
