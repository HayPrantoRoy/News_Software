-- SQL query to add link column to opinions table
-- Run this query in your MySQL database (phpMyAdmin or MySQL command line)

ALTER TABLE `opinions` ADD COLUMN `link` VARCHAR(500) NULL AFTER `image`;
