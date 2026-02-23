<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-envelope"></i> <?= e($title ?? 'Contact') ?>
            </h1>
            <p class="subtitle">
                Get in touch with us
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <!-- replace with contact-specific content later -->
            <p>Use this page to display contact information or a form.</p>
        </div>
    </div>
</section>
