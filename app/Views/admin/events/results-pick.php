<?php
/**
 * Admin Results — Pick Occurrence (recurring events)
 * Variables: $title, $event (array), $occurrences (string[]), $existingResults (array keyed by date)
 */
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-trophy"></i> Post Results
            </h1>
            <p class="subtitle is-6"><?= e($event['title']) ?></p>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns">
            <div class="column is-7">
                <div class="box">
                    <div class="level mb-3">
                        <div class="level-left">
                            <p class="heading level-item mb-0">Select an occurrence to post or update results</p>
                        </div>
                        <div class="level-right">
                            <label class="checkbox level-item" style="font-size:0.85rem;">
                                <input type="checkbox" id="hideFuture">
                                &nbsp;Hide future results
                            </label>
                        </div>
                    </div>

                    <?php if (empty($occurrences)): ?>
                    <p class="has-text-grey">No occurrences found for this event.</p>
                    <?php else: ?>
                    <table class="table is-fullwidth is-hoverable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Results</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($occurrences as $date):
                            $hasResults = isset($existingResults[$date]);
                            $dt = new DateTime($date);
                        ?>
                        <tr data-date="<?= e($date) ?>">
                            <td><?= e($dt->format('D, M j, Y')) ?></td>
                            <td>
                                <?php if ($hasResults): ?>
                                <span class="tag is-success is-light">
                                    <span class="icon"><i class="fas fa-check"></i></span>
                                    <span>Posted</span>
                                </span>
                                <?php else: ?>
                                <span class="tag is-light has-text-grey">Not posted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/events/<?= (int)$event['id'] ?>/results/<?= e($date) ?>"
                                   class="button is-small <?= $hasResults ? 'is-info is-light' : 'is-primary is-light' ?>">
                                    <span class="icon"><i class="fas fa-<?= $hasResults ? 'edit' : 'plus' ?>"></i></span>
                                    <span><?= $hasResults ? 'Edit' : 'Post' ?></span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <div class="mt-2">
                    <a href="/admin/events" class="button is-light">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Back to Events</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const cb    = document.getElementById('hideFuture');
    const today = new Date().toISOString().slice(0, 10);

    function applyFilter() {
        document.querySelectorAll('tr[data-date]').forEach(function (row) {
            row.style.display = (cb.checked && row.dataset.date > today) ? 'none' : '';
        });
        localStorage.setItem('results-hide-future', cb.checked ? '1' : '0');
    }

    // Restore saved preference
    if (localStorage.getItem('results-hide-future') === '1') {
        cb.checked = true;
        applyFilter();
    }

    cb.addEventListener('change', applyFilter);
})();
</script>
