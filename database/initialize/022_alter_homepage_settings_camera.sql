-- Add camera maintenance mode columns to homepage_settings

ALTER TABLE homepage_settings
    ADD COLUMN camera_mode VARCHAR(20) NOT NULL DEFAULT 'live' AFTER bottom_section_image,
    ADD COLUMN camera_maintenance_image VARCHAR(255) NULL AFTER camera_mode;
