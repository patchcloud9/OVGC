<?php
/**
 * Admin list of minutes
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title"><i class="fas fa-file-pdf"></i> <?= e($title) ?></h1>
            <p class="subtitle has-text-white">Manage uploaded meeting minutes</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Board Minutes</a></li>
            </ul>
        </nav>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/board-minutes/create" class="button is-primary">
                <span class="icon"><i class="fas fa-upload"></i></span>
                <span>Upload Minutes</span>
            </a>
        </div>

        <?php if (empty($minutes)): ?>
            <div class="notification is-info">No minutes uploaded yet.</div>
        <?php else: ?>
            <table class="table is-fullwidth is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($minutes as $m): ?>
                        <tr>
                            <td><?= e($m['meeting_date']) ?></td>
                            <td><a href="<?= e($m['file_path']) ?>" target="_blank"><?= e($m['filename']) ?></a></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/board-minutes/<?= e($m['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this record?')){document.getElementById('delete-form-<?= e($m['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                    </a>
                                </div>
                                <form id="delete-form-<?= e($m['id']) ?>" method="POST" action="/admin/board-minutes/<?= e($m['id']) ?>" style="display:none;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <?= csrf_field() ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>
