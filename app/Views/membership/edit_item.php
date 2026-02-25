<?php
$layout = 'main';
?>

<!-- hero for editing membership item -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-box-open"></i> Edit Item</h1>
            <p class="subtitle is-6 has-text-white">Group: <?= e($group['title']) ?></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1000px;">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/membership">Membership</a></li>
                <li><a href="/admin/membership/<?= e($group['id']) ?>/items"><?= e($group['title']) ?></a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit Item</a></li>
            </ul>
        </nav>


        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/membership/<?= e($group['id']) ?>/items/<?= e($item['id']) ?>" style="max-width:600px;">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="field">
                <label class="label">Name</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= e(old('name', $item['name'])) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Price</label>
                <div class="control">
                    <input class="input" type="text" name="price" value="<?= e(old('price', $item['price'])) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order', $item['sort_order'])) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">Notes</label>
                <div class="control">
                    <input class="input" type="text" name="notes" value="<?= e(old('notes', $item['notes'])) ?>">
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Update Item</button>
                </div>
                <div class="control">
                    <a href="/admin/membership/<?= e($group['id']) ?>/items" class="button is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
