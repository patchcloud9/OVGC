<?php

namespace Core;

/**
 * Expands RRULE strings into occurrence dates within a given date range.
 *
 * Supports the patterns used by the golf club events system:
 *   FREQ=DAILY
 *   FREQ=WEEKLY;BYDAY=TU
 *   FREQ=WEEKLY;BYDAY=MO,WE,FR
 *   FREQ=WEEKLY;BYDAY=SA;UNTIL=20261231T235959Z
 *   FREQ=MONTHLY;BYDAY=1SA   (nth weekday, e.g. first Saturday)
 *   FREQ=MONTHLY;BYDAY=-1MO  (last weekday, e.g. last Monday)
 *
 * RRULE values must NOT include the "RRULE:" prefix (store only the rule portion).
 */
class RRuleExpander
{
    /** ISO weekday number (date('N')) mapped to RRULE BYDAY code */
    private const DAY_MAP = [
        'MO' => 1,
        'TU' => 2,
        'WE' => 3,
        'TH' => 4,
        'FR' => 5,
        'SA' => 6,
        'SU' => 7,
    ];

    /**
     * Expand a recurrence rule into occurrence dates within [rangeStart, rangeEnd].
     *
     * @param string    $rrule      RRULE value, e.g. "FREQ=WEEKLY;BYDAY=TU"
     * @param \DateTime $dtStart    Series start datetime (from events.start_datetime)
     * @param string    $rangeStart Range start date as 'Y-m-d'
     * @param string    $rangeEnd   Range end date as 'Y-m-d'
     * @return string[]             Sorted array of 'Y-m-d' occurrence dates
     */
    public static function expand(
        string $rrule,
        \DateTime $dtStart,
        string $rangeStart,
        string $rangeEnd
    ): array {
        $parts = self::parse($rrule);
        $freq  = strtoupper($parts['FREQ'] ?? '');

        $rangeSt = new \DateTime($rangeStart);
        $rangeEn = new \DateTime($rangeEnd);

        // Apply UNTIL as an upper bound on the range
        if (isset($parts['UNTIL'])) {
            $until = self::parseUntil($parts['UNTIL']);
            if ($until < $rangeSt) {
                return [];
            }
            if ($until < $rangeEn) {
                $rangeEn = $until;
            }
        }

        // No occurrence may fall before the series start date
        $effectiveStart = $dtStart > $rangeSt ? clone $dtStart : clone $rangeSt;
        // Normalize to midnight so date comparisons work correctly
        $effectiveStart->setTime(0, 0, 0);
        $rangeEn->setTime(23, 59, 59);

        if ($effectiveStart > $rangeEn) {
            return [];
        }

        switch ($freq) {
            case 'DAILY':
                return self::expandDaily($effectiveStart, $rangeEn);

            case 'WEEKLY':
                $bydays = isset($parts['BYDAY'])
                    ? array_map('trim', explode(',', strtoupper($parts['BYDAY'])))
                    : [self::isoWeekdayCode($dtStart)];
                return self::expandWeekly($bydays, $effectiveStart, $rangeEn);

            case 'MONTHLY':
                return self::expandMonthly(
                    $parts['BYDAY'] ?? null,
                    $dtStart,
                    $effectiveStart,
                    $rangeEn
                );

            default:
                return [];
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Parse "KEY=VAL;KEY=VAL" into an associative array */
    private static function parse(string $rrule): array
    {
        $parts = [];
        foreach (explode(';', $rrule) as $segment) {
            if (strpos($segment, '=') !== false) {
                [$key, $val] = explode('=', $segment, 2);
                $parts[strtoupper(trim($key))] = trim($val);
            }
        }
        return $parts;
    }

    /**
     * Parse UNTIL value — supports "20261231T235959Z" and plain "20261231"
     */
    private static function parseUntil(string $until): \DateTime
    {
        $digits = preg_replace('/[^0-9]/', '', $until);
        if (strlen($digits) >= 14) {
            $dt = \DateTime::createFromFormat('YmdHis', substr($digits, 0, 14));
            if ($dt) {
                return $dt;
            }
        }
        $dt = \DateTime::createFromFormat('Ymd', substr($digits, 0, 8));
        return $dt ?: new \DateTime($until);
    }

    /** Every day from $from through $to */
    private static function expandDaily(\DateTime $from, \DateTime $to): array
    {
        $results = [];
        $current = clone $from;
        while ($current <= $to) {
            $results[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
        return $results;
    }

    /**
     * Every occurrence of the given weekdays (e.g. ['TU','TH']) between $from and $to.
     * Iterates day-by-day — efficient for the date ranges a calendar will request.
     */
    private static function expandWeekly(array $bydays, \DateTime $from, \DateTime $to): array
    {
        $dayNums = array_values(array_filter(
            array_map(fn(string $d) => self::DAY_MAP[$d] ?? null, $bydays)
        ));

        $results = [];
        $current = clone $from;
        while ($current <= $to) {
            if (in_array((int) $current->format('N'), $dayNums, true)) {
                $results[] = $current->format('Y-m-d');
            }
            $current->modify('+1 day');
        }
        return $results;
    }

    /**
     * Monthly recurrence.
     *  - With BYDAY ordinal (e.g. "1SA", "-1MO"): nth weekday of each month.
     *  - Without BYDAY: same day-of-month as dtStart each month.
     */
    private static function expandMonthly(
        ?string $byday,
        \DateTime $dtStart,
        \DateTime $from,
        \DateTime $to
    ): array {
        $results = [];

        // Cursor always points to the 1st of the month being evaluated
        $cursor = new \DateTime($from->format('Y-m-01'));

        // Parse ordinal + day code once, outside the loop
        $ordinal = null;
        $dayNum  = null;
        if ($byday !== null && preg_match('/^(-?\d+)([A-Z]{2})$/i', trim($byday), $m)) {
            $ordinal = (int) $m[1];
            $dayNum  = self::DAY_MAP[strtoupper($m[2])] ?? null;
        }

        while ($cursor <= $to) {
            $year  = (int) $cursor->format('Y');
            $month = (int) $cursor->format('n');

            if ($ordinal !== null && $dayNum !== null) {
                // Nth (or last) weekday of the month
                $occ = self::nthWeekdayOfMonth($ordinal, $dayNum, $year, $month);
            } else {
                // Same day-of-month as dtStart; clamp to month end
                $dom = min((int) $dtStart->format('j'), (int) $cursor->format('t'));
                $occ = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month, $dom));
            }

            if ($occ !== null && $occ >= $from && $occ <= $to) {
                $results[] = $occ->format('Y-m-d');
            }

            $cursor->modify('+1 month');
        }

        return $results;
    }

    /**
     * Return the DateTime of the Nth weekday in a given year/month.
     *   $ordinal  1 = first, 2 = second, -1 = last, -2 = second-to-last
     *   $dayNum   ISO weekday number (1=Mon … 7=Sun)
     *
     * Returns null if the computed date falls outside the target month.
     */
    private static function nthWeekdayOfMonth(int $ordinal, int $dayNum, int $year, int $month): ?\DateTime
    {
        if ($ordinal >= 1) {
            // Find the first occurrence of $dayNum in this month
            $dt     = new \DateTime(sprintf('%04d-%02d-01', $year, $month));
            $offset = ($dayNum - (int) $dt->format('N') + 7) % 7;
            $dt->modify("+{$offset} days");
            // Advance (ordinal - 1) full weeks
            $weeks = $ordinal - 1;
            if ($weeks > 0) {
                $dt->modify("+{$weeks} weeks");
            }
        } else {
            // Find the last occurrence of $dayNum in this month
            $daysInMonth = (int) (new \DateTime(sprintf('%04d-%02d-01', $year, $month)))->format('t');
            $dt          = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth));
            $offset      = ((int) $dt->format('N') - $dayNum + 7) % 7;
            $dt->modify("-{$offset} days");
            // Go back (abs(ordinal) - 1) full weeks
            $weeks = abs($ordinal) - 1;
            if ($weeks > 0) {
                $dt->modify("-{$weeks} weeks");
            }
        }

        return (int) $dt->format('n') === $month ? $dt : null;
    }

    /** Convert a DateTime's day-of-week to the RRULE BYDAY code (e.g. 'TU') */
    private static function isoWeekdayCode(\DateTime $dt): string
    {
        $dow = (int) $dt->format('N');
        return (string) (array_search($dow, self::DAY_MAP, true) ?: 'MO');
    }
}
