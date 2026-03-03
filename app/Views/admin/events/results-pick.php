<?php
/**
 * Admin Results — Pick Occurrence (recurring events)
 * Variables: $title, $event (array), $pastOccurrences (string[]), $existingResults (array keyed by date)
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
                    <p class="heading mb-3">Select an occurrence to post or update results</p>

                    <?php if (empty($pastOccurrences)): ?>
                    <p class="has-text-grey">No past occurrences found for this event.</p>
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
                        <?php foreach ($pastOccurrences as $date):
                            $hasResults = isset($existingResults[$date]);
                            $dt = new DateTime($date);
                        ?>
                        <tr>
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
