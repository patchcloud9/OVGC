<?php

namespace App\Controllers;

class RatesController extends Controller
{
    /**
     * Display the green fees & cart rentals page.
     * Route: GET /rates
     */
    public function index(): void
    {
        $this->view('rates/index', [
            'title' => 'Rates',
        ]);
    }
}
