<?php
/**
 * Public Grill Menu Page
 * Variables: $title (string), $exists (bool)
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

<section class="section" style="padding-top:1rem;">
    <div class="container">
        <?php if (!$exists): ?>
            <div class="notification is-info is-light">
                <i class="fas fa-info-circle"></i> The menu is not currently available. Please check back soon.
            </div>
        <?php else: ?>

            <div class="has-text-right mb-3">
                <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-primary is-small">
                    <span class="icon"><i class="fas fa-download"></i></span>
                    <span>Download</span>
                </a>
            </div>

            <iframe
                src="/assets/menu/menu.pdf"
                style="width:100%; height:82vh; min-height:600px; border:1px solid #dbdbdb; border-radius:4px;"
                title="Grill Menu">
                <div class="notification is-warning is-light mt-4">
                    <p><strong>PDF preview not available in your browser.</strong></p>
                    <p class="mt-2">
                        <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-primary">
                            <span class="icon"><i class="fas fa-download"></i></span>
                            <span>Download Menu PDF</span>
                        </a>
                    </p>
                </div>
            </iframe>

        <?php endif; ?>
    </div>
</section>
