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
}
