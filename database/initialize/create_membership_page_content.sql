-- Membership Page Content Table
-- Stores editable text blocks for the public membership page.

CREATE TABLE IF NOT EXISTS membership_page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,

    top_text TEXT NULL COMMENT 'Paragraph displayed at top of membership page',
    bullets TEXT NULL COMMENT 'Newline-separated list of bullet points',
    bottom_text TEXT NULL COMMENT 'Paragraph displayed at bottom of membership page',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger to ensure only one row exists (singleton pattern)
DELIMITER $$
CREATE TRIGGER membership_page_content_singleton
BEFORE INSERT ON membership_page_content
FOR EACH ROW
BEGIN
    DECLARE row_count INT;
    SELECT COUNT(*) INTO row_count FROM membership_page_content;
    IF row_count >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only one membership page content record is allowed';
    END IF;
END$$
DELIMITER ;
