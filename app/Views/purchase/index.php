<?php
$layout = 'main';
?>

<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-shopping-cart"></i> <?= e($title ?? ($content['page_title'] ?? 'Purchase')) ?>
            </h1>
            <?php if (!empty($content['page_subtitle'])): ?>
                <p class="subtitle">
                    <?= e($content['page_subtitle']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="content" style="max-width:820px;margin:0 auto;text-align:left;">
                <?php if (!empty($content['content_text'])): ?>
                    <?php
                        // Replace {email} placeholder with the theme contact email
                        $text = nl2br(e($content['content_text']));
                        $email = \App\Models\ThemeSetting::get('gallery_contact_email', '');

                        if (!empty($email)) {
                            $text = str_replace('{email}', "<a href=\"mailto:" . e($email) . "\">" . e($email) . "</a>", $text);
                        } else {
                            // Remove the placeholder if no theme email is configured
                            $text = str_replace('{email}', '', $text);
                        }

                        echo $text;
                    ?>
                <?php endif; ?>

                <!-- Centered button -->
                <?php if (!empty($content['button_url'])): ?>
                    <div style="text-align:center;margin-top:2.5rem;">
                        <a href="<?= e($content['button_url']) ?>" class="button is-primary" target="_blank" rel="noopener noreferrer"><?= e($content['button_text'] ?? 'Learn More') ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
