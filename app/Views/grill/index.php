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
            <p class="subtitle">Okanogan Valley Golf Club Clubhouse</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php if (!$exists): ?>
            <div class="notification is-info is-light">
                <i class="fas fa-info-circle"></i> The menu is not currently available. Please check back soon or call the clubhouse at <a href="tel:+15098266937">(509) 826-6937</a>.
            </div>
        <?php else: ?>

            <div class="is-flex is-justify-content-space-between is-align-items-center mb-4" style="flex-wrap:wrap; gap:0.75rem;">
                <p class="has-text-grey">Members receive 10% off all orders. Call <a href="tel:+15098266937">(509) 826-6937</a> to pre-order.</p>
                <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-primary">
                    <span class="icon"><i class="fas fa-download"></i></span>
                    <span>Download Menu</span>
                </a>
            </div>

            <div style="width:100%; height:80vh; min-height:600px;">
                <object
                    data="/assets/menu/menu.pdf"
                    type="application/pdf"
                    style="width:100%; height:100%; border:1px solid #dbdbdb; border-radius:4px;">
                    <div class="notification is-warning is-light">
                        <p><strong>PDF preview not available in your browser.</strong></p>
                        <p class="mt-2">
                            <a href="/assets/menu/menu.pdf" download="OVGC-Menu.pdf" class="button is-primary">
                                <span class="icon"><i class="fas fa-download"></i></span>
                                <span>Download Menu PDF</span>
                            </a>
                        </p>
                    </div>
                </object>
            </div>

        <?php endif; ?>
    </div>
</section>
