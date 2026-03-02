<?php

namespace App\Services;

use App\Models\Event;
use Core\RRuleExpander;

/**
 * EventService
 *
 * Contains all business logic for the events system.
 * Controllers should be thin and delegate here.
 */
class EventService
{
    /** Category metadata: label, hex color, CSS class */
    public const CATEGORIES = [
        'tournament' => ['label' => 'Tournament',    'color' => '#2D6A2D', 'class' => 'ev-tournament'],
        'league'     => ['label' => 'League Play',   'color' => '#3B7DD8', 'class' => 'ev-league'],
        'closed'     => ['label' => 'Closed',        'color' => '#C0392B', 'class' => 'ev-closed'],
        'school'     => ['label' => 'School / Youth','color' => '#E67E22', 'class' => 'ev-school'],
        '21plus'     => ['label' => '21+ Only',      'color' => '#7D3C98', 'class' => 'ev-21plus'],
        'special'    => ['label' => 'Special Event', 'color' => '#B8860B', 'class' => 'ev-special'],
        'other'      => ['label' => 'Other',         'color' => '#607D8B', 'class' => 'ev-other'],
    ];

    // -------------------------------------------------------------------------
    // Calendar feed
    // -------------------------------------------------------------------------

    /**
     * Expand all events into a flat list of FullCalendar-ready occurrence objects
     * for the given date range.
     *
     * Algorithm (per spec section 3.3):
     *  1. Query events overlapping the range.
     *  2. For each event, expand occurrences (one-time or recurring).
     *  3. Apply exceptions and cancelled_from logic.
     *  4. Return all non-skipped occurrences as JSON-ready arrays.
     *
     * @return array[]  Array of FullCalendar event objects
     */
    public function getOccurrencesForRange(string $rangeStart, string $rangeEnd): array
    {
        $events = Event::getForRange($rangeStart, $rangeEnd);

        if (empty($events)) {
            return [];
        }

        // Load all exceptions for these events in one query
        $eventIds  = array_column($events, 'id');
        $rawExc    = Event::getExceptions($eventIds);

        // Index exceptions by event_id => [date => type]
        $exceptions = [];
        foreach ($rawExc as $exc) {
            $exceptions[(int)$exc['event_id']][$exc['exception_date']] = $exc['type'];
        }

        $output = [];
        foreach ($events as $event) {
            $occurrenceDates = $this->getOccurrenceDates($event, $rangeStart, $rangeEnd);
            $eventExc        = $exceptions[(int)$event['id']] ?? [];

            foreach ($occurrenceDates as $date) {
                $status = $this->resolveOccurrenceStatus($event, $date, $eventExc);

                // 'skip' type is silently omitted
                if ($status === 'skip') {
                    continue;
                }

                $output[] = $this->buildFcEvent($event, $date, $status);
            }
        }

        return $output;
    }

    // -------------------------------------------------------------------------
    // Homepage widget
    // -------------------------------------------------------------------------

    /**
     * Return the next $limit upcoming ACTIVE events (no cancelled occurrences).
     * Uses a 90-day lookahead window from today.
     *
     * @return array[]  Simplified event arrays for the homepage widget
     */
    public function getUpcomingEvents(int $limit = 5): array
    {
        $today     = new \DateTime('today');
        $lookahead = (clone $today)->modify('+90 days');

        $rangeStart = $today->format('Y-m-d');
        $rangeEnd   = $lookahead->format('Y-m-d');

        $events = Event::getForRange($rangeStart, $rangeEnd);

        if (empty($events)) {
            return [];
        }

        $eventIds  = array_column($events, 'id');
        $rawExc    = Event::getExceptions($eventIds);
        $exceptions = [];
        foreach ($rawExc as $exc) {
            $exceptions[(int)$exc['event_id']][$exc['exception_date']] = $exc['type'];
        }

        $upcoming = [];
        foreach ($events as $event) {
            $occurrenceDates = $this->getOccurrenceDates($event, $rangeStart, $rangeEnd);
            $eventExc        = $exceptions[(int)$event['id']] ?? [];

            foreach ($occurrenceDates as $date) {
                $status = $this->resolveOccurrenceStatus($event, $date, $eventExc);

                // Homepage widget shows only active events
                if ($status !== 'active') {
                    continue;
                }

                $startDt    = new \DateTime($event['start_datetime']);
                $displayDt  = new \DateTime($date . ' ' . $startDt->format('H:i:s'));
                $endDt      = new \DateTime($event['end_datetime']);
                $endDisplay = new \DateTime($date . ' ' . $endDt->format('H:i:s'));

                $upcoming[] = [
                    'eventId'     => (int)$event['id'],
                    'title'       => $event['title'],
                    'category'    => $event['category'],
                    'all_day'     => (bool)$event['all_day'],
                    'occurrenceDate' => $date,
                    'displayDate' => $displayDt->format('D, M j'),
                    'displayTime' => $displayDt->format('g:i A') . '–' . $endDisplay->format('g:i A'),
                    'detailUrl'   => $this->buildDetailUrl((int)$event['id'], $event['rrule'], $date),
                    'sortKey'     => $displayDt->getTimestamp(),
                ];
            }
        }

        // Sort ascending by occurrence datetime, return first $limit
        usort($upcoming, fn($a, $b) => $a['sortKey'] <=> $b['sortKey']);
        return array_slice($upcoming, 0, $limit);
    }

    // -------------------------------------------------------------------------
    // Detail page helper
    // -------------------------------------------------------------------------

    /**
     * Resolve all data needed to render an event detail page.
     *
     * Returns null if the event doesn't exist.
     * Returns an array with keys:
     *   event, occurrenceDate, status ('active'|'cancelled'), isPostEvent,
     *   results (array|null), isRecurring
     */
    public function getOccurrenceDetail(int $eventId, ?string $occurrenceDate = null): ?array
    {
        $event = Event::find($eventId);
        if (!$event) {
            return null;
        }

        if ($occurrenceDate === null) {
            $occurrenceDate = (new \DateTime($event['start_datetime']))->format('Y-m-d');
        }

        $rawExc = Event::getExceptions([$eventId]);
        $excMap = [];
        foreach ($rawExc as $exc) {
            $excMap[$exc['exception_date']] = $exc['type'];
        }

        $status      = $this->resolveOccurrenceStatus($event, $occurrenceDate, $excMap);
        $isPostEvent = $occurrenceDate < (new \DateTime('today'))->format('Y-m-d');
        $results     = $isPostEvent ? Event::getResult($eventId, $occurrenceDate) : null;

        return [
            'event'          => $event,
            'occurrenceDate' => $occurrenceDate,
            'status'         => $status,
            'isPostEvent'    => $isPostEvent,
            'results'        => $results,
            'isRecurring'    => !empty($event['rrule']),
        ];
    }

    // -------------------------------------------------------------------------
    // Cancellation
    // -------------------------------------------------------------------------

    /**
     * Cancel a series from a given date forward (inclusive).
     *
     * If $fromDate equals the series start date, the entire series is cancelled
     * (status = 'cancelled'). Otherwise only occurrences on/after $fromDate show
     * as cancelled on the calendar (status stays 'active', cancelled_from is set).
     */
    public function cancelFromDate(int $eventId, string $fromDate): void
    {
        $event = Event::find($eventId);
        if (!$event) {
            return;
        }

        $seriesStart = (new \DateTime($event['start_datetime']))->format('Y-m-d');

        if ($fromDate === $seriesStart) {
            // Full cancellation
            Event::update($eventId, [
                'status'         => 'cancelled',
                'cancelled_from' => $fromDate,
            ]);
        } else {
            // Partial cancellation: show occurrences on/after as cancelled
            Event::update($eventId, [
                'cancelled_from' => $fromDate,
            ]);
        }
    }

    /**
     * Restore a cancelled series (clear cancellation flags).
     */
    public function restoreEvent(int $eventId): void
    {
        Event::update($eventId, [
            'status'         => 'active',
            'cancelled_from' => null,
        ]);
    }

    /**
     * Cancel a single occurrence of a recurring event (shows on calendar with cancelled badge).
     */
    public function cancelOccurrence(int $eventId, string $occurrenceDate): void
    {
        Event::addException($eventId, $occurrenceDate, 'cancelled');
    }

    /**
     * Add a skip exception (silently removes the occurrence — used for blackout dates).
     */
    public function addSkipDate(int $eventId, string $date): void
    {
        Event::addException($eventId, $date, 'skip');
    }

    /**
     * Remove a skip exception (un-blackout a date).
     */
    public function removeSkipDate(int $eventId, string $date): void
    {
        $exc = Event::getException($eventId, $date);
        if ($exc && $exc['type'] === 'skip') {
            Event::removeException($eventId, $date);
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Return the raw occurrence dates for an event within the range.
     * Handles both one-time and recurring events.
     *
     * @return string[]  'Y-m-d' dates
     */
    private function getOccurrenceDates(array $event, string $rangeStart, string $rangeEnd): array
    {
        if (empty($event['rrule'])) {
            // One-time event: yield its date if it falls in the range
            $date = (new \DateTime($event['start_datetime']))->format('Y-m-d');
            if ($date >= $rangeStart && $date <= $rangeEnd) {
                return [$date];
            }
            return [];
        }

        return RRuleExpander::expand(
            $event['rrule'],
            new \DateTime($event['start_datetime']),
            $rangeStart,
            $rangeEnd
        );
    }

    /**
     * Determine the display status for one occurrence.
     *
     * Returns 'active', 'cancelled', or 'skip'.
     *
     * Precedence (per spec section 3.3):
     *  1. cancelled_from (wholesale) overrides individual exceptions.
     *  2. Per-occurrence exception type applies if no cancelled_from.
     *  3. Default: active.
     */
    private function resolveOccurrenceStatus(array $event, string $date, array $exceptions): string
    {
        // Rule 1: cancelled_from takes precedence over everything
        if (!empty($event['cancelled_from']) && $date >= $event['cancelled_from']) {
            return 'cancelled';
        }

        // Rule 2: per-occurrence exception
        if (isset($exceptions[$date])) {
            return $exceptions[$date]; // 'skip' or 'cancelled'
        }

        return 'active';
    }

    /**
     * Build a FullCalendar-ready event object for one occurrence.
     */
    private function buildFcEvent(array $event, string $occurrenceDate, string $status): array
    {
        $cat     = $event['category'];
        $meta    = self::CATEGORIES[$cat] ?? self::CATEGORIES['other'];
        $allDay  = (bool)$event['all_day'];

        $startDt = new \DateTime($event['start_datetime']);
        $endDt   = new \DateTime($event['end_datetime']);

        if ($allDay) {
            $start = $occurrenceDate;
            $end   = $occurrenceDate;
        } else {
            $start = $occurrenceDate . 'T' . $startDt->format('H:i:s');
            $end   = $occurrenceDate . 'T' . $endDt->format('H:i:s');
        }

        return [
            'id'          => $event['id'] . '_' . $occurrenceDate,
            'title'       => $event['title'],
            'start'       => $start,
            'end'         => $end,
            'allDay'      => $allDay,
            'color'       => $meta['color'],
            'textColor'   => '#FFFFFF',
            'classNames'  => [$meta['class']],
            'extendedProps' => [
                'eventId'        => (int)$event['id'],
                'occurrenceDate' => $occurrenceDate,
                'category'       => $cat,
                'status'         => $status,
                'detailUrl'      => $this->buildDetailUrl((int)$event['id'], $event['rrule'], $occurrenceDate),
            ],
        ];
    }

    /**
     * Build the URL for an event detail page.
     * One-time events: /events/{id}
     * Recurring events: /events/{id}/{date}
     */
    private function buildDetailUrl(int $id, ?string $rrule, string $occurrenceDate): string
    {
        if (empty($rrule)) {
            return '/events/' . $id;
        }
        return '/events/' . $id . '/' . $occurrenceDate;
    }
}
