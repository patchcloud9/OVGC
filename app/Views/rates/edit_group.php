<?php
$layout = 'main';
?>

<!-- hero for editing rate group -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-edit"></i> Edit Rate Group</h1>
            <p class="subtitle is-6 has-text-white">Modify group settings</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1000px;">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/rates">Rates</a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit Group</a></li>
            </ul>
        </nav>

        <h1 class="title">Edit Rate Group</h1>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/rates/<?= e($group['id']) ?>" style="max-width:600px;">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="field">
                <label class="label">Slug</label>
                <div class="control">
                    <input class="input" type="text" name="slug" value="<?= e(old('slug', $group['slug'])) ?>" required>
                </div>
                <p class="help">URL-friendly identifier (alpha, numbers, dashes).</p>
            </div>

            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" type="text" name="title" value="<?= e(old('title', $group['title'])) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Subtitle</label>
                <div class="control">
                    <input class="input" type="text" name="subtitle" value="<?= e(old('subtitle', $group['subtitle'] ?? '')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">Note</label>
                <div class="control">
                    <textarea class="textarea" name="note"><?= e(old('note', $group['note'])) ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order', $group['sort_order'])) ?>">
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="active" value="1" <?= old('active', $group['active']) ? 'checked' : '' ?>> Active
                    </label>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Update Group</button>
                </div>
                <div class="control">
                    <a href="/admin/rates" class="button is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
