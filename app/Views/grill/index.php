<?php
/**
 * Public Grill Menu Page
 * Variables: $title (string), $imageExists (bool), $pdfExists (bool)
 */
?>

<section class="hero is-primary subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-utensils"></i> Grill Menu
            </h1>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">

        <?php if (!$imageExists && !$pdfExists): ?>
            <div class="notification is-info is-light">
                <i class="fas fa-info-circle"></i> The menu is not currently available. Please check back soon.
            </div>

        <?php elseif ($imageExists): ?>
            <?php if ($pdfExists): ?>
            <div class="has-text-right mb-3">
                <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-primary is-small">
                    <span class="icon"><i class="fas fa-download"></i></span>
                    <span>Download PDF</span>
                </a>
            </div>
            <?php endif; ?>
            <figure class="image" style="max-width:900px; margin:0 auto;">
                <img src="/assets/menu/menu-display.jpg?v=<?= @filemtime(BASE_PATH . '/public/assets/menu/menu-display.jpg') ?>"
                     alt="Grill Menu"
                     style="width:100%; height:auto; border:1px solid #dbdbdb; border-radius:4px;">
            </figure>

        <?php else: ?>
            <div class="has-text-centered" style="padding: 3rem 1rem;">
                <p style="font-size: 5rem; color: #b5bfc9; line-height:1;">
                    <i class="fas fa-file-pdf"></i>
                </p>
                <p class="title is-4 mt-4">Okanogan Valley Golf Club Grill Menu</p>
                <div class="buttons is-centered mt-5">
                    <a href="/assets/menu/menu.pdf" target="_blank" rel="noopener" class="button is-primary is-medium">
                        <span class="icon"><i class="fas fa-eye"></i></span>
                        <span>View Menu</span>
                    </a>
                    <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-light is-medium">
                        <span class="icon"><i class="fas fa-download"></i></span>
                        <span>Download</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
