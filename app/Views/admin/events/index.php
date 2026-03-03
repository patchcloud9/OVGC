<?php
/**
 * Admin Event List
 * Variables: $title, $events (array)
 */

use App\Services\EventService;
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-calendar-alt"></i> Manage Events
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
                <a href="/admin/events/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>New Event</span>
                </a>
            </div>
        </div>

        <?php if (empty($events)): ?>
        <div class="notification is-info">No events yet. <a href="/admin/events/create">Create the first one.</a></div>
        <?php else: ?>
        <div class="box" style="padding:0;overflow:hidden;">
        <div class="table-container">
        <table class="table is-fullwidth is-striped is-hoverable" style="margin:0;">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Start Date</th>
                    <th>Recurring</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $ev):
                $cat      = $ev['category'];
                $catMeta  = EventService::CATEGORIES[$cat] ?? EventService::CATEGORIES['other'];
                $startDt  = new DateTime($ev['start_datetime']);
                $isCancelled = $ev['status'] === 'cancelled'
                    || (!empty($ev['cancelled_from']) && $ev['cancelled_from'] <= date('Y-m-d'));
            ?>
            <tr class="<?= $isCancelled ? 'has-text-grey' : '' ?>">
                <td>
                    <?php if ($isCancelled): ?>
                        <s><?= e($ev['title']) ?></s>
                    <?php else: ?>
                        <?= e($ev['title']) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="tag" style="background:<?= e($catMeta['color']) ?>;color:#fff;">
                        <?= e($catMeta['label']) ?>
                    </span>
                </td>
                <td><?= e($startDt->format('M j, Y')) ?></td>
                <td>
                    <?php if (!empty($ev['rrule'])): ?>
                        <span class="tag is-light"><i class="fas fa-sync-alt fa-xs mr-1"></i> Yes</span>
                    <?php else: ?>
                        <span class="has-text-grey-light">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($ev['status'] === 'cancelled'): ?>
                        <span class="tag is-danger">Cancelled</span>
                    <?php elseif (!empty($ev['cancelled_from'])): ?>
                        <span class="tag is-warning">Part. Cancelled</span>
                    <?php else: ?>
                        <span class="tag is-success">Active</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="buttons are-small" style="flex-wrap:nowrap;">
                        <a href="/admin/events/<?= (int)$ev['id'] ?>/edit" class="button is-info is-light" title="Edit">
                            <span class="icon"><i class="fas fa-edit"></i></span>
                        </a>
                        <a href="/admin/events/<?= (int)$ev['id'] ?>/results" class="button is-success is-light" title="Post Results">
                            <span class="icon"><i class="fas fa-trophy"></i></span>
                        </a>
                        <?php if ($ev['status'] !== 'cancelled'): ?>
                        <a href="/admin/events/<?= (int)$ev['id'] ?>/cancel" class="button is-warning is-light">
                            <span class="icon"><i class="fas fa-ban"></i></span>
                        </a>
                        <?php else: ?>
                        <form method="POST" action="/admin/events/<?= (int)$ev['id'] ?>/restore" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="button is-success is-light" title="Restore">
                                <span class="icon"><i class="fas fa-undo"></i></span>
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="/events/<?= (int)$ev['id'] ?>" class="button is-light" title="View" target="_blank">
                            <span class="icon"><i class="fas fa-eye"></i></span>
                        </a>
                        <form method="POST" action="/admin/events/<?= (int)$ev['id'] ?>/delete"
                              style="display:inline;"
                              onsubmit="return confirm('Permanently delete this event and all its results?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="confirm_delete" value="1">
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
