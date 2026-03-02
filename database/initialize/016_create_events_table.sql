CREATE TABLE IF NOT EXISTS events (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255)  NOT NULL,
    category         ENUM('tournament','league','closed','school','21plus','special','other') NOT NULL DEFAULT 'other',
    description      TEXT          NULL,
    start_datetime   DATETIME      NOT NULL,
    end_datetime     DATETIME      NOT NULL,
    all_day          TINYINT(1)    NOT NULL DEFAULT 0,
    rrule            VARCHAR(500)  NULL,
    status           ENUM('active','cancelled') NOT NULL DEFAULT 'active',
    cancelled_from   DATE          NULL,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status       (status),
    INDEX idx_start        (start_datetime),
    INDEX idx_category     (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
