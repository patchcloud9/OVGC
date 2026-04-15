<?php

namespace App\Controllers;

/**
 * Public Grill Menu Page
 *
 * GET /menu  — shows auto-generated preview image if available,
 *              falls back to open/download buttons if only PDF exists.
 */
class GrillController extends Controller
{
    public function index(): void
    {
        $imageExists = file_exists(BASE_PATH . '/public/assets/menu/menu-display.jpg');
        $pdfExists   = file_exists(BASE_PATH . '/public/assets/menu/menu.pdf');

        $this->view('grill/index', [
            'title'       => 'Grill Menu',
            'imageExists' => $imageExists,
            'pdfExists'   => $pdfExists,
        ]);
    }
}
