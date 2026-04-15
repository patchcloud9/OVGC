<?php
/**
 * Admin Grill Menu PDF Management
 * Variables: $title, $pdfExists, $pdfSize, $pdfModified,
 *            $imageExists, $imagePath, $imageModified
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-utensils"></i> Grill Menu
            </h1>
            <p class="subtitle">Manage the public menu page</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:700px;">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- Display Image -->
        <div class="box">
            <h2 class="title is-5">Display Image <span class="tag is-info is-light ml-2">Shown on menu page</span></h2>

            <?php if ($imageExists): ?>
                <div class="notification is-success is-light mb-3">
                    <span class="icon"><i class="fas fa-check-circle"></i></span>
                    <strong>Image is live.</strong>
                    <span class="has-text-grey ml-2">Last updated: <?= e(date('F j, Y g:i a', $imageModified)) ?></span>
                </div>
                <figure class="image mb-4" style="max-width:400px;">
                    <img src="<?= e($imagePath) ?>?v=<?= $imageModified ?>" alt="Current menu image"
                         style="border:1px solid #dbdbdb; border-radius:4px;">
                </figure>
                <div class="buttons">
                    <a href="/menu" target="_blank" class="button is-info is-light is-small">
                        <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                        <span>View Public Page</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="notification is-warning is-light mb-3">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    No display image uploaded yet.
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/grill-menu/image" enctype="multipart/form-data" class="mt-4">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="label" for="menu_image"><?= $imageExists ? 'Replace image' : 'Upload image' ?></label>
                    <div class="control">
                        <input class="input" type="file" id="menu_image" name="menu_image"
                               accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                               required style="padding:0.4rem;">
                    </div>
                    <p class="help">JPEG, PNG, or WebP. Max 10 MB. Tip: export your PDF as a high-resolution image using Preview (Mac), Adobe Reader, or any online PDF-to-image converter.</p>
                </div>
                <div class="control mt-3">
                    <button type="submit" class="button is-primary">
                        <span class="icon"><i class="fas fa-upload"></i></span>
                        <span><?= $imageExists ? 'Replace Image' : 'Upload Image' ?></span>
                    </button>
                </div>
            </form>

            <?php if ($imageExists): ?>
            <form method="POST" action="/admin/grill-menu/delete-image" class="mt-4">
                <?= csrf_field() ?>
                <button type="submit" class="button is-danger is-light is-small"
                    onclick="return confirm('Remove the display image?')">
                    <span class="icon"><i class="fas fa-trash"></i></span>
                    <span>Remove Image</span>
                </button>
            </form>
            <?php endif; ?>
        </div>

        <!-- PDF Download -->
        <div class="box">
            <h2 class="title is-5">Download PDF <span class="tag is-light ml-2">Optional — shown as download button</span></h2>

            <?php if ($pdfExists): ?>
                <div class="notification is-success is-light mb-3">
                    <span class="icon"><i class="fas fa-check-circle"></i></span>
                    <strong>PDF is live.</strong>
                    <span class="has-text-grey ml-2"><?= e(number_format($pdfSize / 1024, 1)) ?> KB &mdash; Last updated: <?= e(date('F j, Y g:i a', $pdfModified)) ?></span>
                </div>
                <div class="buttons">
                    <a href="/assets/menu/menu.pdf" target="_blank" class="button is-info is-light is-small">
                        <span class="icon"><i class="fas fa-eye"></i></span>
                        <span>Preview PDF</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="notification is-light mb-3">
                    <span class="icon"><i class="fas fa-info-circle"></i></span>
                    No PDF uploaded. Upload one to show a Download button alongside the image.
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/grill-menu" enctype="multipart/form-data" class="mt-4">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="label" for="menu_pdf"><?= $pdfExists ? 'Replace PDF' : 'Upload PDF' ?></label>
                    <div class="control">
                        <input class="input" type="file" id="menu_pdf" name="menu_pdf"
                               accept="application/pdf,.pdf" required style="padding:0.4rem;">
                    </div>
                    <p class="help">PDF only. Max 10 MB.</p>
                </div>
                <div class="control mt-3">
                    <button type="submit" class="button is-primary">
                        <span class="icon"><i class="fas fa-upload"></i></span>
                        <span><?= $pdfExists ? 'Replace PDF' : 'Upload PDF' ?></span>
                    </button>
                </div>
            </form>

            <?php if ($pdfExists): ?>
            <form method="POST" action="/admin/grill-menu/delete-pdf" class="mt-4">
                <?= csrf_field() ?>
                <button type="submit" class="button is-danger is-light is-small"
                    onclick="return confirm('Remove the download PDF?')">
                    <span class="icon"><i class="fas fa-trash"></i></span>
                    <span>Remove PDF</span>
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="mt-2">
            <a href="/admin" class="button is-light">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>Back to Dashboard</span>
            </a>
        </div>

    </div>
</section>
