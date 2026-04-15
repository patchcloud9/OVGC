<?php

namespace App\Controllers;

/**
 * Public Grill Menu Page
 *
 * GET /menu  — displays the current menu PDF with a download link
 */
class GrillController extends Controller
{
    public function index(): void
    {
        $pdfPath = BASE_PATH . '/public/assets/menu/menu.pdf';
        $exists  = file_exists($pdfPath);

        $this->view('grill/index', [
            'title'  => 'Grill Menu',
            'exists' => $exists,
        ]);
    }
}
