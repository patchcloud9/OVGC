<?php
/**
 * Theme Settings Admin View
 * 
 * Allows admins to customize site appearance:
 * - Color palette (primary, secondary, accent)
 * - Logo and favicon
 * - Header style (static/fixed)
 * - Card styling preferences
 */
?>

<!-- Hero for Theme Settings -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-palette"></i></span>
                    <span>Theme Settings</span>
                </span>
            </h1>
            <p class="subtitle is-6 has-text-white">Customize the appearance of your site</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-10-tablet is-8-desktop">
                
                <!-- Page Header -->
                <nav class="breadcrumb" aria-label="breadcrumbs">
                    <ul>
                        <li><a href="/admin">Admin</a></li>
                        <li class="is-active"><a href="#" aria-current="page">Theme Settings</a></li>
                    </ul>
                </nav>
                

                
                <!-- Flash Messages -->
                <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
                
                <!-- Theme Settings Form -->
                <form method="POST" action="/admin/theme" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="box">
                        <h2 class="title is-4">
                            <span class="icon-text">
                                <span class="icon has-text-info">
                                    <i class="fas fa-paint-brush"></i>
                                </span>
                                <span>Color Palette</span>
                            </span>
                        </h2>
                        
                        <!-- Primary Color -->
                        <div class="field">
                            <label class="label">Primary Color</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="primary_color" 
                                        value="<?= e($theme['primary_color'] ?? '#667eea') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="primary_color_text" 
                                        value="<?= e($theme['primary_color'] ?? '#667eea') ?>"
                                        placeholder="#667eea"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Standard button color (submit, save, etc.). Hover will be automatically lighter.</p>
                        </div>
                        
                        <!-- Secondary Color -->
                        <div class="field">
                            <label class="label">Secondary Color (Low Priority Buttons)</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="secondary_color" 
                                        value="<?= e($theme['secondary_color'] ?? '#b5b5b5') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="secondary_color_text" 
                                        value="<?= e($theme['secondary_color'] ?? '#b5b5b5') ?>"
                                        placeholder="#b5b5b5"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Low priority buttons (cancel, back, etc.)</p>
                        </div>
                        
                        <!-- Accent Color -->
                        <div class="field">
                            <label class="label">Accent Color (Messages & Links)</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="accent_color" 
                                        value="<?= e($theme['accent_color'] ?? '#48c78e') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="accent_color_text" 
                                        value="<?= e($theme['accent_color'] ?? '#48c78e') ?>"
                                        placeholder="#48c78e"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Success messages, standard links, and appropriate icons</p>
                        </div>
                        
                        <!-- Danger Color -->
                        <div class="field">
                            <label class="label">Danger Color (Destructive Actions)</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="danger_color" 
                                        value="<?= e($theme['danger_color'] ?? '#f14668') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="danger_color_text" 
                                        value="<?= e($theme['danger_color'] ?? '#f14668') ?>"
                                        placeholder="#f14668"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Important/destructive actions (delete, reset, etc.)</p>
                        </div>
                        
                        <!-- Navbar Color -->
                        <div class="field">
                            <label class="label">Navbar Background Color</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="navbar_color" 
                                        value="<?= e($theme['navbar_color'] ?? '#667eea') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="navbar_color_text" 
                                        value="<?= e($theme['navbar_color'] ?? '#667eea') ?>"
                                        placeholder="#667eea"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Background color for the navigation bar</p>
                        </div>
                        
                        <!-- Navbar Hover Color -->
                        <div class="field">
                            <label class="label">Navbar Hover Color</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="navbar_hover_color" 
                                        value="<?= e($theme['navbar_hover_color'] ?? '#ffffff') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="navbar_hover_color_text" 
                                        value="<?= e($theme['navbar_hover_color'] ?? '#ffffff') ?>"
                                        placeholder="#ffffff"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Color when hovering over navbar items</p>
                        </div>
                        
                        <!-- Navbar Text Color -->
                        <div class="field">
                            <label class="label">Navbar Text Color</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="navbar_text_color" 
                                        value="<?= e($theme['navbar_text_color'] ?? '#ffffff') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="navbar_text_color_text" 
                                        value="<?= e($theme['navbar_text_color'] ?? '#ffffff') ?>"
                                        placeholder="#ffffff"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Default text color for navbar links</p>
                        </div>
                        
                        <!-- Hero Background Color -->
                        <div class="field">
                            <label class="label">Hero Background Color (Optional)</label>
                            <div class="field has-addons">
                                <div class="control">
                                    <input 
                                        class="input color-preview" 
                                        type="color" 
                                        name="hero_background_color" 
                                        value="<?= e($theme['hero_background_color'] ?? '#667eea') ?>"
                                        style="width: 60px; height: 40px; cursor: pointer;">
                                </div>
                                <div class="control is-expanded">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        name="hero_background_color_text" 
                                        value="<?= e($theme['hero_background_color'] ?? '') ?>"
                                        placeholder="Leave empty to use gradient"
                                        readonly>
                                </div>
                            </div>
                            <p class="help">Solid color for hero section (overrides gradient if set)</p>
                        </div>
                    </div>
                    
                    <div class="box">
                        <h2 class="title is-4">
                            <span class="icon-text">
                                <span class="icon has-text-link">
                                    <i class="fas fa-image"></i>
                                </span>
                                <span>Branding Assets</span>
                            </span>
                        </h2>
                        
                        <!-- Homepage Logo Upload -->
                        <div class="field">
                            <label class="label">Homepage Logo</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input 
                                        class="file-input" 
                                        type="file" 
                                        name="homepage_logo" 
                                        accept="image/png,image/jpeg,image/svg+xml"
                                        onchange="updateFileName(this, 'homepage-logo-name')">
                                    <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">Choose logo…</span>
                                    </span>
                                    <span class="file-name" id="homepage-logo-name">
                                        <?php if (!empty($theme['homepage_logo_path'])): ?>
                                            <?= e(basename($theme['homepage_logo_path'])) ?>
                                        <?php else: ?>
                                            No file selected
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                            <p class="help">PNG, JPG, or SVG. Max 2MB. Recommended: 200x50px</p>
                            
                            <?php if (!empty($theme['homepage_logo_path'])): ?>
                                <div class="mt-3">
                                    <p class="has-text-weight-semibold mb-2">Current Logo:</p>
                                    <img src="<?= e($theme['homepage_logo_path']) ?>" alt="Current Logo" style="max-height: 60px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Secondary Logo (future use) -->
                        <div class="field">
                            <label class="label">Secondary Logo (Optional)</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input 
                                        class="file-input" 
                                        type="file" 
                                        name="secondary_logo" 
                                        accept="image/png,image/jpeg,image/svg+xml"
                                        onchange="updateFileName(this, 'secondary-logo-name')">
                                    <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">Choose logo…</span>
                                    </span>
                                    <span class="file-name" id="secondary-logo-name">
                                        <?php if (!empty($theme['secondary_logo_path'])): ?>
                                            <?= e(basename($theme['secondary_logo_path'])) ?>
                                        <?php else: ?>
                                            No file selected
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                            <p class="help">PNG, JPG, or SVG. Max 2MB. This file is stored for later use.</p>
                            <?php if (!empty($theme['secondary_logo_path'])): ?>
                                <div class="mt-3">
                                    <p class="has-text-weight-semibold mb-2">Current Secondary Logo:</p>
                                    <img src="<?= e($theme['secondary_logo_path']) ?>" alt="Secondary Logo" style="max-height: 60px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Site Name -->
                        <div class="field">
                            <label class="label">Site Name (Optional)</label>
                            <div class="control">
                                <input 
                                    class="input" 
                                    type="text" 
                                    name="site_name" 
                                    value="<?= e($theme['site_name'] ?? '') ?>"
                                    placeholder="Enter site name"
                                    maxlength="100">
                            </div>
                            <p class="help">Displayed in navigation after the logo. Leave empty to show nothing.</p>
                        </div>
                        
                        <!-- Gallery Contact Email -->
                        <div class="field">
                            <label class="label">Gallery Contact Email (Optional)</label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="email" 
                                    name="gallery_contact_email" 
                                    value="<?= e($theme['gallery_contact_email'] ?? '') ?>"
                                    placeholder="artist@example.com"
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <p class="help">Email displayed on gallery pages for inquiries. Leave empty to hide.</p>
                        </div>
                        
                        <!-- Footer Tagline -->
                        <div class="field">
                            <label class="label">Footer Tagline (Optional)</label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="text" 
                                    name="footer_tagline" 
                                    value="<?= e($theme['footer_tagline'] ?? '') ?>"
                                    placeholder="Your professional tagline or motto"
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-quote-left"></i>
                                </span>
                            </div>
                            <p class="help">Tagline displayed in the footer. Leave empty to hide.</p>
                        </div>
                        
                        <!-- Favicon Upload -->
                        <div class="field">
                            <label class="label">Favicon</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input 
                                        class="file-input" 
                                        type="file" 
                                        name="favicon" 
                                        accept="image/x-icon,image/png"
                                        onchange="updateFileName(this, 'favicon-name')">
                                    <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">Choose favicon…</span>
                                    </span>
                                    <span class="file-name" id="favicon-name">
                                        <?php if (!empty($theme['favicon_path'])): ?>
                                            <?= e(basename($theme['favicon_path'])) ?>
                                        <?php else: ?>
                                            No file selected
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                            <p class="help">ICO or PNG. Max 2MB. Recommended: 32x32px or 16x16px</p>
                            
                            <?php if (!empty($theme['favicon_path'])): ?>
                                <div class="mt-3">
                                    <p class="has-text-weight-semibold mb-2">Current Favicon:</p>
                                    <img src="<?= e($theme['favicon_path']) ?>" alt="Current Favicon" style="max-height: 32px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    
                    <div class="box">
                        <h2 class="title is-4">
                            <span class="icon-text">
                                <span class="icon has-text-warning">
                                    <i class="fas fa-sliders-h"></i>
                                </span>
                                <span>Layout Options</span>
                            </span>
                        </h2>
                        
                        <!-- Header Style -->
                        <div class="field">
                            <label class="label">Header Style</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="header_style">
                                        <option value="static" <?= ($theme['header_style'] ?? 'static') === 'static' ? 'selected' : '' ?>>
                                            Static (scrolls with page)
                                        </option>
                                        <option value="fixed" <?= ($theme['header_style'] ?? 'static') === 'fixed' ? 'selected' : '' ?>>
                                            Fixed (stays at top)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <p class="help">Choose whether the navigation bar stays fixed at the top</p>
                        </div>
                        
                        <!-- Card Style -->
                        <div class="field">
                            <label class="label">Card Style</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="card_style">
                                        <option value="default" <?= ($theme['card_style'] ?? 'default') === 'default' ? 'selected' : '' ?>>
                                            Default (standard cards)
                                        </option>
                                        <option value="elevated" <?= ($theme['card_style'] ?? 'default') === 'elevated' ? 'selected' : '' ?>>
                                            Elevated (with shadow)
                                        </option>
                                        <option value="flat" <?= ($theme['card_style'] ?? 'default') === 'flat' ? 'selected' : '' ?>>
                                            Flat (no border)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <p class="help">Choose the visual style for content cards</p>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="field is-grouped">
                        <div class="control">
                            <button type="submit" class="button is-primary">
                                <span class="icon">
                                    <i class="fas fa-save"></i>
                                </span>
                                <span>Save Theme Settings</span>
                            </button>
                        </div>
                        <div class="control">
                            <a href="/admin" class="button is-light">
                                <span class="icon">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- Reset to Defaults Form -->
                <hr>
                <form method="POST" action="/admin/theme/reset" id="resetForm" onsubmit="return confirmReset()">
                    <?= csrf_field() ?>
                    <div class="notification is-warning is-light">
                        <p class="has-text-weight-bold mb-2">
                            <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                            Reset to Defaults
                        </p>
                        <p class="mb-3">This will restore all theme settings to their original values. Any custom colors, logo, and favicon will be removed.</p>
                        <button type="submit" class="button is-danger">
                            <span class="icon">
                                <i class="fas fa-undo"></i>
                            </span>
                            <span>Reset Theme to Defaults</span>
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</section>

<script>
// Confirm reset action
function confirmReset() {
    return confirm('Are you sure you want to reset all theme settings to defaults? This action cannot be undone.');
}

// Update file name display when file is selected
function updateFileName(input, displayId) {
    const display = document.getElementById(displayId);
    if (input.files && input.files[0]) {
        display.textContent = input.files[0].name;
    } else {
        display.textContent = 'No file selected';
    }
}

// Sync color picker with text input
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    
    colorInputs.forEach(colorInput => {
        const textInput = colorInput.parentElement.nextElementSibling.querySelector('input[type="text"]');
        
        // Update text when color picker changes
        colorInput.addEventListener('input', function() {
            textInput.value = this.value.toUpperCase();
        });
        
        // Update color picker when text changes (with validation)
        textInput.addEventListener('input', function() {
            const value = this.value.trim();
            if (/^#[0-9A-F]{6}$/i.test(value)) {
                colorInput.value = value;
            }
        });
    });
});
</script>
