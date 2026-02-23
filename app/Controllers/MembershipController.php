<?php

namespace App\Controllers;

class MembershipController extends Controller
{
    /**
     * Display the membership information page.
     * Route: GET /membership
     */
    public function index(): void
    {
        $this->view('membership/index', [
            'title' => 'Membership',
        ]);
    }
}
