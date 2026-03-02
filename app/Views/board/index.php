<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-users"></i> Board Members
            </h1>
            <p class="subtitle">
                Meet our leadership team
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!empty($members)): ?>
            <div class="columns is-multiline">
                <?php foreach ($members as $m): ?>
                    <div class="column is-half-tablet is-one-third-desktop">
                        <div class="box">
                            <div class="media">
                                <?php if (!empty($m['photo_path'])): ?>
                                    <figure class="media-left image is-96x96">
                                        <img class="is-rounded" src="<?= e($m['photo_path']) ?>" alt="<?= e($m['name']) ?>">
                                    </figure>
                                <?php endif; ?>
                                <div class="media-content">
                                    <h3 class="title is-5"><?= e($m['name']) ?></h3>
                                    <p class="subtitle is-6 has-text-grey"><?= e($m['title']) ?></p>
                                    <p><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="has-text-centered has-text-grey">No board members listed at this time.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="title is-4">Board Minutes</h2>

        <?php if (empty($minutes)): ?>
            <p class="has-text-grey">No minutes available.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($minutes as $item): ?>
                    <?php
                        $dt = new \DateTime($item['meeting_date']);
                        $day = (int) $dt->format('j');
                        $suffix = ordinal_suffix($day);
                        $label = $dt->format('Y F ') . $day . $suffix . ' Board Minutes';
                    ?>
                    <li>
                        <a href="<?= e($item['file_path']) ?>" target="_blank"><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($totalPages > 1): ?>
                <nav class="pagination is-centered mt-4" role="navigation" aria-label="pagination">
                    <a href="/board-members?page=<?= max(1, $currentPage - 1) ?>" class="pagination-previous <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" <?= $currentPage <= 1 ? 'disabled' : '' ?>>Previous</a>
                    <a href="/board-members?page=<?= min($totalPages, $currentPage + 1) ?>" class="pagination-next <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" <?= $currentPage >= $totalPages ? 'disabled' : '' ?>>Next</a>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
