<?php
/**
 * Public Flyers Page
 * Variables: $title (string), $flyers (array)
 */
?>

<section class="hero is-primary subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-images"></i> Event Flyers
            </h1>
            <p class="subtitle">Upcoming tournaments and club events</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php if (empty($flyers)): ?>
            <div class="notification is-info is-light">
                <i class="fas fa-info-circle"></i> No flyers are currently posted. Check back soon!
            </div>
        <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($flyers as $flyer):
                    $isImage = strpos($flyer['mime_type'], 'image/') === 0;
                    $expiry  = new DateTime($flyer['expires_at']);
                    $daysLeft = (int) (new DateTime())->diff($expiry)->days;
                    $soon    = $daysLeft <= 7;
                ?>
                <div class="column is-one-third-desktop is-half-tablet">
                    <div class="card" style="height:100%;display:flex;flex-direction:column;">

                        <?php if ($isImage): ?>
                        <div class="card-image">
                            <figure class="image" style="max-height:320px;overflow:hidden;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                                <img src="<?= e($flyer['file_path']) ?>"
                                     alt="<?= e($flyer['title']) ?>"
                                     style="object-fit:contain;max-height:320px;width:100%;">
                            </figure>
                        </div>
                        <?php else: ?>
                        <div class="card-image">
                            <figure class="image is-4by3" style="background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:5rem;color:#b5bfc9;"><i class="fas fa-file-pdf"></i></span>
                            </figure>
                        </div>
                        <?php endif; ?>

                        <div class="card-content" style="flex:1;">
                            <p class="title is-5 mb-1"><?= e($flyer['title']) ?></p>
                            <?php if (!empty($flyer['description'])): ?>
                                <p class="is-size-6 has-text-grey-dark mb-2"><?= nl2br(e($flyer['description'])) ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!$isImage): ?>
                        <footer class="card-footer">
                            <a href="<?= e($flyer['file_path']) ?>" target="_blank" class="card-footer-item">
                                <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                                <span>View PDF</span>
                            </a>
                        </footer>
                        <?php else: ?>
                        <footer class="card-footer">
                            <a href="<?= e($flyer['file_path']) ?>" target="_blank" class="card-footer-item">
                                <span class="icon"><i class="fas fa-expand"></i></span>
                                <span>Full Size</span>
                            </a>
                        </footer>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
