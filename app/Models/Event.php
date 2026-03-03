<?php

namespace App\Models;

class Event extends Model
{
    protected string $table = 'events';

    protected array $fillable = [
        'title',
        'category',
        'description',
        'start_datetime',
        'end_datetime',
        'all_day',
        'rrule',
        'status',
        'cancelled_from',
    ];

    protected bool $timestamps = true;

    // -------------------------------------------------------------------------
    // Event queries
    // -------------------------------------------------------------------------

    /**
     * Fetch events whose occurrence window overlaps [rangeStart, rangeEnd].
     *   - One-time events: start within range (or end within range)
     *   - Recurring events: series started by rangeEnd (EventService handles UNTIL)
     */
    public static function getForRange(string $rangeStart, string $rangeEnd): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}
                WHERE (rrule IS NULL AND start_datetime <= ? AND end_datetime >= ?)
                   OR (rrule IS NOT NULL AND start_datetime <= ?)
                ORDER BY start_datetime ASC";
        return $instance->getDatabase()->fetchAll($sql, [$rangeEnd, $rangeStart, $rangeEnd]);
    }

    /**
     * All events for the admin list, newest first.
     */
    public static function allForAdmin(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} ORDER BY start_datetime DESC";
        return $instance->getDatabase()->fetchAll($sql);
    }

    // -------------------------------------------------------------------------
    // Exception helpers (event_exceptions table)
    // -------------------------------------------------------------------------

    /**
     * Fetch all exceptions for a given set of event IDs.
     * Returns rows keyed numerically; callers group by event_id as needed.
     */
    public static function getExceptions(array $eventIds): array
    {
        if (empty($eventIds)) {
            return [];
        }
        $db           = (new static())->getDatabase();
        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $sql          = "SELECT * FROM event_exceptions WHERE event_id IN ({$placeholders})";
        return $db->fetchAll($sql, $eventIds);
    }

    /**
     * Insert or ignore an exception row.
     * Safe to call even if the exception already exists (IGNORE prevents duplicate error).
     */
    public static function addException(int $eventId, string $date, string $type): void
    {
        $db  = (new static())->getDatabase();
        $sql = "INSERT IGNORE INTO event_exceptions (event_id, exception_date, type)
                VALUES (?, ?, ?)";
        $db->execute($sql, [$eventId, $date, $type]);
    }

    /**
     * Remove an exception for a specific event + date.
     */
    public static function removeException(int $eventId, string $date): void
    {
        $db  = (new static())->getDatabase();
        $sql = "DELETE FROM event_exceptions WHERE event_id = ? AND exception_date = ?";
        $db->execute($sql, [$eventId, $date]);
    }

    /**
     * Get a single exception row for an event + date, or null if none.
     */
    public static function getException(int $eventId, string $date): ?array
    {
        $db  = (new static())->getDatabase();
        $sql = "SELECT * FROM event_exceptions
                WHERE event_id = ? AND exception_date = ?
                LIMIT 1";
        return $db->fetch($sql, [$eventId, $date]);
    }

    // -------------------------------------------------------------------------
    // Results helpers (event_results table)
    // -------------------------------------------------------------------------

    /**
     * Get the results row for a specific event occurrence, or null if not posted yet.
     */
    public static function getResult(int $eventId, string $occurrenceDate): ?array
    {
        $db  = (new static())->getDatabase();
        $sql = "SELECT * FROM event_results
                WHERE event_id = ? AND occurrence_date = ?
                LIMIT 1";
        return $db->fetch($sql, [$eventId, $occurrenceDate]);
    }

    /**
     * Get all results rows for an event, keyed by occurrence_date.
     */
    public static function getResultsForEvent(int $eventId): array
    {
        $db  = (new static())->getDatabase();
        $sql = "SELECT * FROM event_results WHERE event_id = ? ORDER BY occurrence_date DESC";
        $rows = $db->fetchAll($sql, [$eventId]);
        $keyed = [];
        foreach ($rows as $row) {
            $keyed[$row['occurrence_date']] = $row;
        }
        return $keyed;
    }

    /**
     * Count total posted results across all events.
     */
    public static function countResults(): int
    {
        $db  = (new static())->getDatabase();
        $row = $db->fetch("SELECT COUNT(*) AS total FROM event_results er JOIN events e ON e.id = er.event_id");
        return $row ? (int) $row['total'] : 0;
    }

    /**
     * Get a page of posted results across all events, newest occurrence first.
     * Each row includes all event_results columns plus e.title, e.category, e.rrule.
     */
    public static function getRecentResults(int $limit = 10, int $offset = 0): array
    {
        $db  = (new static())->getDatabase();
        $sql = "SELECT er.*, e.title, e.category, e.rrule
                FROM event_results er
                JOIN events e ON e.id = er.event_id
                ORDER BY er.occurrence_date DESC
                LIMIT ? OFFSET ?";
        return $db->fetchAll($sql, [$limit, $offset]);
    }

    /**
     * Upsert a results row (insert or update on duplicate key).
     */
    public static function saveResult(int $eventId, string $occurrenceDate, array $data): void
    {
        $db  = (new static())->getDatabase();
        $sql = "INSERT INTO event_results (event_id, occurrence_date, results_text, conditions_notes)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    results_text      = VALUES(results_text),
                    conditions_notes  = VALUES(conditions_notes),
                    posted_at         = CURRENT_TIMESTAMP";
        $db->execute($sql, [
            $eventId,
            $occurrenceDate,
            $data['results_text']     ?? null,
            $data['conditions_notes'] ?? null,
        ]);
    }

    /**
     * Delete a results row for a specific event occurrence.
     */
    public static function deleteResult(int $eventId, string $occurrenceDate): void
    {
        $db  = (new static())->getDatabase();
        $sql = "DELETE FROM event_results WHERE event_id = ? AND occurrence_date = ?";
        $db->execute($sql, [$eventId, $occurrenceDate]);
    }
}
