-- Add address columns to users table if they don't exist
-- This script adds the necessary address columns for the checkout functionality

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS address_line1 VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS address_line2 VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS state VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS postal_code VARCHAR(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT NULL;
