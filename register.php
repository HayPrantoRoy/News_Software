<?php
session_start();
require_once 'master_connection.php';

$error = '';
$success = '';
$generated_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $number = trim($_POST['number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $news_portal_name = trim($_POST['news_portal_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $editor_in_chief = trim($_POST['editor_in_chief'] ?? '');
    $media_info = trim($_POST['media_info'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');

    // Validation
    if (empty($number) || empty($password) || empty($news_portal_name)) {
        $error = 'সব প্রয়োজনীয় ফিল্ড পূরণ করুন।';
    } elseif ($password !== $confirm_password) {
        $error = 'পাসওয়ার্ড মিলছে না।';
    } elseif (strlen($password) < 6) {
        $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
    } else {
        // Check if number already exists
        $check_stmt = $master_pdo->prepare("SELECT id FROM users WHERE number = ?");
        $check_stmt->execute([$number]);
        if ($check_stmt->rowCount() > 0) {
            $error = 'এই নম্বর ইতিমধ্যে ব্যবহৃত হয়েছে।';
        } else {
            // Handle logo upload
            $logo_image = '';
            if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/logos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_ext = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
                $logo_image = $upload_dir . uniqid() . '.' . $file_ext;
                move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_image);
            }

            try {
                // Insert user into master database with number, password and placeholder db name
                $stmt = $master_pdo->prepare("
                    INSERT INTO users (number, password, database_name)
                    VALUES (?, ?, 'temp')
                ");
                $stmt->execute([$number, $password]);

                $user_id = $master_pdo->lastInsertId();
                
                // Update database name with user ID: news_software_id_{id}
                $final_db_name = 'news_software_id_' . $user_id;
                $stmt = $master_pdo->prepare("UPDATE users SET database_name = ? WHERE id = ?");
                $stmt->execute([$final_db_name, $user_id]);
                
                $generated_number = $number;

            // Create new database for this user
            $master_pdo->exec("CREATE DATABASE IF NOT EXISTS `$final_db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Grant permissions to techshilpo_user
            try {
                $master_pdo->exec("GRANT ALL PRIVILEGES ON `$final_db_name`.* TO 'techshilpo_user'@'localhost'");
                $master_pdo->exec("FLUSH PRIVILEGES");
            } catch (PDOException $e) {
                // User may not exist or already has permissions - continue
            }

                // Connect to new database and create tables with user_id suffix
                $user_pdo = new PDO("mysql:host=$master_host;dbname=$final_db_name;charset=utf8mb4", $master_username, $master_password);
                $user_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Create all tables (no suffix needed - each tenant has own database)
                
                // Create basic_info table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `basic_info` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `news_portal_name` varchar(255) NOT NULL,
                    `image` varchar(100) NOT NULL,
                    `description` varchar(500) NOT NULL,
                    `editor_in_chief` varchar(50) NOT NULL,
                    `media_info` varchar(100) NOT NULL,
                    `privacy_policy` varchar(255) DEFAULT NULL,
                    `about_us` varchar(255) DEFAULT NULL,
                    `comment_policy` varchar(255) DEFAULT NULL,
                    `advertisement_policy` varchar(255) DEFAULT NULL,
                    `terms` varchar(255) DEFAULT NULL,
                    `advertisement_list` varchar(255) DEFAULT NULL,
                    `facebook` varchar(255) DEFAULT NULL,
                    `youtube` varchar(255) DEFAULT NULL,
                    `whatsapp` varchar(255) DEFAULT NULL,
                    `twitter` varchar(255) DEFAULT NULL,
                    `tiktok` varchar(255) DEFAULT NULL,
                    `instagram` varchar(255) DEFAULT NULL,
                    `mobile_number` varchar(20) DEFAULT NULL,
                    `email` varchar(100) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create category table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `category` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `slug` varchar(100) NOT NULL,
                    `is_active` tinyint(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create menus table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `menus` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `menu_name` varchar(100) NOT NULL,
                    `page_link` varchar(255) NOT NULL,
                    `icon` varchar(100) DEFAULT 'fas fa-circle',
                    `sort_order` int(11) DEFAULT 0,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

                // Create reporter table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `reporter` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL,
                    `email` varchar(100) DEFAULT NULL,
                    `mobile` varchar(20) DEFAULT NULL,
                    `address` text DEFAULT NULL,
                    `image` varchar(255) DEFAULT NULL,
                    `designation` varchar(100) DEFAULT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create news table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `news` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `category_id` int(11) NOT NULL,
                    `reporter_id` int(11) NOT NULL,
                    `headline` text NOT NULL,
                    `short_description` text DEFAULT NULL,
                    `slug` varchar(255) DEFAULT NULL,
                    `news_1` text NOT NULL,
                    `image_url` varchar(255) DEFAULT NULL,
                    `image_url_title` varchar(255) DEFAULT NULL,
                    `quote_1` text DEFAULT NULL,
                    `auture_1` varchar(255) DEFAULT NULL,
                    `news_2` text DEFAULT NULL,
                    `image_2` varchar(255) DEFAULT NULL,
                    `image_2_title` varchar(255) DEFAULT NULL,
                    `image_3` varchar(255) DEFAULT NULL,
                    `image_3_title` varchar(255) DEFAULT NULL,
                    `news_3` text DEFAULT NULL,
                    `quote_2` text DEFAULT NULL,
                    `auture_2` varchar(255) DEFAULT NULL,
                    `image_4` varchar(255) DEFAULT NULL,
                    `image_4_title` varchar(255) DEFAULT NULL,
                    `image_5` varchar(255) DEFAULT NULL,
                    `image_5_title` varchar(255) DEFAULT NULL,
                    `news_4` text DEFAULT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `earning` varchar(50) DEFAULT NULL,
                    `video` varchar(255) DEFAULT NULL,
                    `video_earning` varchar(50) DEFAULT NULL,
                    `is_featured` tinyint(1) DEFAULT 0,
                    `is_lead` tinyint(1) DEFAULT 0,
                    `is_first_lead` tinyint(1) DEFAULT 0,
                    `views` int(11) DEFAULT 0,
                    `meta_title` varchar(255) DEFAULT NULL,
                    `meta_description` text DEFAULT NULL,
                    `meta_keywords` varchar(255) DEFAULT NULL,
                    `web_earning` decimal(10,2) DEFAULT 0.00,
                    `is_paid` tinyint(1) DEFAULT 0,
                    `paid_at` datetime DEFAULT NULL,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `category_id` (`category_id`),
                    KEY `reporter_id` (`reporter_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create news_video table (matching news_software.sql)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `news_video` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` varchar(255) NOT NULL,
                    `subtitle` text DEFAULT NULL,
                    `thumbnail` varchar(255) NOT NULL,
                    `youtube_link` varchar(255) NOT NULL,
                    `display_order` int(11) NOT NULL DEFAULT 0,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `is_active` (`is_active`),
                    KEY `created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create podcasts table (matching news_software.sql)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `podcasts` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` varchar(255) NOT NULL,
                    `subtitle` text DEFAULT NULL,
                    `thumbnail` varchar(255) NOT NULL,
                    `youtube_link` varchar(255) NOT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create opinions table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `opinions` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(255) NOT NULL,
                    `link` varchar(500) DEFAULT NULL,
                    `display_order` int(11) DEFAULT 0,
                    `status` tinyint(1) DEFAULT 1,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create quizzes table (matching news_software.sql)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `quizzes` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `question` text NOT NULL,
                    `option_a` varchar(255) NOT NULL,
                    `option_b` varchar(255) NOT NULL,
                    `option_c` varchar(255) NOT NULL,
                    `option_d` varchar(255) NOT NULL,
                    `correct_option` enum('a','b','c','d') NOT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create quiz_votes table (matching news_software.sql)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_votes` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `quiz_id` int(11) NOT NULL,
                    `selected_option` enum('a','b','c','d') NOT NULL,
                    `device_id` varchar(255) NOT NULL,
                    `ip_address` varchar(45) DEFAULT NULL,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_vote` (`quiz_id`,`device_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create photo_cards table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `photo_cards` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` varchar(255) NOT NULL,
                    `image` varchar(255) NOT NULL,
                    `description` text DEFAULT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create news_links table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `news_links` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `news_id` int(11) NOT NULL,
                    `link_url` varchar(255) NOT NULL,
                    `link_title` varchar(255) DEFAULT NULL,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `news_id` (`news_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create roles table (matching news_software.sql structure)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `roles` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `role_name` varchar(100) NOT NULL,
                    `username` varchar(100) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `is_super_admin` tinyint(1) DEFAULT 0,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create role_permissions table (matching news_software.sql structure)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `role_permissions` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `role_id` int(11) NOT NULL,
                    `menu_id` int(11) NOT NULL,
                    `can_view` tinyint(1) DEFAULT 0,
                    `can_edit` tinyint(1) DEFAULT 0,
                    `can_delete` tinyint(1) DEFAULT 0,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `role_id` (`role_id`),
                    KEY `menu_id` (`menu_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create admin_users table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `username` varchar(100) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `role_id` int(11) DEFAULT 1,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `username` (`username`),
                    UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create earnings table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `earnings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `reporter_id` int(11) DEFAULT NULL,
                    `news_id` int(11) DEFAULT NULL,
                    `video_id` int(11) DEFAULT NULL,
                    `amount` decimal(10,2) DEFAULT 0.00,
                    `type` enum('news','video') DEFAULT 'news',
                    `status` enum('pending','paid') DEFAULT 'pending',
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create payments table
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `payments` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `reporter_id` int(11) NOT NULL,
                    `amount` decimal(10,2) NOT NULL,
                    `payment_method` varchar(50) DEFAULT NULL,
                    `transaction_id` varchar(100) DEFAULT NULL,
                    `status` enum('pending','completed','failed') DEFAULT 'pending',
                    `created_at` timestamp DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create settings table (matching news_software.sql - for category display order)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `category_id` int(11) NOT NULL,
                    `serial_order` int(11) NOT NULL DEFAULT 0,
                    `is_active` tinyint(1) DEFAULT 1,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Create video_earning table (matching news_software.sql)
                $user_pdo->exec("CREATE TABLE IF NOT EXISTS `video_earning` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `reporter_id` int(11) NOT NULL,
                    `video_headline` varchar(500) NOT NULL,
                    `earning` decimal(10,2) NOT NULL DEFAULT 0.00,
                    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    `youtube_earning` decimal(10,2) DEFAULT 0.00,
                    `is_paid` tinyint(1) DEFAULT 0,
                    `paid_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Insert basic info into the new database (blank except for registration data)
                $stmt = $user_pdo->prepare("
                    INSERT INTO `basic_info` (id, news_portal_name, image, description, editor_in_chief, media_info, mobile_number, email)
                    VALUES (1, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $news_portal_name, $logo_image, $description, $editor_in_chief, $media_info, $mobile_number, $contact_email
                ]);

                // Insert all menus from news_software.sql
                $user_pdo->exec("INSERT INTO `menus` (`id`, `menu_name`, `page_link`, `icon`, `sort_order`, `is_active`) VALUES
                    (1, 'Dashboard', 'dashboard.php', 'fas fa-chart-line', 1, 1),
                    (2, 'News', 'index.php', 'fas fa-newspaper', 2, 1),
                    (3, 'Videos', 'manage_videos.php', 'fas fa-video', 5, 1),
                    (4, 'Podcasts', 'manage_podcasts.php', 'fas fa-podcast', 6, 1),
                    (5, 'Opinions', 'manage_opinions.php', 'fas fa-comment-dots', 7, 1),
                    (6, 'Quizzes', 'manage_quizzes.php', 'fas fa-question-circle', 8, 1),
                    (7, 'Category', 'category.php', 'fas fa-folder', 9, 1),
                    (8, 'Reporter', 'reporter.php', 'fas fa-users', 10, 1),
                    (9, 'News Links', 'news_links.php', 'fas fa-link', 11, 1),
                    (10, 'Reporter Earning', 'reporter_earning.php', 'fas fa-dollar-sign', 12, 1),
                    (11, 'Video Earning', 'video_earning.php', 'fas fa-video', 13, 1),
                    (12, 'Total Earning', 'total_earning.php', 'fas fa-money-bill-wave', 14, 1),
                    (13, 'Earning Report', 'earning_report.php', 'fas fa-chart-bar', 15, 1),
                    (14, 'Payment', 'payment.php', 'fas fa-credit-card', 16, 1),
                    (15, 'Settings', 'settings.php', 'fas fa-cog', 17, 1),
                    (16, 'Roles', 'role.php', 'fas fa-user-shield', 18, 1),
                    (17, 'Permissions', 'permission.php', 'fas fa-key', 19, 1),
                    (18, 'News Portal Info', 'news_portal_info.php', 'fa-solid fa-circle-info', 4, 1),
                    (19, 'Photo Card', 'photocard.php', 'fa-regular fa-id-badge', 3, 1)
                ");

                // Create default admin role with the user's password
                $role_stmt = $user_pdo->prepare("INSERT INTO `roles` (`id`, `role_name`, `username`, `password`, `is_super_admin`, `is_active`) VALUES (1, 'Super Admin', ?, ?, 1, 1)");
                $role_stmt->execute([$number, $password]);

                // Insert role_permissions for all menus with full access
                $user_pdo->exec("INSERT INTO `role_permissions` (`role_id`, `menu_id`, `can_view`, `can_edit`, `can_delete`) VALUES
                    (1, 1, 1, 1, 1),
                    (1, 2, 1, 1, 1),
                    (1, 3, 1, 1, 1),
                    (1, 4, 1, 1, 1),
                    (1, 5, 1, 1, 1),
                    (1, 6, 1, 1, 1),
                    (1, 7, 1, 1, 1),
                    (1, 8, 1, 1, 1),
                    (1, 9, 1, 1, 1),
                    (1, 10, 1, 1, 1),
                    (1, 11, 1, 1, 1),
                    (1, 12, 1, 1, 1),
                    (1, 13, 1, 1, 1),
                    (1, 14, 1, 1, 1),
                    (1, 15, 1, 1, 1),
                    (1, 16, 1, 1, 1),
                    (1, 17, 1, 1, 1),
                    (1, 18, 1, 1, 1),
                    (1, 19, 1, 1, 1)
                ");

                // Create reporter_earnings_summary view
                $user_pdo->exec("CREATE OR REPLACE VIEW `reporter_earnings_summary` AS
                    SELECT 
                        r.id AS reporter_id,
                        r.name AS reporter_name,
                        COUNT(CASE WHEN combined.source = 'news' THEN 1 END) AS news_count,
                        COUNT(CASE WHEN combined.source = 'video' THEN 1 END) AS video_count,
                        COALESCE(SUM(combined.earning), 0) AS total_earning,
                        COALESCE(SUM(combined.video_earning), 0) AS video_earning,
                        COALESCE(SUM(combined.youtube_earning), 0) AS youtube_earning,
                        COALESCE(SUM(combined.earning), 0) + COALESCE(SUM(combined.video_earning), 0) + COALESCE(SUM(combined.youtube_earning), 0) AS grand_total
                    FROM (
                        SELECT 
                            n.reporter_id AS reporter_id,
                            COALESCE(CAST(n.earning AS DECIMAL(10,2)), 0) AS earning,
                            COALESCE(CAST(n.video_earning AS DECIMAL(10,2)), 0) AS video_earning,
                            0 AS youtube_earning,
                            'news' AS source
                        FROM news n
                        WHERE n.reporter_id IS NOT NULL AND n.reporter_id <> 0
                        UNION ALL
                        SELECT 
                            ve.reporter_id AS reporter_id,
                            COALESCE(ve.earning, 0) AS earning,
                            0 AS video_earning,
                            COALESCE(ve.youtube_earning, 0) AS youtube_earning,
                            'video' AS source
                        FROM video_earning ve
                        WHERE ve.reporter_id IS NOT NULL AND ve.reporter_id <> 0
                    ) combined
                    LEFT JOIN reporter r ON combined.reporter_id = r.id
                    GROUP BY r.id, r.name
                ");

                // Get current domain for news portal link
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $domain = $_SERVER['HTTP_HOST'];
                $portal_link = $protocol . '://' . $domain . '/News_Software/home.php?user_id=' . $user_id;
                $admin_link = $protocol . '://' . $domain . '/News_Software/Admin/index.php?user_id=' . $user_id;

                $success = "রেজিস্ট্রেশন সফল হয়েছে!<br><br>
                    <strong>আপনার নম্বর:</strong> $number<br>
                    <strong>নিউজ পোর্টাল:</strong> <a href='$portal_link' target='_blank'>$portal_link</a><br><br>
                    এই নম্বর ও পাসওয়ার্ড দিয়ে <a href='$admin_link'>অ্যাডমিন প্যানেলে</a> লগইন করুন।";
                
            } catch (Exception $e) {
                $error = 'রেজিস্ট্রেশন ব্যর্থ: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নিউজ পোর্টাল রেজিস্ট্রেশন</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .register-container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 60px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 700px;
            border: 1px solid #f0f0f0;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-container img {
            max-width: 180px;
            height: auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .register-header h1 {
            color: #1a1a1a;
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .register-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group.full-width {
            flex: 100%;
        }
        
        .section-title {
            color: #4a90e2;
            font-weight: 600;
            font-size: 14px;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8e8e8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-title:first-of-type {
            margin-top: 0;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        label.required::after {
            content: ' *';
            color: #e53935;
        }
        
        input[type="text"], 
        input[type="password"], 
        input[type="email"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: Arial, sans-serif;
            background: #fafafa;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #4a90e2;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn-register {
            width: 100%;
            padding: 16px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .btn-register:hover {
            background: #357abd;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }
        
        .error-message {
            background: #fff5f5;
            color: #e53e3e;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #feb2b2;
            font-size: 14px;
        }
        
        .success-message {
            background: #f0fff4;
            color: #276749;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #9ae6b4;
            font-size: 15px;
            line-height: 1.8;
        }
        
        .success-message a {
            color: #4a90e2;
            font-weight: 600;
            text-decoration: none;
        }
        
        .success-message a:hover {
            text-decoration: underline;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .login-link a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
        
        @media (max-width: 600px) {
            .register-container {
                padding: 30px 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .register-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Logo" onerror="this.style.display='none'">
        </div>
        
        <div class="register-header">
            <h1>নিউজ পোর্টাল রেজিস্ট্রেশন</h1>
            <p>আপনার নিজস্ব নিউজ পোর্টাল তৈরি করুন</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php else: ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="section-title">লগইন তথ্য</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">নম্বর (মোবাইল/যেকোনো)</label>
                    <input type="text" name="number" required 
                           value="<?php echo htmlspecialchars($_POST['number'] ?? ''); ?>"
                           placeholder="আপনার নম্বর লিখুন">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">পাসওয়ার্ড</label>
                    <input type="password" name="password" required
                           placeholder="কমপক্ষে ৬ অক্ষর">
                </div>
                <div class="form-group">
                    <label class="required">পাসওয়ার্ড নিশ্চিত করুন</label>
                    <input type="password" name="confirm_password" required
                           placeholder="পাসওয়ার্ড পুনরায় লিখুন">
                </div>
            </div>

            <div class="section-title">পোর্টাল তথ্য</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">নিউজ পোর্টাল নাম</label>
                    <input type="text" name="news_portal_name" required
                           value="<?php echo htmlspecialchars($_POST['news_portal_name'] ?? ''); ?>"
                           placeholder="আপনার পোর্টালের নাম">
                </div>
                <div class="form-group">
                    <label>লোগো ছবি</label>
                    <input type="file" name="logo_image" accept="image/*">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label>বিবরণ</label>
                    <textarea name="description" rows="2"
                              placeholder="সংক্ষিপ্ত বিবরণ"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>সম্পাদক</label>
                    <input type="text" name="editor_in_chief"
                           value="<?php echo htmlspecialchars($_POST['editor_in_chief'] ?? ''); ?>"
                           placeholder="সম্পাদকের নাম">
                </div>
                <div class="form-group">
                    <label>মিডিয়া তথ্য</label>
                    <input type="text" name="media_info"
                           value="<?php echo htmlspecialchars($_POST['media_info'] ?? ''); ?>"
                           placeholder="মিডিয়া তথ্য">
                </div>
            </div>

            <div class="section-title">যোগাযোগ তথ্য</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>মোবাইল নম্বর</label>
                    <input type="text" name="mobile_number"
                           value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>"
                           placeholder="মোবাইল নম্বর">
                </div>
                <div class="form-group">
                    <label>ইমেইল</label>
                    <input type="email" name="contact_email"
                           value="<?php echo htmlspecialchars($_POST['contact_email'] ?? ''); ?>"
                           placeholder="ইমেইল ঠিকানা">
                </div>
            </div>

            <button type="submit" class="btn-register">রেজিস্টার করুন</button>
        </form>
        <?php endif; ?>
        
        <div class="login-link">
            ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="Admin/index.php">লগইন করুন</a>
        </div>
        
        <div class="register-footer">
            সংবাদ প্রশাসন সিস্টেম &copy; <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
