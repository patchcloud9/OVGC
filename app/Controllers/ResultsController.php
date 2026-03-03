<?php

namespace App\Controllers;

use App\Models\Event;
use App\Services\EventService;

/**
 * ResultsController (public)
 *
 * Routes:
 *   GET /results  → index()   Public results listing
 */
class ResultsController extends Controller
{
    /**
     * GET /results
     */
    public function index(): void
    {
        $perPage    = 10;
        $total      = Event::countResults();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));

        $rows = Event::getRecentResults($perPage, ($page - 1) * $perPage);

        // Build detail URL: recurring → /events/{id}/{date}, one-time → /events/{id}
        $results = [];
        foreach ($rows as $row) {
            $row['detailUrl'] = empty($row['rrule'])
                ? '/events/' . (int) $row['event_id']
                : '/events/' . (int) $row['event_id'] . '/' . $row['occurrence_date'];
            $results[] = $row;
        }

        $this->view('results/index', [
            'title'      => 'Event Results',
            'results'    => $results,
            'categories' => EventService::CATEGORIES,
            'page'       => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
