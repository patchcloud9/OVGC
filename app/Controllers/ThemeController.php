<?php

namespace App\Controllers;

use App\Models\ThemeSetting;

/**
 * Theme Controller
 * 
 * Manages site-wide theme customization (admin only).
 */
class ThemeController extends Controller
{
    /**
     * Show theme settings form
     * Route: GET /admin/theme
     */
    public function index(): void
    {
        $theme = ThemeSetting::getSiteTheme();
        
        // If no theme exists yet, create default
        if (!$theme) {
            ThemeSetting::createInitialTheme([]);
            $theme = ThemeSetting::getSiteTheme();
        }
        
        $this->view('admin/theme', [
            'title' => 'Theme Settings',
            'theme' => $theme
        ]);
    }
    
    /**
     * Update theme settings
     * Route: POST /admin/theme
     * Middleware: auth, role:admin, csrf
     */
    public function update(): void
    {
        // Validate input
        $validator = new \Core\Validator($_POST, [
            'primary_color' => 'required',
            'secondary_color' => 'required',
            'accent_color' => 'required',
            'header_style' => 'required|in:fixed,static',
            'card_style' => 'required|in:default,elevated,flat',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = reset($errors)[0];
            $this->flash('error', $firstError);
            $this->redirect('/admin/theme');
            return;
        }
        
        // Prepare update data
        $updateData = [
            'primary_color' => $this->input('primary_color'),
            'secondary_color' => $this->input('secondary_color'),
            'accent_color' => $this->input('accent_color'),
            'danger_color' => $this->input('danger_color'),            'navbar_color' => $this->input('navbar_color'),
            'navbar_hover_color' => $this->input('navbar_hover_color'),            'navbar_text_color' => $this->input('navbar_text_color'),
            'hero_background_color' => $this->input('hero_background_color'),
            'site_name' => $this->input('site_name'),
            'gallery_contact_email' => $this->input('gallery_contact_email'),
            'footer_tagline' => $this->input('footer_tagline'),
            'header_style' => $this->input('header_style'),
            'card_style' => $this->input('card_style'),
        ];
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = $this->handleFileUpload($_FILES['logo'], 'logo');
            if ($logoPath) {
                $updateData['logo_path'] = $logoPath;
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $faviconPath = $this->handleFileUpload($_FILES['favicon'], 'favicon');
            if ($faviconPath) {
                $updateData['favicon_path'] = $faviconPath;
            }
        }
        
        // Handle hero background image upload
        if (isset($_FILES['hero_background']) && $_FILES['hero_background']['error'] === UPLOAD_ERR_OK) {
            $heroPath = $this->handleFileUpload($_FILES['hero_background'], 'hero_background');
            if ($heroPath) {
                $updateData['hero_background_image'] = $heroPath;
            }
        }
        
        // Update theme
        if (ThemeSetting::updateTheme($updateData)) {
            $this->flash('success', 'Theme settings updated successfully!');
        } else {
            $this->flash('error', 'Failed to update theme settings.');
        }
        
        $this->redirect('/admin/theme');
    }
    
    /**
     * Handle file upload for logo/favicon
     * 
     * @param array $file $_FILES array element
     * @param string $type 'logo' or 'favicon'
     * @return string|null File path on success, null on failure
     */
    private function handleFileUpload(array $file, string $type): ?string
    {
        // Ensure file was uploaded via HTTP POST
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->flash('error', 'No valid uploaded file provided for ' . $type);
            return null;
        }

        // Determine the real MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);

        // Allowed types (note: SVGs are excluded by default for security reasons)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $iconTypes = ['image/x-icon', 'image/vnd.microsoft.icon'];

        if ($type === 'favicon') {
            $allowedTypes = array_merge($allowedTypes, $iconTypes);
        }

        if (!in_array($mime, $allowedTypes)) {
            $logService = new \App\Services\LogService();
            $logService->add('warning', 'Theme upload failed - invalid mime type', [
                'type' => $type,
                'mime' => $mime,
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('error', 'Invalid file type for ' . $type);
            return null;
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $logService = new \App\Services\LogService();
            $logService->add('warning', 'Theme upload failed - file too large', [
                'type' => $type,
                'size' => $file['size'],
                'original_name' => sanitize_for_log(['name' => $file['name']])['name'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('error', ucfirst($type) . ' file size must be less than 2MB');
            return null;
        }

        // Create upload directory if it doesn't exist
        $uploadDir = BASE_PATH . '/public/uploads/theme/';
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true)) {
            error_log("Failed to create upload directory: {$uploadDir}");
            $this->flash('error', 'Upload directory not writable. Please check server permissions.');
            return null;
        }

        // Verify directory is writable
        if (!is_writable($uploadDir)) {
            error_log("Upload directory not writable: {$uploadDir}");
            $this->flash('error', 'Upload directory not writable. Please check server permissions.');
            return null;
        }

        // Generate secure random filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
        $filename = $type . '_' . bin2hex(random_bytes(16)) . '.' . $safeExt;
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
            error_log("Failed to move uploaded file to: {$targetPath}");
            $this->flash('error', 'Failed to upload ' . $type . '. Please check server permissions.');
            return null;
        }

        // Successful upload — return public path
        return '/uploads/theme/' . $filename; 
    }
    
    /**
     * Reset theme settings to defaults
     * Route: POST /admin/theme/reset
     * Middleware: auth, role:admin, csrf
     */
    public function reset(): void
    {
        // Default theme values
        $defaults = [
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'accent_color' => '#48c78e',
            'navbar_color' => '#667eea',
            'navbar_hover_color' => '#ffffff',
            'navbar_text_color' => '#ffffff',
            'hero_background_color' => null,
            'hero_background_image' => null,
            'logo_path' => null,
            'favicon_path' => null,
            'header_style' => 'static',
            'card_style' => 'default',
        ];
        
        if (ThemeSetting::updateTheme($defaults)) {
            $this->flash('success', 'Theme settings have been reset to defaults.');
        } else {
            $this->flash('error', 'Failed to reset theme settings.');
        }
        
        $this->redirect('/admin/theme');
    }
}
