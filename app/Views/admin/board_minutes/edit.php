<?php
$layout = 'main';
/** @var array $minute */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-upload"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Edit meeting minutes</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        <form action="/admin/board-minutes/<?= e($minute['id']) ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Meeting Date *</label>
                <div class="control">
                    <input class="input" type="date" name="meeting_date" value="<?= e(old('meeting_date', $minute['meeting_date'])) ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Current File</label>
                <div class="control">
                    <a href="<?= e($minute['file_path']) ?>" target="_blank"><?= e($minute['filename']) ?></a>
                </div>
            </div>
            <div class="field">
                <label class="label">Replace PDF</label>
                <div class="control">
                    <input class="input" type="file" name="pdf" accept="application/pdf">
                </div>
                <p class="help">Leave empty to keep existing file. Max 10MB</p>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</section>
