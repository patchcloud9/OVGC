<?php
/**
 * Public Event Results Page
 * Variables: $title, $results (array), $categories (array)
 */

// Group results by year
$byYear = [];
foreach ($results as $row) {
    $year = substr($row['occurrence_date'], 0, 4);
    $byYear[$year][] = $row;
}
krsort($byYear); // newest year first
?>

<section class="hero is-primary subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-trophy"></i> Event Results
            </h1>
            <p class="subtitle">Tournament and league results</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php if (empty($results)): ?>
        <div class="notification is-light">No results have been posted yet. Check back after events!</div>
        <?php else: ?>

        <?php foreach ($byYear as $year => $yearResults): ?>
        <h2 class="title is-4 mb-3"><?= (int) $year ?></h2>

        <div class="columns is-multiline mb-5">
        <?php foreach ($yearResults as $row):
            $cat     = $row['category'];
            $catMeta = $categories[$cat] ?? $categories['other'];
            $dt      = new DateTime($row['occurrence_date']);
        ?>
        <div class="column is-12-tablet is-6-desktop">
            <a href="<?= e($row['detailUrl']) ?>" class="box result-card" style="display:block;text-decoration:none;color:inherit;">
                <div class="level is-mobile mb-2">
                    <div class="level-left">
                        <span class="tag" style="background:<?= e($catMeta['color']) ?>;color:#fff;">
                            <?= e($catMeta['label']) ?>
                        </span>
                    </div>
                    <div class="level-right">
                        <span class="has-text-grey is-size-7"><?= e($dt->format('M j, Y')) ?></span>
                    </div>
                </div>
                <p class="title is-5 mb-2"><?= e($row['title']) ?></p>
                <?php if (!empty($row['results_text'])): ?>
                <div class="content is-size-7 has-text-grey result-preview">
                    <?= mb_strimwidth(strip_tags($row['results_text']), 0, 120, '…') ?>
                </div>
                <?php endif; ?>
                <p class="is-size-7 has-text-info mt-2">
                    View results <i class="fas fa-arrow-right fa-xs"></i>
                </p>
            </a>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php endif; ?>

        <div class="mt-4">
            <a href="/events" class="button is-light">
                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                <span>Events Calendar</span>
            </a>
        </div>
    </div>
</section>
