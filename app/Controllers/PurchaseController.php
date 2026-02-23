<?php

namespace App\Controllers;

use App\Models\PurchaseContent;
use App\Services\LogService;
use Core\Validator;

/**
 * Purchase Controller
 *
 * Handles both public purchase page display and admin content management.
 */
class PurchaseController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    /**
     * Show public purchase page
     * Route: GET /purchase
     */
    public function index(): void
    {
        $content = PurchaseContent::getContent();

        $this->view('purchase/index', [
            'title' => $content['page_title'] ?? 'Purchase',
            'content' => $content,
        ]);
    }

    /**
     * Show admin edit form
     * Route: GET /admin/purchase
     * Middleware: auth, role:admin
     */
    public function edit(): void
    {
        $content = PurchaseContent::getContent();

        $this->view('purchase/admin', [
            'title' => 'Edit Purchase Page',
            'content' => $content,
        ]);
    }

    /**
     * Update purchase content
     * Route: POST /admin/purchase
     * Middleware: auth, role:admin, csrf
     */
    public function update(): void
    {
        // Validate inputs
        $validator = new Validator(
            [
                'page_title' => $this->input('page_title'),
                'page_subtitle' => $this->input('page_subtitle'),
                'content_text' => $this->input('content_text'),
                'contact_email' => $this->input('contact_email'),
                'button_text' => $this->input('button_text'),
                'button_url' => $this->input('button_url'),
            ],
            [
                'page_title' => 'required|max:100',
                'page_subtitle' => 'max:255',
                'contact_email' => 'email|max:255',
                'button_text' => 'max:100',
                'button_url' => 'max:255',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            $this->redirect('/admin/purchase');
            return;
        }

        // Prepare update data
        $updateData = [
            'page_title' => $this->input('page_title'),
            'page_subtitle' => $this->input('page_subtitle'),
            'content_text' => $this->input('content_text'),
            'contact_email' => $this->input('contact_email'),
            'button_text' => $this->input('button_text'),
            'button_url' => $this->input('button_url'),
        ];

        // Update content
        $success = PurchaseContent::updateContent($updateData);

        if ($success) {
            // Log the action
            $this->logService->add('info', 'Purchase page content updated', [
                'user_id' => auth_user()['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('success', 'Purchase page updated successfully!');
        } else {
            $this->flash('danger', 'Failed to update purchase page');
        }

        $this->redirect('/admin/purchase');
    }
}
