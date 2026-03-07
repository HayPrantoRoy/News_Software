-- Create news_video table for dynamic video section
CREATE TABLE IF NOT EXISTS `news_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `youtube_link` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data (optional - remove if not needed)
INSERT INTO `news_video` (`title`, `subtitle`, `thumbnail`, `youtube_link`, `is_active`) VALUES
('স্বাধীনতা দিবসের বিশেষ প্রতিবেদন', 'বাংলাদেশের ৫৩তম স্বাধীনতা দিবস উপলক্ষে বিশেষ প্রতিবেদন', 'default.jpg', 'wJnBTPUQS5A', 1),
('হিন্দু সম্প্রদায়ের উৎসব', 'দুর্গা পুজার বিশেষ প্রতিবেদন ২০২৬', 'default.jpg', 'dQw4w9WgXcQ', 1);
