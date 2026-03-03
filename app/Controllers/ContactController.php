<?php

namespace App\Controllers;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     * Route: GET /contact
     */
    public function index(): void
    {
        $this->view('contact/index', [
            'title' => 'Contact',
        ]);
    }

    /**
     * Handle contact form submission.
     * Route: POST /contact  [csrf, rate-limit:contact,5,300]
     */
    public function send(): void
    {
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $comment = trim($_POST['comment'] ?? '');

        // Validation
        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($comment === '') {
            $errors[] = 'Please enter a message.';
        }

        if (!empty($errors)) {
            flash_old_input($_POST);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/contact');
            return;
        }

        // reCAPTCHA v3 verification
        if (defined('RECAPTCHA_SECRET_KEY') && RECAPTCHA_SECRET_KEY !== '') {
            $token = trim($_POST['recaptcha_token'] ?? '');
            if (empty($token) || !$this->verifyRecaptcha($token)) {
                $this->flash('error', 'Security check failed. Please try again.');
                $this->redirect('/contact');
                return;
            }
        }

        // Destination: admin contact email from theme settings
        $adminEmail = theme_setting('contact_email');
        if (empty($adminEmail)) {
            $this->flash('error', 'Contact email is not configured. Please call us directly.');
            $this->redirect('/contact');
            return;
        }

        $subject = 'Contact Form — ' . $name;
        $body    = "Name:    {$name}\n"
                 . ($phone !== '' ? "Phone:   {$phone}\n" : '')
                 . "Email:   {$email}\n\n"
                 . "Message:\n{$comment}";

        $mailer = new \Core\Mailer();
        $sent   = $mailer->send($adminEmail, $subject, $body, ['reply_to' => $email]);

        if ($sent) {
            $this->flash('success', 'Your message has been sent. We will be in touch soon.');
        } else {
            $this->flash('error', 'Sorry, your message could not be sent. Please try again or contact us by phone.');
        }

        $this->redirect('/contact');
    }

    /**
     * Verify a reCAPTCHA v3 token with Google's API.
     * Returns true if human (score >= 0.5), false if bot.
     * Fails open if Google's API is unreachable so real users aren't blocked.
     */
    private function verifyRecaptcha(string $token): bool
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'secret'   => RECAPTCHA_SECRET_KEY,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]),
                'timeout' => 5,
            ],
        ]);

        $response = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

        if ($response === false) {
            error_log('reCAPTCHA: API unreachable — failing open');
            return true;
        }

        $data = json_decode($response, true);
        return ($data['success'] ?? false) && ($data['score'] ?? 0) >= 0.5;
    }
}
