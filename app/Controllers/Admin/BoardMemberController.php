<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\BoardMember;
use App\Services\LogService;
use Core\Validator;

class BoardMemberController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    public function index(): void
    {
        $members = BoardMember::allOrdered();
        $this->view('admin/board_members/index', [
            'title' => 'Board Members',
            'members' => $members,
        ]);
    }

    public function create(): void
    {
        $this->view('admin/board_members/create', [
            'title' => 'Add Board Member',
        ]);
    }

    public function store(): void
    {
        $validator = new Validator(
            [
                'name' => $this->input('name'),
                'title' => $this->input('title'),
                'email' => $this->input('email'),
                'sort_order' => $this->input('sort_order'),
            ],
            [
                'name' => 'required|max:100',
                'title' => 'required|max:100',
                'email' => 'required|email|max:255',
                'sort_order' => 'numeric',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/board-members/create');
            return;
        }

        // handle photo upload if provided
        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            if (!in_array($mime, $allowed)) {
                $this->flash('danger', 'Photo must be an image (jpg, png, gif, webp)');
                $this->redirect('/admin/board-members/create');
                return;
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->flash('danger', 'Photo file too large (max 5MB)');
                $this->redirect('/admin/board-members/create');
                return;
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('member_', true) . '.' . $ext;
            $uploadDir = BASE_PATH . '/public/uploads/board_members';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename);
            $photoPath = '/uploads/board_members/' . $filename;
        }

        $member = BoardMember::create([
            'name' => $this->input('name'),
            'title' => $this->input('title'),
            'email' => $this->input('email'),
            'photo_path' => $photoPath,
            'sort_order' => (int) $this->input('sort_order'),
        ]);

        $this->logService->add('info', 'Board member created', ['member_id' => $member['id']]);
        $this->flash('success', 'Board member added');
        $this->redirect('/admin/board-members');
    }

    public function edit(string $id): void
    {
        $mid = (int) $id;
        $member = BoardMember::find($mid);
        if (!$member) {
            $this->flash('danger', 'Member not found');
            $this->redirect('/admin/board-members');
            return;
        }
        $this->view('admin/board_members/edit', [
            'title' => 'Edit Board Member',
            'member' => $member,
        ]);
    }

    public function update(string $id): void
    {
        $mid = (int) $id;
        $member = BoardMember::find($mid);
        if (!$member) {
            $this->flash('danger', 'Member not found');
            $this->redirect('/admin/board-members');
            return;
        }

        $validator = new Validator(
            [
                'name' => $this->input('name'),
                'title' => $this->input('title'),
                'email' => $this->input('email'),
                'sort_order' => $this->input('sort_order'),
            ],
            [
                'name' => 'required|max:100',
                'title' => 'required|max:100',
                'email' => 'required|email|max:255',
                'sort_order' => 'numeric',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/board-members/' . $mid . '/edit');
            return;
        }

        $photoPath = $member['photo_path'];
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            if (!in_array($mime, $allowed)) {
                $this->flash('danger', 'Photo must be an image (jpg, png, gif, webp)');
                $this->redirect('/admin/board-members/' . $mid . '/edit');
                return;
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->flash('danger', 'Photo file too large (max 5MB)');
                $this->redirect('/admin/board-members/' . $mid . '/edit');
                return;
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('member_', true) . '.' . $ext;
            $uploadDir = BASE_PATH . '/public/uploads/board_members';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename);
            $newPath = '/uploads/board_members/' . $filename;
            // remove old file if present
            if (!empty($photoPath)) {
                $oldFull = BASE_PATH . '/public' . $photoPath;
                if (is_file($oldFull)) {
                    @unlink($oldFull);
                }
            }
            $photoPath = $newPath;
        }

        BoardMember::update($mid, [
            'name' => $this->input('name'),
            'title' => $this->input('title'),
            'email' => $this->input('email'),
            'photo_path' => $photoPath,
            'sort_order' => (int) $this->input('sort_order'),
        ]);

        $this->logService->add('info', 'Board member updated', ['member_id' => $mid]);
        $this->flash('success', 'Member updated');
        $this->redirect('/admin/board-members');
    }

    public function destroy(string $id): void
    {
        $mid = (int) $id;
        $member = BoardMember::find($mid);
        if (!$member) {
            $this->flash('danger', 'Member not found');
            $this->redirect('/admin/board-members');
            return;
        }
        // delete photo if exists
        if (!empty($member['photo_path'])) {
            $full = BASE_PATH . '/public' . $member['photo_path'];
            if (is_file($full)) {
                @unlink($full);
            }
        }
        BoardMember::delete($mid);
        $this->logService->add('info', 'Board member deleted', ['member_id' => $mid]);
        $this->flash('success', 'Member removed');
        $this->redirect('/admin/board-members');
    }
}
