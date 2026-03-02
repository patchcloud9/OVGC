-- Create homepage customization table

CREATE TABLE IF NOT EXISTS homepage_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Hero Section
    hero_title VARCHAR(100) DEFAULT 'Welcome Home',
    hero_subtitle VARCHAR(255) DEFAULT 'Your PHP MVC Framework',
    hero_title_color VARCHAR(7) DEFAULT '#ffffff',
    hero_subtitle_color VARCHAR(7) DEFAULT '#f5f5f5',

    hero_background_color VARCHAR(7) DEFAULT '#667eea',
    hero_background_image VARCHAR(255) NULL,

    -- Feature Cards (3 cards)
    card1_title VARCHAR(100) DEFAULT 'Fast & Lightweight',
    card1_text TEXT,

    card2_title VARCHAR(100) DEFAULT 'Easy to Understand',
    card2_text TEXT,

    card3_title VARCHAR(100) DEFAULT 'Built with Love',
    card3_text TEXT,

    card1_button_text VARCHAR(100) DEFAULT 'Learn More',
    card1_button_link VARCHAR(255) DEFAULT '/about',
    card2_button_text VARCHAR(100) DEFAULT 'Learn More',
    card2_button_link VARCHAR(255) DEFAULT '/about',
    card3_button_text VARCHAR(100) DEFAULT 'Learn More',
    card3_button_link VARCHAR(255) DEFAULT '/about',

    -- Bottom Section (2 columns)
    bottom_section_layout VARCHAR(20) DEFAULT 'text-image', -- 'text-image' or 'image-text'
    bottom_section_title VARCHAR(255) DEFAULT 'How This Framework Works',
    bottom_section_text TEXT,
    bottom_section_image VARCHAR(255) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default homepage content moved to seed: database/seed/05_seed_homepage_settings.sql
-- This file now only defines the schema; default rows are created by seed files.
