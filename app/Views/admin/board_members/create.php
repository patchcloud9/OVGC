<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-users"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Add a new board member</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        <form action="/admin/board-members" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Name *</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= e(old('name')) ?>" required maxlength="100">
                </div>
            </div>
            <div class="field">
                <label class="label">Title *</label>
                <div class="control">
                    <input class="input" type="text" name="title" value="<?= e(old('title')) ?>" required maxlength="100">
                </div>
            </div>
            <div class="field">
                <label class="label">Email *</label>
                <div class="control">
                    <input class="input" type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="255">
                </div>
            </div>
            <div class="field">
                <label class="label">Photo</label>
                <div class="control">
                    <input class="input" type="file" name="photo" accept="image/*">
                </div>
                <p class="help">Optional. JPG/PNG/GIF/WebP, max 5MB.</p>
            </div>
            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order', 0)) ?>" min="0">
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</section>
