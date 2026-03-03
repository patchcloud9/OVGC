<?php

namespace App\Controllers;

/**
 * Password Reset Controller
 *
 * Handles the forgot-password / reset-password flow:
 *   GET  /password/forgot → showForgotForm()
 *   POST /password/forgot → sendResetLink()
 *   GET  /password/reset  → showResetForm()
 *   POST /password/reset  → resetPassword()
 *
 * All routes carry the [guest] middleware (logged-in users don't need a reset).
 * Token security: raw token is emailed; only a password_hash() of the token is
 * stored in the DB, so a DB leak cannot be used to reset accounts directly.
 */
class PasswordResetController extends Controller
{
    // -------------------------------------------------------------------------
    // GET /password/forgot
    // -------------------------------------------------------------------------

    public function showForgotForm(): void
    {
        $this->view('auth/forgot-password', ['title' => 'Forgot Password']);
    }

    // -------------------------------------------------------------------------
    // POST /password/forgot  [guest, csrf, rate-limit:password-reset,3,600]
    // -------------------------------------------------------------------------

    public function sendResetLink(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect('/password/forgot');
            return;
        }

        // Process silently — always show the same message regardless of whether
        // the email exists, to prevent account enumeration.
        $this->processResetRequest($email);

        $this->flash('success', 'If that email address is registered, you will receive a password reset link shortly.');
        $this->redirect('/password/forgot');
    }

    // -------------------------------------------------------------------------
    // GET /password/reset?token=xxx&email=xxx
    // -------------------------------------------------------------------------

    public function showResetForm(): void
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if ($token === '' || $email === '') {
            $this->flash('error', 'Invalid reset link.');
            $this->redirect('/password/forgot');
            return;
        }

        if (!$this->verifyToken($email, $token)) {
            $this->flash('error', 'This reset link is invalid or has expired. Please request a new one.');
            $this->redirect('/password/forgot');
            return;
        }

        $this->view('auth/reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $email,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /password/reset  [guest, csrf]
    // -------------------------------------------------------------------------

    public function resetPassword(): void
    {
        $token    = $_POST['token']                 ?? '';
        $email    = $_POST['email']                 ?? '';
        $password = $_POST['password']              ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        $redirectBack = '/password/reset?token=' . urlencode($token) . '&email=' . urlencode($email);

        if ($token === '' || $email === '') {
            $this->flash('error', 'Invalid reset link.');
            $this->redirect('/password/forgot');
            return;
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirect($redirectBack);
            return;
        }

        if ($password !== $confirm) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect($redirectBack);
            return;
        }

        if (!$this->verifyToken($email, $token)) {
            $this->flash('error', 'This reset link is invalid or has expired. Please request a new one.');
            $this->redirect('/password/forgot');
            return;
        }

        // Update password
        $userModel = new \App\Models\User();
        $user      = $userModel->findByEmail($email);

        if (!$user) {
            $this->flash('error', 'Account not found.');
            $this->redirect('/password/forgot');
            return;
        }

        $userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Consume the token
        $db = \Core\Database::getInstance();
        $db->execute('DELETE FROM password_resets WHERE email = ?', [$email]);

        $this->flash('success', 'Your password has been reset. You can now log in.');
        $this->redirect('/login');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Generate a token, store its hash, and send the reset email.
     * Called even when the email does not exist (but silently does nothing).
     */
    private function processResetRequest(string $email): void
    {
        $userModel = new \App\Models\User();
        if (!$userModel->findByEmail($email)) {
            return; // silent — don't reveal whether the email is registered
        }

        $db    = \Core\Database::getInstance();
        $token = bin2hex(random_bytes(32)); // 64-char hex, cryptographically random
        $hash  = password_hash($token, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // One reset per email at a time
        $db->execute('DELETE FROM password_resets WHERE email = ?', [$email]);
        $db->execute(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)',
            [$email, $hash, $expires]
        );

        $resetUrl = APP_URL
            . '/password/reset?token=' . urlencode($token)
            . '&email=' . urlencode($email);

        $siteName = defined('APP_NAME') ? APP_NAME : 'Our Site';
        $body = "You requested a password reset for your {$siteName} account.\n\n"
              . "Click the link below to choose a new password (link expires in 1 hour):\n"
              . $resetUrl . "\n\n"
              . "If you did not request a password reset, you can safely ignore this email.\n"
              . "Your password will not change unless you click the link above.";

        $mailer = new \Core\Mailer();
        $mailer->send($email, "Password Reset — {$siteName}", $body);
    }

    /**
     * Look up an unexpired reset row for $email and verify $token against the
     * stored hash. Returns true only when both match.
     */
    private function verifyToken(string $email, string $token): bool
    {
        $db    = \Core\Database::getInstance();
        $reset = $db->fetch(
            'SELECT token FROM password_resets WHERE email = ? AND expires_at > NOW() LIMIT 1',
            [$email]
        );

        if (!$reset) {
            return false;
        }

        return password_verify($token, $reset['token']);
    }
}
