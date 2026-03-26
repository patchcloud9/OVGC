<?php
// Load events widget styles (widget appears in camera/events section)
?>
<link rel="stylesheet" href="/assets/css/events.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/events.css') ?>">
<?php
// Hero background styling: prefer an uploaded image when present, otherwise use the color
$heroStyle = '';
if (!empty($settings['hero_background_image'])) {
    $heroStyle = "background-image: url('" . e($settings['hero_background_image']) . "'); background-size: cover; background-position: center;";
} else {
    $heroStyle = "background-color: " . e($settings['hero_background_color'] ?? '#667eea') . ";";
}
?>

<!-- Hero Section -->
<section class="hero is-medium homepage-hero" style="<?= $heroStyle ?>; position: relative; min-height: 480px; background-attachment: scroll; overflow: hidden;">
    <!-- Flash Messages positioned at top of hero -->
    <div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 800px; z-index: 10;">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
    </div>
    
    <div class="hero-body" style="padding-top: 120px; position: relative; z-index: 1;">
        <div class="container has-text-centered">
            <?php if (!empty($settings['hero_title'])): ?>
            <h1 class="title is-1" style="color: <?= e($settings['hero_title_color'] ?? '#ffffff') ?>;"><?= e($settings['hero_title']) ?></h1>
            <?php endif; ?>
            <?php if (!empty($settings['hero_subtitle'])): ?>
                <h2 class="subtitle" style="color: <?= e($settings['hero_subtitle_color'] ?? '#f5f5f5') ?>;"><?= tpl(e($settings['hero_subtitle'])) ?></h2>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Feature Cards Section -->
<section class="section home-cards" style="padding-bottom: 0.5rem;">
    <div class="container">
        <div class="columns">
            <!-- Card 1 -->
            <div class="column is-4">
                <div class="box has-text-centered">
                    <div class="card-body">
                        <h3 class="title is-4 mt-3"><?= e($settings['card1_title'] ?? 'Fast Performance') ?></h3>
                        <p><?= tpl(nl2br(e($settings['card1_text'] ?? 'Built with modern PHP and optimized for speed.'))) ?></p>
                    </div>
                    <?php if (!empty($settings['card1_button_text'])): ?>
                    <div class="card-footer mt-4">
                        <a href="<?= e($settings['card1_button_link'] ?? '/membership') ?>" class="button is-primary is-small">
                            <?= e($settings['card1_button_text']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="column is-4">
                <div class="box has-text-centered">
                    <div class="card-body">
                        <h3 class="title is-4 mt-3"><?= e($settings['card2_title'] ?? 'Secure') ?></h3>
                        <p><?= tpl(nl2br(e($settings['card2_text'] ?? 'CSRF protection, authentication, and secure password hashing built in.'))) ?></p>
                    </div>
                    <?php if (!empty($settings['card2_button_text'])): ?>
                    <div class="card-footer mt-4">
                        <a href="<?= e($settings['card2_button_link'] ?? '/membership') ?>" class="button is-primary is-small">
                            <?= e($settings['card2_button_text']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="column is-4">
                <div class="box has-text-centered">
                    <div class="card-body">
                        <h3 class="title is-4 mt-3"><?= e($settings['card3_title'] ?? 'Responsive') ?></h3>
                        <p><?= tpl(nl2br(e($settings['card3_text'] ?? 'Mobile-friendly design using Bulma CSS framework.'))) ?></p>
                    </div>
                    <?php if (!empty($settings['card3_button_text'])): ?>
                    <div class="card-footer mt-4">
                        <a href="<?= e($settings['card3_button_link'] ?? '/membership') ?>" class="button is-primary is-small">
                            <?= e($settings['card3_button_text']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Call-to-action removed - buttons now configured per card -->
    </div>
</section>

<!-- Weather Widget Section -->
<?php if (!empty($weatherData)): ?>
<section class="section" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/weather-widget.php'; ?>
    </div>
</section>
<?php endif; ?>

<!-- Camera + Upcoming Events Section -->
<section class="section" style="padding-top: 0.5rem;">
    <div class="container">
        <div class="columns">
            <!-- Left column for camera -->
            <div class="column is-6">
                <?php if (($settings['camera_mode'] ?? 'live') === 'maintenance' && !empty($settings['camera_maintenance_image'])): ?>
                    <figure class="image">
                        <img src="<?= e($settings['camera_maintenance_image']) ?>" alt="Camera Unavailable" style="border-radius:8px;">
                    </figure>
                    <p class="is-italic is-size-7 mt-2">Camera temporarily unavailable</p>
                <?php else: ?>
                    <figure class="image">
                        <img id="camera1" src="/camera/live" alt="Traffic Camera" style="border-radius:8px;">
                    </figure>
                    <p class="is-italic is-size-7 mt-2">(updates every 10&ndash;60 seconds)</p>
                    <script src="/assets/js/camera-poll.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/camera-poll.js') ?>"></script>
                <?php endif; ?>
            </div>
            <!-- Right column for upcoming events -->
            <div class="column is-6">
                <?php if (!empty($upcomingEvents)): ?>
                    <?php require BASE_PATH . '/app/Views/partials/upcoming-events.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Content Section -->
<?php if (!empty($settings['bottom_section_title']) || !empty($settings['bottom_section_text']) || !empty($settings['bottom_section_image'])): ?>
<section class="section has-background-light">
    <div class="container">
        <div class="columns is-vcentered <?= ($settings['bottom_section_layout'] ?? 'text-image') === 'image-text' ? 'is-reverse-mobile' : '' ?>">
            <?php if (($settings['bottom_section_layout'] ?? 'text-image') === 'text-image'): ?>
                <!-- Text Column -->
                <div class="column is-6">
                    <h2 class="title is-3 has-text-centered"><?= e($settings['bottom_section_title'] ?? 'About This Framework') ?></h2>
                    <div class="content">
                        <p class="is-size-5"><?= tpl(nl2br(e($settings['bottom_section_text'] ?? 'This is a minimal, educational PHP MVC framework demonstrating front controller and routing patterns.'))) ?></p>
                    </div>
                </div>

                <!-- Image Column -->
                <div class="column is-6">
                    <?php if (!empty($settings['bottom_section_image'])): ?>
                        <figure class="image">
                            <img src="<?= e($settings['bottom_section_image']) ?>" alt="<?= e($settings['bottom_section_title']) ?>" style="border-radius: 8px;">
                        </figure>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Image Column (Left) -->
                <div class="column is-6">
                    <?php if (!empty($settings['bottom_section_image'])): ?>
                        <figure class="image">
                            <img src="<?= e($settings['bottom_section_image']) ?>" alt="<?= e($settings['bottom_section_title']) ?>" style="border-radius: 8px;">
                        </figure>
                    <?php endif; ?>
                </div>
                
                <!-- Text Column (Right) -->
                <div class="column is-6">
                    <h2 class="title is-3 has-text-centered"><?= e($settings['bottom_section_title'] ?? 'About This Framework') ?></h2>
                    <div class="content">
                        <p class="is-size-5"><?= tpl(nl2br(e($settings['bottom_section_text'] ?? 'This is a minimal, educational PHP MVC framework demonstrating front controller and routing patterns.'))) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

