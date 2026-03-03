# Okanogan Valley Golf Club Website - AI Coding Instructions

## Architecture Overview

This repository now powers the **Okanogan Valley Golf Club website**. It evolved from a PHP MVC teaching framework but has been fully adapted for the club's needs. The codebase targets **PHP 8.4** and runs on a standard LAMP/LEMP stack. The architecture remains a simple front‑controller/MVC pattern, which makes the application easy to extend and maintain.

### Core Components

- **Front Controller**: ALL requests hit `public/index.php`, which dispatches to the router
- **Router**: `core/Router.php` uses regex patterns to match URLs to controller methods
- **Controllers**: Extend `App\Controllers\Controller` base class with view/redirect/JSON helpers
- **Views**: PHP templates using layouts (wrapper templates) and partials (reusable snippets)
- **Services**: Business logic layer (see `LogService.php` for file-based data storage pattern)

### Request Flow

```
Browser → public/index.php → Router::dispatch() → Controller → View → Response
```

## Critical Patterns

> **Environment & coding style**
>
> - PHP 8.4 is required. There is **no local PHP** — the site runs on shared RackNerd/CPanel hosting only.
> - Follow **PSR‑12** for PHP formatting.
> - **No Composer, no vendor directory.** All dependencies must be implemented as `Core\` classes. Never add packages.
> - Deployment: push to GitHub → CPanel pulls automatically. No CI/CD pipeline.
> - Unit/feature tests (PHPUnit) are planned but not yet set up.

## Critical Patterns

### 1. Routing System

Routes are defined in `config/routes.php` using regex patterns:

```php
'GET' => [
    '/users/(\d+)' => ['UserController', 'show'],  // Captures numeric ID
    '/posts/([a-z-]+)/comments/(\d+)' => ['PostController', 'showComment'],  // Multiple params
]
```

**Key behaviors:**
- URL parameters are captured via regex groups and passed as string arguments to controller methods
- The router converts `UserController` → `App\Controllers\UserController`
- Subdirectory controllers work: `'Admin\EventController'` → `App\Controllers\Admin\EventController` → `app/Controllers/Admin/EventController.php` with `namespace App\Controllers\Admin`
- Always cast URL params: `$userId = (int) $id;` before using as array keys
- Trailing slashes are normalized (both `/about` and `/about/` work)

### 2. Controller Conventions

Controllers extend `Controller` base class which provides:

- `view($path, $data, $layout)` - Render views with optional layout wrapper
- `partial($path, $data)` - Render without layout
- `json($data, $code)` - Return JSON responses
- `redirect($url)` - HTTP redirects
- `input($key)` / `query($key)` - Access POST/GET data
- `flash($type, $message)` - Session-based flash messages

**View rendering:** 
- Views use `extract()` to convert data array to variables: `['user' => $user]` becomes `$user`
- Layout wraps view content in `$content` variable (see `app/Views/layouts/main.php`)
- Always escape output: `<?= htmlspecialchars($title) ?>`

### 3. Autoloading

PSR-4 style autoloader in `core/Autoloader.php` maps:
- `Core\` → `/core/`
- `App\` → `/app/`

When adding new classes, follow namespace structure exactly. File `app/Services/LogService.php` must use `namespace App\Services;`

### 4. Database & Models

**Database Layer:**
- PDO wrapper at `core/Database.php` - singleton pattern with prepared statements
- Model base class at `app/Models/Model.php` with CRUD: `find()`, `all()`, `create()`, `update()`, `delete()`
- Models use `$table`, `$fillable`, `$timestamps` properties
- Example models: `User.php` and `Log.php` (in `app/Models`)

**Database Setup:**
- SQL files in `database/initialize/` create tables (prefixed with a three-digit number for ordering, e.g., `001_create_users_table.sql`)
- SQL files in `database/seed/` populate test data (prefixed with a three-digit number for ordering, e.g., `001_seed_users.sql`)
- Run initialization (POSIX): `cat database/initialize/*.sql | mysql -u user -p dbname`
- Run initialization (PowerShell): `Get-ChildItem -Path database\\initialize\\*.sql | Sort-Object Name | Get-Content | mysql -u user -p dbname`
- Run seeds (POSIX): `cat database/seed/*.sql | mysql -u user -p dbname`
- Run seeds (PowerShell): `Get-ChildItem -Path database\\seed\\*.sql | Sort-Object Name | Get-Content | mysql -u user -p dbname`
- Note: SQL migration files were intentionally removed in this repo to favor explicit `create_*` + `seed_*` files for new installs. If you require migrations for upgrades, migrate them into `database/migrations/` and add a runner script.
- See `database/README.md` for detailed instructions.

**Adding New Tables:**
1. Create a new initialization file using a three-digit order prefix, e.g. `database/initialize/003_create_tablename.sql`:
   ```sql
   CREATE TABLE IF NOT EXISTS posts (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       title VARCHAR(255) NOT NULL,
       content TEXT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
       INDEX idx_user_id (user_id)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

2. Create model `app/Models/Post.php`:
   ```php
   <?php
   namespace App\Models;
   
   class Post extends Model
   {
       protected string $table = 'posts';
       protected array $fillable = ['user_id', 'title', 'content'];
       protected bool $timestamps = true;
   }
   ```

3. Optionally create `database/seed/##_seed_posts.sql`:
   ```sql
   INSERT INTO posts (user_id, title, content) VALUES
   (1, 'First Post', 'Content here'),
   (2, 'Another Post', 'More content');
   ```

### 5. Service Layer Pattern

See `app/Services/LogService.php` and `app/Services/EventService.php` for the project's service pattern:
- Services handle business logic and data operations
- Controllers instantiate services and coordinate between them
- Services use Models for database access
- Legacy: Some services use file-based JSON storage in `storage/`

### 6. Events System

Three DB tables: `events`, `event_exceptions`, `event_results` (migrations 016–018).

**Key classes:**
- `Core\RRuleExpander` — expands RRULE strings to `Y-m-d` occurrence dates. No Composer. Supports `FREQ=DAILY/WEEKLY/MONTHLY`, `BYDAY`, `UNTIL`.
- `App\Models\Event` — CRUD + `getForRange()`, `getExceptions()`, `addException()`, `saveResult()`, etc.
- `App\Services\EventService` — `getOccurrencesForRange()` (feed), `getUpcomingEvents()` (widget), `cancelFromDate()`, `cancelOccurrence()`, `addSkipDate()`, `getOccurrenceDetail()`.
- `App\Controllers\EventController` — public routes: `/events`, `/events/feed`, `/events/{id}`, `/events/{id}/{date}`
- `App\Controllers\Admin\EventController` — admin routes under `/admin/events/...`

**Occurrence status logic (in priority order):**
1. `cancelled_from` set AND `occurrenceDate >= cancelled_from` → `cancelled`
2. `event_exceptions` row with `type='skip'` → omit entirely (not returned in feed)
3. `event_exceptions` row with `type='cancelled'` → `cancelled` (shown with badge)
4. Default → `active`

**RRULE storage:** store only the rule portion, no `RRULE:` prefix. E.g. `FREQ=WEEKLY;BYDAY=TU`.

**Detail page URL pattern:**
- One-time event: `/events/{id}`
- Recurring occurrence: `/events/{id}/YYYY-MM-DD`

**FullCalendar feed:** `GET /events/feed?start=YYYY-MM-DD&end=YYYY-MM-DD` returns a flat JSON array of occurrence objects. FullCalendar v6 loaded from CDN in `app/Views/events/index.php` (no `$extraHead` slot in main layout).

**Homepage widget:** `HomeController::index()` calls `EventService::getUpcomingEvents(5)` wrapped in a try/catch (fails silently if table missing). Partial at `app/Views/partials/upcoming-events.php`.

### 7. Weather Widget

Serves current conditions + 3-day forecast on the homepage with zero external HTTP per page load.

**Key classes / files:**
- `App\Services\WeatherService` — fetches from free NWS API (`api.weather.gov`; no key required), caches result to `storage/cache/weather-data.json` (JSON). Cache TTL = 30 min; widget accepts data up to 60 min old before hiding itself.
- `app/Views/partials/weather-widget.php` — renders the widget using the Weather Icons CDN font (`wi-*` classes). Receives `$weatherData` from the controller.
- `public/cron-weather.php` — HTTP endpoint for the server cron job. Protected by a key (`?key=<WEATHER_KEY>`; env var `WEATHER_KEY` or fallback). Call every 30 minutes.
- `scripts/fetch_weather.php` — CLI equivalent for manual or SSH-based refresh.

**Data flow:**
1. Cron hits `GET /cron-weather.php?key=…` every 30 min → `WeatherService::updateCache()`
2. NWS API calls: `/points/48.4104,-119.5296` → forecast URL + nearest station → observations
3. Processed data saved to `storage/cache/weather-data.json`
4. `HomeController::index()` calls `WeatherService::getWidgetData()` (reads local JSON, no HTTP)
5. `$weatherData` passed to view → `partials/weather-widget.php` renders the widget

**NWS condition → icon mapping:** `WeatherService::iconClass(string $condition, bool $isDaytime)` returns a `wi-*` class. Condition codes are extracted from the NWS icon URL path (e.g. `…/icons/land/day/ovc?size=medium` → `"ovc"`).

**Cron errors** logged to `storage/logs/cron-error.log`.

**Key:** default `477kHwPEw6ZBSUbhEB`; override via `WEATHER_KEY` environment variable.

### 8. Camera Widget

Live traffic camera on the homepage with corruption-safe serving.

**Key files:**
- `app/Controllers/CameraController.php` — `GET /camera/live` controller. No auth, no middleware.
- `public/uploads/camera1.jpg` — source image overwritten by the FTP camera continuously.
- `storage/cache/camera1_stable.jpg` — last known-good frame promoted by the controller.
- `public/uploads/.htaccess` — sets `Cache-Control: no-store` on all files in `uploads/` so Apache never serves a stale frame from its own cache.

**Serving logic (in priority order):**
1. `clearstatcache()` to flush PHP's internal stat cache.
2. `getimagesize($source)` — if it returns a non-false result the file is fully written; promote to stable and serve.
3. If `getimagesize()` fails (mid-FTP write) and stable copy is ≤ `MAX_STABLE_AGE` (60 s) old → serve stable.
4. Safety valve: if stable is missing or too old, serve source anyway — client-side JS discards undecoded frames.

**Client-side refresh (in `app/Views/home/index.php`):**
- A hidden `Image()` loader fetches `/camera/live?t=<Date.now()>` every 17 seconds (cache-busting via query string).
- Only swaps the visible `<img id="camera1">` when `loader.naturalWidth > 0` (browser decoded successfully).
- `onerror` is a no-op — keeps showing the last good frame on network errors.

**Route:**
```php
'/camera/live' => ['CameraController', 'live'],
```
No middleware (public, read-only image endpoint). Output: `Content-Type: image/jpeg` with `no-store` cache headers.

**Upload directory:**
- `public/uploads/` — writable by the web server; camera writes here via FTP.
- `.htaccess` in that directory enforces no-cache headers at the Apache level.
- The `uploads/` directory should **not** execute PHP (add `php_flag engine off` or equivalent if needed).

### 9. Authentication & Password Reset

**Login/Logout:**
- `GET /login` → `AuthController::showLogin()` | `POST /login` → `AuthController::login()` — middleware: `guest, csrf, rate-limit:login,5,300`
- `POST /logout` → `AuthController::logout()` — middleware: `auth, csrf`

**Registration:** Disabled. Both `GET /register` and `POST /register` redirect immediately to `/login`. Code is preserved in `AuthController` — remove the early `redirect('/login'); return;` blocks to re-enable.

**Password Reset:**
- `GET  /password/forgot` → `PasswordResetController::showForgotForm()`
- `POST /password/forgot` → `PasswordResetController::sendResetLink()` — middleware: `guest, csrf, rate-limit:password-reset,3,600`
- `GET  /password/reset`  → `PasswordResetController::showResetForm()`
- `POST /password/reset`  → `PasswordResetController::resetPassword()` — middleware: `guest, csrf`

**Token security:** `bin2hex(random_bytes(32))` raw token is emailed; only `password_hash()` of the token is stored in the `password_resets` DB table. A DB leak cannot be used to reset accounts directly. Tokens expire after 1 hour. Expired tokens are purged opportunistically on every new reset request (no cron needed).

**Key files:**
- `app/Controllers/AuthController.php`
- `app/Controllers/PasswordResetController.php`
- `app/Views/auth/login.php`, `forgot-password.php`, `reset-password.php`, `register.php`
- `database/initialize/021_create_password_resets.sql`

## Development Workflow

### Running the Application

**Primary method:**
Code is deployed via GitHub → NerdRack/CPanel using CPanel's GitHub Version Control feature. The repo is checked out to `/home/okanogan/public_html` on the server, and a `.htaccess` file redirects the web root to the `public/` folder. Development work happens on the test site (`https://test.okanoganvalleygolf.com/`); the live site is `https://www.okanoganvalleygolf.com/`. There is no Docker or containerized environment in use – just a standard LAMP stack provided by the host.

Application cannot be run directly; it must be served by a web server (Apache via CPanel in this case).

**Production Infrastructure (host details):**
- Hosted on NerdRack using CPanel.
- SSL/TLS certificates managed by CPanel; HTTPS is enforced by redirect rules.
- All HTTP traffic redirected to HTTPS via `.htaccess`.

### Debugging

- `APP_DEBUG` constant (in `config/config.php`) controls error logging
- Router logs matched routes to `error_log` when debug is enabled
- Flash messages use session storage - check `$_SESSION['flash']` structure
- The `/debug` route shows request/server info

### Testing Routes

Visit `/debug` on your local test or live site (e.g. `https://test.okanoganvalleygolf.com/debug`) to inspect:
- Current HTTP method and URI
- Server variables
- Route matching diagnostics

## Adding New Features

### New Route + Controller

1. **Add route** to `config/routes.php`:
   ```php
   'GET' => [
       '/products/(\d+)' => ['ProductController', 'show'],
   ]
   ```

2. **Create controller** at `app/Controllers/ProductController.php`:
   ```php
   <?php
   namespace App\Controllers;
   
   class ProductController extends Controller
   {
       public function show(string $id): void
       {
           $this->view('products/show', ['productId' => $id]);
       }
   }
   ```

3. **Create view** at `app/Views/products/show.php`:
   - Access layout variables like `$content`, `$title`
   - Use `<?= $productId ?>` for safe output

### New Model & Database Table

1. **Create SQL initialization file** `database/initialize/##_create_tablename.sql`
2. **Create model** at `app/Models/TableName.php` extending `Model`
3. **Run SQL**: `mysql -u user -p dbname < database/initialize/##_create_tablename.sql`
4. **Use in code**: `TableName::find($id)`, `TableName::create($data)`

### New Service

Follow `LogService.php` pattern:
- Store in `app/Services/`
- Use `namespace App\Services;`
- Constructor injects or instantiates dependencies
- Public methods for business logic
- Use Models for database operations

## Important Constraints

- **Database:** Uses MySQL via PDO with prepared statements
- **Models:** Extend `App\Models\Model` base class for CRUD operations
- **Middleware:** Pipeline implemented (CSRF, Auth, Rate Limiting, etc.)
- **Sessions started globally:** `session_start()` called in `public/index.php`
- **No dependency injection:** Controllers manually instantiate services

## Common Pitfalls

1. **URL parameters are strings:** Always cast before arithmetic: `(int) $id`
2. **Views need data extraction:** Use `$this->view()`, not direct `require`
3. **Flash messages are single-use:** Retrieved via `getFlash()` which unsets them
4. **Layouts wrap content:** The view goes into `$content`, not rendered directly
5. **Namespaces must match paths:** `App\Services\FooService` → `app/Services/FooService.php`

## Configuration

All config in `config/config.php` using constants:
- `APP_NAME`, `APP_DEBUG`, `APP_URL`
- `DB_*` constants configured and in use
- Timezone set to `America/Los_Angeles`

**Note:** Basic `.env` file loader has been added to `config/config.php`. Use a `.env` file for development and deployment configuration, but never commit your production `.env`. See `.env.example` for required keys.

## Roadmap

See [ROADMAP.md](../ROADMAP.md) for the changelog, planned work, optional enhancements, and security checklist.
