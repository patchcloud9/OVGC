<?php
/**
 * Admin Edit Flyer
 * Variables: $title (string), $flyer (array)
 */
$isImage = strpos($flyer['mime_type'], 'image/') === 0;
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-edit"></i> Edit Flyer
            </h1>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <div class="columns is-centered">
        <div class="column is-8-desktop">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="box">
            <!-- Current file preview -->
            <div class="mb-4">
                <p class="label mb-2">Current File</p>
                <?php if ($isImage): ?>
                    <figure class="image" style="max-width:260px;border:1px solid #ddd;border-radius:4px;overflow:hidden;">
                        <img src="<?= e($flyer['file_path']) ?>" alt="<?= e($flyer['title']) ?>" style="object-fit:contain;">
                    </figure>
                <?php else: ?>
                    <span class="icon has-text-danger" style="font-size:3rem;"><i class="fas fa-file-pdf"></i></span>
                <?php endif; ?>
                <p class="is-size-7 has-text-grey mt-1">
                    <a href="<?= e($flyer['file_path']) ?>" target="_blank">
                        <?= e($flyer['filename']) ?>
                    </a>
                </p>
            </div>

            <form method="POST" action="/admin/flyers/<?= (int) $flyer['id'] ?>/edit" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Title -->
                <div class="field">
                    <label class="label">Title <span class="has-text-danger">*</span></label>
                    <div class="control">
                        <input class="input" type="text" name="title"
                               value="<?= e(old('title') ?? $flyer['title']) ?>"
                               placeholder="e.g. Summer Tournament" required maxlength="255">
                    </div>
                </div>

                <!-- Description -->
                <div class="field">
                    <label class="label">Description <span class="has-text-grey is-size-7">(optional)</span></label>
                    <div class="control">
                        <textarea class="textarea" name="description" rows="3"
                                  placeholder="Short description shown below the title"
                                  maxlength="1000"><?= e(old('description') ?? $flyer['description']) ?></textarea>
                    </div>
                </div>

                <!-- Replace file (optional) -->
                <div class="field">
                    <label class="label">Replace File <span class="has-text-grey is-size-7">(optional — leave blank to keep current)</span></label>
                    <div class="control">
                        <input class="input" type="file" name="flyer" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf">
                    </div>
                    <p class="help">JPG, PNG, GIF, WebP, or PDF &mdash; max 10&thinsp;MB</p>
                </div>

                <!-- Expiry date -->
                <div class="field">
                    <label class="label">Expires On <span class="has-text-danger">*</span></label>
                    <div class="control">
                        <input class="input" type="date" name="expires_at"
                               value="<?= e(old('expires_at') ?? $flyer['expires_at']) ?>"
                               required>
                    </div>
                    <p class="help">The flyer will no longer appear on the public page after this date.</p>
                </div>

                <div class="field is-grouped mt-5">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon"><i class="fas fa-save"></i></span>
                            <span>Save Changes</span>
                        </button>
                    </div>
                    <div class="control">
                        <a href="/admin/flyers" class="button is-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

        </div>
        </div>
    </div>
</section>
