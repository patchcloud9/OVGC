-- Menu Items Table
-- Stores hierarchical navigation menu items with support for dropdowns

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Menu item details
    title VARCHAR(100) NOT NULL COMMENT 'Display text for the menu item',
    url VARCHAR(255) NOT NULL COMMENT 'Link destination (e.g., /, /about, /gallery)',
    icon VARCHAR(50) NULL COMMENT 'Font Awesome icon class (e.g., fa-home, fa-user)',

    -- Hierarchy support for dropdowns
    parent_id INT NULL COMMENT 'NULL for top-level items, ID for dropdown children',

    -- Ordering
    display_order INT NOT NULL DEFAULT 0 COMMENT 'Lower numbers appear first',

    -- Visibility control
    visibility ENUM('public', 'authenticated', 'admin') DEFAULT 'public' COMMENT 'Who can see this menu item',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = visible, 0 = hidden',

    -- Special flags
    is_system TINYINT(1) DEFAULT 0 COMMENT '1 = system-managed (user menu), 0 = editable',
    open_new_tab TINYINT(1) DEFAULT 0 COMMENT '1 = target="_blank", 0 = normal link',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_parent_id (parent_id),
    INDEX idx_display_order (display_order),
    INDEX idx_visibility (visibility),
    INDEX idx_is_active (is_active),

    -- Foreign key constraint
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


