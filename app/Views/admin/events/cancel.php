<?php
/**
 * Admin Cancel Event Form
 * Variables: $title, $event (array)
 */
$startDt     = new DateTime($event['start_datetime']);
$seriesStart = $startDt->format('Y-m-d');
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-ban"></i> Cancel Event
            </h1>
            <p class="subtitle is-6"><?= e($event['title']) ?></p>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns">
            <div class="column is-5">
                <div class="box">
                    <div class="notification is-warning is-light mb-4">
                        <strong>How cancellation works:</strong>
                        <ul class="mt-2" style="list-style:disc;padding-left:1.2rem;">
                            <li>Pick the <em>first date to cancel</em>.</li>
                            <li>That date and all future occurrences will show as <strong>Cancelled</strong> on the calendar.</li>
                            <li>If you pick the series start date
                                (<strong><?= e($startDt->format('M j, Y')) ?></strong>),
                                the entire series is cancelled.</li>
                            <li>To undo, use the <strong>Restore</strong> button on the event list.</li>
                        </ul>
                    </div>

                    <form method="POST" action="/admin/events/<?= (int)$event['id'] ?>/cancel">
                        <?= csrf_field() ?>

                        <div class="field">
                            <label class="label">Cancel from date <span class="has-text-danger">*</span></label>
                            <div class="control">
                                <input class="input" type="date" name="cancel_from_date" required
                                    min="<?= e($seriesStart) ?>"
                                    value="<?= e(date('Y-m-d')) ?>">
                            </div>
                            <p class="help">This date and all future occurrences will be marked cancelled.</p>
                        </div>

                        <div class="buttons mt-4">
                            <button type="submit" class="button is-danger">
                                <span class="icon"><i class="fas fa-ban"></i></span>
                                <span>Apply Cancellation</span>
                            </button>
                            <a href="/admin/events" class="button is-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
