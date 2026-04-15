<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Flyer;
use App\Services\LogService;
use Core\Validator;

/**
 * Admin Flyer Controller
 *
 * GET  /admin/flyers              — list all flyers
 * GET  /admin/flyers/create       — upload form
 * POST /admin/flyers/create       — store new flyer
 * GET  /admin/flyers/{id}/edit    — edit form
 * POST /admin/flyers/{id}/edit    — update flyer metadata / replace file
 * POST /admin/flyers/{id}/delete  — delete flyer
 */
class FlyerController extends Controller
{
    private const ALLOWED_MIME = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
    ];

    private const MAX_BYTES = 10 * 1024 * 1024; // 10 MB

    private const DEFAULT_EXPIRY_DAYS = 90;

    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function index(): void
    {
        $flyers = Flyer::allForAdmin();

        $this->view('admin/flyers/index', [
            'title'  => 'Manage Flyers',
            'flyers' => $flyers,
        ]);
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): void
    {
        $defaultExpiry = date('Y-m-d', strtotime('+' . self::DEFAULT_EXPIRY_DAYS . ' days'));

        $this->view('admin/flyers/create', [
            'title'         => 'Add Flyer',
            'defaultExpiry' => $defaultExpiry,
        ]);
    }

    public function store(): void
    {
        $errors = $this->validateFields();
        if ($errors) {
            $this->flash('danger', implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/flyers/create');
            return;
        }

        $upload = $this->handleUpload();
        if (!$upload) {
            $this->redirect('/admin/flyers/create');
            return;
        }

        Flyer::create([
            'title'         => trim($this->input('title')),
            'description'   => trim($this->input('description') ?? ''),
            'filename'      => $upload['filename'],
            'file_path'     => $upload['file_path'],
            'mime_type'     => $upload['mime_type'],
            'expires_at'    => $this->input('expires_at'),
            'display_order' => Flyer::getNextDisplayOrder(),
            'uploaded_by'   => auth_user()['id'],
        ]);

        $this->logService->add('info', 'Flyer uploaded', [
            'title'   => trim($this->input('title')),
            'user_id' => auth_user()['id'],
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        $this->flash('success', 'Flyer added successfully!');
        $this->redirect('/admin/flyers');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(string $id): void
    {
        $flyer = $this->findOrFail((int) $id);

        $this->view('admin/flyers/edit', [
            'title' => 'Edit Flyer',
            'flyer' => $flyer,
        ]);
    }

    public function update(string $id): void
    {
        $fid   = (int) $id;
        $flyer = $this->findOrFail($fid);

        $errors = $this->validateFields();
        if ($errors) {
            $this->flash('danger', implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/flyers/' . $fid . '/edit');
            return;
        }

        $data = [
            'title'       => trim($this->input('title')),
            'description' => trim($this->input('description') ?? ''),
            'expires_at'  => $this->input('expires_at'),
        ];

        // Replace file only if a new one was submitted
        if (!empty($_FILES['flyer']['name']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handleUpload();
            if (!$upload) {
                $this->redirect('/admin/flyers/' . $fid . '/edit');
                return;
            }
            // Delete old file from disk
            $this->deleteFile($flyer['file_path']);

            $data['filename']  = $upload['filename'];
            $data['file_path'] = $upload['file_path'];
            $data['mime_type'] = $upload['mime_type'];
        }

        Flyer::update($fid, $data);

        $this->logService->add('info', 'Flyer updated', [
            'flyer_id' => $fid,
            'user_id'  => auth_user()['id'],
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        $this->flash('success', 'Flyer updated successfully!');
        $this->redirect('/admin/flyers');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(string $id): void
    {
        $fid   = (int) $id;
        $flyer = $this->findOrFail($fid);

        $this->deleteFile($flyer['file_path']);
        Flyer::delete($fid);

        $this->logService->add('info', 'Flyer deleted', [
            'flyer_id' => $fid,
            'title'    => $flyer['title'],
            'user_id'  => auth_user()['id'],
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        $this->flash('success', 'Flyer deleted.');
        $this->redirect('/admin/flyers');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $flyer = Flyer::find($id);
        if (!$flyer) {
            $this->flash('danger', 'Flyer not found.');
            $this->redirect('/admin/flyers');
            exit;
        }
        return $flyer;
    }

    private function validateFields(): array
    {
        $validator = new Validator(
            [
                'title'      => $this->input('title'),
                'expires_at' => $this->input('expires_at'),
            ],
            [
                'title'      => 'required|min:2|max:255',
                'expires_at' => 'required|date',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            return $errors;
        }

        return [];
    }

    /**
     * Validate and move the uploaded file.
     * Returns ['filename', 'file_path', 'mime_type'] on success, null on failure.
     */
    private function handleUpload(): ?array
    {
        $phpError = $_FILES['flyer']['error'] ?? UPLOAD_ERR_NO_FILE;

        if (!isset($_FILES['flyer']) || $phpError !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_NO_FILE    => 'No file was selected. Please choose a file.',
                UPLOAD_ERR_INI_SIZE   => 'The file exceeds the server upload size limit. Contact your host to raise upload_max_filesize.',
                UPLOAD_ERR_FORM_SIZE  => 'The file exceeds the form size limit.',
                UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded. Please try again.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server error: no temporary upload directory is configured.',
                UPLOAD_ERR_CANT_WRITE => 'Server error: failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
            ];
            $msg = $messages[$phpError] ?? "Upload failed (PHP error code: {$phpError}).";
            $this->flash('danger', $msg);
            return null;
        }

        $file  = $_FILES['flyer'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);

        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            $this->flash('danger', 'Invalid file type. Allowed: JPG, PNG, GIF, WebP, PDF.');
            return null;
        }

        if ($file['size'] > self::MAX_BYTES) {
            $this->flash('danger', 'File too large. Maximum 10 MB allowed.');
            return null;
        }

        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = uniqid('flyer_', true) . '.' . $ext;
        $uploadDir = BASE_PATH . '/public/uploads/flyers';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
            $this->flash('danger', 'Failed to save file. Please check directory permissions.');
            return null;
        }

        return [
            'filename'  => $filename,
            'file_path' => '/uploads/flyers/' . $filename,
            'mime_type' => $mime,
        ];
    }

    private function deleteFile(string $relativePath): void
    {
        $full = BASE_PATH . '/public' . $relativePath;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
