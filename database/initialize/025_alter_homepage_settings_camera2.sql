-- Add a second camera slot toggle to homepage_settings.
-- 'live' shows the camera 2 feed (and moves Upcoming Events to a full-width row
-- below both cameras); 'maintenance' (default) shows Upcoming Events in that column instead.

ALTER TABLE homepage_settings
    ADD COLUMN camera2_mode VARCHAR(20) NOT NULL DEFAULT 'maintenance' AFTER camera_maintenance_image;
