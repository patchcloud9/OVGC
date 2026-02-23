<?php
/**
 * Public About Page
 */
$layout = 'main';
?>

<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-user-circle"></i> <?= e($content['page_title'] ?? 'About the Artist') ?>
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

        <!-- Section 1 -->
        <?php if (!empty($content['section1_text']) || !empty($content['section1_image'])): ?>
        <?php
            // Get alignment values
            $s1_align_h = $content['section1_text_align_h'] ?? 'left';
            $s1_align_v = $content['section1_text_align_v'] ?? 'top';

            // Map horizontal alignment to Bulma classes
            $s1_h_class = match($s1_align_h) {
                'center' => 'has-text-centered',
                'right' => 'has-text-right',
                default => 'has-text-left'
            };

            // Map vertical alignment to flex styles
            $s1_v_style = match($s1_align_v) {
                'middle' => 'display: flex; align-items: center;',
                'bottom' => 'display: flex; align-items: flex-end;',
                default => ''
            };
        ?>
        <div class="columns is-variable is-8 mb-6">
            <?php if (($content['section1_image_position'] ?? 'left') === 'left'): ?>
                <!-- Image on left -->
                <?php if (!empty($content['section1_image'])): ?>
                <div class="column is-5">
                    <figure class="image">
                        <img src="<?= e($content['section1_image']) ?>" alt="<?= e($content['page_title']) ?>" style="width: 100%; height: auto;">
                    </figure>
                </div>
                <?php endif; ?>
                <div class="column <?= empty($content['section1_image']) ? '' : 'is-7' ?>" style="<?= $s1_v_style ?>">
                    <div class="content is-size-5 <?= $s1_h_class ?>">
                        <?= nl2br(e($content['section1_text'] ?? '')) ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Image on right -->
                <div class="column <?= empty($content['section1_image']) ? '' : 'is-7' ?>" style="<?= $s1_v_style ?>">
                    <div class="content is-size-5 <?= $s1_h_class ?>">
                        <?= nl2br(e($content['section1_text'] ?? '')) ?>
                    </div>
                </div>
                <?php if (!empty($content['section1_image'])): ?>
                <div class="column is-5">
                    <figure class="image">
                        <img src="<?= e($content['section1_image']) ?>" alt="<?= e($content['page_title']) ?>" style="width: 100%; height: auto;">
                    </figure>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Section 2 -->
        <?php if (!empty($content['section2_text']) || !empty($content['section2_image'])): ?>
        <?php
            // Get alignment values
            $s2_align_h = $content['section2_text_align_h'] ?? 'left';
            $s2_align_v = $content['section2_text_align_v'] ?? 'top';

            // Map horizontal alignment to Bulma classes
            $s2_h_class = match($s2_align_h) {
                'center' => 'has-text-centered',
                'right' => 'has-text-right',
                default => 'has-text-left'
            };

            // Map vertical alignment to flex styles
            $s2_v_style = match($s2_align_v) {
                'middle' => 'display: flex; align-items: center;',
                'bottom' => 'display: flex; align-items: flex-end;',
                default => ''
            };
        ?>
        <div class="columns is-variable is-8 mt-6">
            <?php if (($content['section2_image_position'] ?? 'left') === 'left'): ?>
                <!-- Image on left -->
                <?php if (!empty($content['section2_image'])): ?>
                <div class="column is-5">
                    <figure class="image">
                        <img src="<?= e($content['section2_image']) ?>" alt="<?= e($content['page_title']) ?>" style="width: 100%; height: auto;">
                    </figure>
                </div>
                <?php endif; ?>
                <div class="column <?= empty($content['section2_image']) ? '' : 'is-7' ?>" style="<?= $s2_v_style ?>">
                    <div class="content is-size-5 <?= $s2_h_class ?>">
                        <?= nl2br(e($content['section2_text'] ?? '')) ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Image on right -->
                <div class="column <?= empty($content['section2_image']) ? '' : 'is-7' ?>" style="<?= $s2_v_style ?>">
                    <div class="content is-size-5 <?= $s2_h_class ?>">
                        <?= nl2br(e($content['section2_text'] ?? '')) ?>
                    </div>
                </div>
                <?php if (!empty($content['section2_image'])): ?>
                <div class="column is-5">
                    <figure class="image">
                        <img src="<?= e($content['section2_image']) ?>" alt="<?= e($content['page_title']) ?>" style="width: 100%; height: auto;">
                    </figure>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Artist Signature -->
        <?php if (!empty($content['artist_signature'])): ?>
        <div class="has-text-centered mt-6">
            <p class="is-size-4 has-text-weight-semibold">
                <?= e($content['artist_signature']) ?>
            </p>
        </div>
        <?php endif; ?>

    </div>
</section>
