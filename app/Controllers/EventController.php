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
    // iCal subscription feed
    // -------------------------------------------------------------------------

    /**
     * GET /events/calendar.ics
     *
     * Outputs an iCalendar (RFC 5545) feed covering the past 30 days and
     * the next 12 months.  Users subscribe by URL in Google Calendar, Apple
     * Calendar, Outlook, etc.  Cancelled occurrences are included with
     * STATUS:CANCELLED so subscribers see cancellations.
     */
    public function ical(): void
    {
        $rangeStart = (new \DateTime('-30 days'))->format('Y-m-d');
        $rangeEnd   = (new \DateTime('+12 months'))->format('Y-m-d');

        $occurrences = $this->service->getOccurrencesForRange($rangeStart, $rangeEnd);

        // Drop 'skip' occurrences — they are hidden dates, not real events
        $occurrences = array_values(array_filter($occurrences, function ($occ) {
            return ($occ['extendedProps']['status'] ?? '') !== 'skip';
        }));

        $tzId    = date_default_timezone_get() ?: 'America/Los_Angeles';
        $dtstamp = gmdate('Ymd\THis\Z');
        $domain  = parse_url(APP_URL, PHP_URL_HOST) ?: 'okanoganvalleygolf.com';
        $calName = APP_NAME . ' Events';

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="ovgc-events.ics"');

        $out = "BEGIN:VCALENDAR\r\n"
             . "VERSION:2.0\r\n"
             . "PRODID:-//" . APP_NAME . "//Events//EN\r\n"
             . "CALSCALE:GREGORIAN\r\n"
             . "METHOD:PUBLISH\r\n"
             . "X-WR-CALNAME:" . $this->icalEscape($calName) . "\r\n"
             . "X-WR-TIMEZONE:" . $tzId . "\r\n";

        foreach ($occurrences as $occ) {
            $props  = $occ['extendedProps'];
            $uid    = 'event-' . $props['eventId'] . '-' . $props['occurrenceDate'] . '@' . $domain;
            $status = ($props['status'] === 'cancelled') ? 'CANCELLED' : 'CONFIRMED';
            $url    = rtrim(APP_URL, '/') . $props['detailUrl'];

            if ($occ['allDay']) {
                // iCal DATE format, end is already exclusive (FullCalendar convention)
                $dtStart = 'DTSTART;VALUE=DATE:' . str_replace('-', '', $occ['start']);
                $dtEnd   = 'DTEND;VALUE=DATE:'   . str_replace('-', '', $occ['end']);
            } else {
                // iCal DATETIME with local timezone: strip dashes and colons from 'Y-m-d\TH:i:s'
                $fmt     = str_replace(['-', ':'], '', $occ['start']); // 20260307T140000
                $fmtEnd  = str_replace(['-', ':'], '', $occ['end']);
                $dtStart = 'DTSTART;TZID=' . $tzId . ':' . $fmt;
                $dtEnd   = 'DTEND;TZID='   . $tzId . ':' . $fmtEnd;
            }

            $out .= "BEGIN:VEVENT\r\n"
                  . "UID:" . $uid . "\r\n"
                  . "DTSTAMP:" . $dtstamp . "\r\n"
                  . $dtStart . "\r\n"
                  . $dtEnd   . "\r\n"
                  . "SUMMARY:" . $this->icalEscape($occ['title']) . "\r\n"
                  . "STATUS:" . $status . "\r\n"
                  . "URL:" . $url . "\r\n"
                  . "END:VEVENT\r\n";
        }

        $out .= "END:VCALENDAR\r\n";

        echo $out;
        exit;
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    /** Escape special characters per RFC 5545 */
    private function icalEscape(string $str): string
    {
        return str_replace(
            ['\\',  "\n",  ',',   ';'],
            ['\\\\','\\n', '\\,', '\\;'],
            $str
        );
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
