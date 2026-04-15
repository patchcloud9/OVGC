ALTER TABLE events
    ADD COLUMN flyer_id INT NULL DEFAULT NULL AFTER cancelled_from,
    ADD CONSTRAINT fk_events_flyer
        FOREIGN KEY (flyer_id) REFERENCES flyers(id) ON DELETE SET NULL;
