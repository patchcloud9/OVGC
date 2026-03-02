<?php
$layout = 'main';
/** @var array $member */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-users"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Update member information</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        <form action="/admin/board-members/<?= e($member['id']) ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Name *</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= e(old('name', $member['name'])) ?>" required maxlength="100">
                </div>
            </div>
            <div class="field">
                <label class="label">Title *</label>
                <div class="control">
                    <input class="input" type="text" name="title" value="<?= e(old('title', $member['title'])) ?>" required maxlength="100">
                </div>
            </div>
            <div class="field">
                <label class="label">Email *</label>
                <div class="control">
                    <input class="input" type="email" name="email" value="<?= e(old('email', $member['email'])) ?>" required maxlength="255">
                </div>
            </div>
            <?php if (!empty($member['photo_path'])): ?>
                <div class="field">
                    <label class="label">Current Photo</label>
                    <p><img src="<?= e($member['photo_path']) ?>" alt="photo" style="max-height:100px;"></p>
                </div>
            <?php endif; ?>
            <div class="field">
                <label class="label">Replace Photo</label>
                <div class="control">
                    <input class="input" type="file" name="photo" accept="image/*">
                </div>
                <p class="help">Leave empty to keep existing image. JPG/PNG/GIF/WebP, max 5MB.</p>
            </div>
            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order', $member['sort_order'])) ?>" min="0">
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</section>
