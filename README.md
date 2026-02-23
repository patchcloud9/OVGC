# PHP MVC Framework Skeleton

A minimal, educational PHP MVC framework demonstrating the front controller and routing pattern.

## Folder Structure

```
php-framework/
├── public/                 # Web root (point Apache/Nginx here)
│   ├── index.php           # Front controller - ALL requests go here
│   ├── .htaccess           # Apache URL rewriting rules
│   └── assets/             # CSS, JS, images (publicly accessible)
│
├── app/                    # Your application code
│   ├── Controllers/        # Handle requests, return responses
│   ├── Middleware/         # Authentication, CSRF, rate limiting
│   ├── Models/             # Database models (User, Log, etc.)
│   ├── Services/           # Business logic (AuthService, LogService)
│   └── Views/              # HTML templates
│       ├── layouts/        # Master templates (header, footer, nav)
│       ├── partials/       # Reusable snippets
│       ├── auth/           # Login, register pages
│       ├── admin/          # Admin panel
│       ├── users/          # User management
│       ├── logs/           # Application logs
│       └── errors/         # Error pages (404, 500)
│
├── core/                   # Framework engine (reusable across projects)
│   ├── Autoloader.php      # PSR-4 style class autoloading
│   ├── Router.php          # URL matching and dispatching
│   ├── Middleware.php      # Middleware base class
│   ├── Database.php        # PDO wrapper for database access
│   ├── Validator.php       # Input validation with rules
│   ├── RateLimiter.php     # Rate limiting implementation
│   └── helpers.php         # Global helper functions
│
├── config/
│   ├── config.php          # App settings (DB, timezone, etc.)
│   └── routes.php          # Route definitions
│
├── storage/                # Writable directory
│   ├── logs/
│   └── cache/
│
└── database/               # Database setup
    ├── initialize/         # Table creation SQL files
    └── seed/              # Data seeding SQL files
```

## Database Setup

This framework uses MySQL with PDO for database access.

### 1. Create Database

```sql
CREATE DATABASE myapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configure Connection

Edit `config/config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'myapp');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Initialize Tables

Run the SQL files in `database/initialize/` (files are named `create_*.sql`):

```bash
# POSIX (Linux/macOS)
cat database/initialize/create_*.sql | mysql -u your_username -p myapp
```

Windows (PowerShell):

```powershell
Get-ChildItem -Path database\\initialize\\create_*.sql | Sort-Object Name | Get-Content | mysql -u your_username -p myapp
```
Note: This repository uses a simple create+seed workflow. Migration SQL files have been removed from `database/migrations/` to keep the setup process explicit and idempotent for new installs. If you maintain running installations you may prefer a migrations approach — contact the maintainer if you need a migration runner added back.

### 4.1 Seed Test Data (Optional)

Run the seed SQL files in `database/seed/` to insert default/example data (files are named `seed_*.sql`):

```bash
# POSIX (Linux/macOS)
cat database/seed/seed_*.sql | mysql -u your_username -p myapp
```

Windows (PowerShell):

```powershell
Get-ChildItem -Path database\\seed\\seed_*.sql | Sort-Object Name | Get-Content | mysql -u your_username -p myapp
```
### 4. Seed Test Data (Optional)

```bash
# POSIX (Linux/macOS)
cat database/seed/seed_*.sql | mysql -u your_username -p myapp
```

Windows (PowerShell):

```powershell
Get-ChildItem -Path database\\seed\\seed_*.sql | Sort-Object Name | Get-Content | mysql -u your_username -p myapp
```

See [database/README.md](database/README.md) for detailed instructions.

## How to Run

### Option 1: Docker (Recommended)

See complete Docker configuration in `docs/docker/`:
- `docker-compose.yml` - Full service configuration
- `entrypoint.sh` - Container initialization script with automatic directory creation

The entrypoint automatically:
- Installs pdo_mysql extension
- Enables Apache mod_rewrite
- Configures DocumentRoot to /public
- Creates storage and upload directories
- Sets proper permissions for www-data

Run:
```bash
docker-compose up
```

Visit: http://localhost:8080

### Option 2: PHP Built-in Server

```bash
cd public
php -S localhost:8080
```

Note: URL rewriting won't work perfectly with the built-in server.

### Option 3: Apache/XAMPP/MAMP

Point your document root to the `public/` folder.

## How Routing Works

1. **All requests hit `public/index.php`** (via `.htaccess` rewrite)
2. **Router loads routes from `config/routes.php`**
3. **Router matches URL patterns to controller methods**
4. **Controller handles request and returns response**

### Example Route

```php
// config/routes.php
'GET' => [
    '/users/(\d+)' => ['UserController', 'show'],
]
```

When you visit `/users/42`:
- Pattern `/users/(\d+)` matches
- `(\d+)` captures `42`
- Router calls `UserController::show('42')`

### Route Patterns

| Pattern | Matches | Example |
|---------|---------|---------|
| `/` | Exact root | `/` |
| `/about` | Exact path | `/about` |
| `/users/(\d+)` | Digits | `/users/123` |
| `/posts/([a-z-]+)` | Lowercase + hyphens | `/posts/my-first-post` |
| `/api/v(\d+)/users` | Version number | `/api/v2/users` |

## Key Files to Study

1. **`public/index.php`** - The single entry point
2. **`core/Router.php`** - How URL matching works
3. **`core/Database.php`** - PDO wrapper for database operations
4. **`config/routes.php`** - Route definitions
5. **`app/Controllers/Controller.php`** - Base controller with helpers
6. **`app/Controllers/UserController.php`** - Example with database CRUD operations
7. **`app/Models/Model.php`** - Base model with CRUD methods
8. **`app/Models/User.php`** - Example model implementation

## Recent Changes & Notes
- Migrations: This repo no longer includes SQL migration files; use the `database/initialize/` create scripts and the `database/seed/` files for fresh installs. If you need incremental migrations for upgrades, consider adding a `database/migrations/` workflow.
- Placeholders: The homepage and purchase pages support the `{email}` placeholder — it will be replaced by the Theme Settings `gallery_contact_email` value when rendering public views (admin fields should use `{email}` to insert the site-wide contact email).
- Page subtitles: `page_subtitle` is supported on About and Purchase pages (stored in their respective tables and editable in admin).
- Purchase Page: Public route available at `/purchase` and editable in Admin → Pages → Purchase.
- Footer menu: Footer Quick Links are now driven by `menu_items` DB table (same visibility rules as the main navbar).
- Profile link: The user 'Profile' link was removed from the nav (no active profile page in this project).

## Using Models

The framework includes a Model base class for database operations:

```php
use App\Models\User;

// Find by ID
$user = User::find(1);

// Get all records
$users = User::all();

// Find by conditions
$admins = User::where(['role' => 'admin']);

// Create
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
    'role' => 'user',
]);

// Update
User::update(1, ['name' => 'Jane Doe']);

// Delete
User::delete(1);
```

## Next Steps

The framework includes many production-ready features:

- [x] **Database Layer** - PDO wrapper with Model base class for CRUD operations
- [x] **Middleware System** - Pipeline-based request filtering (CSRF, Auth, Rate Limiting)
- [x] **Security Hardening** - CSRF protection, XSS prevention, SQL injection protection
- [x] **Authentication & Authorization** - Session-based auth with role-based access control
- [x] **Input Validation** - Comprehensive validation rules with error handling
- [x] **Rate Limiting** - Token bucket algorithm for form submissions
- [x] **Logging System** - Dual persistence (database + file) with graceful degradation
- [x] **Admin Panel** - User management with card-based UI
- [x] **Mobile-Friendly Views** - Responsive design with Bulma CSS
- [x] **UI Customization** - Theme settings with color palette, logo/favicon uploads, dynamic styling (Phase 1-3 complete)

### Features in Detail

**Middleware Available:**
- `csrf` - CSRF token validation
- `auth` - Require authentication
- `guest` - Require NOT authenticated
- `role:admin` - Require specific role
- `rate-limit:key,max,seconds` - Rate limiting

**Security Features:**
- Automatic CSRF protection on state-changing requests
- Password hashing with bcrypt
- XSS prevention with `e()` helper
- SQL injection protection via prepared statements
- Secure session configuration
- Rate limiting on login/register

**Admin Features:**
- User management (create, edit, delete)
- Role-based access control
- Application logs with search and pagination
- Unauthorized access attempt logging
- Mobile-optimized card layouts

### Still TODO (Optional Enhancements)

- [ ] UI Customization Phase 4 - User light/dark mode toggle
- [ ] Environment variables (.env file support)
- [ ] API authentication (token-based)
- [ ] Email functionality
- [ ] Caching layer
- [ ] Testing infrastructure (PHPUnit)
