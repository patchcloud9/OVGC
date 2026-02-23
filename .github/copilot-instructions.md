# PHP MVC Framework - AI Coding Instructions

## Architecture Overview

This is a **minimal, educational PHP MVC framework** demonstrating front controller and routing patterns. The framework is intentionally lightweight - not a production framework (yet, but hope to be someday) but a teaching tool for understanding MVC fundamentals.

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

### 1. Routing System

Routes are defined in [config/routes.php](config/routes.php) using regex patterns:

```php
'GET' => [
    '/users/(\d+)' => ['UserController', 'show'],  // Captures numeric ID
    '/posts/([a-z-]+)/comments/(\d+)' => ['PostController', 'showComment'],  // Multiple params
]
```

**Key behaviors:**
- URL parameters are captured via regex groups and passed as string arguments to controller methods
- The router automatically converts `UserController` → `App\Controllers\UserController`
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
- Layout wraps view content in `$content` variable (see [app/Views/layouts/main.php](app/Views/layouts/main.php))
- Always escape output: `<?= htmlspecialchars($title) ?>`

### 3. Autoloading

PSR-4 style autoloader in [core/Autoloader.php](core/Autoloader.php) maps:
- `Core\` → `/core/`
- `App\` → `/app/`

When adding new classes, follow namespace structure exactly. File `app/Services/LogService.php` must use `namespace App\Services;`

### 4. Database & Models

**Database Layer:**
- PDO wrapper at [core/Database.php](core/Database.php) - singleton pattern with prepared statements
- Model base class at [app/Models/Model.php](app/Models/Model.php) with CRUD: `find()`, `all()`, `create()`, `update()`, `delete()`
- Models use `$table`, `$fillable`, `$timestamps` properties
- Example models: [User.php](app/Models/User.php), [Log.php](app/Models/Log.php)

**Database Setup:**
- SQL files in `database/initialize/` create tables (named `create_<table>_table.sql`, e.g., `create_users_table.sql`)
- SQL files in `database/seed/` populate test data (named `seed_<name>.sql`, e.g., `seed_users.sql`)
- Run initialization (POSIX): `cat database/initialize/create_*.sql | mysql -u user -p dbname`
- Run initialization (PowerShell): `Get-ChildItem -Path database\\initialize\\create_*.sql | Sort-Object Name | Get-Content | mysql -u user -p dbname`
- Run seeds (POSIX): `cat database/seed/seed_*.sql | mysql -u user -p dbname`
- Run seeds (PowerShell): `Get-ChildItem -Path database\\seed\\seed_*.sql | Sort-Object Name | Get-Content | mysql -u user -p dbname`
- Note: SQL migration files were intentionally removed in this repo to favor explicit `create_*` + `seed_*` files for new installs. If you require migrations for upgrades, migrate them into `database/migrations/` and add a runner script.
- See `database/README.md` for detailed instructions.

**Adding New Tables:**
1. Create `database/initialize/##_create_tablename.sql`:
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

See [app/Services/LogService.php](app/Services/LogService.php) for the project's service pattern:
- Services handle business logic and data operations
- Controllers instantiate services and coordinate between them
- Services should use Models for database access
- Legacy: Some services use file-based JSON storage in `storage/`

## Development Workflow

### Running the Application

**Primary method:** Push to GitHub, pull from GitHub to a virtual server. (framework.hexgrid.org)
Application cannot be run directly; must use a web server.

**Production Infrastructure:**
- HTTPS handled by nginx reverse proxy manager
- SSL/TLS certificates managed at proxy level
- All HTTP traffic automatically redirected to HTTPS

### Debugging

- `APP_DEBUG` constant (in [config/config.php](config/config.php)) controls error logging
- Router logs matched routes to `error_log` when debug is enabled
- Flash messages use session storage - check `$_SESSION['flash']` structure
- The `/debug` route shows request/server info

### Testing Routes

Visit [/debug](https://framework.hexgrid.org/debug) to see:
- Current HTTP method and URI
- Server variables
- Route matching diagnostics

## Adding New Features

### New Route + Controller

1. **Add route** to [config/routes.php](config/routes.php):
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
- **Sessions started globally:** `session_start()` called in [public/index.php](public/index.php)
- **No dependency injection:** Controllers manually instantiate services

## Common Pitfalls

1. **URL parameters are strings:** Always cast before arithmetic: `(int) $id`
2. **Views need data extraction:** Use `$this->view()`, not direct `require`
3. **Flash messages are single-use:** Retrieved via `getFlash()` which unsets them
4. **Layouts wrap content:** The view goes into `$content`, not rendered directly
5. **Namespaces must match paths:** `App\Services\FooService` → `app/Services/FooService.php`

## Configuration

All config in [config/config.php](config/config.php) using constants:
- `APP_NAME`, `APP_DEBUG`, `APP_URL`
- `DB_*` constants configured and in use
- Timezone set to `America/Los_Angeles`

**Note:** Basic `.env` file loader has been added to `config/config.php`. Use a `.env` file for development and deployment configuration, but never commit your production `.env`. See `.env.example` for required keys.

## Roadmap to Production

### Features — Completed (Stable) ✅

This project has a stable core with security and admin features completed and ready for production after deployment hardening. Highlights include:

- **Core / Data**
  - PDO-based `Database` wrapper and `Model` base class (CRUD, migrations/seeds).
  - Seeding and SQL-based initialization in `database/initialize/` and `database/seed/`.

- **Security**
  - CSRF protection, `e()` helper for safe output, input validation (Core\Validator), and prepared statements across DB access.
  - Password hashing, rate limiting, and development-only debug tools gated by `APP_DEBUG`.

- **Routing & Middleware**
  - Middleware pipeline with built-in middleware (csrf, auth, guest, rate-limit, log-request) and clean route definitions.

- **Authentication & Authorization**
  - Session-based auth, role-based permissions, login/register, and auth helpers.

- **Error Handling & Logging**
  - Global exception handler, 404/500 views, HTTP exception classes, and a dual-persistence `LogService` (DB + file fallback).

- **Admin UI & Theming**
  - Responsive admin UI, theme settings, color pickers, secure file uploads (MIME checks, random filenames), and dynamic CSS application.

- **Developer conveniences**
  - Basic `.env` loader for development, documented deploy checklist, and helpful utilities.

**Minor remaining:** user theme light/dark toggle (planned phase 4)

- Pattern: Admin configures theme → Stored in database → Applied globally via CSS variables → Cached per request

**Remaining:**
- **Phase 4** - User light/dark toggle (session-based preference)

### Features — Planned (Priority Order) 🚧

The following are the highest-priority features to finish before production readiness. Each item includes a recommended next step.

1. **Environment & Secrets (High Priority)**
   - Replace the basic in-repo `.env` loader with `vlucas/phpdotenv` or platform secrets.
   - Move all sensitive config (DB credentials, `APP_DEBUG`) to environment variables and ensure CI/hosting uses secret stores.

2. **Testing & CI (High Priority)**
   - Add PHPUnit with a test suite (unit + feature) and configure GitHub Actions to run tests on push.
   - Introduce a test database (SQLite or separate MySQL instance) for CI runs.

3. **Production Hardening (High Priority)**
   - Add and enforce security headers (CSP, HSTS, X-Frame-Options) at the reverse proxy.
   - Implement asset versioning and static caching strategy; add a monitoring and alerting integration (Sentry/Uptime).

4. **Developer Experience (Medium Priority)**
   - Debug toolbar (dev only), CLI scaffolding (`make:controller`, `make:model`), and hot-reload for local development.

5. **Performance & Ops (Medium Priority)**
   - Add caching (file/Redis for expensive queries), enable OPcache in production, and plan CDN for static assets.

### Features — Optional (Nice to Have) ✨

These enhancements are valuable but not required for initial production rollout. Prioritize once core and security items are done.

- **API & Integrations** — RESTful controllers, token-based auth, versioning (`/api/v1/`), and CORS policy.
- **Advanced Storage & Media** — SVG sanitizer, image optimization, and optional S3/remote storage support.
- **Enhanced Developer Tools** — CLI generators, code scaffolding, and richer debug tooling.
- **Advanced Security & Policies** — granular RBAC, audit logs, and per-user rate limits.
- **Performance Upgrades** — Redis caching, query optimization, advanced asset pipelines, CDN support.

### Critical Security Checklist Before Production

- [x] Enable `display_errors = 0` in production PHP config
- [x] Use HTTPS only (✅ nginx reverse proxy manager)
- [x] Implement CSRF protection on all state-changing routes (✅ tokens + validation)
- [x] Validate and sanitize ALL user input (✅ e() helper, prepared statements, Validator class)
- [x] Use prepared statements for ALL database queries (✅ PDO with prepared statements)
- [x] Set secure session cookie flags: `httponly`, `secure`, `samesite` (✅ configured in index.php)
- [x] Implement rate limiting on authentication endpoints (✅ RateLimiter class, applied to contact and user creation)
- [ ] Add Content Security Policy headers (recommended policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline' if needed)
- [x] Configure proper file upload restrictions (type, size, location) (✅ ThemeController validates type, 2MB limit, dedicated directory)
- [x] Disallow SVG uploads unless sanitized (SVG can contain executable content) — Theme uploads exclude SVG by default
- [ ] Remove or protect debug/test routes in production
- [ ] Set restrictive file permissions (755 for directories, 644 for files)
- [ ] Disable directory listing in web server config
- [ ] Keep framework and dependencies updated
- [x] Implement proper error logging (✅ dual database + file logging with graceful degradation)
- [x] Use environment variables for sensitive configuration (basic `.env` loader implemented; prefer CI secret stores in production)

> Deploy checklist: **Set `APP_DEBUG=false`** and `APP_ENV=production`; ensure `.env` is not committed, rotate database credentials, enable CSP/HSTS at the reverse proxy, and verify upload directories and file permissions prior to going live.

### Implementation Priority

**Phase 1 - Core Stability (Weeks 1-2)** ✅ COMPLETE
- ✅ Database layer + Models
- Environment configuration (.env) - IMPLEMENTED (basic loader; consider replacing with `vlucas/phpdotenv` or secrets manager for production)
- ✅ Error handling & logging

**Phase 2 - Security (Weeks 3-4)** ✅ COMPLETE
- ✅ CSRF protection
- ✅ Input validation
- ✅ Rate limiting
- ✅ Middleware pipeline
- ✅ Authentication & Authorization

**Phase 3 - User Experience (Weeks 5-6)** ✅ COMPLETE
- ✅ Admin panel with user management
- ✅ Application logs with dual persistence
- ✅ Mobile-friendly card layouts
- ✅ Search and pagination for logs
- ✅ Role-based redirects after login
- ✅ Unauthorized access logging

**Phase 4 - Developer Tools (Weeks 7-8)** - PLANNED
- CLI commands
- Debug toolbar

**Phase 5 - Production Ready (Weeks 9-10)** - PLANNED
- Environment variables (.env support)
- Security hardening (CSP headers, file upload restrictions)
- Deployment configuration
- Monitoring setup

### When Adding Each Feature

**Database class example:**
```php
// core/Database.php
namespace Core;

class Database
{
    private \PDO $pdo;
    
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
```

**Model base class example:**
```php
// app/Models/Model.php
namespace App\Models;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    
    public function find(int $id): ?array
    {
        return $this->db->query(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        )->fetch();
    }
}
```

**Middleware example:**
```php
// app/Middleware/AuthMiddleware.php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        return true;
    }
}
```
