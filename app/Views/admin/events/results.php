<?php
/**
 * Admin Post-Event Results Form
 * Variables: $title, $event (array), $occurrenceDate (string), $results (array|null)
 */
$occDt = new DateTime($occurrenceDate . ' ' . (new DateTime($event['start_datetime']))->format('H:i:s'));
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-trophy"></i> Post Results
            </h1>
            <p class="subtitle is-6">
                <?= e($event['title']) ?> &mdash; <?= e($occDt->format('M j, Y')) ?>
            </p>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns">
            <div class="column is-8">
                <div class="box">
                    <form method="POST"
                          action="/admin/events/<?= (int)$event['id'] ?>/results/<?= e($occurrenceDate) ?>">
                        <?= csrf_field() ?>

                        <div class="field">
                            <label class="label">Results / Leaderboard</label>
                            <div class="control">
                                <textarea class="textarea" name="results_text" rows="10"
                                    placeholder="Winners, scores, leaderboard... Basic HTML is allowed."><?= e($results['results_text'] ?? '') ?></textarea>
                            </div>
                            <p class="help">Basic HTML allowed (bold, lists, etc.).</p>
                        </div>

                        <div class="field">
                            <label class="label">Course Conditions / Notes <span class="has-text-grey">(optional)</span></label>
                            <div class="control">
                                <textarea class="textarea" name="conditions_notes" rows="3"
                                    placeholder="Weather, course conditions, attendance notes..."><?= e($results['conditions_notes'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <?php if ($results): ?>
                        <p class="help mb-3 has-text-grey">
                            Last updated: <?= e((new DateTime($results['posted_at']))->format('M j, Y g:i A')) ?>
                        </p>
                        <?php endif; ?>

                        <div class="buttons">
                            <button type="submit" class="button is-primary">
                                <span class="icon"><i class="fas fa-save"></i></span>
                                <span><?= $results ? 'Update Results' : 'Post Results' ?></span>
                            </button>
                            <a href="/admin/events" class="button is-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
