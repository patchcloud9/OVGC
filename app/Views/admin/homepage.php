<?php
$layout = 'main';
?>

<!-- Hero for Homepage Settings -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <span class="icon-text">
                    <span class="icon has-text-info"><i class="fas fa-home"></i></span>
                    <span>Homepage Settings</span>
                </span>
            </h1>
            <p class="subtitle is-6 has-text-white">Configure the homepage content and appearance</p>
        </div>
    </div>
</section>

<div class="section">
    <!-- Breadcrumb moved below hero -->
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Homepage Settings</a></li>
            </ul>
        </nav>
        
        <!-- Page Header -->
        <h1 class="title is-3">
            <span class="icon-text">
                <span class="icon has-text-info">
                    <i class="fas fa-home"></i>
                </span>
                <span>Customize Homepage</span>
            </span>
        </h1>
        <p class="subtitle is-6 mb-5">Configure the homepage content and appearance</p>
        
        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <!-- Settings Form -->
        <form method="POST" action="/admin/homepage" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <!-- Hero Section -->
            <div class="box">
                <h2 class="title is-4">Hero Section</h2>
                <p class="subtitle is-6 has-text-grey">The large banner at the top of the homepage</p>
                
                <div class="field">
                    <label class="label">Hero Title</label>
                    <div class="control">
                        <input type="text" name="hero_title" class="input" value="<?= e($settings['hero_title'] ?? 'Welcome Home') ?>" required maxlength="100" placeholder="Welcome Home">
                    </div>
                    <p class="help">Main heading displayed on the hero banner</p>
                </div>

                <!-- Title Color (moved directly under title) -->
                <div class="field">
                    <label class="label">Hero Title Color</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input color-preview" type="color" name="hero_title_color" value="<?= e($settings['hero_title_color'] ?? '#FFFFFF') ?>" style="width: 60px; height: 40px; cursor: pointer;">
                        </div>
                        <div class="control is-expanded">
                            <input class="input" type="text" name="hero_title_color_text" value="<?= e(strtoupper($settings['hero_title_color'] ?? '#FFFFFF')) ?>" placeholder="#FFFFFF">
                        </div>
                    </div>
                    <p class="help">Color for the main title</p>
                </div>

                <div class="field">
                    <label class="label">Hero Subtitle</label>
                    <div class="control">
                        <input type="text" name="hero_subtitle" class="input" value="<?= e($settings['hero_subtitle'] ?? 'Your PHP MVC Framework') ?>" maxlength="255" placeholder="Your PHP MVC Framework">
                    </div>
                    <p class="help">Subheading displayed below the title (optional). Use <code>{email}</code> to insert the contact email from Theme Settings.</p>
                </div>

                <!-- Subtitle Color (moved directly under subtitle) -->
                <div class="field">
                    <label class="label">Hero Subtitle Color</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input color-preview" type="color" name="hero_subtitle_color" value="<?= e($settings['hero_subtitle_color'] ?? '#F5F5F5') ?>" style="width: 60px; height: 40px; cursor: pointer;">
                        </div>
                        <div class="control is-expanded">
                            <input class="input" type="text" name="hero_subtitle_color_text" value="<?= e(strtoupper($settings['hero_subtitle_color'] ?? '#F5F5F5')) ?>" placeholder="#F5F5F5">
                        </div>
                    </div>
                    <p class="help">Color for the subtitle</p>
                </div>
                
                <div class="field">
                    <label class="label">Hero Background</label>
                    <p class="help">If a background image is uploaded it will be used; otherwise the selected background color will be applied.</p>
                </div>
                
                <div class="field">
                    <label class="label">Background Color</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input color-preview" type="color" name="hero_background_color" value="<?= e($settings['hero_background_color'] ?? '#667EEA') ?>" style="width: 60px; height: 40px; cursor: pointer;">
                        </div>
                        <div class="control is-expanded">
                            <input class="input" type="text" name="hero_background_color_text" value="<?= e(strtoupper($settings['hero_background_color'] ?? '#667EEA')) ?>" placeholder="#667EEA">
                        </div>
                    </div>
                    <p class="help">Used when "Solid Color" is selected</p>
                </div>
                
                <div class="field">
                    <label class="label">Background Image</label>
                    <div class="control">
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file" name="hero_background_image" accept="image/*">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Choose a file…
                                    </span>
                                </span>
                                <span class="file-name">
                                    <?= !empty($settings['hero_background_image']) ? basename($settings['hero_background_image']) : 'No file chosen' ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <p class="help">Used when "Image" is selected. JPG, PNG, GIF, or WebP. Max 5MB.</p>
                    <?php if (!empty($settings['hero_background_image'])): ?>
                        <div class="mt-3">
                            <figure class="image is-128x128">
                                <img src="<?= e($settings['hero_background_image']) ?>" alt="Current hero background">
                            </figure>
                            <button type="button" class="button is-small is-danger mt-2" onclick="clearHeroImage()">
                                <span class="icon">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>Remove Image</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Feature Cards -->
            <div class="box mt-5">
                <h2 class="title is-4">Feature Cards</h2>
                <p class="subtitle is-6 has-text-grey">Three cards displayed below the hero section</p>
                
                <!-- Card 1 -->
                <div class="mb-5">
                    <h3 class="subtitle is-5 has-text-weight-semibold">Card 1</h3>
                    
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control">
                            <input type="text" name="card1_title" class="input" value="<?= e($settings['card1_title'] ?? 'Fast Performance') ?>" required maxlength="100">
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea name="card1_text" class="textarea" rows="3"><?= e($settings['card1_text'] ?? 'Built with modern PHP and optimized for speed.') ?></textarea>
                        </div>
                        <p class="help">Use <code>{email}</code> to insert the contact email from Theme Settings.</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Text</label>
                        <div class="control">
                            <input type="text" name="card1_button_text" class="input" value="<?= e($settings['card1_button_text'] ?? 'Learn More') ?>" maxlength="100">
                        </div>
                        <p class="help">Text to display on the card button (optional)</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Link</label>
                        <div class="control">
                            <input type="text" name="card1_button_link" class="input" value="<?= e($settings['card1_button_link'] ?? '/about') ?>" maxlength="255" placeholder="/about">
                        </div>
                        <p class="help">URL the card button links to (e.g., /about, /contact)</p>
                    </div>
                </div>
                
                <hr>
                
                <!-- Card 2 -->
                <div class="mb-5">
                    <h3 class="subtitle is-5 has-text-weight-semibold">Card 2</h3>
                    
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control">
                            <input type="text" name="card2_title" class="input" value="<?= e($settings['card2_title'] ?? 'Secure') ?>" required maxlength="100">
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea name="card2_text" class="textarea" rows="3"><?= e($settings['card2_text'] ?? 'CSRF protection, authentication, and secure password hashing built in.') ?></textarea>
                        </div>
                        <p class="help">Use <code>{email}</code> to insert the contact email from Theme Settings.</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Text</label>
                        <div class="control">
                            <input type="text" name="card2_button_text" class="input" value="<?= e($settings['card2_button_text'] ?? 'Learn More') ?>" maxlength="100">
                        </div>
                        <p class="help">Text to display on the card button (optional)</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Link</label>
                        <div class="control">
                            <input type="text" name="card2_button_link" class="input" value="<?= e($settings['card2_button_link'] ?? '/about') ?>" maxlength="255" placeholder="/about">
                        </div>
                        <p class="help">URL the card button links to (e.g., /about, /contact)</p>
                    </div>
                </div>
                
                <hr>
                
                <!-- Card 3 -->
                <div class="mb-5">
                    <h3 class="subtitle is-5 has-text-weight-semibold">Card 3</h3>
                    
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control">
                            <input type="text" name="card3_title" class="input" value="<?= e($settings['card3_title'] ?? 'Responsive') ?>" required maxlength="100">
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea name="card3_text" class="textarea" rows="3"><?= e($settings['card3_text'] ?? 'Mobile-friendly design using Bulma CSS framework.') ?></textarea>
                        </div>
                        <p class="help">Use <code>{email}</code> to insert the contact email from Theme Settings.</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Text</label>
                        <div class="control">
                            <input type="text" name="card3_button_text" class="input" value="<?= e($settings['card3_button_text'] ?? 'Learn More') ?>" maxlength="100">
                        </div>
                        <p class="help">Text to display on the card button (optional)</p>
                    </div>

                    <div class="field">
                        <label class="label">Button Link</label>
                        <div class="control">
                            <input type="text" name="card3_button_link" class="input" value="<?= e($settings['card3_button_link'] ?? '/about') ?>" maxlength="255" placeholder="/about">
                        </div>
                        <p class="help">URL the card button links to (e.g., /about, /contact)</p>
                    </div>
                </div>
            </div>
            

            
            <!-- Bottom Section -->
            <div class="box">
                <h2 class="title is-4">Bottom Content Section</h2>
                <p class="subtitle is-6 has-text-grey">Two-column section at the bottom of the page</p>
                
                <div class="field">
                    <label class="label">Layout</label>
                    <div class="control">
                        <label class="radio">
                            <input type="radio" name="bottom_section_layout" value="text-image" <?= ($settings['bottom_section_layout'] ?? 'text-image') === 'text-image' ? 'checked' : '' ?>>
                            Text Left, Image Right
                        </label>
                        <label class="radio">
                            <input type="radio" name="bottom_section_layout" value="image-text" <?= ($settings['bottom_section_layout'] ?? 'text-image') === 'image-text' ? 'checked' : '' ?>>
                            Image Left, Text Right
                        </label>
                    </div>
                </div>
                
                <div class="field">
                    <label class="label">Title</label>
                    <div class="control">
                        <input type="text" name="bottom_section_title" class="input" value="<?= e($settings['bottom_section_title'] ?? 'About This Framework') ?>" required maxlength="255">
                    </div>
                </div>
                
                <div class="field">
                    <label class="label">Text Content</label>
                    <div class="control">
                        <textarea name="bottom_section_text" class="textarea" rows="6"><?= e($settings['bottom_section_text'] ?? 'This is a minimal, educational PHP MVC framework demonstrating front controller and routing patterns. Built with clean code and modern practices, it provides a foundation for understanding how web frameworks work.') ?></textarea>
                    </div>
                    <p class="help">Use <code>{email}</code> to insert the contact email from Theme Settings.</p>
                </div>
                
                <div class="field">
                    <label class="label">Image</label>
                    <div class="control">
                        <div class="file has-name is-fullwidth">
                            <label class="file-label">
                                <input class="file-input" type="file" name="bottom_section_image" accept="image/*">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Choose a file…
                                    </span>
                                </span>
                                <span class="file-name">
                                    <?= !empty($settings['bottom_section_image']) ? basename($settings['bottom_section_image']) : 'No file chosen' ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <p class="help">JPG, PNG, GIF, or WebP. Max 5MB.</p>
                    <?php if (!empty($settings['bottom_section_image'])): ?>
                        <div class="mt-3">
                            <figure class="image is-16by9" style="max-width: 400px;">
                                <img src="<?= e($settings['bottom_section_image']) ?>" alt="Current bottom section image" style="object-fit: cover;">
                            </figure>
                            <button type="button" class="button is-small is-danger mt-2" onclick="clearBottomImage()">
                                <span class="icon">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>Remove Image</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">
                        <span class="icon">
                            <i class="fas fa-save"></i>
                        </span>
                        <span>Save Settings</span>
                    </button>
                </div>
                <div class="control">
                    <a href="/admin" class="button is-light">
                        <span class="icon">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        <span>Back to Admin</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Hidden forms for clearing images -->
<form id="clearHeroImageForm" method="POST" action="/admin/homepage/clear-hero-image" style="display: none;">
    <?= csrf_field() ?>
</form>

<form id="clearBottomImageForm" method="POST" action="/admin/homepage/clear-bottom-image" style="display: none;">
    <?= csrf_field() ?>
</form>

<!-- JavaScript for file input names and clear functions -->
<script>
function clearHeroImage() {
    if (confirm('Are you sure you want to remove this image?')) {
        document.getElementById('clearHeroImageForm').submit();
    }
}

function clearBottomImage() {
    if (confirm('Are you sure you want to remove this image?')) {
        document.getElementById('clearBottomImageForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Update file input names
    const fileInputs = document.querySelectorAll('.file-input');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const fileName = e.target.files[0]?.name || 'No file chosen';
            const fileNameSpan = input.parentElement.querySelector('.file-name');
            if (fileNameSpan) {
                fileNameSpan.textContent = fileName;
            }
        });
    });
    


    // Sync color picker with hex text input (same UX as Theme settings)
    const colorInputs = document.querySelectorAll('.color-preview');

    colorInputs.forEach(colorInput => {
        const textInput = colorInput.parentElement.nextElementSibling.querySelector('input[type="text"]');

        // Update text when color picker changes
        colorInput.addEventListener('input', function() {
            if (textInput) textInput.value = this.value.toUpperCase();
        });

        // Update color picker when text changes (with validation)
        if (textInput) {
            textInput.addEventListener('input', function() {
                const value = this.value.trim();
                if (/^#[0-9A-F]{6}$/i.test(value)) {
                    colorInput.value = value;
                }
            });
        }
    });
});
</script>
