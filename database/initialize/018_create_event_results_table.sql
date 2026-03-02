CREATE TABLE IF NOT EXISTS event_results (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    event_id          INT           NOT NULL,
    occurrence_date   DATE          NOT NULL,
    results_text      TEXT          NULL,
    conditions_notes  TEXT          NULL,
    posted_at         TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX uq_event_result (event_id, occurrence_date),
    CONSTRAINT fk_result_event
        FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
