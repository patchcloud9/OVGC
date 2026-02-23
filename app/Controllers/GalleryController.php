<?php

namespace App\Controllers;

use App\Models\GalleryImage;
use App\Services\LogService;
use Core\Validator;

/**
 * Gallery Controller
 * 
 * Handles both public gallery viewing and admin management.
 * 
 * Public routes:
 * - GET /gallery - View all images
 * - GET /gallery/{id} - View single image
 * 
 * Admin routes:
 * - GET /admin/gallery - Manage images (upload/delete)
 * - POST /admin/gallery - Upload new image
 * - DELETE /admin/gallery/{id} - Delete image
 */
class GalleryController extends Controller
{
    private LogService $logService;
    
    public function __construct()
    {
        $this->logService = new LogService();
    }
    
    /**
     * Public gallery - show all images in card layout
     */
    public function index(): void
    {
        // Get page from query string, default to 1
        $page = (int) ($this->query('page') ?? 1);
        
        // Get paginated images
        $pagination = GalleryImage::paginate($page, 12);
        
        $this->view('gallery/index', [
            'title' => 'Gallery',
            'images' => $pagination['images'],
            'currentPage' => $pagination['page'],
            'totalPages' => $pagination['totalPages'],
            'total' => $pagination['total']
        ]);
    }
    
    /**
     * Show single image with details
     */
    public function show(string $id): void
    {
        $imageId = (int) $id;
        $image = GalleryImage::findWithUploader($imageId);
        
        if (!$image) {
            throw new \Core\Exceptions\NotFoundHttpException("Image not found");
        }
        
        $this->view('gallery/show', [
            'title' => $image['title'],
            'image' => $image
        ]);
    }
    
    /**
     * Admin gallery management page
     */
    public function adminIndex(): void
    {
        $images = GalleryImage::allWithUploaders();
        $stats = [
            'total' => count($images),
            'recent' => count(array_filter($images, function($img) {
                return strtotime($img['created_at']) > strtotime('-7 days');
            }))
        ];
        
        $this->view('gallery/admin', [
            'title' => 'Manage Gallery',
            'images' => $images,
            'stats' => $stats
        ]);
    }
    
    /**
     * Upload new image (admin only)
     */
    public function store(): void
    {
        // Validate form input
        $validator = new Validator(
            [
                'title' => $this->input('title'),
                'description' => $this->input('description'),
                'price_type' => $this->input('price_type'),
                'price_amount' => $this->input('price_amount'),
                'prints_url' => $this->input('prints_url'),
            ],
            [
                'title' => 'required|min:3|max:255',
                'description' => 'max:1000',
                'price_type' => 'required|in:hide,amount,sold_prints,not_for_sale',
                'price_amount' => 'numeric',
                'prints_url' => 'url',
            ]
        );
        
        if ($validator->fails()) {
            // Flatten nested errors array
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Validate file upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            // Log the upload error
            $this->logService->add('warning', 'Gallery upload failed - no file or upload error', [
                'user_id' => auth_user()['id'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('danger', 'No image uploaded or upload error occurred');
            $this->redirect('/admin/gallery');
            return;
        }
        
        $file = $_FILES['image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        // Note: finfo_close() is deprecated in PHP 8.5+ and resources are freed automatically
        
        if (!in_array($mimeType, $allowedTypes)) {
            $this->logService->add('warning', 'Gallery upload failed - invalid mime type', [
                'user_id' => auth_user()['id'] ?? null,
                'mime' => $mimeType,
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('danger', 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->logService->add('warning', 'Gallery upload failed - file too large', [
                'user_id' => auth_user()['id'] ?? null,
                'size' => $file['size'],
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('danger', 'File size too large. Maximum 5MB allowed.');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('gallery_', true) . '.' . $extension;
        
        // Upload directory
        $uploadDir = BASE_PATH . '/public/uploads/gallery';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0775, true)) {
                $this->logService->add('error', 'Gallery upload failed - could not create upload directory', [
                    'upload_dir' => $uploadDir,
                    'user_id' => auth_user()['id'] ?? null,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                $this->flash('danger', 'Failed to create upload directory. Please check permissions.');
                $this->redirect('/admin/gallery');
                return;
            }
        }

        // Check if directory is writable
        if (!is_writable($uploadDir)) {
            $this->logService->add('error', 'Gallery upload failed - upload directory not writable', [
                'upload_dir' => $uploadDir,
                'user_id' => auth_user()['id'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return;
        }
        
        $filePath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->logService->add('error', 'Gallery upload failed - move_uploaded_file failed', [
                'user_id' => auth_user()['id'] ?? null,
                'target' => $filePath,
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('danger', 'Failed to save uploaded file. Please check directory permissions.');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Save to database
        $image = GalleryImage::create([
            'title' => $this->input('title'),
            'description' => $this->input('description') ?? '',
            'filename' => $filename,
            'file_path' => '/uploads/gallery/' . $filename,
            'uploaded_by' => auth_user()['id'],
            'display_order' => GalleryImage::getNextDisplayOrder(),
            'price_type' => $this->input('price_type') ?? 'hide',
            'price_amount' => $this->input('price_amount') ?: null,
            'prints_available' => $this->input('prints_available') === '1' ? 1 : 0,
            'prints_url' => $this->input('prints_url') ?: null,
        ]);
        
        // Log the action
        $this->logService->add('info', 'Gallery image uploaded', [
            'image_id' => $image['id'],
            'title' => $image['title'],
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        $this->flash('success', 'Image uploaded successfully!');
        $this->redirect('/admin/gallery');
    }
    
    /**     * Show edit form for existing image (admin only)
     */
    public function edit(string $id): void
    {
        $imageId = (int) $id;
        $image = GalleryImage::find($imageId);
        
        if (!$image) {
            $this->flash('danger', 'Image not found');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Get uploader name
        $uploader = \App\Models\User::find($image['uploaded_by']);
        $image['uploader_name'] = $uploader['name'] ?? 'Unknown';
        
        $this->view('gallery/edit', [
            'title' => 'Edit Gallery Image',
            'image' => $image
        ]);
    }
    
    /**
     * Update existing image metadata (admin only)
     */
    public function update(string $id): void
    {
        $imageId = (int) $id;
        $image = GalleryImage::find($imageId);
        
        if (!$image) {
            $this->flash('danger', 'Image not found');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Validate form input
        $validator = new Validator(
            [
                'title' => $this->input('title'),
                'description' => $this->input('description'),
                'price_type' => $this->input('price_type'),
                'price_amount' => $this->input('price_amount'),
                'prints_url' => $this->input('prints_url'),
            ],
            [
                'title' => 'required|min:3|max:255',
                'description' => 'max:1000',
                'price_type' => 'required|in:hide,amount,sold_prints,not_for_sale',
                'price_amount' => 'numeric',
                'prints_url' => 'url',
            ]
        );
        
        if ($validator->fails()) {
            // Flatten nested errors array
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            $this->redirect('/admin/gallery/' . $imageId . '/edit');
            return;
        }
        
        // Update the image metadata
        $success = GalleryImage::update($imageId, [
            'title' => $this->input('title'),
            'description' => $this->input('description') ?? '',
            'price_type' => $this->input('price_type') ?? 'hide',
            'price_amount' => $this->input('price_amount') ?: null,
            'prints_available' => $this->input('prints_available') === '1' ? 1 : 0,
            'prints_url' => $this->input('prints_url') ?: null,
        ]);
        
        if ($success) {
            // Log the action
            $this->logService->add('info', 'Gallery image updated', [
                'image_id' => $imageId,
                'title' => $this->input('title'),
                'user_id' => auth_user()['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            $this->flash('success', 'Image updated successfully!');
        } else {
            $this->flash('warning', 'No changes were made');
        }
        
        $this->redirect('/admin/gallery');
    }
    
    /**     * Delete image (admin only)
     */
    public function destroy(string $id): void
    {
        $imageId = (int) $id;
        $image = GalleryImage::find($imageId);
        
        if (!$image) {
            $this->flash('danger', 'Image not found');
            $this->redirect('/admin/gallery');
            return;
        }
        
        // Delete physical file
        $filePath = BASE_PATH . '/public' . $image['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        GalleryImage::delete($imageId);
        
        // Log the action
        $this->logService->add('info', 'Gallery image deleted', [
            'image_id' => $imageId,
            'title' => $image['title'],
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        $this->flash('success', 'Image deleted successfully');
        $this->redirect('/admin/gallery');
    }
    
    /**
     * Reorder images (admin only)
     */
    public function reorder(): void
    {
        $imageId = (int) $this->input('image_id');
        $direction = $this->input('direction');
        
        if (!in_array($direction, ['up', 'down'])) {
            $this->flash('danger', 'Invalid direction');
            $this->redirect('/admin/gallery');
            return;
        }
        
        $success = GalleryImage::swapOrder($imageId, $direction);
        
        if ($success) {
            $this->flash('success', 'Image order updated');
        } else {
            $this->flash('info', 'Image is already at the ' . ($direction === 'up' ? 'top' : 'bottom'));
        }
        
        // Log the action
        $this->logService->add('info', 'Gallery image reordered', [
            'image_id' => $imageId,
            'direction' => $direction,
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        $this->redirect('/admin/gallery');
    }
}
