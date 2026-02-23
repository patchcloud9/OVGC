<?php

namespace App\Controllers;

class ContactController extends Controller
{
    /**
     * Display the contact page (initially based on membership template)
     * Route: GET /contact
     */
    public function index(): void
    {
        $this->view('contact/index', [
            'title' => 'Contact',
        ]);
    }
}
