# OVGC — Claude Code Reference

## Project

**Okanogan Valley Golf Club website.** PHP 8.4 MVC, LAMP stack, no Docker.
Deployed via GitHub → RackNerd/CPanel GitHub Version Control → `/home/okanogan/public_html`.
- Live: `https://www.okanoganvalleygolf.com/`
- Test: `https://test.okanoganvalleygolf.com/`
- Debug route: `/debug` (shows request info; keep gated behind `APP_DEBUG`)

## Key Files

| File | Purpose |
|------|---------|
| `public/index.php` | Front controller — all requests enter here |
| `config/routes.php` | All route definitions |
| `config/config.php` | Constants: APP_*, DB_*, timezone; loads `.env` |
| `core/Router.php` | Regex URL matching + middleware dispatch |
| `core/Database.php` | PDO singleton wrapper |
| `core/helpers.php` | Global helpers: `e()`, `csrf_field()`, `flash()`, `old()` |
| `app/Controllers/Controller.php` | Base controller with `view/redirect/json/flash` helpers |
| `app/Models/Model.php` | Base model with `find/all/where/create/update/delete` |
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

## Routing

```php
// config/routes.php
'GET' => [
    '/rates' => ['RatesController', 'index'],
    '/users/(\d+)' => ['UserController', 'show', ['auth', 'role:admin']],
],
'POST' => [
    '/users' => ['UserController', 'store', ['auth', 'role:admin', 'csrf']],
],
```

- URL captures are strings — always cast: `(int) $id`
- Middleware list is the 3rd element; admin routes always need `['auth', 'role:admin']`
- Available middleware: `auth`, `guest`, `csrf`, `role:admin`, `rate-limit:key,max,secs`, `log-request`

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

## Views

- Layout: `app/Views/layouts/main.php` — views render into `$content`
- Partials: `$this->partial('partials/messages')` etc.
- Data passed: `$this->view('path/name', ['key' => $val])` — keys become `$key` via `extract()`
- Always escape untrusted output: `<?= e($var) ?>` (not `htmlspecialchars` directly)
- Form repopulation: `<?= old('field') ?>`

## Security Rules

- CSRF: include `<?= csrf_field() ?>` in every state-changing form; add `csrf` to route middleware
- Output: use `e()` helper for all user-supplied content
- Passwords: `password_hash($pass, PASSWORD_DEFAULT)` / `password_verify()`
- Rate limiting: apply `rate-limit:key,max,secs` to login/register/contact routes
- Never interpolate user input into SQL — use prepared statements via `Database::query()`

## Configuration

All config in `config/config.php` as constants (`APP_NAME`, `APP_DEBUG`, `DB_HOST`, etc.).
A `.env` file is supported for development — never commit it. See `.env.example`.

## Common Pitfalls

1. URL params are strings — cast before arithmetic: `(int) $id`
2. Flash messages are single-use — `getFlash()` clears them on read
3. Layout wraps via `$content` — don't `require` views directly
4. Namespace must match path — `App\Services\FooService` → `app/Services/FooService.php`
5. `php -S` has limited URL rewriting — use a real Apache instance for local dev

## Current Status

Core, security, middleware, auth, admin UI, theming, and content management are all complete and stable.
Outstanding: Phase 4 (user light/dark mode toggle), PHPUnit test suite, CSP headers, full production hardening.
