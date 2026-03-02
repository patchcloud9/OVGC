-- Gallery Images Table
-- Stores uploaded images for the public gallery

CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,

    -- Pricing & Prints
    price_type VARCHAR(50) DEFAULT 'hide' COMMENT 'hide|amount|sold_prints|not_for_sale',
    price_amount DECIMAL(10,2) NULL COMMENT 'Price in dollars when price_type = amount',
    prints_available TINYINT(1) DEFAULT 0 COMMENT '0 = prints not available, 1 = prints available',
    prints_url VARCHAR(512) NULL COMMENT 'URL to print purchase page',

    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(512) NOT NULL,
    uploaded_by INT NOT NULL,

    -- Display order for manual ordering
    display_order INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_created_at (created_at),
    INDEX idx_display_order (display_order),
    INDEX idx_price_type (price_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Set initial display_order based on created_at (newest first gets lower order number)
SET @row_number = 0;
UPDATE gallery_images 
SET display_order = (@row_number:=@row_number + 1)
ORDER BY created_at DESC;
