-- Add display_order column to news_video table
ALTER TABLE `news_video` 
ADD COLUMN `display_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Order in which videos appear' AFTER `youtube_link`;

-- Add index for better performance
ALTER TABLE `news_video` 
ADD INDEX `display_order` (`display_order`);

-- Update existing records with sequential display_order
SET @row_number = 0;
UPDATE `news_video` 
SET `display_order` = (@row_number:=@row_number + 1) 
ORDER BY `created_at` DESC;
