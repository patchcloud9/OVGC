<?php
/**
 * Admin Event Create / Edit Form
 * Variables: $title, $event (null=create, array=edit), $categories, $skipDates (array)
 */

$isEdit    = ($event !== null);
$action    = $isEdit ? '/admin/events/' . (int)$event['id'] . '/edit' : '/admin/events/create';

// Parse existing datetime values for the form
$startDt   = $isEdit ? new DateTime($event['start_datetime']) : null;
$endDt     = $isEdit ? new DateTime($event['end_datetime'])   : null;
$startDate = $startDt ? $startDt->format('Y-m-d') : '';
$startTime = $startDt ? $startDt->format('H:i')   : '08:00';
$endDate   = $endDt   ? $endDt->format('Y-m-d')   : '';
$endTime   = $endDt   ? $endDt->format('H:i')     : '17:00';

// Parse RRULE into component parts
$hasRrule  = $isEdit && !empty($event['rrule']);
$rruleParts = [];
if ($hasRrule) {
    foreach (explode(';', $event['rrule']) as $seg) {
        if (strpos($seg, '=') !== false) {
            [$k, $v] = explode('=', $seg, 2);
            $rruleParts[strtoupper($k)] = $v;
        }
    }
}
$rruleFreq   = $rruleParts['FREQ']  ?? 'WEEKLY';
$rruleByday  = $rruleParts['BYDAY'] ?? '';
$rruleUntil  = '';
if (!empty($rruleParts['UNTIL'])) {
    $u = preg_replace('/[^0-9]/', '', $rruleParts['UNTIL']);
    if (strlen($u) >= 8) {
        $rruleUntil = substr($u, 0, 4) . '-' . substr($u, 4, 2) . '-' . substr($u, 6, 2);
    }
}
// WEEKLY BYDAY days (may be comma-separated: "MO,WE,FR")
$rruleWeeklyDays = $rruleFreq === 'WEEKLY' ? array_map('trim', explode(',', $rruleByday)) : [];
// MONTHLY BYDAY (ordinal+day like "1SA")
$rruleMonthlyByday = ($rruleFreq === 'MONTHLY') ? $rruleByday : '';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <i class="fas fa-calendar-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
                <?= $isEdit ? 'Edit Event' : 'Create Event' ?>
            </h1>
            <?php if ($isEdit): ?>
            <p class="subtitle is-6 has-text-warning-light">
                <i class="fas fa-exclamation-triangle fa-xs"></i>
                Changes here update <strong>all occurrences</strong> of this series.
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="<?= e($action) ?>" id="event-form">
            <?= csrf_field() ?>

            <div class="columns">
                <div class="column is-8">
                    <div class="box">
                        <!-- Title -->
                        <div class="field">
                            <label class="label">Title <span class="has-text-danger">*</span></label>
                            <div class="control">
                                <input class="input" type="text" name="title" required
                                    value="<?= e($isEdit ? $event['title'] : old('title', '')) ?>"
                                    placeholder="e.g. Men's League">
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="field">
                            <label class="label">Category <span class="has-text-danger">*</span></label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="category">
                                        <?php foreach ($categories as $key => $cat):
                                            $sel = ($isEdit ? $event['category'] : old('category', 'other')) === $key;
                                        ?>
                                        <option value="<?= e($key) ?>" <?= $sel ? 'selected' : '' ?>>
                                            <?= e($cat['label']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="field">
                            <label class="label">Description</label>
                            <div class="control">
                                <textarea class="textarea" name="description" rows="4"
                                    placeholder="Include member-only restrictions, handicap limits, contact info, etc."><?= e($isEdit ? $event['description'] : old('description', '')) ?></textarea>
                            </div>
                            <p class="help">Freeform. Eligibility rules, contact details, and any notes belong here.</p>
                        </div>
                    </div><!-- /.box -->

                    <!-- Date / Time -->
                    <div class="box">
                        <h2 class="title is-5 mb-4"><i class="fas fa-clock"></i> Date &amp; Time</h2>

                        <div class="field">
                            <label class="checkbox">
                                <input type="checkbox" id="all-day-cb" name="all_day" value="1"
                                    <?= ($isEdit && $event['all_day']) ? 'checked' : '' ?>>
                                &nbsp;All Day Event
                            </label>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Start Date <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <input class="input" type="date" name="start_date" required
                                            value="<?= e($startDate ?: old('start_date', '')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="column" id="start-time-col">
                                <div class="field">
                                    <label class="label">Start Time</label>
                                    <div class="control">
                                        <input class="input" type="time" name="start_time"
                                            value="<?= e($startTime ?: old('start_time', '08:00')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">End Date <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <input class="input" type="date" name="end_date" required
                                            value="<?= e($endDate ?: old('end_date', '')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="column" id="end-time-col">
                                <div class="field">
                                    <label class="label">End Time</label>
                                    <div class="control">
                                        <input class="input" type="time" name="end_time"
                                            value="<?= e($endTime ?: old('end_time', '17:00')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box date/time -->

                    <!-- Recurrence -->
                    <div class="box">
                        <h2 class="title is-5 mb-4"><i class="fas fa-sync-alt"></i> Recurrence</h2>

                        <div class="field">
                            <label class="checkbox">
                                <input type="checkbox" id="recurring-cb" name="recurring" value="1"
                                    <?= $hasRrule ? 'checked' : '' ?>>
                                &nbsp;Recurring Event
                            </label>
                        </div>

                        <div id="recurrence-fields" style="<?= $hasRrule ? '' : 'display:none;' ?>">
                            <!-- Frequency -->
                            <div class="field">
                                <label class="label">Repeat</label>
                                <div class="control">
                                    <div class="select">
                                        <select id="freq-select" name="freq">
                                            <option value="DAILY"   <?= $rruleFreq === 'DAILY'   ? 'selected' : '' ?>>Every Day</option>
                                            <option value="WEEKLY"  <?= $rruleFreq === 'WEEKLY'  ? 'selected' : '' ?>>Every Week</option>
                                            <option value="MONTHLY" <?= $rruleFreq === 'MONTHLY' ? 'selected' : '' ?>>Every Month</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Weekly: days of week -->
                            <div id="weekly-fields" style="<?= $rruleFreq === 'WEEKLY' && $hasRrule ? '' : 'display:none;' ?>">
                                <div class="field">
                                    <label class="label">On these days</label>
                                    <div class="control">
                                        <?php
                                        $weekDays = ['MO'=>'Mon','TU'=>'Tue','WE'=>'Wed','TH'=>'Thu','FR'=>'Fri','SA'=>'Sat','SU'=>'Sun'];
                                        foreach ($weekDays as $code => $label):
                                            $checked = in_array($code, $rruleWeeklyDays, true) ? 'checked' : '';
                                        ?>
                                        <label class="checkbox mr-3">
                                            <input type="checkbox" class="weekly-day-cb" name="weekly_days[]"
                                                value="<?= e($code) ?>" <?= $checked ?>>
                                            <?= e($label) ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Hidden BYDAY field assembled by JS -->
                                <input type="hidden" id="byday-weekly" name="byday" value="<?= $rruleFreq === 'WEEKLY' ? e($rruleByday) : '' ?>">
                            </div>

                            <!-- Monthly: nth weekday -->
                            <div id="monthly-fields" style="<?= $rruleFreq === 'MONTHLY' && $hasRrule ? '' : 'display:none;' ?>">
                                <div class="field">
                                    <label class="label">On</label>
                                    <div class="control">
                                        <input class="input" type="text" id="byday-monthly" name="byday"
                                            value="<?= e($rruleMonthlyByday) ?>"
                                            placeholder="e.g. 1SA (1st Sat), -1MO (last Mon), 2TU (2nd Tue)">
                                    </div>
                                    <p class="help">Format: <code>1SA</code> = 1st Saturday, <code>-1MO</code> = last Monday, <code>2TU</code> = 2nd Tuesday</p>
                                </div>
                            </div>

                            <!-- DAILY has no extra fields -->

                            <!-- Until date -->
                            <div class="field">
                                <label class="label">Repeat Until <span class="has-text-grey">(optional)</span></label>
                                <div class="control">
                                    <input class="input" type="date" name="until"
                                        value="<?= e($rruleUntil) ?>"
                                        style="max-width:220px;">
                                </div>
                                <p class="help">Leave blank for an indefinitely recurring event.</p>
                            </div>
                        </div><!-- /#recurrence-fields -->
                    </div><!-- /.box recurrence -->

                    <!-- Skip / Blackout Dates -->
                    <div class="box">
                        <h2 class="title is-5 mb-3"><i class="fas fa-calendar-times"></i> Blackout Dates</h2>
                        <p class="help mb-3">Dates on which this event does NOT occur (silently removed from calendar).</p>

                        <div id="skip-tags" class="tags mb-2">
                            <?php foreach ($skipDates as $sd): ?>
                            <span class="tag is-light is-medium skip-date-tag">
                                <?= e($sd) ?>
                                <button type="button" class="delete is-small" data-date="<?= e($sd) ?>"></button>
                            </span>
                            <?php endforeach; ?>
                        </div>

                        <div class="field has-addons">
                            <div class="control">
                                <input class="input" type="date" id="skip-date-picker" style="max-width:200px;">
                            </div>
                            <div class="control">
                                <button type="button" id="add-skip-date" class="button is-light">
                                    <span class="icon"><i class="fas fa-plus"></i></span>
                                    <span>Add Date</span>
                                </button>
                            </div>
                        </div>

                        <!-- Comma-separated list submitted to server -->
                        <input type="hidden" id="skip-dates-input" name="skip_dates"
                            value="<?= e(implode(',', $skipDates)) ?>">
                    </div><!-- /.box blackout -->

                    <!-- Linked Flyer -->
                    <div class="box">
                        <h2 class="title is-5 mb-3"><i class="fas fa-images"></i> Linked Flyer</h2>
                        <p class="help mb-3">Optionally attach a flyer to this event. It will appear on the event detail page.</p>

                        <?php
                        $selectedFlyerId = $isEdit ? ($event['flyer_id'] ?? null) : null;
                        ?>

                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="flyer_id" id="flyer-select">
                                        <option value="">— No flyer —</option>
                                        <?php foreach ($flyers as $f):
                                            $expired = (bool)($f['is_expired'] ?? false);
                                            $sel = ((int)$f['id'] === (int)$selectedFlyerId);
                                        ?>
                                        <option value="<?= (int)$f['id'] ?>"
                                            <?= $sel ? 'selected' : '' ?>
                                            data-path="<?= e($f['file_path']) ?>"
                                            data-mime="<?= e($f['mime_type']) ?>">
                                            <?= e($f['title']) ?><?= $expired ? ' (expired)' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Live thumbnail preview -->
                        <div id="flyer-preview" class="mt-3" style="display:none;">
                            <img id="flyer-preview-img" src="" alt=""
                                 style="max-width:100%;max-height:220px;object-fit:contain;border:1px solid #ddd;border-radius:4px;">
                            <p id="flyer-preview-pdf" class="has-text-danger" style="display:none;">
                                <span class="icon"><i class="fas fa-file-pdf fa-2x"></i></span>
                                <span>PDF flyer attached</span>
                            </p>
                        </div>
                    </div><!-- /.box flyer -->

                </div><!-- /.column is-8 -->
            </div><!-- /.columns -->

            <div class="buttons">
                <button type="submit" class="button is-primary">
                    <span class="icon"><i class="fas fa-save"></i></span>
                    <span><?= $isEdit ? 'Save Changes' : 'Create Event' ?></span>
                </button>
                <a href="/admin/events" class="button is-light">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script src="/assets/js/admin-event-form.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/admin-event-form.js') ?>"></script>
