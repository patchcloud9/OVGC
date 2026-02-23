-- About Content Table
-- Stores content for the About page with two sections (image + text pairs)

CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Page title
    page_title VARCHAR(100) NOT NULL DEFAULT 'About the Artist' COMMENT 'Main heading for about page',
    page_subtitle VARCHAR(255) NULL COMMENT 'Optional subtitle displayed under the page title',

    -- Section 1 (top)
    section1_image VARCHAR(255) NULL COMMENT 'Path to first section image',
    section1_text TEXT NULL COMMENT 'Text content for first section',
    section1_image VARCHAR(255) NULL COMMENT 'Path to first section image',
    section1_text TEXT NULL COMMENT 'Text content for first section',
    section1_image_position ENUM('left', 'right') DEFAULT 'left' COMMENT 'Image position for section 1',
    section1_text_align_h ENUM('left', 'center', 'right') DEFAULT 'left' COMMENT 'Horizontal text alignment for section 1',
    section1_text_align_v ENUM('top', 'middle', 'bottom') DEFAULT 'top' COMMENT 'Vertical text alignment for section 1',

    -- Section 2 (bottom)
    section2_image VARCHAR(255) NULL COMMENT 'Path to second section image',
    section2_text TEXT NULL COMMENT 'Text content for second section',
    section2_image_position ENUM('left', 'right') DEFAULT 'left' COMMENT 'Image position for section 2',
    section2_text_align_h ENUM('left', 'center', 'right') DEFAULT 'left' COMMENT 'Horizontal text alignment for section 2',
    section2_text_align_v ENUM('top', 'middle', 'bottom') DEFAULT 'top' COMMENT 'Vertical text alignment for section 2',

    -- Artist signature/name
    artist_signature VARCHAR(100) NULL COMMENT 'Artist name displayed at bottom',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger to ensure only one row exists (singleton pattern)
DELIMITER $$
CREATE TRIGGER about_content_singleton_check
BEFORE INSERT ON about_content
FOR EACH ROW
BEGIN
    DECLARE row_count INT;
    SELECT COUNT(*) INTO row_count FROM about_content;
    IF row_count >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only one about content record is allowed';
    END IF;
END$$
DELIMITER ;


