CREATE TABLE IF NOT EXISTS event_exceptions (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    event_id         INT           NOT NULL,
    exception_date   DATE          NOT NULL,
    type             ENUM('skip','cancelled') NOT NULL,
    UNIQUE INDEX uq_event_exception (event_id, exception_date),
    CONSTRAINT fk_exception_event
        FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
