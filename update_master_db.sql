-- Add 'number' column to master database users table
-- Run this SQL in phpMyAdmin or MySQL command line

USE master_news_software_db;

-- Add number column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS `number` VARCHAR(50) UNIQUE AFTER `id`;

-- Update existing records to use id as number (temporary)
UPDATE users SET number = id WHERE number IS NULL;
