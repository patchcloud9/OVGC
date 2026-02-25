<?php
/**
 * Rate Group Management View
 */
$layout = 'main';
?>

<!-- hero for Rates admin -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <span class="icon-text">
                    <span class="icon has-text-white"><i class="fas fa-dollar-sign"></i></span>
                    <span>Manage Rate Groups</span>
                </span>
            </h1>
            <p class="subtitle has-text-white">Define cards used on the public rates page</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Rates</a></li>
            </ul>
        </nav> <!-- header shown in hero -->

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- page content editor (rules and optional scorecard) -->
        <div class="box mb-5">
            <h2 class="title is-4">Page Content</h2>
            <form method="POST" action="/admin/rates/content" enctype="multipart/form-data">
                <div class="field">
                    <label class="label" for="rules_text">Rules (one per line)</label>
                    <div class="control">
                        <textarea class="textarea" name="rules_text" id="rules_text" rows="6"><?= e(old('rules_text', $pageContent['rules_text'] ?? '')) ?></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="scorecard_file">Scorecard Image (jpg/png)</label>
                    <div class="control">
                        <input type="file" name="scorecard_file" id="scorecard_file" accept="image/jpeg,image/png">
                    </div>
                    <?php if (!empty($pageContent['scorecard_path'])): ?>
                        <p class="mt-2">
                            Current image:
                            <img src="<?= e($pageContent['scorecard_path']) ?>" alt="Scorecard" style="max-height:100px;display:block;">
                            <a href="<?= e($pageContent['scorecard_path']) ?>" target="_blank">view/download</a>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-link">Save Content</button>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            </form>
        </div>

        <?php if (empty($groups)): ?>
            <div class="box">
                <div class="notification is-info">
                    No rate groups defined yet.
                </div>
                <div class="mt-4">
                    <a href="/admin/rates/create" class="button is-primary">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Create Group</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="box">
            <!-- desktop/tablet: table layout -->
            <div class="table-container is-hidden-mobile">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Slug</th>
                            <th>Title</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                        <tr>
                            <td><?= e($group['sort_order']) ?></td>
                            <td><code><?= e($group['slug']) ?></code></td>
                            <td><?= e($group['title']) ?></td>
                            <td><?= $group['active'] ? '<span class="tag is-success">Yes</span>' : '<span class="tag is-light">No</span>' ?></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/rates/<?= e($group['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                        <span>Edit</span>
                                    </a>
                                    <a href="/admin/rates/<?= e($group['id']) ?>/rates" class="button is-small is-primary">
                                        <span class="icon is-small"><i class="fas fa-list"></i></span>
                                        <span>Rates</span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this group and all its rates?')){document.getElementById('delete-form-<?= e($group['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                        <span>Delete</span>
                                    </a>
                                    <form id="delete-form-<?= e($group['id']) ?>" method="POST" action="/admin/rates/<?= e($group['id']) ?>" style="display:none;">
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

            <!-- mobile: card stack -->
            <div class="is-hidden-tablet">
                <?php foreach ($groups as $group): ?>
                    <div class="box">
                        <h3 class="title is-5">
                            <?= e($group['title']) ?> <span class="is-size-6 has-text-grey">#<?= e($group['sort_order']) ?></span>
                        </h3>
                        <p><code><?= e($group['slug']) ?></code></p>
                        <p class="mt-1"><?= $group['active'] ? '<span class="tag is-success">Active</span>' : '<span class="tag is-light">Inactive</span>' ?></p>
                        <div class="buttons is-small mt-2">
                            <a href="/admin/rates/<?= e($group['id']) ?>/edit" class="button is-info" title="Edit">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="/admin/rates/<?= e($group['id']) ?>/rates" class="button is-primary" title="Rates">
                                <span class="icon is-small"><i class="fas fa-list"></i></span>
                            </a>
                            <a href="#" class="button is-danger" title="Delete" onclick="if(confirm('Delete this group and all its rates?')){document.getElementById('delete-form-<?= e($group['id']) ?>').submit();}return false;">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                            </a>
                            <form id="delete-form-<?= e($group['id']) ?>" method="POST" action="/admin/rates/<?= e($group['id']) ?>" style="display:none;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- add create button at bottom and close box -->
            <div class="mt-4">
                <a href="/admin/rates/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Create Group</span>
                </a>
            </div>
        </div> <!-- end groups box -->
        <?php endif; ?>
    </div>
</section>
