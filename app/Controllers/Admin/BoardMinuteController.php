<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\BoardMinute;
use App\Services\LogService;
use Core\Validator;

class BoardMinuteController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    public function index(): void
    {
        // list all minutes descending by date
        $minutes = BoardMinute::allOrdered();
        $this->view('admin/board_minutes/index', [
            'title' => 'Board Minutes',
            'minutes' => $minutes,
        ]);
    }

    public function create(): void
    {
        $this->view('admin/board_minutes/create', [
            'title' => 'Upload Board Minutes',
        ]);
    }

    public function store(): void
    {
        $validator = new Validator(
            [
                'meeting_date' => $this->input('meeting_date'),
            ],
            [
                'meeting_date' => 'required|date',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/board-minutes/create');
            return;
        }

        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('danger', 'PDF file is required');
            $this->redirect('/admin/board-minutes/create');
            return;
        }

        $file = $_FILES['pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        if ($mime !== 'application/pdf') {
            $this->flash('danger', 'Only PDF files are allowed');
            $this->redirect('/admin/board-minutes/create');
            return;
        }
        if ($file['size'] > 10 * 1024 * 1024) {
            $this->flash('danger', 'PDF file too large (max 10MB)');
            $this->redirect('/admin/board-minutes/create');
            return;
        }

        $ext = 'pdf';
        $filename = uniqid('minutes_', true) . '.' . $ext;
        $uploadDir = BASE_PATH . '/public/uploads/board_minutes';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename);
        $filePath = '/uploads/board_minutes/' . $filename;

        BoardMinute::create([
            'meeting_date' => $this->input('meeting_date'),
            'filename' => $file['name'],
            'file_path' => $filePath,
        ]);

        $this->logService->add('info', 'Board minutes uploaded', ['meeting_date' => $this->input('meeting_date')]);
        $this->flash('success', 'Minutes uploaded');
        $this->redirect('/admin/board-minutes');
    }

    public function edit(string $id): void
    {
        $mid = (int) $id;
        $minute = BoardMinute::find($mid);
        if (!$minute) {
            $this->flash('danger', 'Minutes not found');
            $this->redirect('/admin/board-minutes');
            return;
        }
        $this->view('admin/board_minutes/edit', [
            'title' => 'Edit Minutes',
            'minute' => $minute,
        ]);
    }

    public function update(string $id): void
    {
        $mid = (int) $id;
        $minute = BoardMinute::find($mid);
        if (!$minute) {
            $this->flash('danger', 'Minutes not found');
            $this->redirect('/admin/board-minutes');
            return;
        }

        $validator = new Validator(
            [
                'meeting_date' => $this->input('meeting_date'),
            ],
            [
                'meeting_date' => 'required|date',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/board-minutes/' . $mid . '/edit');
            return;
        }

        $filePath = $minute['file_path'];
        $filenameOriginal = $minute['filename'];
        if (!empty($_FILES['pdf']['name']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            if ($mime !== 'application/pdf') {
                $this->flash('danger', 'Only PDF files are allowed');
                $this->redirect('/admin/board-minutes/' . $mid . '/edit');
                return;
            }
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->flash('danger', 'PDF file too large (max 10MB)');
                $this->redirect('/admin/board-minutes/' . $mid . '/edit');
                return;
            }
            $ext = 'pdf';
            $newFilename = uniqid('minutes_', true) . '.' . $ext;
            $uploadDir = BASE_PATH . '/public/uploads/board_minutes';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $newFilename);
            $filePath = '/uploads/board_minutes/' . $newFilename;
            $filenameOriginal = $file['name'];
        }

        BoardMinute::update($mid, [
            'meeting_date' => $this->input('meeting_date'),
            'filename' => $filenameOriginal,
            'file_path' => $filePath,
        ]);

        $this->logService->add('info', 'Board minutes updated', ['id' => $mid]);
        $this->flash('success', 'Minutes updated');
        $this->redirect('/admin/board-minutes');
    }

    public function destroy(string $id): void
    {
        $mid = (int) $id;
        $minute = BoardMinute::find($mid);
        if (!$minute) {
            $this->flash('danger', 'Minutes not found');
            $this->redirect('/admin/board-minutes');
            return;
        }
        // attempt to remove the file from disk as well
        if (!empty($minute['file_path'])) {
            $full = BASE_PATH . '/public' . $minute['file_path'];
            if (is_file($full)) {
                @unlink($full);
            }
        }

        BoardMinute::delete($mid);
        $this->logService->add('info', 'Board minutes deleted', ['id' => $mid]);
        $this->flash('success', 'Minutes removed');
        $this->redirect('/admin/board-minutes');
    }
}
