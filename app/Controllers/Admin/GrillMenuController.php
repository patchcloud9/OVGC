<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

/**
 * Admin Grill Menu Controller
 *
 * GET  /admin/grill-menu  — show current PDF status + upload form
 * POST /admin/grill-menu  — upload a new PDF
 * POST /admin/grill-menu/delete  — remove the current PDF
 */
class GrillMenuController extends Controller
{
    private const PDF_PATH = '/public/assets/menu/menu.pdf';
    private const PDF_DIR  = '/public/assets/menu/';
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    public function index(): void
    {
        $fullPath = BASE_PATH . self::PDF_PATH;
        $exists   = file_exists($fullPath);

        $this->view('admin/grill-menu/index', [
            'title'    => 'Grill Menu PDF',
            'exists'   => $exists,
            'fileSize' => $exists ? filesize($fullPath) : null,
            'modified' => $exists ? filemtime($fullPath) : null,
        ]);
    }

    public function upload(): void
    {
        if (empty($_FILES['menu_pdf']) || $_FILES['menu_pdf']['error'] !== UPLOAD_ERR_OK) {
            $code = $_FILES['menu_pdf']['error'] ?? -1;
            $this->flash('error', 'Upload failed (error code ' . $code . '). Please try again.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $file = $_FILES['menu_pdf'];

        // Validate by actual file contents, not browser-supplied MIME
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if ($mimeType !== 'application/pdf') {
            $this->flash('error', 'Only PDF files are accepted.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->flash('error', 'File exceeds the 10 MB size limit.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $dir = BASE_PATH . self::PDF_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            $this->flash('error', 'Could not create upload directory. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . 'menu.pdf')) {
            $this->flash('error', 'Failed to save file. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->flash('success', 'Menu PDF updated successfully.');
        $this->redirect('/admin/grill-menu');
    }

    public function destroy(): void
    {
        $fullPath = BASE_PATH . self::PDF_PATH;

        if (!file_exists($fullPath)) {
            $this->flash('error', 'No menu PDF found to delete.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        if (!unlink($fullPath)) {
            $this->flash('error', 'Could not delete file. Check server permissions.');
            $this->redirect('/admin/grill-menu');
            return;
        }

        $this->flash('success', 'Menu PDF removed.');
        $this->redirect('/admin/grill-menu');
    }
}
