<?php
/**
 * Public Gallery View
 * 
 * Displays all gallery images in a responsive card-based grid layout.
 */
?>

<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-images"></i> Gallery
            </h1>
            <p class="subtitle">
                Browse our collection of images
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        
        <?php if (empty($images)): ?>
            <!-- Empty State -->
            <div class="notification is-info">
                <p class="has-text-centered">
                    <i class="fas fa-image fa-3x mb-3"></i>
                    <br>
                    <strong>No images yet</strong>
                    <br>
                    Check back later for new content!
                </p>
            </div>
        <?php else: ?>
            <!-- Image Grid -->
            <div class="columns is-multiline">
                <?php foreach ($images as $image): ?>
                    <div class="column is-one-quarter-desktop is-one-third-tablet is-full-mobile">
                        <div class="card gallery-card">
                            <div class="card-image">
                                <figure class="image gallery-image-container">
                                    <a href="#" class="gallery-open"
                                       data-path="<?= e($image['file_path']) ?>"
                                       data-title="<?= e($image['title']) ?>"
                                       data-description="<?= e($image['description']) ?>"
                                       data-price-type="<?= e($image['price_type'] ?? '') ?>"
                                       data-price-amount="<?= e($image['price_amount'] ?? '') ?>"
                                       data-prints-available="<?= isset($image['prints_available']) && $image['prints_available'] ? '1' : '0' ?>"
                                       data-prints-url="<?= e($image['prints_url'] ?? '') ?>"
                                       data-theme-email="<?= e(\App\Models\ThemeSetting::get('gallery_contact_email', '')) ?>">
                                        <img src="<?= e($image['file_path']) ?>" alt="<?= e($image['title']) ?>" class="gallery-image">
                                    </a>
                                </figure>
                            </div>
                            <div class="card-content">
                                <div class="content">
                                    <p class="title is-5">
                                        <a href="#" class="gallery-open"
                                           data-path="<?= e($image['file_path']) ?>"
                                           data-title="<?= e($image['title']) ?>"
                                           data-description="<?= e($image['description']) ?>"
                                           data-price-type="<?= e($image['price_type'] ?? '') ?>"
                                           data-price-amount="<?= e($image['price_amount'] ?? '') ?>"
                                           data-prints-available="<?= isset($image['prints_available']) && $image['prints_available'] ? '1' : '0' ?>"
                                           data-prints-url="<?= e($image['prints_url'] ?? '') ?>"
                                           data-theme-email="<?= e(\App\Models\ThemeSetting::get('gallery_contact_email', '')) ?>">
                                            <?= e($image['title']) ?>
                                        </a>
                                    </p>
                                    <?php if (!empty($image['description'])): ?>
                                        <p class="subtitle is-6">
                                            <?= e(substr($image['description'], 0, 100)) ?><?= strlen($image['description']) > 100 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($image['price_type']) && $image['price_type'] !== 'hide'): ?>
                                        <div class="mt-3">
                                            <?php if ($image['price_type'] === 'amount' && !empty($image['price_amount'])): ?>
                                                <p class="has-text-weight-semibold is-size-6 has-text-left" style="margin-bottom:0;">
                                                    <span>$<?= number_format((float)$image['price_amount'], 2) ?> for the original artwork</span>
                                                </p>
                                                <?php $themeEmail = \App\Models\ThemeSetting::get('gallery_contact_email', ''); if (!empty($themeEmail)): ?>
                                                    <p class="is-size-7" style="margin:0;">Email: <a href="mailto:<?= e($themeEmail) ?>"><?= e($themeEmail) ?></a></p>
                                                <div class="mt-2"></div>
                                                <?php endif; ?>
                                            <?php elseif ($image['price_type'] === 'sold_prints'): ?>
                                                <p class="has-text-weight-semibold is-size-7">
                                                    <span class="icon-text">
                                                        <span class="icon has-text-warning">
                                                            <i class="fas fa-certificate"></i>
                                                        </span>
                                                        <span>Original Sold</span>
                                                    </span>
                                                </p>
                                            <?php elseif ($image['price_type'] === 'not_for_sale'): ?>
                                                <p class="has-text-grey is-size-7">
                                                    <span class="icon-text">
                                                        <span class="icon">
                                                            <i class="fas fa-ban"></i>
                                                        </span>
                                                        <span>Not for Sale</span>
                                                    </span>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <!-- Slightly smaller spacer between email and prints button -->
                                            <div class="mt-3" style="height:0.6rem;"></div>
                                            <?php if (isset($image['prints_available']) && $image['prints_available']): ?>
                                                <div class="mt-2" style="text-align:center;">
                                                    <?php if (!empty($image['prints_url'])): ?>
                                                        <a href="<?= e($image['prints_url']) ?>" target="_blank" rel="noopener noreferrer" class="button is-small is-primary">
                                                            <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                                                        <span>Purchase prints and other merchandise</span>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="button is-small is-info" disabled>
                                                            <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                                                            <span>Purchase prints and other merchandise</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination is-centered mt-5" role="navigation" aria-label="pagination">
                    <a href="/gallery?page=<?= max(1, $currentPage - 1) ?>" 
                       class="pagination-previous <?= $currentPage <= 1 ? 'is-disabled' : '' ?>"
                       <?= $currentPage <= 1 ? 'disabled' : '' ?>>
                        Previous
                    </a>
                    <a href="/gallery?page=<?= min($totalPages, $currentPage + 1) ?>" 
                       class="pagination-next <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>"
                       <?= $currentPage >= $totalPages ? 'disabled' : '' ?>>
                        Next
                    </a>
                    <ul class="pagination-list">
                        <?php
                        // Calculate page range to show
                        $range = 2; // Show 2 pages on each side of current page
                        $start = max(1, $currentPage - $range);
                        $end = min($totalPages, $currentPage + $range);
                        
                        // Show first page if not in range
                        if ($start > 1): ?>
                            <li><a href="/gallery?page=1" class="pagination-link">1</a></li>
                            <?php if ($start > 2): ?>
                                <li><span class="pagination-ellipsis">&hellip;</span></li>
                            <?php endif; ?>
                        <?php endif;
                        
                        // Show page numbers in range
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li>
                                <a href="/gallery?page=<?= $i ?>" 
                                   class="pagination-link <?= $i === $currentPage ? 'is-current' : '' ?>"
                                   <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor;
                        
                        // Show last page if not in range
                        if ($end < $totalPages): ?>
                            <?php if ($end < $totalPages - 1): ?>
                                <li><span class="pagination-ellipsis">&hellip;</span></li>
                            <?php endif; ?>
                            <li><a href="/gallery?page=<?= $totalPages ?>" class="pagination-link"><?= $totalPages ?></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <!-- Results info -->
                <p class="has-text-centered has-text-grey mt-3">
                    Showing page <?= $currentPage ?> of <?= $totalPages ?> (<?= $total ?> total images)
                </p>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
</section>

<style>
    /* Gallery card layout - equal heights */
    .gallery-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .card-content {
        flex-grow: 1;
    }
    
    /* Fixed height container for images to maintain consistent card heights */
    .gallery-image-container {
        height: 308px; /* increased to accommodate padding */
        display: flex;
        align-items: flex-start;
        justify-content: center;
        background-color: #fff;
        overflow: hidden;
        padding-top: 8px; /* buffer above image */
    }

    /* Images fit within container while maintaining aspect ratio */
    .gallery-image {
        /* Fill container height so full image is visible top-to-bottom; allow whitespace on the sides */
        max-height: 300px; /* fixed height for full image display */
        width: auto;
        max-width: 100%;
        object-fit: contain;
        display: block;
    }
    
    .gallery-image {
        transition: transform 0.3s ease;
    }
    
    .card-image:hover .gallery-image {
        transform: scale(1.05);
    }
    
    /* Mobile adjustments */
    @media screen and (max-width: 768px) {
        .gallery-image-container {
            height: 258px; /* increased to accommodate padding */
        }

        .gallery-image {
            max-height: 250px; /* maintain full image display on mobile */
        }

        /* Full width columns on mobile with proper padding */
        .column.is-full-mobile {
            padding: 0.75rem;
        }
    }

    /* Purchase button text wrapping and responsive sizing */
    .card-content .button {
        white-space: normal;
        height: auto;
        min-height: 2.5em;
        padding: 0.5em 0.75em;
    }

    .card-content .button span:not(.icon) {
        flex: 1;
        text-align: center;
    }

    /* Make purchase buttons full-width on smaller screens */
    @media screen and (max-width: 1215px) {
        .card-content .button {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }

    /* Fullscreen overlay for image preview */
    .gallery-overlay {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .gallery-overlay.is-active { display: flex; }

    .gallery-overlay-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.85);
    }

    .gallery-overlay-content {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 100%;
        max-height: 100vh;
        overflow: hidden;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gallery-overlay-inner {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gallery-overlay-image {
        max-width: 100%;
        max-height: 95vh;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .gallery-overlay-close {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: transparent;
        border: none;
        color: #fff;
        font-size: 2rem;
        cursor: pointer;
        z-index: 2;
    }

    @media screen and (max-width: 768px) {
        .gallery-overlay-content { padding: 0.5rem; }
    }
</style>

<!-- Full-screen Gallery Overlay -->
<div id="galleryOverlay" class="gallery-overlay" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="gallery-overlay-backdrop" id="galleryOverlayBackdrop"></div>
    <div class="gallery-overlay-content" role="document">
        <button class="gallery-overlay-close" id="galleryOverlayClose" aria-label="Close">&times;</button>
        <div class="gallery-overlay-inner">
            <img id="galleryOverlayImage" src="" alt="" class="gallery-overlay-image" />
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const opens = document.querySelectorAll('.gallery-open');
        const overlay = document.getElementById('galleryOverlay');
        const backdrop = document.getElementById('galleryOverlayBackdrop');
        const closeBtn = document.getElementById('galleryOverlayClose');
        const imgEl = document.getElementById('galleryOverlayImage');

        function open(data) {
            imgEl.src = data.path || '';
            imgEl.alt = data.title || '';
            overlay.classList.add('is-active');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            closeBtn.focus();
        }

        function close() {
            overlay.classList.remove('is-active');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            imgEl.src = '';
            imgEl.alt = '';
        }

        opens.forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const ds = this.dataset;
                open({ path: ds.path, title: ds.title });
            });
        });

        closeBtn.addEventListener('click', close);
        backdrop.addEventListener('click', close);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') close();
        });

        // Protect overlay image from right-click / drag
        imgEl.addEventListener('contextmenu', function(e) { e.preventDefault(); });
        imgEl.addEventListener('dragstart', function(e) { e.preventDefault(); });
    });
</script>
    });
</script>
