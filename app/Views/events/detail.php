<?php
/**
 * Event Detail Page
 * Handles both pre-event (upcoming) and post-event (results) states.
 * Also handles one-time and recurring occurrence URLs.
 *
 * Variables:
 *   $event          array  — full events row
 *   $occurrenceDate string — 'Y-m-d'
 *   $status         string — 'active' or 'cancelled'
 *   $isPostEvent    bool   — occurrence date < today
 *   $results        array|null — event_results row if posted
 *   $isRecurring    bool
 *   $title          string
 */

use App\Services\EventService;

$cat     = $event['category'];
$catMeta = EventService::CATEGORIES[$cat] ?? EventService::CATEGORIES['other'];

$startDt  = new DateTime($event['start_datetime']);
$endDt    = new DateTime($event['end_datetime']);
$allDay   = (bool)$event['all_day'];

// Build display date/time for this occurrence
$occDt    = new DateTime($occurrenceDate . ' ' . $startDt->format('H:i:s'));
$occEndDt = new DateTime($occurrenceDate . ' ' . $endDt->format('H:i:s'));

if ($allDay) {
    $displayDateTime = $occDt->format('l, F j, Y') . ' — All Day';
} else {
    $displayDateTime = $occDt->format('l, F j, Y')
        . ' &bull; '
        . $occDt->format('g:i A') . '–' . $occEndDt->format('g:i A');
}

// Recurring human-readable description
$recurringLabel = '';
if ($isRecurring && !empty($event['rrule'])) {
    $parts = [];
    foreach (explode(';', $event['rrule']) as $seg) {
        if (strpos($seg, '=') !== false) {
            [$k, $v] = explode('=', $seg, 2);
            $parts[$k] = $v;
        }
    }
    $freq = $parts['FREQ'] ?? '';
    $labels = ['DAILY' => 'Daily', 'WEEKLY' => 'Weekly', 'MONTHLY' => 'Monthly'];
    $recurringLabel = $labels[$freq] ?? '';
    if ($freq === 'WEEKLY' && !empty($parts['BYDAY'])) {
        $dayNames = ['MO'=>'Mon','TU'=>'Tue','WE'=>'Wed','TH'=>'Thu','FR'=>'Fri','SA'=>'Sat','SU'=>'Sun'];
        $days = array_map(fn($d) => $dayNames[trim($d)] ?? $d, explode(',', $parts['BYDAY']));
        $recurringLabel .= ' — ' . implode(', ', $days);
    }
    if (!empty($parts['UNTIL'])) {
        $u = preg_replace('/[^0-9]/', '', $parts['UNTIL']);
        $untilDt = DateTime::createFromFormat('Ymd', substr($u, 0, 8));
        if ($untilDt) {
            $recurringLabel .= ' through ' . $untilDt->format('M j, Y');
        }
    }
    $recurringLabel .= ' &bull; ' . $startDt->format('g:i A') . '–' . $endDt->format('g:i A');
}
?>

<link rel="stylesheet" href="/assets/css/events.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/events.css') ?>">

<section class="hero is-primary subpage-hero">
    <div class="hero-body">
        <div class="container">
            <span class="tag ev-cat-tag mb-2" style="background:<?= e($catMeta['color']) ?>;color:#fff;font-size:0.8rem;">
                <?= e($catMeta['label']) ?>
            </span>
            <h1 class="title"><?= e($event['title']) ?></h1>
            <p class="subtitle"><?= $displayDateTime ?></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="columns">
            <div class="column is-8">

                <?php if ($status === 'cancelled'): ?>
                <div class="notification is-danger ev-cancelled-banner mb-4">
                    <span class="icon"><i class="fas fa-ban"></i></span>
                    <strong>This event has been cancelled.</strong>
                </div>
                <?php endif; ?>

                <?php if ($isRecurring && $recurringLabel): ?>
                <div class="notification is-light mb-4" style="padding:0.75rem 1rem;">
                    <span class="icon has-text-info"><i class="fas fa-sync-alt"></i></span>
                    Recurring: <?= $recurringLabel ?>
                </div>
                <?php endif; ?>

                <?php if ($status !== 'cancelled'): ?>

                    <?php if ($results): ?>
                    <!-- ── Results posted (pre- or post-event) ── -->
                    <h2 class="title is-4 mt-4"><i class="fas fa-trophy"></i> Results</h2>
                    <div class="content ev-results-content">
                        <?= format_results($results['results_text'] ?? '') ?>
                    </div>
                    <?php if (!empty($results['conditions_notes'])): ?>
                    <div class="notification is-light mt-4">
                        <p class="heading">Course Conditions / Notes</p>
                        <div class="content"><?= nl2br(e($results['conditions_notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                    <p class="has-text-grey is-size-7 mt-2">
                        Posted <?= e((new DateTime($results['posted_at']))->format('M j, Y')) ?>
                    </p>
                    <?php if (!empty($event['description'])): ?>
                    <div class="content mt-4">
                        <?= nl2br(e($event['description'])) ?>
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <!-- ── No results yet ── -->
                    <?php if (!empty($event['description'])): ?>
                    <div class="content">
                        <?= nl2br(e($event['description'])) ?>
                    </div>
                    <?php else: ?>
                    <p class="has-text-grey">No additional details available.</p>
                    <?php endif; ?>
                    <?php if ($isPostEvent): ?>
                    <div class="notification is-light mt-4">
                        Results have not been posted yet. Check back soon.
                    </div>
                    <?php endif; ?>
                    <?php endif; // results ?>

                <?php endif; // not cancelled ?>

                <div class="mt-5">
                    <a href="/events" class="button is-light">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Back to Calendar</span>
                    </a>
                    <?php if (is_admin()): ?>
                    <a href="/admin/events/<?= (int)$event['id'] ?>/results/<?= e($occurrenceDate) ?>"
                       class="button is-info is-light">
                        <span class="icon"><i class="fas fa-trophy"></i></span>
                        <span>Back to Results</span>
                    </a>
                    <?php endif; ?>
                </div>

            </div><!-- /.column -->
        </div><!-- /.columns -->
    </div>
</section>
