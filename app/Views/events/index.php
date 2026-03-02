<?php
/**
 * Public Events Calendar Page
 * Renders the FullCalendar shell; event data is fetched via AJAX from /events/feed.
 *
 * Variables: $title, $categories (array from EventService::CATEGORIES)
 */
?>

<!-- FullCalendar v6 (loaded here so it only affects this page) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

<section class="hero is-primary subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-calendar-alt"></i> Events Calendar
            </h1>
            <p class="subtitle">Upcoming tournaments, league play, and club events</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- Subscribe to Calendar (top right) -->
        <div class="has-text-right mb-2">
            <a href="/events/calendar.ics" class="button is-light is-small" title="Subscribe in Google Calendar, Apple Calendar, or Outlook">
                <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                <span>Subscribe to Calendar</span>
            </a>
            <p class="is-size-7 has-text-grey mt-1">
                Paste the link into Google Calendar → "Add by URL", Apple Calendar → "Subscribe", or Outlook → "Add from web".
            </p>
        </div>

        <!-- Calendar -->
        <div class="box">
            <div id="events-calendar"></div>
        </div>

        <!-- Category Legend -->
        <div class="box">
            <p class="heading mb-2">Event Categories</p>
            <div class="ev-legend">
                <?php foreach ($categories as $key => $cat): ?>
                    <span class="ev-legend-item">
                        <span class="ev-legend-dot" style="background:<?= e($cat['color']) ?>"></span>
                        <span class="ev-legend-label"><?= e($cat['label']) ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- FullCalendar JS + init (deferred until DOM ready) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="/assets/js/events-calendar.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/events-calendar.js') ?>"></script>
<link rel="stylesheet" href="/assets/css/events.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/events.css') ?>">
