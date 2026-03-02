-- Board Minutes table
CREATE TABLE IF NOT EXISTS board_minutes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_date DATE NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- index on date for sorting/searching
CREATE INDEX idx_meeting_date ON board_minutes(meeting_date);