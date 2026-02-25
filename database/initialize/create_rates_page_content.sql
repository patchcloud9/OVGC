-- Rates Page Content Table
-- Stores editable rules text and location of the scorecard PDF used on the public rates page.

CREATE TABLE IF NOT EXISTS rates_page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,

    rules_text TEXT NULL COMMENT 'Line separated list of golf course rules',
    scorecard_path VARCHAR(255) NULL COMMENT 'Public-facing path to the scorecard file (relative to project root)',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- enforce singleton row
DELIMITER $$
CREATE TRIGGER rates_page_content_singleton
BEFORE INSERT ON rates_page_content
FOR EACH ROW
BEGIN
    DECLARE row_count INT;
    SELECT COUNT(*) INTO row_count FROM rates_page_content;
    IF row_count >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only one rates page content record is allowed';
    END IF;
END$$
DELIMITER ;
