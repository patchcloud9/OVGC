<?php
/**
 * Admin listing for board members
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-users"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Manage board member directory</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Board Members</a></li>
            </ul>
        </nav>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/board-members/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Add Member</span>
            </a>
        </div>

        <?php if (empty($members)): ?>
            <div class="notification is-info">No members yet.</div>
        <?php else: ?>
            <div class="table-container is-hidden-mobile">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?= e($m['sort_order']) ?></td>
                            <td><?= e($m['name']) ?></td>
                            <td><?= e($m['title']) ?></td>
                            <?php $email = $m['email'] ?: theme_setting('contact_email'); ?>
                            <td><a href="mailto:<?= e($email) ?>"><?= e($email) ?><?= empty($m['email']) ? ' <em>(site contact)</em>' : '' ?></a></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/board-members/<?= e($m['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this member?')){document.getElementById('delete-form-<?= e($m['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                    </a>
                                </div>
                                <form id="delete-form-<?= e($m['id']) ?>" method="POST" action="/admin/board-members/<?= e($m['id']) ?>" style="display:none;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <?= csrf_field() ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- mobile cards -->
            <div class="is-hidden-tablet">
                <?php foreach ($members as $m): ?>
                    <div class="box">
                        <h3 class="title is-5"><?= e($m['name']) ?></h3>
                        <p class="is-size-6 has-text-grey"><?= e($m['title']) ?></p>
                        <?php $email = $m['email'] ?: theme_setting('contact_email'); ?>
                        <p><a href="mailto:<?= e($email) ?>"><?= e($email) ?><?= empty($m['email']) ? ' <em>(site contact)</em>' : '' ?></a></p>
                        <div class="buttons is-small mt-2">
                            <a href="/admin/board-members/<?= e($m['id']) ?>/edit" class="button is-info"><span class="icon is-small"><i class="fas fa-edit"></i></span></a>
                            <a href="#" class="button is-danger" onclick="if(confirm('Delete this member?')){document.getElementById('delete-form-<?= e($m['id']) ?>').submit();}return false;"><span class="icon is-small"><i class="fas fa-trash"></i></span></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
