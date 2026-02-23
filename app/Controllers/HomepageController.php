<?php

namespace App\Controllers;

use App\Models\HomepageSetting;
use Core\Validator;

/**
 * Homepage Controller
 * 
 * Manages homepage customization (admin only).
 */
class HomepageController extends Controller
{
    /**
     * Show homepage settings form
     * Route: GET /admin/homepage
     */
    public function index(): void
    {
        $settings = HomepageSetting::getSettings();
        
        // If no settings exist yet, create defaults
        if (!$settings) {
            $this->flash('info', 'No homepage settings found. Using defaults.');
        }
        
        $this->view('admin/homepage', [
            'title' => 'Homepage Settings',
            'settings' => $settings
        ]);
    }
    
    /**
     * Update homepage settings
     * Route: POST /admin/homepage
     * Middleware: auth, role:admin, csrf
     */
    public function update(): void
    {
        // Debug: Log that we're in the update method (only in debug mode)
        if (is_debug()) {
            error_log("HomepageController::update() called");
            error_log("POST data: " . print_r($_POST, true));
        }
        
        // Validate input
        $validator = new Validator($_POST, [
            'hero_title' => 'required|max:100',
            'hero_subtitle' => 'max:255',
            'card1_title' => 'required|max:100',
            'card2_title' => 'required|max:100',
            'card3_title' => 'required|max:100',
            'card1_button_text' => 'max:100',
            'card1_button_link' => 'max:255',
            'card2_button_text' => 'max:100',
            'card2_button_link' => 'max:255',
            'card3_button_text' => 'max:100',
            'card3_button_link' => 'max:255',
            'bottom_section_layout' => 'required|in:text-image,image-text',
            'bottom_section_title' => 'required|max:255',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (is_debug()) {
                error_log("Validation failed: " . print_r($errors, true));
            }
            $firstError = reset($errors)[0];
            $this->flash('error', $firstError);
            $this->redirect('/admin/homepage');
            return;
        }
        
        if (is_debug()) {
            error_log("Validation passed");
        }
        
        // Prepare update data
        $updateData = [
            'hero_title' => $this->input('hero_title'),
            'hero_subtitle' => $this->input('hero_subtitle'),
            'hero_title_color' => $this->input('hero_title_color'),
            'hero_subtitle_color' => $this->input('hero_subtitle_color'),
            'hero_background_color' => $this->input('hero_background_color'),
            'card1_title' => $this->input('card1_title'),
            'card1_text' => $this->input('card1_text'),
            'card2_title' => $this->input('card2_title'),
            'card2_text' => $this->input('card2_text'),
            'card3_title' => $this->input('card3_title'),
            'card3_text' => $this->input('card3_text'),
            'card1_button_text' => $this->input('card1_button_text'),
            'card1_button_link' => $this->input('card1_button_link'),
            'card2_button_text' => $this->input('card2_button_text'),
            'card2_button_link' => $this->input('card2_button_link'),
            'card3_button_text' => $this->input('card3_button_text'),
            'card3_button_link' => $this->input('card3_button_link'),
            'bottom_section_layout' => $this->input('bottom_section_layout'),
            'bottom_section_title' => $this->input('bottom_section_title'),
            'bottom_section_text' => $this->input('bottom_section_text'),
        ];
        
        // Get existing settings to preserve image paths if not uploading new ones
        $existingSettings = HomepageSetting::getSettings();
        $uploadErrors = [];

        // Normalize color inputs (support hex typed in the paired text inputs)
        $updateData['hero_title_color'] = $this->normalizeHexColor($updateData['hero_title_color'], $this->input('hero_title_color_text'), '#FFFFFF');
        $updateData['hero_subtitle_color'] = $this->normalizeHexColor($updateData['hero_subtitle_color'], $this->input('hero_subtitle_color_text'), '#F5F5F5');
        $updateData['hero_background_color'] = $this->normalizeHexColor($updateData['hero_background_color'], $this->input('hero_background_color_text'), '#667EEA');
        
        // Handle hero background image upload
        if (isset($_FILES['hero_background_image']) && $_FILES['hero_background_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            // File was selected (even if upload failed)
            $imagePath = $this->handleFileUpload($_FILES['hero_background_image'], 'hero background');
            if ($imagePath) {
                $updateData['hero_background_image'] = $imagePath;
            } else {
                $uploadErrors[] = 'hero background';
            }
        } elseif (!empty($existingSettings['hero_background_image'])) {
            // No new file selected, preserve existing hero image
            $updateData['hero_background_image'] = $existingSettings['hero_background_image'];
        }
        
        // Handle bottom section image upload
        if (isset($_FILES['bottom_section_image']) && $_FILES['bottom_section_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            // File was selected (even if upload failed)
            $imagePath = $this->handleFileUpload($_FILES['bottom_section_image'], 'bottom section');
            if ($imagePath) {
                $updateData['bottom_section_image'] = $imagePath;
            } else {
                $uploadErrors[] = 'bottom section';
            }
        } elseif (!empty($existingSettings['bottom_section_image'])) {
            // No new file selected, preserve existing bottom section image
            $updateData['bottom_section_image'] = $existingSettings['bottom_section_image'];
        }
        
        // Update settings
        if (is_debug()) {
            error_log("Attempting to update settings...");
            error_log("Update data: " . print_r($updateData, true));
        }

        $result = HomepageSetting::updateSettings($updateData);
        if (is_debug()) {
            error_log("Update result: " . ($result ? 'true' : 'false'));
        }
        
        if ($result) {
            if (!empty($uploadErrors)) {
                // Settings saved but some images failed
                $this->flash('warning', 'Settings saved but some images failed to upload (check errors above)');
            } else {
                $this->flash('success', 'Homepage settings updated successfully!');
            }
        } else {
            $this->flash('error', 'Failed to update homepage settings');
        }
        
        $this->redirect('/admin/homepage');
    }
    
    /**
     * Clear hero background image
     * Route: POST /admin/homepage/clear-hero-image
     * Middleware: auth, role:admin, csrf
     */
    public function clearHeroImage(): void
    {
        $settings = HomepageSetting::getSettings();
        
        if ($settings && !empty($settings['hero_background_image'])) {
            // Delete the physical file
            $filePath = BASE_PATH . '/public' . $settings['hero_background_image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            // Update database - use Model::update directly to only update this field
            HomepageSetting::update($settings['id'], ['hero_background_image' => '']);
            $this->flash('success', 'Hero background image cleared successfully!');
        } else {
            $this->flash('info', 'No hero background image to clear');
        }
        
        $this->redirect('/admin/homepage');
    }
    
    /**
     * Clear bottom section image
     * Route: POST /admin/homepage/clear-bottom-image
     * Middleware: auth, role:admin, csrf
     */
    public function clearBottomImage(): void
    {
        $settings = HomepageSetting::getSettings();
        
        if ($settings && !empty($settings['bottom_section_image'])) {
            // Delete the physical file
            $filePath = BASE_PATH . '/public' . $settings['bottom_section_image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            // Update database - use Model::update directly to only update this field
            HomepageSetting::update($settings['id'], ['bottom_section_image' => '']);
            $this->flash('success', 'Bottom section image cleared successfully!');
        } else {
            $this->flash('info', 'No bottom section image to clear');
        }
        
        $this->redirect('/admin/homepage');
    }
    
    /**
     * Normalize a hex color value. Uses $primary if both inputs are empty/invalid.
     */
    private function normalizeHexColor(?string $valueFromPicker, ?string $valueFromText, string $default = '#FFFFFF'): string
    {
        $val = $valueFromPicker ?? $valueFromText ?? '';
        $val = strtoupper(trim((string)$val));

        if (preg_match('/^#[0-9A-F]{6}$/i', $val)) {
            return $val;
        }

        // If the picker provided an invalid value but the text input has a valid one, use it
        $textVal = strtoupper(trim((string)$valueFromText));
        if (preg_match('/^#[0-9A-F]{6}$/i', $textVal)) {
            return $textVal;
        }

        return $default;
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload(array $file, string $prefix): ?string
    {
        // Check for upload errors first
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Image exceeds PHP upload_max_filesize (' . ini_get('upload_max_filesize') . ')',
                UPLOAD_ERR_FORM_SIZE => 'Image exceeds form MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'Image was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No image was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write image to disk',
                UPLOAD_ERR_EXTENSION => 'Upload blocked by PHP extension',
            ];
            
            $message = $errorMessages[$file['error']] ?? 'Unknown upload error';

            // Log the upload failure
            $logService = new \App\Services\LogService();
            $logService->add('warning', 'Homepage upload failed - upload error', [
                'prefix' => $prefix,
                'error' => $message,
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('error', $prefix . ' upload failed: ' . $message);
            return null;
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        // Note: finfo_close() is deprecated in PHP 8.5+ and unnecessary (auto-freed)
        
        if (!in_array($mimeType, $allowedTypes)) {
            $this->flash('warning', 'Invalid image type for ' . $prefix . '. Only JPG, PNG, GIF, and WebP allowed.');
            return null;
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->flash('warning', 'Image too large for ' . $prefix . '. Maximum 5MB allowed.');
            return null;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . uniqid() . '.' . $extension;
        
        // Upload directory
        $uploadDir = BASE_PATH . '/public/uploads/homepage';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        
        $filePath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return '/uploads/homepage/' . $filename;
        }
        
        $this->flash('warning', 'Failed to upload ' . $prefix . ' image');
        return null;
    }
}
