<?php
/**
 * Upcoming Events Widget — homepage partial
 * Expects $upcomingEvents from the calling scope (array from EventService::getUpcomingEvents())
 * Optional $spreadEvents (bool): true lays items out in a wrapping row (full-width placement),
 * false/omitted stacks them in a single column (narrow-column placement).
 */
?>
<?php if (!empty($upcomingEvents)): ?>
<section class="upcoming-events<?= !empty($spreadEvents) ? ' upcoming-events--spread' : '' ?>">
    <h2 class="upcoming-events-heading">Upcoming Events</h2>
    <div class="upcoming-events-items">
        <?php foreach ($upcomingEvents as $ev): ?>
        <a href="<?= e($ev['detailUrl']) ?>" class="ev-upcoming-item ev-cat-<?= e($ev['category']) ?>">
            <span class="ev-upcoming-date"><?= e($ev['displayDate']) ?></span>
            <span class="ev-upcoming-title"><?= e($ev['title']) ?></span>
            <?php if ($ev['all_day']): ?>
                <span class="ev-upcoming-time">All Day</span>
            <?php else: ?>
                <span class="ev-upcoming-time"><?= e($ev['displayTime']) ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>
    <a href="/events" class="ev-view-all">View Full Calendar &rarr;</a>
</section>
<?php endif; ?>
