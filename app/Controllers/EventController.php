<?php

namespace App\Controllers;

use App\Services\EventService;

/**
 * EventController (public)
 *
 * Routes:
 *   GET /events              → index()               Public calendar page
 *   GET /events/feed         → feed()                FullCalendar JSON feed
 *   GET /events/(\d+)        → show($id)             One-time event detail
 *   GET /events/(\d+)/(.+)   → showOccurrence($id, $date)  Recurring occurrence detail
 */
class EventController extends Controller
{
    private EventService $service;

    public function __construct()
    {
        $this->service = new EventService();
    }

    // -------------------------------------------------------------------------
    // Public calendar page
    // -------------------------------------------------------------------------

    /**
     * GET /events
     * Renders the calendar shell. FullCalendar fetches event data via /events/feed.
     */
    public function index(): void
    {
        $this->view('events/index', [
            'title'      => 'Events Calendar',
            'categories' => EventService::CATEGORIES,
        ]);
    }

    // -------------------------------------------------------------------------
    // FullCalendar JSON feed
    // -------------------------------------------------------------------------

    /**
     * GET /events/feed?start=YYYY-MM-DD&end=YYYY-MM-DD
     *
     * FullCalendar calls this when the user navigates months.
     * Returns a JSON array of occurrence objects.
     */
    public function feed(): void
    {
        $start = $this->query('start', '');
        $end   = $this->query('end', '');

        // FullCalendar may send ISO 8601 datetime strings — extract just the date
        $start = substr($start, 0, 10);
        $end   = substr($end,   0, 10);

        if (!$this->isValidDate($start) || !$this->isValidDate($end)) {
            $this->json(['error' => 'Invalid date range'], 400);
            return;
        }

        $occurrences = $this->service->getOccurrencesForRange($start, $end);
        $this->json($occurrences);
    }

    // -------------------------------------------------------------------------
    // Event detail pages
    // -------------------------------------------------------------------------

    /**
     * GET /events/{id}
     * Detail page for a one-time event (no date segment in URL).
     */
    public function show(string $id): void
    {
        $detail = $this->service->getOccurrenceDetail((int) $id);

        if (!$detail) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $this->view('events/detail', array_merge($detail, [
            'title' => e($detail['event']['title']),
        ]));
    }

    /**
     * GET /events/{id}/{date}
     * Detail page for one occurrence of a recurring event.
     * $date is expected as YYYY-MM-DD.
     */
    public function showOccurrence(string $id, string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw new \Core\Exceptions\NotFoundHttpException('Invalid occurrence date');
        }

        $detail = $this->service->getOccurrenceDetail((int) $id, $date);

        if (!$detail) {
            throw new \Core\Exceptions\NotFoundHttpException('Event not found');
        }

        $this->view('events/detail', array_merge($detail, [
            'title' => e($detail['event']['title']),
        ]));
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    private function isValidDate(string $date): bool
    {
        if (strlen($date) !== 10) {
            return false;
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        return $dt && $dt->format('Y-m-d') === $date;
    }
}
