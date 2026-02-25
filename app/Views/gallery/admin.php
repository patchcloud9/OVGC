<?php
/**
 * Admin Gallery Management View
 * 
 * Upload, view, and delete gallery images (admin only).
 */
?>

<!-- hero updated to dark subpage style and removed container wrapper -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-images"></i> Manage Gallery
            </h1>
            <p class="subtitle">
                Upload and manage gallery images
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Gallery</a></li>
            </ul>
        </nav>
        
        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <!-- Stats Cards -->
        <div class="columns">
            <div class="column">
                <div class="card">
                    <div class="card-content">
                        <div class="level">
                            <div class="level-item has-text-centered">
                                <div>
                                    <p class="heading">Total Images</p>
                                    <p class="title"><?= $stats['total'] ?></p>
                                </div>
                            </div>
                            <div class="level-item has-text-centered">
                                <div>
                                    <p class="heading">Recent (7 days)</p>
                                    <p class="title"><?= $stats['recent'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upload Form -->
        <div class="card mb-5">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-upload"></i></span>
                    <span>Upload New Image</span>
                </p>
            </header>
            <div class="card-content">
                <form action="/admin/gallery" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="field">
                        <label class="label">Image File *</label>
                        <div class="control">
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="image" accept="image/*" required onchange="updateFileName(this)">
                                    <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">
                                            Choose a file…
                                        </span>
                                    </span>
                                    <span class="file-name" id="file-name">
                                        No file selected
                                    </span>
                                </label>
                            </div>
                        </div>
                        <p class="help">Supported formats: JPG, PNG, GIF, WebP. Max size: 5MB</p>
                    </div>
                    
                    <div class="field">
                        <label class="label">Title *</label>
                        <div class="control">
                            <input class="input" type="text" name="title" placeholder="Enter image title" required maxlength="255">
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea class="textarea" name="description" placeholder="Optional description" rows="3" maxlength="1000"></textarea>
                        </div>
                        <p class="help">Maximum 1000 characters</p>
                    </div>
                    
                    <!-- Pricing Options -->
                    <div class="field">
                        <label class="label">Pricing Display</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="price_type" id="price-type-select" onchange="togglePriceAmount()">
                                    <option value="hide">Hide - Don't show pricing</option>
                                    <option value="amount">Show Price - Display dollar amount</option>
                                    <option value="sold_prints">Original Sold (Prints Available)</option>
                                    <option value="not_for_sale">Not for Sale</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Amount (conditional) -->
                    <div class="field" id="price-amount-field" style="display: none;">
                        <label class="label">Price Amount</label>
                        <div class="control has-icons-left">
                            <input class="input" type="number" name="price_amount" placeholder="400.00" step="0.01" min="0" max="999999.99">
                            <span class="icon is-small is-left">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <p class="help">Price for original artwork (e.g., 400.00)</p>
                    </div>
                    
                    <!-- Prints Available -->
                    <div class="field">
                        <label class="label">Print Availability</label>
                        <div class="control">
                            <label class="checkbox">
                                <input type="hidden" name="prints_available" value="0">
                                <input type="checkbox" name="prints_available" value="1" id="prints-available-checkbox" onchange="togglePrintsUrl()">
                                Prints are available for purchase
                            </label>
                        </div>
                    </div>
                    
                    <!-- Prints URL (conditional) -->
                    <div class="field" id="prints-url-field" style="display: none;">
                        <label class="label">Prints Purchase URL</label>
                        <div class="control has-icons-left">
                            <input class="input" type="url" name="prints_url" placeholder="https://example.com/prints/artwork-123" maxlength="512">
                            <span class="icon is-small is-left">
                                <i class="fas fa-link"></i>
                            </span>
                        </div>
                        <p class="help">Link to where customers can purchase prints (Etsy, Fine Art America, etc.)</p>
                    </div>
                    
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-primary">
                                <span class="icon">
                                    <i class="fas fa-upload"></i>
                                </span>
                                <span>Upload Image</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Images Grid -->
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-images"></i></span>
                    <span>Uploaded Images (<?= count($images) ?>)</span>
                </p>
            </header>
            <div class="card-content">
                
                <?php if (empty($images)): ?>
                    <div class="notification is-info">
                        <p class="has-text-centered">
                            <i class="fas fa-image fa-3x mb-3"></i>
                            <br>
                            <strong>No images yet</strong>
                            <br>
                            Upload your first image using the form above!
                        </p>
                    </div>
                <?php else: ?>
                    
                    <!-- Image Cards -->
                    <div class="columns is-multiline">
                        <?php foreach ($images as $image): ?>
                            <div class="column is-one-third" id="image-<?= e($image['id']) ?>">
                                <div class="card gallery-admin-card">
                                    <div class="card-image">
                                        <figure class="image gallery-admin-image-container">
                                            <img src="<?= e($image['file_path']) ?>" alt="<?= e($image['title']) ?>" class="gallery-admin-image">
                                        </figure>
                                    </div>
                                    <div class="card-content">
                                        <div class="content">
                                            <p class="title is-6"><?= e($image['title']) ?></p>
                                            <?php if (!empty($image['description'])): ?>
                                                <p class="is-size-7">
                                                    <?= e(substr($image['description'], 0, 80)) ?><?= strlen($image['description']) > 80 ? '...' : '' ?>
                                                </p>
                                            <?php endif; ?>
                                            <p class="is-size-7 has-text-grey">
                                                <i class="fas fa-user"></i> <?= e($image['uploader_name'] ?? 'Unknown') ?>
                                                <br>
                                                <i class="fas fa-clock"></i> <?= date('M j, Y g:i A', strtotime($image['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <footer class="card-footer is-flex-direction-column">
                                        <div class="card-footer-row">
                                            <a href="#" class="card-footer-item" onclick="moveImage(<?= e($image['id']) ?>, 'up'); return false;">
                                                <span class="icon"><i class="fas fa-arrow-up"></i></span>
                                                <span>Up</span>
                                            </a>
                                            <a href="#" class="card-footer-item" onclick="moveImage(<?= e($image['id']) ?>, 'down'); return false;">
                                                <span class="icon"><i class="fas fa-arrow-down"></i></span>
                                                <span>Down</span>
                                            </a>
                                        </div>
                                        <div class="card-footer-row">
                                            <a href="/admin/gallery/<?= e($image['id']) ?>/edit" class="card-footer-item">
                                                <span class="icon"><i class="fas fa-edit"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <a href="<?= e($image['file_path']) ?>" class="card-footer-item" target="_blank" rel="noopener noreferrer">
                                                <span class="icon"><i class="fas fa-eye"></i></span>
                                                <span>View</span>
                                            </a>
                                        </div>
                                        <div class="card-footer-row">
                                            <a href="#" class="card-footer-item has-text-danger" onclick="deleteImage(<?= e($image['id']) ?>, '<?= e($image['title']) ?>'); return false;">
                                                <span class="icon"><i class="fas fa-trash"></i></span>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </footer>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
</section>

<!-- Hidden delete form -->
<form id="delete-form" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Hidden reorder form -->
<form id="reorder-form" method="POST" action="/admin/gallery/reorder" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="image_id" id="reorder-image-id">
    <input type="hidden" name="direction" id="reorder-direction">
</form>

<script>
    // Update file name display
    function updateFileName(input) {
        const fileName = input.files[0]?.name || 'No file selected';
        document.getElementById('file-name').textContent = fileName;
    }
    
    // Delete image with confirmation
    function deleteImage(imageId, imageTitle) {
        if (!confirm(`Are you sure you want to delete "${imageTitle}"?\n\nThis action cannot be undone.`)) {
            return;
        }
        
        const form = document.getElementById('delete-form');
        form.action = `/admin/gallery/${imageId}`;
        form.submit();
    }
    
    // Move image up or down - save position to return to after reload
    function moveImage(imageId, direction) {
        // Store the image ID in session storage to scroll back after page reload
        sessionStorage.setItem('scrollToImage', imageId);
        
        document.getElementById('reorder-image-id').value = imageId;
        document.getElementById('reorder-direction').value = direction;
        document.getElementById('reorder-form').submit();
    }
    
    // On page load, scroll back to the image that was moved
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToImageId = sessionStorage.getItem('scrollToImage');
        
        if (scrollToImageId) {
            const imageElement = document.getElementById('image-' + scrollToImageId);
            
            if (imageElement) {
                // Scroll to the image with smooth behavior
                setTimeout(() => {
                    imageElement.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    // Add a brief highlight effect
                    imageElement.style.transition = 'background-color 0.5s';
                    imageElement.style.backgroundColor = 'rgba(72, 199, 142, 0.1)';
                    
                    setTimeout(() => {
                        imageElement.style.backgroundColor = '';
                    }, 1000);
                }, 100);
            }
            
            // Clear the stored image ID
            sessionStorage.removeItem('scrollToImage');
        }
    });
</script>

<style>
    /* Gallery admin card layout - equal heights */
    .gallery-admin-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .gallery-admin-card .card-content {
        flex-grow: 1;
    }
    
    /* Card footer with two rows */
    .card-footer.is-flex-direction-column {
        flex-direction: column;
    }
    
    .card-footer-row {
        display: flex;
        width: 100%;
        border-bottom: 1px solid #ededed;
    }
    
    .card-footer-row:last-child {
        border-bottom: none;
    }
    
    .card-footer-row .card-footer-item {
        flex: 1;
        border-right: 1px solid #ededed;
        border-left: none;
    }
    
    .card-footer-row .card-footer-item:last-child {
        border-right: none;
    }
    
    .card-footer-row .card-footer-item:first-child {
        border-left: none;
    }
    
    /* Card footer link colors */
    .card-footer-item:not(.has-text-danger) {
        color: var(--primary-color);
    }
    
    .card-footer-item:not(.has-text-danger):hover {
        color: var(--primary-hover-color);
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Fixed height container for images */
    .gallery-admin-image-container {
        height: 258px; /* increased to accommodate padding */
        display: flex;
        align-items: flex-start; /* align to top with padding */
        justify-content: center;
        background-color: #fff;
        overflow: hidden;
        padding-top: 8px; /* buffer above image */
    }

    /* Images fit within container */
    .gallery-admin-image {
        /* Fill container height so full image is visible top-to-bottom; allow whitespace on the sides */
        max-height: 250px; /* fixed height for full image display */
        width: auto;
        max-width: 100%;
        object-fit: contain;
        display: block;
        transition: transform 0.3s ease;
    }
    
    .card-image:hover .gallery-admin-image {
        transform: scale(1.05);
    }
</style>
<script>
    // Toggle price amount field based on pricing type
    function togglePriceAmount() {
        const priceType = document.getElementById('price-type-select').value;
        const priceAmountField = document.getElementById('price-amount-field');
        
        if (priceType === 'amount') {
            priceAmountField.style.display = 'block';
        } else {
            priceAmountField.style.display = 'none';
        }
    }
    
    // Toggle prints URL field based on checkbox
    function togglePrintsUrl() {
        const printsCheckbox = document.getElementById('prints-available-checkbox');
        const printsUrlField = document.getElementById('prints-url-field');
        
        if (printsCheckbox.checked) {
            printsUrlField.style.display = 'block';
        } else {
            printsUrlField.style.display = 'none';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePriceAmount();
        togglePrintsUrl();
    });