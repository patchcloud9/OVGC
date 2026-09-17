-- Add a second camera/image slot to homepage_settings (mirrors camera_mode / camera_maintenance_image)

ALTER TABLE homepage_settings
    ADD COLUMN camera2_mode VARCHAR(20) NOT NULL DEFAULT 'maintenance' AFTER camera_maintenance_image,
    ADD COLUMN camera2_maintenance_image VARCHAR(255) NULL AFTER camera2_mode;
