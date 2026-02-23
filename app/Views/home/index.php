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
<section class="hero is-medium" style="<?= $heroStyle ?>; position: relative; min-height: 480px; background-attachment: scroll; overflow: hidden;">
    <!-- Flash Messages positioned at top of hero -->
    <div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 800px; z-index: 10;">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
    </div>
    
    <div class="hero-body" style="padding-top: 120px; position: relative; z-index: 1;">
        <div class="container has-text-centered">
            <h1 class="title is-1" style="color: <?= e($settings['hero_title_color'] ?? '#ffffff') ?>;"><?= e($settings['hero_title'] ?? 'Welcome Home') ?></h1>
            <?php if (!empty($settings['hero_subtitle'])): ?>
                <?php
                    $hero_sub = $settings['hero_subtitle'];
                    $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                    $hero_sub = e($hero_sub);
                    if (!empty($themeEmail)) {
                        $hero_sub = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $hero_sub);
                    } else {
                        $hero_sub = str_replace('{email}', '', $hero_sub);
                    }
                ?>
                <h2 class="subtitle" style="color: <?= e($settings['hero_subtitle_color'] ?? '#f5f5f5') ?>;"><?= $hero_sub ?></h2>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Feature Cards Section -->
<section class="section home-cards">
    <div class="container">
        <div class="columns">
            <!-- Card 1 -->
            <div class="column is-4">
                <div class="box has-text-centered">
                    <div class="card-body">
                        <h3 class="title is-4 mt-3"><?= e($settings['card1_title'] ?? 'Fast Performance') ?></h3>
                        <?php
                            $card1 = $settings['card1_text'] ?? 'Built with modern PHP and optimized for speed.';
                            $card1 = nl2br(e($card1));
                            $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                            if (!empty($themeEmail)) {
                                $card1 = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $card1);
                            } else {
                                $card1 = str_replace('{email}', '', $card1);
                            }
                        ?>
                        <p><?= $card1 ?></p>
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
                        <?php
                            $card2 = $settings['card2_text'] ?? 'CSRF protection, authentication, and secure password hashing built in.';
                            $card2 = nl2br(e($card2));
                            $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                            if (!empty($themeEmail)) {
                                $card2 = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $card2);
                            } else {
                                $card2 = str_replace('{email}', '', $card2);
                            }
                        ?>
                        <p><?= $card2 ?></p>
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
                        <?php
                            $card3 = $settings['card3_text'] ?? 'Mobile-friendly design using Bulma CSS framework.';
                            $card3 = nl2br(e($card3));
                            $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                            if (!empty($themeEmail)) {
                                $card3 = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $card3);
                            } else {
                                $card3 = str_replace('{email}', '', $card3);
                            }
                        ?>
                        <p><?= $card3 ?></p>
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
                        <?php
                            $bottomText = $settings['bottom_section_text'] ?? 'This is a minimal, educational PHP MVC framework demonstrating front controller and routing patterns.';
                            $bottomText = nl2br(e($bottomText));
                            $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                            if (!empty($themeEmail)) {
                                $bottomText = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $bottomText);
                            } else {
                                $bottomText = str_replace('{email}', '', $bottomText);
                            }
                        ?>
                        <p class="is-size-5"><?= $bottomText ?></p>
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
                        <?php
                            $bottomText = $settings['bottom_section_text'] ?? 'This is a minimal, educational PHP MVC framework demonstrating front controller and routing patterns.';
                            $bottomText = nl2br(e($bottomText));
                            $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', '');
                            if (!empty($themeEmail)) {
                                $bottomText = str_replace('{email}', "<a href=\"mailto:" . e($themeEmail) . "\">" . e($themeEmail) . "</a>", $bottomText);
                            } else {
                                $bottomText = str_replace('{email}', '', $bottomText);
                            }
                        ?>
                        <p class="is-size-5"><?= $bottomText ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

