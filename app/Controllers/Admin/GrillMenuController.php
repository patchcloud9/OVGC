<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

/**
 * Admin Grill Menu Controller
 *
 * GET  /admin/grill-menu         — show current files + upload forms
 * POST /admin/grill-menu/pdf     — upload/replace the download PDF
 * POST /admin/grill-menu/image   — upload/replace the display image
 * POST /admin/grill-menu/delete-pdf   — remove the PDF
 * POST /admin/grill-menu/delete-image — remove the display image
 */
class GrillMenuController extends Controller
{
    private const PDF_PATH   = '/public/assets/menu/menu.pdf';
    private const IMAGE_PATH = '/public/assets/menu/menu-display';  // ext added on upload
    private const ASSET_DIR  = '/public/assets/menu/';
    private const MAX_SIZE   = 10 * 1024 * 1024; // 10 MB

    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function index(): void
    {
        $pdfPath   = BASE_PATH . self::PDF_PATH;
        $imagePath = $this->findImagePath();

        $this->view('admin/grill-menu/index', [
            'title'         => 'Grill Menu',
            'pdfExists'     => file_exists($pdfPath),
            'pdfSize'       => file_exists($pdfPath) ? filesize($pdfPath) : null,
            'pdfModified'   => file_exists($pdfPath) ? filemtime($pdfPath) : null,
            'imageExists'   => $imagePath !== null,
            'imagePath'     => $imagePath ? str_replace(BASE_PATH . '/public', '', $imagePath) : null,
            'imageModified' => $imagePath ? filemtime($imagePath) : null,
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
            $this->flash('error', 'Only PDF files are accepted here.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->flash('error', 'File exceeds the 10 MB size limit.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->ensureDir();

        if (!move_uploaded_file($file['tmp_name'], BASE_PATH . self::PDF_PATH)) {
            $this->flash('error', 'Failed to save file. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->flash('success', 'Menu PDF updated.');
        $this->redirect('/admin/grill-menu');
    }

    public function uploadImage(): void
    {
        if (empty($_FILES['menu_image']) || $_FILES['menu_image']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Upload failed (code ' . ($_FILES['menu_image']['error'] ?? -1) . '). Please try again.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $file = $_FILES['menu_image'];

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_IMAGE_TYPES[$mimeType])) {
            $this->flash('error', 'Only JPEG, PNG, or WebP images are accepted.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->flash('error', 'File exceeds the 10 MB size limit.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->ensureDir();

        // Remove any existing display image (may have different extension)
        $existing = $this->findImagePath();
        if ($existing) {
            unlink($existing);
        }

        $ext  = self::ALLOWED_IMAGE_TYPES[$mimeType];
        $dest = BASE_PATH . self::IMAGE_PATH . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->flash('error', 'Failed to save image. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->flash('success', 'Display image updated.');
        $this->redirect('/admin/grill-menu');
    }

    public function destroyPdf(): void
    {
        $path = BASE_PATH . self::PDF_PATH;
        if (!file_exists($path)) {
            $this->flash('error', 'No PDF found.');
        } elseif (!unlink($path)) {
            $this->flash('error', 'Could not delete PDF. Check server permissions.');
        } else {
            $this->flash('success', 'Menu PDF removed.');
        }
        $this->redirect('/admin/grill-menu');
    }

    public function destroyImage(): void
    {
        $path = $this->findImagePath();
        if (!$path) {
            $this->flash('error', 'No display image found.');
        } elseif (!unlink($path)) {
            $this->flash('error', 'Could not delete image. Check server permissions.');
        } else {
            $this->flash('success', 'Display image removed.');
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

    /** Returns the full filesystem path of the display image, or null if none exists. */
    private function findImagePath(): ?string
    {
        foreach (self::ALLOWED_IMAGE_TYPES as $ext) {
            $path = BASE_PATH . self::IMAGE_PATH . '.' . $ext;
            if (file_exists($path)) {
                return $path;
            }
        }
        return null;
    }
}
