# OVGC — Claude Code Reference

## Project

**Okanogan Valley Golf Club website.** PHP 8.4 MVC, LAMP stack, no Docker.
Deployed via GitHub → RackNerd/CPanel GitHub Version Control → `/home/okanogan/public_html`.
**No local PHP or Composer.** No vendor directory. All dependencies are custom in-framework classes.
- Live: `https://www.okanoganvalleygolf.com/`
- Test: `https://test.okanoganvalleygolf.com/`
- Debug route: `/debug` (shows request info; keep gated behind `APP_DEBUG`)

## Key Files

| File | Purpose |
|------|---------|
| `public/index.php` | Front controller — all requests enter here |
| `config/routes.php` | All route definitions |
| `config/config.php` | Constants: APP_*, DB_*, timezone (copy of config.example.php with real values) |
| `core/Router.php` | Regex URL matching + middleware dispatch |
| `core/Database.php` | PDO singleton wrapper |
| `core/helpers.php` | Global helpers: `e()`, `csrf_field()`, `flash()`, `old()`, `tpl()` |
| `core/RRuleExpander.php` | RRULE→date expansion (no Composer; DAILY/WEEKLY/MONTHLY+UNTIL) |
| `app/Controllers/Controller.php` | Base controller with `view/redirect/json/flash` helpers |
| `app/Controllers/AuthController.php` | Login, logout; registration disabled (redirects to `/login`) |
| `app/Controllers/PasswordResetController.php` | Forgot/reset password flow; tokens stored hashed in `password_resets` table |
| `app/Controllers/EventController.php` | Public calendar, `/events/feed` JSON, detail pages |
| `app/Controllers/Admin/EventController.php` | Admin CRUD for events (subdirectory controller) |
| `app/Models/Model.php` | Base model with `find/all/where/create/update/delete` |
| `app/Models/Event.php` | Events model + exception/result helpers |
| `app/Services/EventService.php` | Occurrence expansion, cancellation, upcoming widget logic |
| `app/Services/WeatherService.php` | NWS API fetch, JSON cache, widget icon mapping |
| `app/Views/partials/weather-widget.php` | Homepage weather widget (current + 3-day forecast) |
| `public/cron-weather.php` | HTTP cron endpoint → calls `WeatherService::updateCache()` |
| `app/Controllers/Admin/DocsController.php` | Admin docs viewer — `GET /admin/docs` (search/landing) + `GET /admin/docs/[slug]` (show) |
| `app/Views/docs/*.html` | HTML doc source files (converted from `.md`; add new docs here as `.html`) |
| `public/assets/css/docs.css` | Styles for the docs viewer (typography, callouts, sidebar, search results) |
| `app/Controllers/CameraController.php` | Serves FTP camera image via `GET /camera/live` with corruption protection |
| `public/uploads/camera1.jpg` | FTP-dropped source image (continuously overwritten by camera) |
| `storage/cache/camera1_stable.jpg` | Last known-good frame promoted by `CameraController` |
| `public/uploads/.htaccess` | Disables Apache caching for uploaded files (camera feed) |
| `core/DebugBar.php` | Dev-only debug toolbar singleton — collects queries, views, route, exceptions |
| `app/Views/partials/debug-bar.php` | Debug toolbar HTML — rendered by `main.php` when `DebugBar::isVisible()` |
| `.github/copilot-instructions.md` | Full detailed architecture reference |

## Request Flow

```
Browser → public/index.php → Router::dispatch() → Middleware pipeline → Controller method → View → Response
```

## Autoloading

PSR-4 in `core/Autoloader.php`:
- `Core\` → `core/`
- `App\` → `app/`

Namespaces must match directory paths exactly.
Subdirectory controllers work: `['Admin\EventController', 'method']` → `App\Controllers\Admin\EventController` → `app/Controllers/Admin/EventController.php`.

## Routing

```php
// config/routes.php
'GET' => [
    '/rates' => ['RatesController', 'index'],
    '/users/(\d+)' => ['UserController', 'show', ['auth', 'role:admin']],
    '/admin/events/(\d+)/results/(\d{4}-\d{2}-\d{2})' => ['Admin\EventController', 'resultsForm', ['auth', 'role:admin']],
],
'POST' => [
    '/users' => ['UserController', 'store', ['auth', 'role:admin', 'csrf']],
],
```

- URL captures are strings — always cast: `(int) $id`
- Middleware list is the 3rd element; admin routes always need `['auth', 'role:admin']`
- Available middleware: `auth`, `guest`, `csrf`, `role:admin`, `rate-limit:key,max,secs`, `log-request`

## Documentation Rule

**After any significant change — new feature, new file pattern, architectural decision — update these three files before considering the task done:**
1. `CLAUDE.md` (this file) — keep Key Files table, Views rules, Current Status, and Common Pitfalls current
2. `.github/copilot-instructions.md` — update the relevant numbered section with implementation details
3. `README.md` — update Folder Structure or feature descriptions if the public-facing picture changed

Do this without being asked. If the change is minor (bug fix, copy tweak), skip README but still update CLAUDE.md/copilot-instructions if any pattern changed.

## Adding a Feature

1. **Route** → `config/routes.php`
2. **Controller** → `app/Controllers/NameController.php` (extends `Controller`, namespace `App\Controllers`)
3. **View** → `app/Views/category/name.php` (data vars come from `extract()`, escape with `e()`)
4. **Model** (if needed) → `app/Models/Name.php` (extends `Model`, set `$table`, `$fillable`)
5. **DB table** (if needed) → `database/initialize/NNN_create_tablename.sql` (3-digit prefix for order)

## Database

- PDO with prepared statements throughout — never raw query interpolation
- Seeds in `database/seed/NNN_seed_name.sql` (3-digit prefix)
- Model `$timestamps = true` auto-manages `created_at`/`updated_at`
- Tables: lowercase plural (`menu_items`), InnoDB, utf8mb4_unicode_ci
- Next migration number: **023**

## Views

- Layout: `app/Views/layouts/main.php` — views render into `$content`
- Partials: `$this->partial('partials/messages')` etc.
- Data passed: `$this->view('path/name', ['key' => $val])` — keys become `$key` via `extract()`
- Always escape untrusted output: `<?= e($var) ?>` (not `htmlspecialchars` directly)
- Form repopulation: `<?= old('field') ?>`
- No `$extraHead` slot in main layout — page-specific CDN assets go directly in the view file
- **No inline `<script>` blocks in any view** — all JS lives in `public/assets/js/`. Include with cache-bust: `<script src="/assets/js/file.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/file.js') ?>"></script>`
- If a PHP value is needed in JS, pass it via a `data-*` attribute on a DOM element; read it in the external JS file
- **Template variables in admin-editable text:** wrap `e()` output in `tpl()` to expand `{{name}}`, `{{email}}`, `{{phone}}`, `{{address1}}`, `{{address2}}`, `{{city}}` from theme settings. Pattern: `<?= tpl(e($text)) ?>` or `<?= tpl(nl2br(e($text))) ?>` for multi-line fields. Email/phone render as clickable links.

## Security Rules

- CSRF: include `<?= csrf_field() ?>` in every state-changing form; add `csrf` to route middleware
- Output: use `e()` helper for all user-supplied content
- Passwords: `password_hash($pass, PASSWORD_DEFAULT)` / `password_verify()`
- Rate limiting: apply `rate-limit:key,max,secs` to login/register/contact routes
- Never interpolate user input into SQL — use prepared statements via `Database::query()`
- Super-admin account protection: `user_id === 6` is now enforced in `UserController` to prevent other admins from editing/deleting this account (UI and backend checks)

## Configuration

All config in `config/config.php` as constants (`APP_NAME`, `APP_DEBUG`, `DB_HOST`, etc.).
Copy `config/config.example.php` → `config/config.php` and fill in real values. Never commit `config.php`.

## Common Pitfalls

1. **Git commands must be run separately and never use `cd ... && git ...`** — always use `git -C /c/Users/jeffb/Desktop/OVGC <subcommand>` so each call starts with `git` and matches the allow rules. Never chain with `&&`. All common git commands (`add`, `commit`, `status`, `diff`, `log`, `fetch`, `pull`, `branch`, `show`) are auto-allowed with the `-C` prefix. `git push` always requires user approval (deny list). Correct push sequence: `git -C ... add [files]` → `git -C ... commit -m "..."` → `git -C ... push` (user approves only the push).
2. URL params are strings — cast before arithmetic: `(int) $id`
2. Flash messages are single-use — `getFlash()` clears them on read
3. Layout wraps via `$content` — don't `require` views directly
4. Namespace must match path — `App\Services\FooService` → `app/Services/FooService.php`
5. `php -S` has limited URL rewriting — use a real Apache instance for local dev
6. No Composer/vendor — never add third-party packages; implement as `Core\` classes instead

## Current Status

Core, security, middleware, auth, admin UI, theming, content management, **Events system**, **Weather widget**, **Camera widget**, **Template Variables**, and **Admin Documentation system** are all complete and stable.

**Admin Documentation system:** Searchable HTML docs viewer at `GET /admin/docs` (admin-only). Sidebar lists all guides; search splits each doc by `<h2>` and returns section-level results with excerpts and anchor links. Doc files live in `app/Views/docs/*.html` (static HTML partials). Order and metadata controlled by the `DOCS` constant in `DocsController`. A prominent docs banner on the admin dashboard (`admin/index.php`) links directly to `/admin/docs`. CSS in `public/assets/css/docs.css`. Current docs: dashboard, homepage, theme, banners, menu-management, user-management, rates, membership, board-members, contact, board-minutes, **events** (creating events, categories, recurring logic, blackout dates), **events-managing** (editing, cancelling, restoring, deleting), **event-results** (posting/formatting/removing results).

**Weather widget:** NWS API (free, no key) → `storage/cache/weather-data.json` (30-min cron) → rendered server-side by `WeatherService` + `partials/weather-widget.php`. Cron endpoint: `GET /cron-weather.php?key=<WEATHER_KEY>`. Widget hidden gracefully when cache is absent.

**Camera widget:** FTP camera drops `public/uploads/camera1.jpg`. `GET /camera/live` → `CameraController::live()` validates the JPEG with `getimagesize()`, promotes good frames to `storage/cache/camera1_stable.jpg`, and falls back to the stable copy during mid-write corruption. `public/assets/js/camera-poll.js` polls `/camera/live?t=<timestamp>` every 17 s using a hidden `Image()` loader and only swaps the visible `<img id="camera1">` when `naturalWidth > 0`. Admin can switch to **Maintenance Mode** via `/admin/homepage` — shows a static uploaded image instead; live polling JS is suppressed when `#camera1` is absent from the DOM.

**Password reset:** `GET/POST /password/forgot` → `PasswordResetController`. Raw token emailed; only `password_hash()` of token stored in `password_resets` table (expires 1 hour). Expired tokens purged opportunistically on each new request. Self-registration is disabled — accounts are created manually by an admin.

**Debug toolbar:** Fixed bottom bar rendered when `APP_DEBUG=true` AND (admin is logged in OR request IP is in `DEBUG_ALLOWED_IPS`). Tabs: Queries (with timing), Route, Views, Session, Request. Data collected via hooks in `Database::query()`, `Router::callController()`, `Controller::view()`. CSS/JS in `public/assets/css/debug-bar.css` and `public/assets/js/debug-bar.js`. Configure allowed IPs in `config/config.php`: `define('DEBUG_ALLOWED_IPS', '127.0.0.1,::1,your.ip.here');`

See [ROADMAP.md](ROADMAP.md) for planned work, the security checklist, and changelog.
