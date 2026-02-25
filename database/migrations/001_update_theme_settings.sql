-- Migration: update theme_settings schema for new logo fields

ALTER TABLE theme_settings
    -- rename the existing logo column to clarify purpose
    CHANGE COLUMN logo_path homepage_logo_path VARCHAR(255) DEFAULT NULL
        COMMENT 'Path to uploaded homepage/logo file',
    -- add a new column intended for an alternate or secondary logo
    ADD COLUMN secondary_logo_path VARCHAR(255) DEFAULT NULL
        COMMENT 'Secondary logo' AFTER homepage_logo_path,
    -- drop the unused hero background image field
    DROP COLUMN hero_background_image;

-- Note: existing application code may need to be updated to use the
-- renamed homepage_logo_path and to ignore the dropped column.