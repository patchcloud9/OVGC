<?php
/**
 * Admin Flyer List
 * Variables: $title (string), $flyers (array)
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-images"></i> Manage Flyers
            </h1>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="level">
            <div class="level-left"></div>
            <div class="level-right">
                <a href="/admin/flyers/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Add Flyer</span>
                </a>
            </div>
        </div>

        <?php if (empty($flyers)): ?>
            <div class="notification is-info">
                No flyers yet. <a href="/admin/flyers/create">Add the first one.</a>
            </div>
        <?php else: ?>
        <div class="box" style="padding:0;overflow:hidden;">
        <div class="table-container">
        <table class="table is-fullwidth is-striped is-hoverable" style="margin:0;">
            <thead>
                <tr>
                    <th style="width:60px;">Preview</th>
                    <th>Title</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($flyers as $flyer):
                $isImage   = strpos($flyer['mime_type'], 'image/') === 0;
                $expired   = (bool) $flyer['is_expired'];
                $expiry    = new DateTime($flyer['expires_at']);
                $daysLeft  = $expired ? 0 : (int) (new DateTime())->diff($expiry)->days;
                $soon      = !$expired && $daysLeft <= 7;
            ?>
            <tr class="<?= $expired ? 'has-text-grey' : '' ?>">
                <td style="vertical-align:middle;padding:0.4rem 0.75rem;">
                    <?php if ($isImage): ?>
                        <img src="<?= e($flyer['file_path']) ?>"
                             alt="<?= e($flyer['title']) ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                    <?php else: ?>
                        <span class="icon is-large has-text-danger"><i class="fas fa-file-pdf fa-2x"></i></span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align:middle;">
                    <?php if ($expired): ?>
                        <s><?= e($flyer['title']) ?></s>
                    <?php else: ?>
                        <?= e($flyer['title']) ?>
                    <?php endif; ?>
                    <?php if (!empty($flyer['description'])): ?>
                        <p class="is-size-7 has-text-grey"><?= e(mb_substr($flyer['description'], 0, 80)) ?><?= mb_strlen($flyer['description']) > 80 ? '…' : '' ?></p>
                    <?php endif; ?>
                </td>
                <td style="vertical-align:middle;">
                    <?= e($expiry->format('M j, Y')) ?>
                    <?php if ($soon): ?>
                        <br><span class="tag is-warning is-light is-small"><?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?> left</span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align:middle;">
                    <?php if ($expired): ?>
                        <span class="tag is-danger">Expired</span>
                    <?php elseif ($soon): ?>
                        <span class="tag is-warning">Expiring Soon</span>
                    <?php else: ?>
                        <span class="tag is-success">Active</span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align:middle;">
                    <div class="buttons are-small" style="flex-wrap:nowrap;">
                        <a href="/admin/flyers/<?= (int) $flyer['id'] ?>/edit"
                           class="button is-info is-light" title="Edit">
                            <span class="icon"><i class="fas fa-edit"></i></span>
                        </a>
                        <a href="<?= e($flyer['file_path']) ?>" target="_blank"
                           class="button is-light" title="View">
                            <span class="icon"><i class="fas fa-eye"></i></span>
                        </a>
                        <form method="POST" action="/admin/flyers/<?= (int) $flyer['id'] ?>/delete"
                              style="display:inline;"
                              onsubmit="return confirm('Delete this flyer? This cannot be undone.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="button is-danger is-light" title="Delete">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="/admin" class="button is-light">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>Admin Panel</span>
            </a>
        </div>
    </div>
</section>
