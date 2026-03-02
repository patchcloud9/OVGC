<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-upload"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Upload a new minutes PDF</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        <form action="/admin/board-minutes" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Meeting Date *</label>
                <div class="control">
                    <input class="input" type="date" name="meeting_date" value="<?= e(old('meeting_date')) ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">PDF File *</label>
                <div class="control">
                    <input class="input" type="file" name="pdf" accept="application/pdf" required>
                </div>
                <p class="help">Max 10MB.</p>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</section>
