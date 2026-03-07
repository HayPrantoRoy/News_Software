-- Add image title/caption columns to news table
-- Run this SQL in phpMyAdmin or MySQL command line

ALTER TABLE `news` 
ADD COLUMN `image_url_title` VARCHAR(255) DEFAULT NULL COMMENT 'Caption for main image' AFTER `image_url`,
ADD COLUMN `image_2_title` VARCHAR(255) DEFAULT NULL COMMENT 'Caption for image 2' AFTER `image_2`,
ADD COLUMN `image_3_title` VARCHAR(255) DEFAULT NULL COMMENT 'Caption for image 3' AFTER `image_3`,
ADD COLUMN `image_4_title` VARCHAR(255) DEFAULT NULL COMMENT 'Caption for image 4' AFTER `image_4`,
ADD COLUMN `image_5_title` VARCHAR(255) DEFAULT NULL COMMENT 'Caption for image 5' AFTER `image_5`;

-- If you get an error that a column already exists, you can run them individually:
-- ALTER TABLE `news` ADD COLUMN `image_url_title` VARCHAR(255) DEFAULT NULL AFTER `image_url`;
-- ALTER TABLE `news` ADD COLUMN `image_2_title` VARCHAR(255) DEFAULT NULL AFTER `image_2`;
-- ALTER TABLE `news` ADD COLUMN `image_3_title` VARCHAR(255) DEFAULT NULL AFTER `image_3`;
-- ALTER TABLE `news` ADD COLUMN `image_4_title` VARCHAR(255) DEFAULT NULL AFTER `image_4`;
-- ALTER TABLE `news` ADD COLUMN `image_5_title` VARCHAR(255) DEFAULT NULL AFTER `image_5`;
