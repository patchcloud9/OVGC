<?php

namespace App\Controllers;

/**
 * Public Grill Menu Page
 *
 * GET /menu  — displays the menu image (if uploaded) with a PDF download button
 */
class GrillController extends Controller
{
    private const ALLOWED_EXTS = ['jpg', 'png', 'webp'];

    public function index(): void
    {
        $imagePath = null;
        foreach (self::ALLOWED_EXTS as $ext) {
            $candidate = BASE_PATH . '/public/assets/menu/menu-display.' . $ext;
            if (file_exists($candidate)) {
                $imagePath = '/assets/menu/menu-display.' . $ext;
                break;
            }
        }

        $pdfExists = file_exists(BASE_PATH . '/public/assets/menu/menu.pdf');

        $this->view('grill/index', [
            'title'     => 'Grill Menu',
            'imagePath' => $imagePath,
            'pdfExists' => $pdfExists,
        ]);
    }
}
