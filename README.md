# Okanogan Valley Golf Club Website

This repository contains the code that powers the Okanogan Valley Golf Club public site and admin panel. It is built on a simple PHP 8.4 front‑controller/MVC architecture originally derived from an educational PHP framework.

The app runs on a standard LAMP stack and is deployed via RackNerd/CPanel using GitHub Version Control. Docker is not used in production, and all routing, controllers, and views are tailored to the golf club's requirements.

## Folder Structure

```
php-framework/
├── public/                 # Web root (point Apache/Nginx here)
│   ├── index.php           # Front controller - ALL requests go here
│   ├── .htaccess           # Apache URL rewriting + CSP/security headers
│   └── assets/             # CSS, JS, images (publicly accessible)
│       ├── css/app.css     # Custom styles
│       └── js/             # External JS files (no inline <script> blocks in views)
│
├── app/                    # Your application code
│   ├── Controllers/        # Handle requests, return responses
│   │   └── Admin/          # Admin subdirectory controllers (e.g. Admin\EventController)
│   ├── Middleware/         # Authentication, CSRF, rate limiting
│   ├── Models/             # Database models (User, Log, Event, etc.)
│   ├── Services/           # Business logic (AuthService, LogService, EventService)
│   └── Views/              # HTML templates
│       ├── layouts/        # Master templates (header, footer, nav)
│       ├── partials/       # Reusable snippets (messages, nav, upcoming-events)
│       ├── auth/           # Login, register pages
│       ├── admin/          # Admin panel
│       │   └── events/     # Admin event management views
│       ├── events/         # Public calendar and detail views
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
│   ├── RRuleExpander.php   # RRULE→occurrence expansion (no Composer)
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
CREATE DATABASE ovgc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configure Connection

Edit `config/config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ovgc');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Initialize Tables

Run the SQL files in `database/initialize/` (files are named with a numeric prefix, e.g. `001_create_*.sql`, to enforce execution order):

### 4.1 Seed Test Data (Optional)

Run the seed SQL files in `database/seed/` to insert default/example data (files are named with a numeric prefix for order, e.g. `001_seed_*.sql`):

See [database/README.md](database/README.md) for detailed instructions.

## How to Run

The application is designed to run under a traditional Apache/PHP environment. In production it is deployed to RackNerd using CPanel's GitHub Version Control feature:

1. Configure a repository on RackNerd/CPanel to pull from GitHub.
2. Set the document root to the `public/` directory.
3. Ensure PHP 8.4 (or newer) is selected and `pdo_mysql` is enabled.
4. Upload or initialize the database using the scripts in `database/initialize/` and `database/seed/` (see pages above).

For local development you can use any LAMP stack (XAMPP, MAMP, WAMP) or the PHP built‑in server for quick tests:

```bash
cd public
php -S localhost:8080
```

URL rewriting may be limited with the built‑in server; the preferred local setup is still an Apache instance pointed at `public/`.

> **Note:** The original framework included Docker support, but the golf club deployment does not use containers; the instructions above supersede the previous Docker section.
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

See [ROADMAP.md](ROADMAP.md) for the changelog, planned work, and security checklist.

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
- Content Security Policy header in `public/.htaccess`
- No inline `<script>` blocks — all JS in external files under `public/assets/js/`

**Admin Features:**
- User management (create, edit, delete)
- Role-based access control
- Application logs with search and pagination
- Unauthorized access attempt logging
- Mobile-optimized card layouts