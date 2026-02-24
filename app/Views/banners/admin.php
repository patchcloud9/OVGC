<?php
/**
 * Banner Management View
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Banners</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary"><i class="fas fa-bullhorn"></i></span>
                <span>Manage Banners</span>
            </span>
        </h1>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/banners/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Create Banner</span>
            </a>
        </div>

        <?php if (empty($banners)): ?>
            <div class="notification is-info">
                No banners defined yet.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Position</th>
                            <th>Text</th>
                            <th>Colour</th>
                            <th>Sort</th>
                            <th>Dismissable</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                        <tr>
                            <td><?= e($b['page']) ?></td>
                            <td><?= e($b['position']) ?></td>
                            <td><?= e(substr($b['text'],0,60)) ?><?= strlen($b['text'])>60?'…':'' ?></td>
                            <td><?= e($b['colour']) ?></td>
                            <td><?= e($b['sort_order']) ?></td>
                            <td><?= $b['dismissable'] ? '<span class="tag is-success">Yes</span>' : '<span class="tag is-light">No</span>' ?></td>
                            <td><?= e($b['start_at'] ?? '') ?></td>
                            <td><?= e($b['end_at'] ?? '') ?></td>
                            <td><?= $b['active'] ? '<span class="tag is-success">Yes</span>' : '<span class="tag is-light">No</span>' ?></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/banners/<?= e($b['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span><span>Edit</span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this banner?')){document.getElementById('delete-<?= e($b['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span><span>Delete</span>
                                    </a>
                                    <form id="delete-<?= e($b['id']) ?>" method="POST" action="/admin/banners/<?= e($b['id']) ?>" style="display:none;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
