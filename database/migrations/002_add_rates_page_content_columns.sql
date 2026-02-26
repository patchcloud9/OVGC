-- Migration: add optional paragraph columns to rates_page_content

ALTER TABLE rates_page_content
    ADD COLUMN above_rates TEXT NULL
        COMMENT 'Optional paragraph displayed above the rate cards' AFTER scorecard_path,
    ADD COLUMN above_format VARCHAR(50) NULL
        COMMENT 'Comma-separated formatting flags (bold,italic) for above text' AFTER above_rates,
    ADD COLUMN below_rates TEXT NULL
        COMMENT 'Optional paragraph displayed below the rate cards' AFTER above_format,
    ADD COLUMN below_format VARCHAR(50) NULL
        COMMENT 'Comma-separated formatting flags (bold,italic) for below text' AFTER below_rates;

-- Note: this migration is required only for databases that already have the
-- rates_page_content table (existing installations). New installs use the
-- updated initialize SQL which already includes these columns.