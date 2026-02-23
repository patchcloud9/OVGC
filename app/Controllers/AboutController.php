<?php

namespace App\Controllers;

use App\Models\AboutContent;
use App\Services\LogService;
use Core\Validator;

/**
 * About Controller
 *
 * Handles both public about page display and admin content management.
 */
class AboutController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    /**
     * Show public about page
     * Route: GET /about
     */
    public function index(): void
    {
        $content = AboutContent::getContent();

        $this->view('about/index', [
            'title' => $content['page_title'] ?? 'About',
            'content' => $content,
        ]);
    }

    /**
     * Show admin edit form
     * Route: GET /admin/about
     * Middleware: auth, role:admin
     */
    public function edit(): void
    {
        $content = AboutContent::getContent();

        $this->view('about/admin', [
            'title' => 'Edit About Page',
            'content' => $content,
        ]);
    }

    /**
     * Update about content
     * Route: POST /admin/about
     * Middleware: auth, role:admin, csrf
     */
    public function update(): void
    {
        // Validate text inputs
        $validator = new Validator(
            [
                'page_title' => $this->input('page_title'),
                'page_subtitle' => $this->input('page_subtitle'),
                'section1_text' => $this->input('section1_text'),
                'section2_text' => $this->input('section2_text'),
                'artist_signature' => $this->input('artist_signature'),
            ],
            [
                'page_title' => 'required|max:100',
                'page_subtitle' => 'max:255',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            $this->redirect('/admin/about');
            return;
        }

        $currentContent = AboutContent::getContent();

        // Prepare update data
        $updateData = [
            'page_title' => $this->input('page_title'),
            'page_subtitle' => $this->input('page_subtitle'),
            'section1_text' => $this->input('section1_text'),
            'section1_image_position' => $this->input('section1_image_position', 'left'),
            'section1_text_align_h' => $this->input('section1_text_align_h', 'left'),
            'section1_text_align_v' => $this->input('section1_text_align_v', 'top'),
            'section2_text' => $this->input('section2_text'),
            'section2_image_position' => $this->input('section2_image_position', 'left'),
            'section2_text_align_h' => $this->input('section2_text_align_h', 'left'),
            'section2_text_align_v' => $this->input('section2_text_align_v', 'top'),
            'artist_signature' => $this->input('artist_signature'),
        ];

        // Handle section 1 image upload
        if (isset($_FILES['section1_image']) && $_FILES['section1_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload('section1_image', 'section1');
            if ($imagePath) {
                $updateData['section1_image'] = $imagePath;
            }
        }

        // Handle section 2 image upload
        if (isset($_FILES['section2_image']) && $_FILES['section2_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload('section2_image', 'section2');
            if ($imagePath) {
                $updateData['section2_image'] = $imagePath;
            }
        }

        // Update content
        $success = AboutContent::updateContent($updateData);

        if ($success) {
            // Log the action
            $this->logService->add('info', 'About page content updated', [
                'user_id' => auth_user()['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('success', 'About page updated successfully!');
        } else {
            $this->flash('danger', 'Failed to update about page');
        }

        $this->redirect('/admin/about');
    }

    /**
     * Clear section image
     * Route: POST /admin/about/clear-image
     * Middleware: auth, role:admin, csrf
     */
    public function clearImage(): void
    {
        $section = $this->input('section');

        if (!in_array($section, ['section1', 'section2'])) {
            $this->flash('danger', 'Invalid section');
            $this->redirect('/admin/about');
            return;
        }

        $field = $section . '_image';
        $content = AboutContent::getContent();

        // Delete the physical file if it exists
        if (!empty($content[$field])) {
            $filePath = BASE_PATH . '/public' . $content[$field];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Update database to clear image path
        AboutContent::updateContent([$field => null]);

        $this->flash('success', 'Image removed successfully');
        $this->redirect('/admin/about');
    }

    /**
     * Handle image upload for about sections
     *
     * @param string $fileKey The $_FILES key
     * @param string $prefix Filename prefix (section1 or section2)
     * @return string|null The public path to the uploaded image
     */
    private function handleImageUpload(string $fileKey, string $prefix): ?string
    {
        $file = $_FILES[$fileKey];

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedTypes)) {
            $this->flash('danger', 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.');
            return null;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->flash('danger', 'Image too large. Maximum size is 5MB.');
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . bin2hex(random_bytes(16)) . '.' . $extension;

        // Create upload directory if it doesn't exist
        $uploadDir = BASE_PATH . '/public/uploads/about';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Move uploaded file
        $uploadPath = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return '/uploads/about/' . $filename;
        }

        $this->flash('danger', 'Failed to upload image');
        return null;
    }
}
