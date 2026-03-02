/**
 * Admin event create/edit form behaviour:
 *   - Show/hide time fields when All Day is toggled
 *   - Show/hide recurrence fields when Recurring is toggled
 *   - Show/hide Weekly vs Monthly sub-fields based on Frequency
 *   - Assemble BYDAY hidden input from checkboxes (WEEKLY)
 *   - Blackout date tag management (add / remove skip dates)
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Element refs ──────────────────────────────────────────────────────────
    var allDayCb       = document.getElementById('all-day-cb');
    var startTimeCol   = document.getElementById('start-time-col');
    var endTimeCol     = document.getElementById('end-time-col');

    var recurringCb    = document.getElementById('recurring-cb');
    var recurrenceDiv  = document.getElementById('recurrence-fields');
    var freqSelect     = document.getElementById('freq-select');
    var weeklyDiv      = document.getElementById('weekly-fields');
    var monthlyDiv     = document.getElementById('monthly-fields');
    var bydayWeekly    = document.getElementById('byday-weekly');
    var weeklyDayCbs   = document.querySelectorAll('.weekly-day-cb');

    var skipPicker     = document.getElementById('skip-date-picker');
    var addSkipBtn     = document.getElementById('add-skip-date');
    var skipTagsDiv    = document.getElementById('skip-tags');
    var skipDatesInput = document.getElementById('skip-dates-input');

    // ── All Day toggle ────────────────────────────────────────────────────────
    function applyAllDay() {
        var hidden = allDayCb && allDayCb.checked;
        if (startTimeCol) startTimeCol.style.display = hidden ? 'none' : '';
        if (endTimeCol)   endTimeCol.style.display   = hidden ? 'none' : '';
    }
    if (allDayCb) {
        allDayCb.addEventListener('change', applyAllDay);
        applyAllDay();
    }

    // ── Recurring toggle ──────────────────────────────────────────────────────
    function applyRecurring() {
        if (!recurrenceDiv) return;
        recurrenceDiv.style.display = (recurringCb && recurringCb.checked) ? '' : 'none';
    }
    if (recurringCb) {
        recurringCb.addEventListener('change', applyRecurring);
        applyRecurring();
    }

    // ── Frequency sub-fields ──────────────────────────────────────────────────
    function applyFreq() {
        if (!freqSelect) return;
        var freq = freqSelect.value;
        if (weeklyDiv)  weeklyDiv.style.display  = (freq === 'WEEKLY')  ? '' : 'none';
        if (monthlyDiv) monthlyDiv.style.display = (freq === 'MONTHLY') ? '' : 'none';

        // Swap the "name" attribute so only the visible field is submitted
        if (bydayWeekly) {
            bydayWeekly.name = (freq === 'WEEKLY') ? 'byday' : '_byday_weekly';
        }
        var bydayMonthly = document.getElementById('byday-monthly');
        if (bydayMonthly) {
            bydayMonthly.name = (freq === 'MONTHLY') ? 'byday' : '_byday_monthly';
        }
    }
    if (freqSelect) {
        freqSelect.addEventListener('change', applyFreq);
        applyFreq();
    }

    // ── Assemble BYDAY from weekly checkboxes ─────────────────────────────────
    function assembleWeeklyByday() {
        var days = [];
        weeklyDayCbs.forEach(function (cb) {
            if (cb.checked) days.push(cb.value);
        });
        if (bydayWeekly) bydayWeekly.value = days.join(',');
    }
    weeklyDayCbs.forEach(function (cb) {
        cb.addEventListener('change', assembleWeeklyByday);
    });
    assembleWeeklyByday();

    // ── Skip date tag management ───────────────────────────────────────────────
    function getSkipDates() {
        if (!skipDatesInput || !skipDatesInput.value.trim()) return [];
        return skipDatesInput.value.split(',').map(function (d) { return d.trim(); }).filter(Boolean);
    }

    function saveSkipDates(dates) {
        if (skipDatesInput) skipDatesInput.value = dates.join(',');
    }

    function renderSkipTags(dates) {
        if (!skipTagsDiv) return;
        skipTagsDiv.innerHTML = '';
        dates.forEach(function (d) {
            var span = document.createElement('span');
            span.className = 'tag is-light is-medium skip-date-tag';
            span.textContent = d + ' ';

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'delete is-small';
            btn.dataset.date = d;
            btn.addEventListener('click', function () {
                var current = getSkipDates();
                current = current.filter(function (x) { return x !== d; });
                saveSkipDates(current);
                renderSkipTags(current);
            });
            span.appendChild(btn);
            skipTagsDiv.appendChild(span);
        });
    }

    // Wire up existing remove buttons (rendered by PHP on edit)
    document.querySelectorAll('.skip-date-tag .delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = btn.dataset.date;
            var current = getSkipDates();
            current = current.filter(function (x) { return x !== d; });
            saveSkipDates(current);
            renderSkipTags(current);
        });
    });

    if (addSkipBtn && skipPicker) {
        addSkipBtn.addEventListener('click', function () {
            var d = skipPicker.value;
            if (!d) return;
            var current = getSkipDates();
            if (!current.includes(d)) {
                current.push(d);
                current.sort();
                saveSkipDates(current);
                renderSkipTags(current);
            }
            skipPicker.value = '';
        });
    }
});
