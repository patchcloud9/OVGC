<?php
/**
 * About Page Admin - Edit Content
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">About Page</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-user-circle"></i>
                </span>
                <span>Edit About Page</span>
            </span>
        </h1>
        <p class="subtitle">Customize the About the Artist page content</p>

        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/about" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Page Title -->
            <div class="box">
                <h2 class="title is-4">Page Title</h2>
                <div class="field">
                    <label class="label">Main Heading</label>
                    <div class="control">
                        <input type="text" name="page_title" class="input" value="<?= e($content['page_title'] ?? 'About the Artist') ?>" required maxlength="100">
                    </div>
                    <p class="help">The main heading displayed at the top of the about page</p>
                </div>

                <div class="field mt-4">
                    <label class="label">Subtitle</label>
                    <div class="control">
                        <input type="text" name="page_subtitle" class="input" value="<?= e($content['page_subtitle'] ?? '') ?>" maxlength="255">
                    </div>
                    <p class="help">Optional subtitle displayed under the main heading</p>
                </div>
            </div>

            <!-- Section 1 -->
            <div class="box mt-5">
                <h2 class="title is-4">Section 1 (Top)</h2>

                <div class="field">
                    <label class="label">Image</label>
                    <div class="control">
                        <div class="file has-name is-fullwidth">
                            <label class="file-label">
                                <input class="file-input" type="file" name="section1_image" accept="image/*">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">Choose a file…</span>
                                </span>
                                <span class="file-name">
                                    <?= !empty($content['section1_image']) ? basename($content['section1_image']) : 'No file chosen' ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <p class="help">JPG, PNG, GIF, or WebP. Max 5MB.</p>

                    <?php if (!empty($content['section1_image'])): ?>
                        <div class="mt-3">
                            <figure class="image" style="max-width: 400px;">
                                <img src="<?= e($content['section1_image']) ?>" alt="Section 1 image">
                            </figure>
                            <button type="button" class="button is-small is-danger mt-2" onclick="clearSectionImage('section1')">
                                <span class="icon"><i class="fas fa-times"></i></span>
                                <span>Remove Image</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label">Image Position</label>
                    <div class="control">
                        <label class="radio">
                            <input type="radio" name="section1_image_position" value="left" <?= ($content['section1_image_position'] ?? 'left') === 'left' ? 'checked' : '' ?>>
                            Left
                        </label>
                        <label class="radio">
                            <input type="radio" name="section1_image_position" value="right" <?= ($content['section1_image_position'] ?? 'left') === 'right' ? 'checked' : '' ?>>
                            Right
                        </label>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <label class="label">Text Horizontal Alignment</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="section1_text_align_h">
                                        <option value="left" <?= ($content['section1_text_align_h'] ?? 'left') === 'left' ? 'selected' : '' ?>>Left</option>
                                        <option value="center" <?= ($content['section1_text_align_h'] ?? 'left') === 'center' ? 'selected' : '' ?>>Center</option>
                                        <option value="right" <?= ($content['section1_text_align_h'] ?? 'left') === 'right' ? 'selected' : '' ?>>Right</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Text Vertical Alignment</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="section1_text_align_v">
                                        <option value="top" <?= ($content['section1_text_align_v'] ?? 'top') === 'top' ? 'selected' : '' ?>>Top</option>
                                        <option value="middle" <?= ($content['section1_text_align_v'] ?? 'top') === 'middle' ? 'selected' : '' ?>>Middle</option>
                                        <option value="bottom" <?= ($content['section1_text_align_v'] ?? 'top') === 'bottom' ? 'selected' : '' ?>>Bottom</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Text Content</label>
                    <div class="control">
                        <textarea name="section1_text" class="textarea" rows="8"><?= e($content['section1_text'] ?? '') ?></textarea>
                    </div>
                    <p class="help">Use line breaks to create paragraphs</p>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="box mt-5">
                <h2 class="title is-4">Section 2 (Bottom)</h2>

                <div class="field">
                    <label class="label">Image</label>
                    <div class="control">
                        <div class="file has-name is-fullwidth">
                            <label class="file-label">
                                <input class="file-input" type="file" name="section2_image" accept="image/*">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">Choose a file…</span>
                                </span>
                                <span class="file-name">
                                    <?= !empty($content['section2_image']) ? basename($content['section2_image']) : 'No file chosen' ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <p class="help">JPG, PNG, GIF, or WebP. Max 5MB.</p>

                    <?php if (!empty($content['section2_image'])): ?>
                        <div class="mt-3">
                            <figure class="image" style="max-width: 400px;">
                                <img src="<?= e($content['section2_image']) ?>" alt="Section 2 image">
                            </figure>
                            <button type="button" class="button is-small is-danger mt-2" onclick="clearSectionImage('section2')">
                                <span class="icon"><i class="fas fa-times"></i></span>
                                <span>Remove Image</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label">Image Position</label>
                    <div class="control">
                        <label class="radio">
                            <input type="radio" name="section2_image_position" value="left" <?= ($content['section2_image_position'] ?? 'left') === 'left' ? 'checked' : '' ?>>
                            Left
                        </label>
                        <label class="radio">
                            <input type="radio" name="section2_image_position" value="right" <?= ($content['section2_image_position'] ?? 'left') === 'right' ? 'checked' : '' ?>>
                            Right
                        </label>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <label class="label">Text Horizontal Alignment</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="section2_text_align_h">
                                        <option value="left" <?= ($content['section2_text_align_h'] ?? 'left') === 'left' ? 'selected' : '' ?>>Left</option>
                                        <option value="center" <?= ($content['section2_text_align_h'] ?? 'left') === 'center' ? 'selected' : '' ?>>Center</option>
                                        <option value="right" <?= ($content['section2_text_align_h'] ?? 'left') === 'right' ? 'selected' : '' ?>>Right</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Text Vertical Alignment</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="section2_text_align_v">
                                        <option value="top" <?= ($content['section2_text_align_v'] ?? 'top') === 'top' ? 'selected' : '' ?>>Top</option>
                                        <option value="middle" <?= ($content['section2_text_align_v'] ?? 'top') === 'middle' ? 'selected' : '' ?>>Middle</option>
                                        <option value="bottom" <?= ($content['section2_text_align_v'] ?? 'top') === 'bottom' ? 'selected' : '' ?>>Bottom</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Text Content</label>
                    <div class="control">
                        <textarea name="section2_text" class="textarea" rows="8"><?= e($content['section2_text'] ?? '') ?></textarea>
                    </div>
                    <p class="help">Use line breaks to create paragraphs</p>
                </div>
            </div>

            <!-- Artist Signature -->
            <div class="box mt-5">
                <h2 class="title is-4">Artist Signature</h2>
                <div class="field">
                    <label class="label">Name</label>
                    <div class="control">
                        <input type="text" name="artist_signature" class="input" value="<?= e($content['artist_signature'] ?? '') ?>" maxlength="100">
                    </div>
                    <p class="help">Artist name displayed at the bottom of the page (optional)</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">
                        <span class="icon"><i class="fas fa-save"></i></span>
                        <span>Save Changes</span>
                    </button>
                </div>
                <div class="control">
                    <a href="/admin" class="button is-light">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Back to Admin</span>
                    </a>
                </div>
            </div>
        </form>

    </div>
</section>

<!-- Hidden form for clearing images -->
<form id="clearImageForm" method="POST" action="/admin/about/clear-image" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="section" id="clear-section">
</form>

<script>
    // Update file input names
    document.addEventListener('DOMContentLoaded', () => {
        const fileInputs = document.querySelectorAll('.file-input');

        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const fileName = e.target.files[0]?.name || 'No file chosen';
                const fileNameSpan = input.closest('.file').querySelector('.file-name');
                if (fileNameSpan) {
                    fileNameSpan.textContent = fileName;
                }
            });
        });
    });

    // Clear section image
    function clearSectionImage(section) {
        if (!confirm('Are you sure you want to remove this image?')) {
            return;
        }

        document.getElementById('clear-section').value = section;
        document.getElementById('clearImageForm').submit();
    }
</script>
