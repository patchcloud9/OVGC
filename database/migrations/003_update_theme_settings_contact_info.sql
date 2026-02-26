-- Migration: make site name & contact email required + add phone/address fields to theme_settings

ALTER TABLE theme_settings
    CHANGE COLUMN site_name site_name VARCHAR(100) NOT NULL COMMENT 'Site name displayed in navigation',
    CHANGE COLUMN gallery_contact_email contact_email VARCHAR(255) NOT NULL COMMENT 'Contact email shown in footer',
    ADD COLUMN phone_number VARCHAR(50) DEFAULT NULL COMMENT 'Contact phone number' AFTER contact_email,
    ADD COLUMN address1 VARCHAR(255) DEFAULT NULL COMMENT 'First line of address' AFTER phone_number,
    ADD COLUMN address2 VARCHAR(255) DEFAULT NULL COMMENT 'Second address line (optional)' AFTER address1,
    ADD COLUMN city_state_zip VARCHAR(255) DEFAULT NULL COMMENT 'City/State/ZIP combined' AFTER address2;

-- This migration ensures older installs receive the new columns and non-null
-- constraints. New installations already get the correct schema from the
-- initialize script.