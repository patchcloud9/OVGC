CREATE TABLE IF NOT EXISTS flyers (
    id           INT           AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255)  NOT NULL,
    description  TEXT          NULL,
    filename     VARCHAR(255)  NOT NULL,
    file_path    VARCHAR(512)  NOT NULL,
    mime_type    VARCHAR(100)  NOT NULL,
    expires_at   DATE          NOT NULL,
    display_order INT          NOT NULL DEFAULT 0,
    uploaded_by  INT           NOT NULL,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expires_at    (expires_at),
    INDEX idx_display_order (display_order),
    INDEX idx_uploaded_by   (uploaded_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
