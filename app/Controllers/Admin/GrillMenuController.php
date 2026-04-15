<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

/**
 * Admin Grill Menu Controller
 *
 * GET  /admin/grill-menu              — show current files + upload form
 * POST /admin/grill-menu              — upload PDF (auto-converts to display image via Imagick)
 * POST /admin/grill-menu/delete-pdf   — remove PDF and generated image
 */
class GrillMenuController extends Controller
{
    private const PDF_PATH   = '/public/assets/menu/menu.pdf';
    private const IMAGE_PATH = '/public/assets/menu/menu-display.jpg';
    private const ASSET_DIR  = '/public/assets/menu/';
    private const MAX_SIZE   = 10 * 1024 * 1024; // 10 MB
    private const IMAGE_DPI  = 150; // Resolution for PDF→image conversion

    public function index(): void
    {
        $pdfPath   = BASE_PATH . self::PDF_PATH;
        $imagePath = BASE_PATH . self::IMAGE_PATH;

        $this->view('admin/grill-menu/index', [
            'title'         => 'Grill Menu',
            'pdfExists'     => file_exists($pdfPath),
            'pdfSize'       => file_exists($pdfPath) ? filesize($pdfPath) : null,
            'pdfModified'   => file_exists($pdfPath) ? filemtime($pdfPath) : null,
            'imageExists'   => file_exists($imagePath),
            'imageModified' => file_exists($imagePath) ? filemtime($imagePath) : null,
            'imagickAvailable' => class_exists('Imagick'),
        ]);
    }

    public function uploadPdf(): void
    {
        if (empty($_FILES['menu_pdf']) || $_FILES['menu_pdf']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Upload failed (code ' . ($_FILES['menu_pdf']['error'] ?? -1) . '). Please try again.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $file = $_FILES['menu_pdf'];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if ($finfo->file($file['tmp_name']) !== 'application/pdf') {
            $this->flash('error', 'Only PDF files are accepted.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->flash('error', 'File exceeds the 10 MB size limit.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->ensureDir();

        $pdfDest = BASE_PATH . self::PDF_PATH;
        if (!move_uploaded_file($file['tmp_name'], $pdfDest)) {
            $this->flash('error', 'Failed to save PDF. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        // Attempt auto-conversion to display image
        $converted = $this->convertToImage($pdfDest);

        if ($converted) {
            $this->flash('success', 'Menu PDF uploaded and preview image generated successfully.');
        } else {
            $this->flash('warning', 'PDF uploaded, but automatic image conversion failed (Imagick may not be available on this server). Visitors will see open/download buttons instead of an inline preview.');
        }

        $this->redirect('/admin/grill-menu');
    }

    public function destroyPdf(): void
    {
        $deleted = false;

        $pdfPath = BASE_PATH . self::PDF_PATH;
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
            $deleted = true;
        }

        $imagePath = BASE_PATH . self::IMAGE_PATH;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($deleted) {
            $this->flash('success', 'Menu PDF and preview image removed.');
        } else {
            $this->flash('error', 'No menu PDF found.');
        }

        $this->redirect('/admin/grill-menu');
    }

    // -------------------------------------------------------------------------

    private function ensureDir(): void
    {
        $dir = BASE_PATH . self::ASSET_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Convert the first page of the PDF to a JPEG using Imagick.
     * Returns true on success, false if Imagick is unavailable or conversion fails.
     */
    private function convertToImage(string $pdfPath): bool
    {
        if (!class_exists('Imagick')) {
            return false;
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution(self::IMAGE_DPI, self::IMAGE_DPI);
            $imagick->readImage($pdfPath . '[0]'); // first page only
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(90);
            $imagick->setImageColorspace(\Imagick::COLORSPACE_SRGB);
            // Flatten to white background (PDFs may have transparent background)
            $imagick->setImageBackgroundColor('white');
            $imagick = $imagick->flattenImages();
            $imagick->writeImage(BASE_PATH . self::IMAGE_PATH);
            $imagick->destroy();
            return true;
        } catch (\Exception $e) {
            error_log('GrillMenu Imagick conversion failed: ' . $e->getMessage());
            return false;
        }
    }
}
