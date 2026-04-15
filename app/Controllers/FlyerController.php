<?php

namespace App\Controllers;

use App\Models\Flyer;

/**
 * Public Flyer Controller
 *
 * GET /flyers  — mini-gallery of all active (non-expired) flyers
 */
class FlyerController extends Controller
{
    public function index(): void
    {
        $flyers = Flyer::getActive();

        $this->view('flyers/index', [
            'title'  => 'Event Flyers',
            'flyers' => $flyers,
        ]);
    }
}
