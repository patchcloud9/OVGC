<?php
/**
 * Admin Add Flyer
 * Variables: $title (string), $defaultExpiry (string Y-m-d)
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-plus"></i> Add Flyer
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
            <form method="POST" action="/admin/flyers/create" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Title -->
                <div class="field">
                    <label class="label">Title <span class="has-text-danger">*</span></label>
                    <div class="control">
                        <input class="input" type="text" name="title"
                               value="<?= e(old('title') ?? '') ?>"
                               placeholder="e.g. Summer Tournament" required maxlength="255">
                    </div>
                </div>

                <!-- Description -->
                <div class="field">
                    <label class="label">Description <span class="has-text-grey is-size-7">(optional)</span></label>
                    <div class="control">
                        <textarea class="textarea" name="description" rows="3"
                                  placeholder="Short description shown below the title"
                                  maxlength="1000"><?= e(old('description') ?? '') ?></textarea>
                    </div>
                </div>

                <!-- File -->
                <div class="field">
                    <label class="label">Flyer File <span class="has-text-danger">*</span></label>
                    <div class="control">
                        <input class="input" type="file" name="flyer" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf" required>
                    </div>
                    <p class="help">JPG, PNG, GIF, WebP, or PDF &mdash; max 10&thinsp;MB</p>
                </div>

                <!-- Expiry date -->
                <div class="field">
                    <label class="label">
                        Expires On <span class="has-text-danger">*</span>
                        <span class="has-text-grey is-size-7 ml-2">(defaults to 90 days from today)</span>
                    </label>
                    <div class="control">
                        <input class="input" type="date" name="expires_at"
                               value="<?= e(old('expires_at') ?? $defaultExpiry) ?>"
                               required>
                    </div>
                    <p class="help">The flyer will no longer appear on the public page after this date.</p>
                </div>

                <div class="field is-grouped mt-5">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon"><i class="fas fa-upload"></i></span>
                            <span>Upload Flyer</span>
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
