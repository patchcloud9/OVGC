-- Theme Settings Table
-- Stores site-wide theme configuration (colors, logo, favicon, layout options)
-- Single row table - only one theme configuration per site

CREATE TABLE IF NOT EXISTS theme_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Color Palette
    primary_color VARCHAR(7) NOT NULL DEFAULT '#667eea' COMMENT 'Primary brand color (hex)',
    secondary_color VARCHAR(7) NOT NULL DEFAULT '#764ba2' COMMENT 'Secondary accent color (hex)',
    accent_color VARCHAR(7) NOT NULL DEFAULT '#48c78e' COMMENT 'Tertiary accent color (hex)',
    danger_color VARCHAR(7) NOT NULL DEFAULT '#f14668' COMMENT 'Danger/destructive color (hex)',

    -- Navbar / Navigation
    navbar_color VARCHAR(7) DEFAULT '#667eea' COMMENT 'Navbar background color (hex)',
    navbar_hover_color VARCHAR(7) DEFAULT '#ffffff' COMMENT 'Navbar hover color (hex)',
    navbar_text_color VARCHAR(7) DEFAULT '#ffffff' COMMENT 'Navbar text color (hex)',

    -- Brand Assets & Site Info
    homepage_logo_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to uploaded homepage/logo file',
    secondary_logo_path VARCHAR(255) DEFAULT NULL COMMENT 'Secondary logo',
    favicon_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to uploaded favicon file',
    site_name VARCHAR(100) NOT NULL COMMENT 'Site name displayed in navigation',
    contact_email VARCHAR(255) NOT NULL COMMENT 'Contact email shown in footer',
    phone_number VARCHAR(50) DEFAULT NULL COMMENT 'Contact phone number',
    address1 VARCHAR(255) DEFAULT NULL COMMENT 'First line of address',
    address2 VARCHAR(255) DEFAULT NULL COMMENT 'Second address line (optional)',
    city_state_zip VARCHAR(255) DEFAULT NULL COMMENT 'City/State/ZIP combined',
    footer_tagline VARCHAR(255) DEFAULT NULL COMMENT 'Optional footer tagline',

    -- Hero / Header Customization
    hero_background_color VARCHAR(7) DEFAULT NULL COMMENT 'Hero background color (hex)',
    -- hero_background_image removed; handled elsewhere or not used

    -- Layout Options
    header_style ENUM('fixed', 'static') NOT NULL DEFAULT 'static' COMMENT 'Header positioning',
    card_style ENUM('default', 'elevated', 'flat') NOT NULL DEFAULT 'default' COMMENT 'Card appearance style',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure only one row exists (singleton pattern)
-- This trigger prevents multiple theme configurations
DELIMITER //
CREATE TRIGGER theme_settings_singleton_check
BEFORE INSERT ON theme_settings
FOR EACH ROW
BEGIN
    DECLARE row_count INT;
    SELECT COUNT(*) INTO row_count FROM theme_settings;
    IF row_count >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only one theme configuration is allowed';
    END IF;
END//
DELIMITER ;
