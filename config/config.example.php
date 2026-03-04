<?php
/**
 * Main Configuration File
 *
 * Copy this file to config.php and fill in your values.
 * Never commit config.php — it contains secrets.
 */

// Application settings
define('APP_NAME', 'Okanogan Valley Golf Club');
define('APP_ENV',  'production');   // 'development' or 'production'
define('APP_DEBUG', false);         // Set to true on dev/test only — never on live
define('APP_URL',  'https://www.okanoganvalleygolf.com');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', '');              // your database name
define('DB_USER', '');              // your database username
define('DB_PASS', '');              // your database password

// Timezone
date_default_timezone_set('America/Los_Angeles');

// Mail / SMTP
// Gmail:   MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls
//          MAIL_PASSWORD must be a Google App Password (not your account password).
//          Create one at: myaccount.google.com → Security → App passwords
// Outlook: MAIL_HOST=smtp-mail.outlook.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls
define('MAIL_HOST',         'smtp.gmail.com');
define('MAIL_PORT',         587);
define('MAIL_ENCRYPTION',   'tls');     // 'tls' = STARTTLS (587), 'ssl' = port 465
define('MAIL_USERNAME',     '');        // full email address
define('MAIL_PASSWORD',     '');        // app password or account password
define('MAIL_FROM_ADDRESS', '');        // address emails are sent from
define('MAIL_FROM_NAME',    APP_NAME);  // display name in From header

// Google reCAPTCHA v3
// Register at https://www.google.com/recaptcha (choose "v3"), add your domain.
// Leave empty to silently skip reCAPTCHA.
define('RECAPTCHA_SITE_KEY',   '');     // public — embedded in page JS
define('RECAPTCHA_SECRET_KEY', '');     // private — server-side only

// Debug toolbar — allowed IPs (comma-separated) that may view the toolbar without
// being logged in as admin. Useful for viewing the toolbar on the test server.
// 127.0.0.1 and ::1 (localhost) are always safe defaults.
define('DEBUG_ALLOWED_IPS', '127.0.0.1,::1');
