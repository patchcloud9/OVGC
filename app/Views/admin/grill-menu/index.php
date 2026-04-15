<?php
/**
 * Admin Grill Menu PDF Management
 * Variables: $title (string), $exists (bool), $fileSize (?int), $modified (?int)
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-utensils"></i> Grill Menu PDF
            </h1>
            <p class="subtitle">Upload and manage the public grill menu</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:640px;">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="box">
            <h2 class="title is-5">Current Menu</h2>

            <?php if ($exists): ?>
                <div class="notification is-success is-light mb-4">
                    <span class="icon"><i class="fas fa-check-circle"></i></span>
                    <strong>A menu PDF is currently live.</strong>
                </div>
                <table class="table is-fullwidth is-narrow mb-4">
                    <tbody>
                        <tr>
                            <th style="width:40%">File size</th>
                            <td><?= e(number_format($fileSize / 1024, 1)) ?> KB</td>
                        </tr>
                        <tr>
                            <th>Last updated</th>
                            <td><?= e(date('F j, Y g:i a', $modified)) ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="buttons">
                    <a href="/assets/menu/menu.pdf" target="_blank" class="button is-info is-light">
                        <span class="icon"><i class="fas fa-eye"></i></span>
                        <span>Preview PDF</span>
                    </a>
                    <a href="/menu" target="_blank" class="button is-light">
                        <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                        <span>View Public Page</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="notification is-warning is-light mb-4">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <strong>No menu PDF uploaded yet.</strong> The public menu page will show a "check back soon" message until a PDF is uploaded.
                </div>
            <?php endif; ?>
        </div>

        <!-- Upload Form -->
        <div class="box">
            <h2 class="title is-5"><?= $exists ? 'Replace Menu PDF' : 'Upload Menu PDF' ?></h2>
            <form method="POST" action="/admin/grill-menu" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="label" for="menu_pdf">Select PDF file</label>
                    <div class="control">
                        <input
                            class="input"
                            type="file"
                            id="menu_pdf"
                            name="menu_pdf"
                            accept="application/pdf,.pdf"
                            required
                            style="padding:0.4rem;">
                    </div>
                    <p class="help">PDF only. Maximum 10 MB. Replaces the existing menu immediately on upload.</p>
                </div>
                <div class="field mt-4">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon"><i class="fas fa-upload"></i></span>
                            <span><?= $exists ? 'Replace PDF' : 'Upload PDF' ?></span>
                        </button>
                        <a href="/admin" class="button is-light ml-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($exists): ?>
        <!-- Delete -->
        <div class="box">
            <h2 class="title is-5 has-text-danger">Remove Menu</h2>
            <p class="mb-4 has-text-grey">Removing the PDF will hide the menu from the public page until a new one is uploaded.</p>
            <form method="POST" action="/admin/grill-menu/delete">
                <?= csrf_field() ?>
                <button type="submit" class="button is-danger"
                    onclick="return confirm('Remove the menu PDF? The public page will show a \'check back soon\' message.')">
                    <span class="icon"><i class="fas fa-trash"></i></span>
                    <span>Remove PDF</span>
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</section>
