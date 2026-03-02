CREATE TABLE IF NOT EXISTS membership_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    group_id     INT NOT NULL,
    sort_order   INT NOT NULL DEFAULT 0,
    name         VARCHAR(100) NOT NULL,
    price        DECIMAL(8,2) NOT NULL,
    notes        VARCHAR(150) DEFAULT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_group (group_id, sort_order),
    CONSTRAINT fk_membership_group FOREIGN KEY (group_id) REFERENCES membership_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
