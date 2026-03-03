<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Log;
use Core\Mailer;

/**
 * Admin Controller
 *
 * Handles admin panel and admin-only functionality.
 * All routes protected by role:admin middleware.
 */
class AdminController extends Controller
{
    /**
     * Show admin dashboard
     * Route: GET /admin
     * Middleware: auth, role:admin
     */
    public function index(): void
    {
        // Get recent logs if available
        $recentLogs = [];
        try {
            $recentLogs = Log::recent(5);
        } catch (\Exception $e) {
            // Logs might not be available
        }

        $this->view('admin/index', [
            'title' => 'Admin Panel',
            'recentLogs' => $recentLogs,
        ]);
    }

    /**
     * Show test email form
     * Route: GET /admin/test-email
     * Middleware: auth, role:admin
     */
    public function testEmail(): void
    {
        $configured = defined('MAIL_HOST') && MAIL_HOST !== ''
            && defined('MAIL_USERNAME') && MAIL_USERNAME !== ''
            && defined('MAIL_PASSWORD') && MAIL_PASSWORD !== '';

        $this->view('admin/test-email', [
            'title'      => 'Test Email',
            'configured' => $configured,
            'mailHost'   => defined('MAIL_HOST')         ? MAIL_HOST         : '',
            'mailPort'   => defined('MAIL_PORT')         ? MAIL_PORT         : 587,
            'mailUser'   => defined('MAIL_USERNAME')     ? MAIL_USERNAME     : '',
            'mailFrom'   => defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : '',
            'mailEnc'    => defined('MAIL_ENCRYPTION')   ? MAIL_ENCRYPTION   : 'tls',
        ]);
    }

    /**
     * Send a test email
     * Route: POST /admin/test-email
     * Middleware: auth, role:admin, csrf
     */
    public function sendTestEmail(): void
    {
        $to = trim($_POST['to'] ?? '');

        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid recipient email address.');
            $this->redirect('/admin/test-email');
            return;
        }

        $mailer  = new Mailer();
        $subject = 'Test Email — ' . (defined('APP_NAME') ? APP_NAME : 'OVGC');
        $body    = "This is a test email sent from the admin panel.\n\n"
                 . "Site:    " . (defined('APP_URL') ? APP_URL : $_SERVER['HTTP_HOST']) . "\n"
                 . "Sent at: " . date('Y-m-d H:i:s T') . "\n"
                 . "Sent by: " . (auth_user()['name'] ?? 'admin') . "\n";

        $ok = $mailer->send($to, $subject, $body);

        if ($ok) {
            $this->flash('success', "Test email sent successfully to {$to}.");
        } else {
            $this->flash('error', 'Failed to send test email. Check your SMTP configuration and the server error log.');
        }

        $this->redirect('/admin/test-email');
    }
}
