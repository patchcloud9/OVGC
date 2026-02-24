<?php
$layout = 'main';
?>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/rates">Rates</a></li>
                <li><a href="/admin/rates/<?= e($group['id']) ?>/rates"><?= e($group['title']) ?></a></li>
                <li class="is-active"><a href="#" aria-current="page">Create Rate</a></li>
            </ul>
        </nav>

        <h1 class="title">Create Rate for <?= e($group['title']) ?></h1>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/rates/<?= e($group['id']) ?>/rates" style="max-width:600px;">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <input class="input" type="text" name="description" value="<?= e(old('description')) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Price</label>
                <div class="control">
                    <input class="input" type="text" name="price" value="<?= e(old('price')) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order',0)) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">Notes</label>
                <div class="control">
                    <input class="input" type="text" name="notes" value="<?= e(old('notes')) ?>">
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Save Rate</button>
                </div>
                <div class="control">
                    <a href="/admin/rates/<?= e($group['id']) ?>/rates" class="button is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
