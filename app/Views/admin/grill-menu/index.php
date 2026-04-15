<?php
/**
 * Admin Grill Menu Management
 * Variables: $title, $pdfExists, $pdfSize, $pdfModified,
 *            $imageExists, $imageModified, $imagickAvailable
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-utensils"></i> Grill Menu
            </h1>
            <p class="subtitle">Upload a PDF — a preview image is generated automatically</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:680px;">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php if (!$imagickAvailable): ?>
        <div class="notification is-warning is-light mb-4">
            <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
            <strong>Auto-conversion unavailable.</strong> Imagick is not installed on this server. Uploading a PDF will still work — visitors will see "View" and "Download" buttons instead of an inline image.
        </div>
        <?php endif; ?>

        <!-- Current Status -->
        <div class="box">
            <h2 class="title is-5">Current Status</h2>

            <div class="columns">
                <div class="column">
                    <p class="heading">PDF</p>
                    <?php if ($pdfExists): ?>
                        <span class="tag is-success is-medium">
                            <span class="icon"><i class="fas fa-check"></i></span>
                            <span>Uploaded</span>
                        </span>
                        <p class="is-size-7 has-text-grey mt-1">
                            <?= e(number_format($pdfSize / 1024, 1)) ?> KB &mdash;
                            <?= e(date('M j, Y g:i a', $pdfModified)) ?>
                        </p>
                    <?php else: ?>
                        <span class="tag is-light is-medium">None</span>
                    <?php endif; ?>
                </div>
                <div class="column">
                    <p class="heading">Preview Image</p>
                    <?php if ($imageExists): ?>
                        <span class="tag is-success is-medium">
                            <span class="icon"><i class="fas fa-check"></i></span>
                            <span>Generated</span>
                        </span>
                        <p class="is-size-7 has-text-grey mt-1">
                            <?= e(date('M j, Y g:i a', $imageModified)) ?>
                        </p>
                    <?php else: ?>
                        <span class="tag is-light is-medium">None</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($imageExists): ?>
            <figure class="image mt-4" style="max-width:360px;">
                <img src="/assets/menu/menu-display.jpg?v=<?= $imageModified ?>"
                     alt="Menu preview"
                     style="border:1px solid #dbdbdb; border-radius:4px;">
            </figure>
            <?php endif; ?>

            <?php if ($pdfExists): ?>
            <div class="buttons mt-4">
                <a href="/assets/menu/menu.pdf" target="_blank" class="button is-info is-light is-small">
                    <span class="icon"><i class="fas fa-eye"></i></span>
                    <span>Preview PDF</span>
                </a>
                <a href="/menu" target="_blank" class="button is-light is-small">
                    <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                    <span>View Public Page</span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Upload -->
        <div class="box">
            <h2 class="title is-5"><?= $pdfExists ? 'Replace Menu PDF' : 'Upload Menu PDF' ?></h2>
            <form method="POST" action="/admin/grill-menu" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="label" for="menu_pdf">Select PDF</label>
                    <div class="control">
                        <input class="input" type="file" id="menu_pdf" name="menu_pdf"
                               accept="application/pdf,.pdf" required style="padding:0.4rem;">
                    </div>
                    <p class="help">PDF only. Max 10 MB.<?= $imagickAvailable ? ' A preview image will be generated automatically from the first page.' : '' ?></p>
                </div>
                <div class="field mt-4">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon"><i class="fas fa-upload"></i></span>
                            <span><?= $pdfExists ? 'Replace PDF' : 'Upload PDF' ?></span>
                        </button>
                        <a href="/admin" class="button is-light ml-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Remove -->
        <?php if ($pdfExists): ?>
        <div class="box">
            <h2 class="title is-5 has-text-danger">Remove Menu</h2>
            <p class="mb-4 has-text-grey">Removes the PDF and preview image. The public page will show a "check back soon" message.</p>
            <form method="POST" action="/admin/grill-menu/delete-pdf">
                <?= csrf_field() ?>
                <button type="submit" class="button is-danger"
                    onclick="return confirm('Remove the menu PDF and preview image?')">
                    <span class="icon"><i class="fas fa-trash"></i></span>
                    <span>Remove Menu</span>
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</section>
