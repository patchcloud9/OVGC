<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;

/**
 * Admin\EventController
 *
 * Routes (all protected by auth + role:admin):
 *   GET  /admin/events                         → index()
 *   GET  /admin/events/create                  → create()
 *   POST /admin/events/create                  → store()
 *   GET  /admin/events/(\d+)/edit              → edit($id)
 *   POST /admin/events/(\d+)/edit              → update($id)
 *   GET  /admin/events/(\d+)/cancel            → cancelForm($id)
 *   POST /admin/events/(\d+)/cancel            → cancelStore($id)
 *   POST /admin/events/(\d+)/restore           → restore($id)
 *   POST /admin/events/(\d+)/delete            → destroy($id)
 *   GET  /admin/events/(\d+)/results            → resultsIndex($id)
 *   GET  /admin/events/(\d+)/results/(.+)      → resultsForm($id, $date)
 *   POST /admin/events/(\d+)/results/(.+)      → resultsStore($id, $date)
 */
class EventController extends Controller
{
    private EventService $service;

    public function __construct()
    {
        $this->service = new EventService();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    /**
     * GET /admin/events
     */
    public function index(): void
    {
        $this->view('admin/events/index', [
            'title'  => 'Manage Events',
            'events' => Event::allForAdmin(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    /**
     * GET /admin/events/create
     */
    public function create(): void
    {
        $this->view('admin/events/form', [
            'title'      => 'Create Event',
            'event'      => null,
            'categories' => EventService::CATEGORIES,
            'skipDates'  => [],
        ]);
    }

    /**
     * POST /admin/events/create
     */
    public function store(): void
    {
        $data   = $this->buildEventData($this->all());
        $errors = $this->validateEventData($data);

        if ($errors) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/events/create');
            return;
        }

        $event = Event::create($data);

        // Save skip dates (blackout dates set at creation)
        if (!empty($_POST['skip_dates'])) {
            foreach (array_filter(array_map('trim', explode(',', $_POST['skip_dates']))) as $d) {
                if ($this->isValidDate($d)) {
                    $this->service->addSkipDate((int)$event['id'], $d);
                }
            }
        }

        $this->flash('success', 'Event created.');
        $this->redirect('/admin/events');
    }

    // -------------------------------------------------------------------------
    // Edit
    // -------------------------------------------------------------------------

    /**
     * GET /admin/events/{id}/edit
     */
    public function edit(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        // Load existing skip exceptions for display in the form
        $exceptions = Event::getExceptions([(int)$id]);
        $skipDates  = array_values(array_map(
            fn($e) => $e['exception_date'],
            array_filter($exceptions, fn($e) => $e['type'] === 'skip')
        ));

        $this->view('admin/events/form', [
            'title'      => 'Edit Event',
            'event'      => $event,
            'categories' => EventService::CATEGORIES,
            'skipDates'  => $skipDates,
        ]);
    }

    /**
     * POST /admin/events/{id}/edit
     */
    public function update(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $data   = $this->buildEventData($this->all());
        $errors = $this->validateEventData($data);

        if ($errors) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/events/' . $id . '/edit');
            return;
        }

        Event::update((int) $id, $data);

        // Sync skip dates: delete all existing skips, re-add from form
        $existing = Event::getExceptions([(int)$id]);
        foreach ($existing as $exc) {
            if ($exc['type'] === 'skip') {
                Event::removeException((int)$id, $exc['exception_date']);
            }
        }
        if (!empty($_POST['skip_dates'])) {
            foreach (array_filter(array_map('trim', explode(',', $_POST['skip_dates']))) as $d) {
                if ($this->isValidDate($d)) {
                    $this->service->addSkipDate((int)$id, $d);
                }
            }
        }

        $this->flash('success', 'Event updated. All occurrences reflect these changes.');
        $this->redirect('/admin/events');
    }

    // -------------------------------------------------------------------------
    // Cancel
    // -------------------------------------------------------------------------

    /**
     * GET /admin/events/{id}/cancel
     */
    public function cancelForm(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $this->view('admin/events/cancel', [
            'title' => 'Cancel Event',
            'event' => $event,
        ]);
    }

    /**
     * POST /admin/events/{id}/cancel
     */
    public function cancelStore(string $id): void
    {
        $event    = Event::find((int) $id);
        $fromDate = trim($_POST['cancel_from_date'] ?? '');

        if (!$event || !$this->isValidDate($fromDate)) {
            $this->flash('error', 'Invalid cancellation date.');
            $this->redirect('/admin/events/' . $id . '/cancel');
            return;
        }

        $this->service->cancelFromDate((int) $id, $fromDate);
        $this->flash('success', 'Event cancelled from ' . $fromDate . ' forward.');
        $this->redirect('/admin/events');
    }

    /**
     * POST /admin/events/{id}/restore
     * Un-cancel a previously cancelled event.
     */
    public function restore(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $this->service->restoreEvent((int) $id);
        $this->flash('success', 'Event restored.');
        $this->redirect('/admin/events');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    /**
     * POST /admin/events/{id}/delete
     * Hard delete — CASCADE handles exceptions and results.
     */
    public function destroy(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        // Two-step confirmation via hidden field
        if (($_POST['confirm_delete'] ?? '') !== '1') {
            $this->flash('error', 'Please confirm the deletion.');
            $this->redirect('/admin/events');
            return;
        }

        Event::delete((int) $id);
        $this->flash('success', 'Event deleted permanently.');
        $this->redirect('/admin/events');
    }

    // -------------------------------------------------------------------------
    // Results
    // -------------------------------------------------------------------------

    /**
     * GET /admin/events/{id}/results
     * Entry point: redirect one-time events directly; show occurrence picker for recurring.
     */
    public function resultsIndex(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        // One-time event — go straight to the results form for its start date
        if (empty($event['rrule'])) {
            $date = (new \DateTime($event['start_datetime']))->format('Y-m-d');
            $this->redirect('/admin/events/' . (int) $id . '/results/' . $date);
            return;
        }

        // Recurring — show all occurrences (past and upcoming) to pick from
        $rangeStart  = (new \DateTime($event['start_datetime']))->format('Y-m-d');
        $rangeEnd    = (new \DateTime('+2 years'))->format('Y-m-d');
        $occurrences = $this->service->getOccurrencesForRange($rangeStart, $rangeEnd);

        // Filter to this event's occurrences, newest first
        $dates = [];
        foreach ($occurrences as $occ) {
            $props = $occ['extendedProps'];
            if ((int) $props['eventId'] === (int) $id) {
                $dates[] = $props['occurrenceDate'];
            }
        }
        usort($dates, fn($a, $b) => strcmp($b, $a));

        $existingResults = Event::getResultsForEvent((int) $id);

        $this->view('admin/events/results-pick', [
            'title'           => 'Post Results — ' . e($event['title']),
            'event'           => $event,
            'occurrences'     => $dates,
            'existingResults' => $existingResults,
        ]);
    }

    /**
     * GET /admin/events/{id}/results/{date}
     */
    public function resultsForm(string $id, string $date): void
    {
        $event = Event::find((int) $id);
        if (!$event || !$this->isValidDate($date)) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $results = Event::getResult((int) $id, $date);

        $this->view('admin/events/results', [
            'title'          => 'Post Results — ' . e($event['title']),
            'event'          => $event,
            'occurrenceDate' => $date,
            'results'        => $results,
        ]);
    }

    /**
     * POST /admin/events/{id}/results/{date}
     */
    public function resultsStore(string $id, string $date): void
    {
        $event = Event::find((int) $id);
        if (!$event || !$this->isValidDate($date)) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        Event::saveResult((int) $id, $date, [
            'results_text'    => $_POST['results_text']    ?? null,
            'conditions_notes' => $_POST['conditions_notes'] ?? null,
        ]);

        $this->flash('success', 'Results saved.');
        $this->redirect('/admin/events/' . (int) $id . '/results/' . $date);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build an array of event column values from POST data.
     */
    private function buildEventData(array $post): array
    {
        $allDay    = !empty($post['all_day']);
        $recurring = !empty($post['recurring']);

        $startDate = trim($post['start_date'] ?? '');
        $startTime = $allDay ? '00:00' : trim($post['start_time'] ?? '00:00');
        $endDate   = trim($post['end_date'] ?? $startDate);
        $endTime   = $allDay ? '23:59:59' : trim($post['end_time'] ?? '23:59:59');

        $rrule = null;
        if ($recurring) {
            $freq   = strtoupper(trim($post['freq'] ?? 'WEEKLY'));
            $byday  = trim($post['byday'] ?? '');
            $until  = trim($post['until'] ?? '');
            $rrule  = $this->buildRrule($freq, $byday, $until);
        }

        return [
            'title'          => trim($post['title']       ?? ''),
            'category'       => trim($post['category']    ?? 'other'),
            'description'    => trim($post['description'] ?? ''),
            'start_datetime' => $startDate . ' ' . $startTime . ':00',
            'end_datetime'   => $endDate   . ' ' . $endTime,
            'all_day'        => $allDay ? 1 : 0,
            'rrule'          => $rrule,
            'status'         => 'active',
            'cancelled_from' => null,
        ];
    }

    /**
     * Validate event data, returning an array of error strings (empty = valid).
     */
    private function validateEventData(array $data): array
    {
        $errors = [];
        if (empty($data['title'])) {
            $errors[] = 'Title is required.';
        }
        if (empty($data['start_datetime']) || $data['start_datetime'] === ' :00') {
            $errors[] = 'Start date is required.';
        }
        if (empty($data['end_datetime'])) {
            $errors[] = 'End date is required.';
        }
        $allowed = array_keys(EventService::CATEGORIES);
        if (!in_array($data['category'], $allowed, true)) {
            $errors[] = 'Invalid category.';
        }
        return $errors;
    }

    /**
     * Assemble an RRULE string from form parts.
     */
    private function buildRrule(string $freq, string $byday, string $until): string
    {
        $rule = 'FREQ=' . $freq;
        if ($freq === 'WEEKLY' && $byday !== '') {
            $rule .= ';BYDAY=' . $byday;
        }
        if ($freq === 'MONTHLY' && $byday !== '') {
            $rule .= ';BYDAY=' . $byday;
        }
        if ($until !== '') {
            // Convert YYYY-MM-DD to YYYYMMDDТ235959Z
            $rule .= ';UNTIL=' . str_replace('-', '', $until) . 'T235959Z';
        }
        return $rule;
    }

    private function isValidDate(string $date): bool
    {
        if (strlen($date) !== 10) {
            return false;
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        return $dt && $dt->format('Y-m-d') === $date;
    }
}
