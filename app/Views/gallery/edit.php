<?php
/**
 * Edit Gallery Image View
 * 
 * Form to update an existing gallery image's metadata.
 */
?>

<section class="section">
    <div class="container">
        
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/gallery">Gallery</a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit Image</a></li>
            </ul>
        </nav>
        
        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-edit"></i>
                </span>
                <span>Edit Gallery Image</span>
            </span>
        </h1>
        
        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <div class="columns">
            <!-- Image Preview -->
            <div class="column is-5">
                <div class="card">
                    <div class="card-image">
                        <figure class="image">
                            <img src="<?= e($image['file_path']) ?>" alt="<?= e($image['title']) ?>" style="width: 100%; height: auto; object-fit: contain;">
                        </figure>
                    </div>
                    <div class="card-content">
                        <p class="has-text-grey-light is-size-7">
                            <i class="fas fa-info-circle"></i> Image file cannot be changed. Upload a new image to replace this one.
                        </p>
                        <p class="is-size-7 mt-2">
                            <strong>Filename:</strong> <?= e($image['filename']) ?><br>
                            <strong>Uploaded:</strong> <?= date('M j, Y g:i A', strtotime($image['created_at'])) ?><br>
                            <strong>By:</strong> <?= e($image['uploader_name'] ?? 'Unknown') ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Edit Form -->
            <div class="column is-7">
                <div class="card">
                    <div class="card-content">
                        <form action="/admin/gallery/<?= e($image['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="field">
                                <label class="label">Title *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="title" value="<?= e($image['title']) ?>" placeholder="Artwork title" required minlength="3" maxlength="255">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-heading"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="field">
                                <label class="label">Description</label>
                                <div class="control">
                                    <textarea class="textarea" name="description" placeholder="Optional description" rows="4" maxlength="1000"><?= e($image['description'] ?? '') ?></textarea>
                                </div>
                                <p class="help">Maximum 1000 characters</p>
                            </div>
                            
                            <!-- Pricing Options -->
                            <div class="field">
                                <label class="label">Pricing Display</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="price_type" id="price-type-select" onchange="togglePriceAmount()">
                                            <option value="hide" <?= ($image['price_type'] ?? 'hide') === 'hide' ? 'selected' : '' ?>>Hide - Don't show pricing</option>
                                            <option value="amount" <?= ($image['price_type'] ?? '') === 'amount' ? 'selected' : '' ?>>Show Price - Display dollar amount</option>
                                            <option value="sold_prints" <?= ($image['price_type'] ?? '') === 'sold_prints' ? 'selected' : '' ?>>Original Sold (Prints Available)</option>
                                            <option value="not_for_sale" <?= ($image['price_type'] ?? '') === 'not_for_sale' ? 'selected' : '' ?>>Not for Sale</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price Amount (conditional) -->
                            <div class="field" id="price-amount-field">
                                <label class="label">Price Amount</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="number" name="price_amount" value="<?= e($image['price_amount'] ?? '') ?>" placeholder="400.00" step="0.01" min="0" max="999999.99">
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
                                        <input type="checkbox" name="prints_available" value="1" id="prints-available-checkbox" onchange="togglePrintsUrl()" <?= !empty($image['prints_available']) ? 'checked' : '' ?>>
                                        Prints are available for purchase
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Prints URL (conditional) -->
                            <div class="field" id="prints-url-field">
                                <label class="label">Prints Purchase URL</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="url" name="prints_url" value="<?= e($image['prints_url'] ?? '') ?>" placeholder="https://example.com/prints/artwork-123" maxlength="512">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-link"></i>
                                    </span>
                                </div>
                                <p class="help">Link to where customers can purchase prints (Etsy, Fine Art America, etc.)</p>
                            </div>
                            
                            <hr>
                            
                            <div class="field is-grouped">
                                <div class="control">
                                    <button type="submit" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span>Save Changes</span>
                                    </button>
                                </div>
                                <div class="control">
                                    <a href="/admin/gallery" class="button is-light">
                                        <span class="icon">
                                            <i class="fas fa-times"></i>
                                        </span>
                                        <span>Cancel</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

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
</script>
