<?php
/**
 * Upcoming Events Widget — homepage partial
 * Expects $upcomingEvents from the calling scope (array from EventService::getUpcomingEvents())
 */
?>
<?php if (!empty($upcomingEvents)): ?>
<section class="upcoming-events">
    <h2 class="upcoming-events-heading">Upcoming Events</h2>
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
    <a href="/events" class="ev-view-all">View Full Calendar &rarr;</a>
</section>
<?php endif; ?>
