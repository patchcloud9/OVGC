<?php
/**
 * Membership Group Management View
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Membership</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary"><i class="fas fa-users"></i></span>
                <span>Membership</span>
            </span>
        </h1>
        <p class="subtitle">Define cards used on the public membership page</p>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/membership/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Create Group</span>
            </a>
        </div>

        <?php if (empty($groups)): ?>
            <div class="notification is-info">
                No membership groups defined yet.
            </div>
        <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($groups as $group): ?>
                    <div class="column is-full-mobile is-half-tablet is-one-third-desktop">
                        <div class="box">
                            <h3 class="title is-5"><?= e($group['title']) ?></h3>
                            <p><strong>Slug:</strong> <code><?= e($group['slug']) ?></code></p>
                            <p><strong>Order:</strong> <?= e($group['sort_order']) ?></p>
                            <p><strong>Active:</strong> <?= $group['active'] ? '<span class="tag is-success">Yes</span>' : '<span class="tag is-light">No</span>' ?></p>
                            <div class="buttons mt-3 is-small is-flex-wrap-wrap">
                                <a href="/admin/membership/<?= e($group['id']) ?>/edit" class="button is-info">
                                    <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                    <span>Edit</span>
                                </a>
                                <a href="/admin/membership/<?= e($group['id']) ?>/items" class="button is-primary">
                                    <span class="icon is-small"><i class="fas fa-list"></i></span>
                                    <span>Items</span>
                                </a>
                                <a href="#" class="button is-danger" onclick="if(confirm('Delete this group and all its items?')){document.getElementById('delete-form-<?= e($group['id']) ?>').submit();}return false;">
                                    <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                    <span>Delete</span>
                                </a>
                                <form id="delete-form-<?= e($group['id']) ?>" method="POST" action="/admin/membership/<?= e($group['id']) ?>" style="display:none;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
