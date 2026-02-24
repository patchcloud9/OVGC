CREATE TABLE IF NOT EXISTS page_banners (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    page        VARCHAR(50) NOT NULL,
    position    ENUM('top','bottom') NOT NULL DEFAULT 'top',
    text        TEXT NOT NULL,
    colour      ENUM('info','warning','danger','none') NOT NULL DEFAULT 'info',
    dismissable TINYINT(1) NOT NULL DEFAULT 0,
    sort_order  INT NOT NULL DEFAULT 0,
    active      TINYINT(1) NOT NULL DEFAULT 1,
    start_at    DATETIME      NULL,
    end_at      DATETIME      NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page (page),
    INDEX idx_active (active, start_at, end_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
