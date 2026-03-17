<?php
session_start();

// Get user_id from URL and store in session for multi-tenant support
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_SESSION['current_user_id']) ? intval($_SESSION['current_user_id']) : 0);
if ($user_id > 0) {
    $_SESSION['current_user_id'] = $user_id;
}

// URL suffix for maintaining user_id across pages
$user_id_param = $user_id > 0 ? "?user_id=$user_id" : "";
$user_id_suffix = $user_id > 0 ? "&user_id=$user_id" : "";

include 'connection.php';
date_default_timezone_set('Asia/Dhaka');

// AJAX handler for podcast date filtering
if (isset($_GET['ajax_podcasts'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'podcasts' => []];
    
    try {
        $date = isset($_GET['date']) ? $_GET['date'] : null;
        
        if ($date) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if (!$dateObj) {
                throw new Exception('Invalid date format');
            }
            
            $stmt = $conn->prepare("SELECT id, title, subtitle, thumbnail, youtube_link, created_at 
                                    FROM $tbl_podcasts 
                                    WHERE is_active = 1 
                                    AND DATE(created_at) = ? 
                                    ORDER BY created_at DESC 
                                    LIMIT 8");
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT id, title, subtitle, thumbnail, youtube_link, created_at 
                                    FROM $tbl_podcasts 
                                    WHERE is_active = 1 
                                    ORDER BY created_at DESC 
                                    LIMIT 8");
        }
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $ytLink = $row['youtube_link'];
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $ytLink, $matches);
                $ytId = $matches[1] ?? '';
                
                $response['podcasts'][] = [
                    'id' => $row['id'],
                    'title' => htmlspecialchars($row['title']),
                    'subtitle' => htmlspecialchars($row['subtitle'] ?? ''),
                    'thumbnail' => !empty($row['thumbnail']) ? $row['thumbnail'] : 'default.jpg',
                    'youtube_id' => $ytId,
                    'created_at' => $row['created_at']
                ];
            }
            $response['success'] = true;
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}

$uploadPath = 'Admin/img/';

// Helper function for Bangla date
function banglaDate($date) {
    $engDATE = array(0,1,2,3,4,5,6,7,8,9,'January','February','March','April','May','June','July','August','September','October','November','December');
    $bangDATE = array('০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর');
    
    $format = date('d F Y, H:i', strtotime($date));
    $format = str_replace($engDATE, $bangDATE, $format);
    return $format;
}

// Fetch 3 random news
$sql_random = "SELECT n.headline, n.slug AS news_slug, n.image_url, n.created_at, c.slug AS category_slug
               FROM $tbl_news n
               JOIN $tbl_category c ON n.category_id = c.id
               WHERE n.is_active = 1
               ORDER BY RAND()
               LIMIT 3";

$result_random = $conn->query($sql_random);

// Fetch opinions for slider (last 10 only)
$sql_opinions = "SELECT id, image, link
                 FROM $tbl_opinions 
                 WHERE status = 1 
                 ORDER BY created_at DESC
                 LIMIT 10";
$result_opinions = $conn->query($sql_opinions);
$opinions = [];
if ($result_opinions && $result_opinions->num_rows > 0) {
    while($row = $result_opinions->fetch_assoc()) {
        $opinions[] = $row;
    }
}

// Fetch basic_info for footer
$basic_info = [];
$sql_basic_info = "SELECT id, news_portal_name, image, description, editor_in_chief, media_info, 
                   privacy_policy, about_us, comment_policy, advertisement_policy, terms, 
                   advertisement_list, facebook, youtube, whatsapp, twitter, tiktok, instagram, 
                   mobile_number, email FROM $tbl_basic_info LIMIT 1";
$result_basic_info = $conn->query($sql_basic_info);
if ($result_basic_info && $result_basic_info->num_rows > 0) {
    $basic_info = $result_basic_info->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <title><?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?> | সর্বশেষ বাংলা খবর</title>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6023610381338539" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <?php if (!empty($basic_info['image'])): ?>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($basic_info['image']); ?>" />
    <?php endif; ?>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/fontawesome-5.0.8/css/fontawesome-all.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/main_css.css">
    
<link rel="amphtml" href="https://hindus-news.com/amp/news.php">
<link rel="canonical" href="https://hindus-news.com/news.php">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <!--===============================================================================================-->
    <style>
        /* ===== NEW HERO SECTION STYLES ===== */
        .hero-section {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        
        .hero-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
        }
        
        /* Hero Main Container */
        .hero-main {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-left: 30px;
        }
        
        /* Top Row - Featured Left + Side News Right */
        .hero-top-row {
            display: grid;
            grid-template-columns: 450px 350px;
            gap: 0;
            border: 1px solid #ddd;
            width: fit-content;
        }
        
        .hero-top-row .featured-large {
            border-right: 1px solid #ddd;
        }
        
        /* Large Featured News - Left Side */
        .featured-large {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
        }
        
        .featured-large-image {
            width: 450px;
            height: 250px;
            overflow: hidden;
        }
        
        .featured-large-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .featured-large:hover .featured-large-image img {
            transform: scale(1.03);
        }
        
        .featured-large-content {
            padding: 15px;
        }
        
        .featured-large-headline {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            line-height: 1.4;
            margin-bottom: 12px;
        }
        
        .featured-large-headline a {
            color: #333;
            text-decoration: none;
        }
        
        .featured-large-headline a:hover {
            color: #dc2626;
        }
        
        .featured-large-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #333;
            font-size: 14px;
        }
        
        .featured-large-meta i {
            font-size: 13px;
            color: #9ca3af;
        }
        
        /* Side News List - Right Side */
        .side-news-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            background: #fff;
            padding: 15px;
        }
        
        .side-news-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .side-news-item:first-child {
            padding-top: 0;
        }
        
        .side-news-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .side-news-thumb {
            width: 130px;
            height: 75px;
            flex-shrink: 0;
            overflow: hidden;
        }
        
        .side-news-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .side-news-item:hover .side-news-thumb img {
            transform: scale(1.05);
        }
        
        .side-news-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .side-news-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            line-height: 1.4;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .side-news-title a {
            color: #333;
            text-decoration: none;
        }
        
        .side-news-title a:hover {
            color: #dc2626;
        }
        
        .side-news-meta {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #333;
            font-size: 12px;
        }
        
        .side-news-meta i {
            font-size: 11px;
            color: #9ca3af;
        }
        
        /* Bottom News Row - 6 Cards (3x2) */
        .hero-bottom-news {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding-top: 20px;
            width: 800px;
        }
        
        .bottom-news-card {
            background: #fff;
        }
        
        .bottom-news-card .card-image {
            width: 100%;
            height: 140px;
            overflow: hidden;
        }
        
        .bottom-news-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .bottom-news-card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .bottom-news-headline {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            line-height: 1.4;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        
        .bottom-news-headline a {
            color: #333;
            text-decoration: none;
        }
        
        .bottom-news-headline a:hover {
            color: #dc2626;
        }
        
        .bottom-news-excerpt {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .bottom-news-meta {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #9ca3af;
            font-size: 12px;
        }
        
        .bottom-news-meta i {
            font-size: 11px;
        }
        
        /* Center Column - Tabs and News List */
        .hero-center {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-left: 1px solid #e5e7eb;
            padding-left: 10px;
            padding-right: 15px;
            margin-left: 0;
        }
        
        
        .hero-center-banner {
            width: 100%;
            margin-bottom: 0;
        }
        
        .hero-center-banner img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .hero-center-tabs {
            display: flex;
            border: 1px solid #ddd;
            border-bottom: none;
        }
        
        .hero-center-tab {
            flex: 1;
            padding: 10px 15px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            background: #fff;
            border: none;
            border-right: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .hero-center-tab:last-child {
            border-right: none;
        }
        
        .hero-center-tab.active {
            background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
            color: white;
        }
        
        .hero-center-tab:hover:not(.active) {
            background: #f5f5f5;
        }
        
        .hero-center-list {
            padding: 0;
            max-height: 320px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-top: none;
        }
        
        .hero-center-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
            background: #fff;
        }
        
        .hero-center-item:last-child {
            border-bottom: none;
        }
        
        .hero-center-number {
            font-size: 26px;
            font-weight: 300;
            color: #c62828;
            min-width: 30px;
            line-height: 1.2;
        }
        
        .hero-center-text {
            font-size: 15px;
            color: #333;
            line-height: 1.5;
            font-weight: 500;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hero-center-text a {
            color: #333;
            text-decoration: none;
        }
        
        .hero-center-text a:hover {
            color: #c62828;
        }
        
        .hero-center-btn {
            display: block;
            background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
        }
        
        .hero-center-btn:hover {
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            color: white;
        }
        
        .hero-center-tags {
            padding: 15px 10px;
            border-top: 1px solid #e5e7eb;
        }
        
        .hero-center-tags-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
        }
        
        .hero-center-tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .hero-center-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border: 1px solid #e53935;
            border-radius: 20px;
            color: #e53935;
            font-size: 13px;
            text-decoration: none;
        }
        
        .hero-center-tag:hover {
            background: #e53935;
            color: white;
        }
        
        .headline-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        /* Responsive Styles */
        @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .hero-main {
                margin-left: 0;
            }
            .hero-center {
                border-left: none;
                padding-left: 0;
                padding-right: 0;
                max-width: 100%;
            }
            .hero-top-row {
                grid-template-columns: 1fr 300px;
                width: 100%;
            }
            .hero-bottom-news {
                width: 100%;
            }
        }
        
        @media (max-width: 900px) {
            .hero-top-row {
                grid-template-columns: 1fr;
                gap: 0;
                border: none;
                width: 100%;
            }
            .hero-top-row .featured-large {
                border-right: none;
            }
            .featured-large {
                background: #fff;
            }
            .featured-large-image {
                width: 100%;
                height: auto;
                aspect-ratio: 16/9;
            }
            .featured-large-headline {
                font-size: 20px;
            }
            .featured-large-content {
                background: #f9f9f9;
                padding: 15px;
            }
            .featured-large-headline,
            .featured-large-headline a {
                color: #333 !important;
            }
            .featured-large-meta {
                color: #666 !important;
            }
            .featured-large-meta i {
                color: #666 !important;
            }
            .side-news-list {
                display: flex;
                flex-direction: column;
                gap: 0;
                background: #fff;
                padding: 0;
                margin-top: 15px;
                border-top: 1px solid #e5e7eb;
                padding-top: 10px;
            }
            .side-news-item {
                display: flex;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
            }
            .side-news-item:last-child {
                border-bottom: none;
            }
            .side-news-thumb {
                width: 120px;
                height: 75px;
            }
            .hero-bottom-news {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 10px;
                border: none;
                border-radius: 0;
            }
            .hero-main {
                gap: 0;
            }
            .featured-large-image {
                height: auto;
                aspect-ratio: 16/9;
            }
            .featured-large-headline {
                font-size: 18px;
            }
            .side-news-thumb {
                width: 100px;
                height: 65px;
            }
            .side-news-title {
                font-size: 14px;
            }
            .side-news-meta {
                font-size: 11px;
            }
            .hero-center {
                margin-top: 15px;
            }
            .hero-center-banner img {
                width: 100%;
                height: auto;
            }
            .hero-center-tabs {
                border: none;
            }
            .hero-center-tab {
                padding: 10px;
                font-size: 13px;
            }
            .hero-center-list {
                max-height: 280px;
                border: 1px solid #eee;
            }
            .hero-center-item {
                padding: 10px 8px;
            }
            .hero-center-number {
                font-size: 20px;
                min-width: 25px;
            }
            .hero-center-text {
                font-size: 14px;
            }
            .hero-center-btn {
                padding: 10px;
                font-size: 13px;
            }
            .hero-center-tags {
                padding: 12px 8px;
            }
            .hero-center-tag {
                font-size: 12px;
                padding: 5px 10px;
            }
        }
        
        @media (max-width: 500px) {
            .hero-section {
                padding: 5px;
            }
            .featured-large-content {
                padding: 12px;
            }
            .featured-large-headline {
                font-size: 16px;
                margin-bottom: 8px;
            }
            .featured-large-meta {
                font-size: 11px;
            }
            .side-news-thumb {
                width: 90px;
                height: 60px;
            }
            .side-news-title {
                font-size: 13px;
            }
            .hero-center-number {
                font-size: 18px;
                min-width: 22px;
            }
            .hero-center-text {
                font-size: 13px;
            }
            .hero-center-tag {
                font-size: 11px;
                padding: 4px 8px;
            }
        }
        /* ===== END HERO SECTION STYLES ===== */

        /* ===== MODERN VIDEO SECTION ===== */
        .video-section {
            background: #1a1a1a;
            padding: 30px 0;
            margin: 30px 0;
        }
        
        .video-section-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .video-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .video-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .video-title-icon {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-size: 20px;
        }
        
        .video-badge {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .video-badge img {
            height: 3px;
            width: auto;
            display: block;
        }
        
        .video-title h2 {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin: 0;
        }
        
        .video-tabs {
            display: flex;
            gap: 5px;
        }
        
        .video-tab {
            padding: 8px 20px;
            background: #dc2626;
            color: white;
            border: none;
            font-size: 13px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .video-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .video-thumbnail {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        
        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .video-item:hover .video-thumbnail img {
            transform: scale(1.05);
        }
        
        .video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 45px;
            height: 45px;
            background: rgba(220, 38, 38, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .video-item:hover .video-play-btn {
            transform: translate(-50%, -50%) scale(1.1);
            background: #dc2626;
        }
        
        .video-info {
            background: linear-gradient(to bottom, #2d2d2d, #1a1a1a);
            padding: 12px;
        }
        
        .video-headline {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff !important;
            line-height: 1.4;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-headline a {
            color: #f59e0b;
            text-decoration: none;
        }
        
        .video-excerpt {
            display: none;
            font-size: 11px;
            color: #ffffff !important;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }
        
        .video-see-all {
            text-align: right;
            margin-top: 20px;
        }
        
        .video-see-all a {
            color: #f59e0b;
            font-size: 13px;
            text-decoration: none;
        }
        
        .video-see-all a:hover {
            text-decoration: underline;
        }
        
        /* Video Modal */
        .video-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .video-modal.active {
            display: flex;
        }
        
        .video-modal-content {
            width: 90%;
            max-width: 900px;
            position: relative;
        }
        
        .video-modal-close {
            position: absolute;
            top: -45px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 10px;
        }
        
        .video-modal-close:hover {
            color: #FF0000;
        }
        
        .video-modal iframe {
            width: 100%;
            height: 500px;
            border-radius: 12px;
        }
        
        @media (max-width: 1024px) {
            .video-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .video-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .video-thumbnail {
                height: 150px;
            }
            .video-modal iframe {
                height: 300px;
            }
            .video-tabs {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .video-thumbnail {
                height: 130px;
            }
            .video-headline {
                font-size: 14px;
            }
            .video-excerpt {
                display: none;
            }
        }

        /* ===== PODCAST SECTION ===== */
        .podcast-section {
            background: #1a1a1a;
            padding: 30px 0;
            margin: 30px 0;
        }
        
        .podcast-section-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .podcast-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .podcast-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .podcast-title-icon {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-size: 20px;
        }
        
        .podcast-title h2 {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin: 0;
        }
        
        .podcast-date-picker {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .podcast-date-picker input[type="date"] {
            padding: 8px 16px;
            background: #2d2d2d;
            color: white;
            border: 1px solid #444;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .podcast-date-picker input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        
        .podcast-date-nav {
            display: flex;
            gap: 5px;
        }
        
        .podcast-date-nav button {
            padding: 8px 12px;
            background: #dc2626;
            color: white;
            border: none;
            font-size: 13px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .podcast-date-nav button:hover {
            background: #b91c1c;
        }
        
        .podcast-reset-btn {
            padding: 8px 16px;
            background: transparent;
            color: white;
            border: 1px solid #444;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .podcast-reset-btn:hover {
            background: #444;
            border-color: #666;
        }
        
        .podcast-grid {
            display: flex;
            overflow: hidden;
            gap: 20px;
        }
        
        .podcast-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            min-width: calc(25% - 15px);
            flex-shrink: 0;
            transition: transform 0.5s ease;
        }
        
        .podcast-thumbnail {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        
        .podcast-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .podcast-item:hover .podcast-thumbnail img {
            transform: scale(1.05);
        }
        
        .podcast-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 45px;
            height: 45px;
            background: rgba(220, 38, 38, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .podcast-item:hover .podcast-play-btn {
            transform: translate(-50%, -50%) scale(1.1);
            background: #dc2626;
        }
        
        .podcast-info {
            background: linear-gradient(to bottom, #2d2d2d, #1a1a1a);
            padding: 12px;
        }
        
        .podcast-headline {
            font-size: 13px;
            font-weight: 600;
            color: #f59e0b !important;
            line-height: 1.4;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .podcast-excerpt {
            font-size: 11px;
            color: #ffffff !important;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }
        
        /* Podcast Slider Dots - Show on all screens */
        .podcast-slider-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }
        .podcast-slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #444;
            cursor: pointer;
            transition: all 0.3s;
        }
        .podcast-slider-dot.active {
            background: #dc2626;
            width: 24px;
            border-radius: 4px;
        }
        
        @media (max-width: 1024px) {
            .podcast-item {
                min-width: calc(33.333% - 14px);
            }
        }
        
        @media (max-width: 768px) {
            .podcast-item {
                min-width: calc(50% - 10px);
            }
            .podcast-thumbnail {
                height: 180px;
            }
        }
        
        @media (max-width: 480px) {
            .podcast-section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .podcast-title h2 {
                font-size: 16px;
            }
            .podcast-date-picker {
                width: 100%;
                justify-content: space-between;
                gap: 8px;
            }
            .podcast-date-picker input[type="date"] {
                padding: 6px 10px;
                font-size: 12px;
                flex: 1;
            }
            .podcast-date-nav button {
                padding: 6px 10px;
                font-size: 12px;
            }
            .podcast-reset-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
            .podcast-item {
                min-width: 100%;
            }
            .podcast-thumbnail {
                height: 200px;
            }
            .podcast-headline {
                font-size: 14px;
            }
            .podcast-excerpt {
                display: block;
            }
        }

        /* ===== QUIZ SECTION STYLES - White/Red Theme ===== */
        .quiz-category-section {
            max-width: 1250px;
            margin: 0 auto 40px;
            padding: 0 15px;
        }
        
        .quiz-category-section .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #dc2626;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
        }
        
        .quiz-layout-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .quiz-news-left {
            padding: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .quiz-news-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .quiz-news-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 0;
        }
        
        .quiz-news-card-content {
            padding-top: 12px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        .quiz-news-card-content h3 {
            margin-bottom: auto;
        }
        
        .quiz-news-card h3 {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.5;
            color: #1e3a5f;
            margin: 0 0 8px 0;
        }
        
        .quiz-news-card h3 a {
            color: #1e3a5f;
            text-decoration: none;
        }
        
        .quiz-news-card h3 a:hover {
            color: #dc2626;
        }
        
        .quiz-news-card p {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            margin: 0;
        }
        
        .quiz-box {
            background: linear-gradient(135deg, #fefefe 0%, #fafafa 100%);
            border-left: 2px solid #dc2626;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 2px solid #dc2626;
        }
        
        .quiz-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 700;
            color: #dc2626;
        }
        
        .quiz-title .quiz-icon {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
        }
        
        .quiz-nav {
            display: flex;
            gap: 5px;
        }
        
        .quiz-nav button {
            background: #fff;
            border: 1px solid #dc2626;
            color: #dc2626;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .quiz-nav button:hover:not(:disabled) {
            background: #dc2626;
            color: #fff;
        }
        
        .quiz-nav button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .quiz-counter {
            font-size: 11px;
            color: #666;
            text-align: center;
            margin-bottom: 12px;
            background: #fff;
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .quiz-question {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #1a1a1a;
            font-weight: 600;
        }
        
        .quiz-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .quiz-option {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            position: relative;
            overflow: hidden;
            min-height: 48px;
        }
        
        .quiz-option:hover:not(.voted) {
            background: #fef2f2;
            border-color: #dc2626;
        }
        
        .quiz-option.voted {
            cursor: default;
            transform: none !important;
        }
        
        .quiz-option.correct {
            background: #dcfce7;
            border-color: #16a34a;
        }
        
        .quiz-option.wrong {
            background: #fef2f2;
            border-color: #dc2626;
        }
        
        .quiz-option.selected {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.3);
        }
        
        .quiz-selected-tag {
            font-size: 9px;
            font-weight: 700;
            color: #fff;
            background: #dc2626;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 8px;
            flex-shrink: 0;
        }
        
        .quiz-option.correct .quiz-selected-tag {
            background: #16a34a;
        }
        
        .quiz-option-letter {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #dc2626;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .quiz-option.correct .quiz-option-letter {
            background: #16a34a !important;
        }
        
        .quiz-option.wrong .quiz-option-letter {
            background: #dc2626 !important;
        }
        
        .quiz-option-text {
            flex: 1;
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }
        
        .quiz-option-count {
            font-size: 11px;
            font-weight: 700;
            color: #666;
            background: #f3f4f6;
            padding: 4px 10px;
            border-radius: 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .quiz-option.correct .quiz-option-count {
            background: #16a34a;
            color: #fff;
        }
        
        .quiz-option.wrong .quiz-option-count {
            background: #dc2626;
            color: #fff;
        }
        
        .quiz-progress-bar {
            position: absolute !important;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: rgba(220, 38, 38, 0.08);
            transition: width 0.5s ease;
            z-index: 0;
            pointer-events: none;
        }
        
        .quiz-option.correct .quiz-progress-bar {
            background: rgba(22, 163, 74, 0.12);
        }
        
        .quiz-option-letter,
        .quiz-option-text,
        .quiz-option-count {
            position: relative;
            z-index: 1;
        }
        
        .quiz-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        
        .quiz-participants {
            font-size: 12px;
            color: #666;
            text-align: center;
            background: #fff;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .quiz-participants i {
            margin-right: 6px;
            color: #dc2626;
        }
        
        .quiz-loading {
            text-align: center;
            padding: 40px 15px;
            color: #666;
        }
        
        .quiz-loading i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
            color: #dc2626;
        }
        
        @media (max-width: 992px) {
            .quiz-layout-grid {
                grid-template-columns: 1fr;
            }
            .quiz-box {
                border-left: none;
                border-top: 2px solid #dc2626;
            }
        }
        
        @media (max-width: 768px) {
            .quiz-category-section {
                padding: 0 10px;
                margin-bottom: 25px;
            }
            .quiz-news-left {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .quiz-news-card img {
                height: 200px;
            }
            .quiz-box {
                padding: 15px;
            }
            .quiz-option {
                padding: 10px 12px;
                min-height: 44px;
            }
        }
        
        @media (max-width: 480px) {
            .quiz-category-section {
                padding: 0 8px;
            }
            .quiz-layout-grid {
                border-radius: 8px;
            }
            .quiz-news-left {
                padding: 12px;
            }
            .quiz-news-card img {
                height: 180px;
            }
            .quiz-news-card h3 {
                font-size: 15px;
            }
            .quiz-news-card p {
                font-size: 13px;
            }
            .quiz-box {
                padding: 12px;
            }
            .quiz-question {
                font-size: 14px;
            }
            .quiz-option {
                padding: 10px;
                gap: 10px;
            }
            .quiz-option-text {
                font-size: 12px;
            }
            .quiz-option-letter {
                width: 22px;
                height: 22px;
                font-size: 11px;
            }
            .quiz-option-count {
                font-size: 10px;
                padding: 3px 8px;
            }
        }

        /* ===== CATEGORY SECTION STYLES - UNIFIED LAYOUT ===== */
        .categories-wrapper {
            max-width: 1250px;
            margin: 0 auto 40px;
            padding: 0 15px;
        }
        
        .dual-category-row {
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            gap: 0;
            margin-bottom: 40px;
        }
        
        .dual-category-row .category-divider {
            background: #e0e0e0;
            width: 1px;
        }
        
        .category-box {
            padding: 0 25px;
        }
        
        .category-box:first-child {
            padding-left: 0;
        }
        
        .category-box:last-child {
            padding-right: 0;
        }
        
        .category-box .cat-header {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 3px solid #c00;
        }
        
        .category-box .cat-header .cat-icon {
            width: 28px;
            height: 28px;
            background: #c00;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .category-box .cat-header .cat-name {
            flex: 1;
        }
        
        .category-box .cat-header .cat-arrow {
            width: 28px;
            height: 28px;
            border: 2px solid #1a1a1a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-box .cat-header .cat-arrow:hover {
            background: #c00;
            border-color: #c00;
            color: white;
        }
        
        /* Left Category - New Layout */
        .category-box.style-left .cat-content {
            display: flex;
            flex-direction: column;
            gap: 0;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
        }
        
        /* Top Row: Featured + Side Item */
        .category-box.style-left .top-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        /* Main Featured with Overlay */
        .category-box.style-left .main-featured {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
            border-right: 1px solid #e0e0e0;
            padding-right: 15px;
        }
        
        .category-box.style-left .main-featured img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        
        .category-box.style-left .main-featured .overlay-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
        }
        
        .category-box.style-left .main-featured h3 {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            line-height: 1.4;
            margin: 0 0 8px 0;
        }
        
        .category-box.style-left .main-featured h3 a {
            color: #fff;
            text-decoration: none;
        }
        
        .category-box.style-left .main-featured h3 a:hover {
            color: #ffd700;
        }
        
        .category-box.style-left .main-featured p {
            font-size: 14px;
            color: #ddd;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Side Item */
        .category-box.style-left .side-item {
            display: flex;
            flex-direction: column;
            padding-left: 15px;
        }
        
        .category-box.style-left .side-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .category-box.style-left .side-item h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
        }
        
        .category-box.style-left .side-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .category-box.style-left .side-item h4 a:hover {
            color: #c00;
        }
        
        /* Bottom Row: 3 Cards Grid */
        .category-box.style-left .bottom-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            padding-top: 15px;
        }
        
        .category-box.style-left .bottom-row .card-item {
            padding: 0 15px;
            border-right: 1px solid #e0e0e0;
        }
        
        .category-box.style-left .bottom-row .card-item:first-child {
            padding-left: 0;
        }
        
        .category-box.style-left .bottom-row .card-item:last-child {
            border-right: none;
            padding-right: 0;
        }
        
        .category-box.style-left .bottom-row .card-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .category-box.style-left .bottom-row .card-item h4 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .category-box.style-left .bottom-row .card-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .category-box.style-left .bottom-row .card-item h4 a:hover {
            color: #c00;
        }
        
        @media (max-width: 768px) {
            .category-box.style-left .top-row {
                grid-template-columns: 1fr;
            }
            .category-box.style-left .main-featured {
                border-right: none;
                padding-right: 0;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            .category-box.style-left .side-item {
                padding-left: 0;
            }
            .category-box.style-left .bottom-row {
                grid-template-columns: repeat(2, 1fr);
            }
            .category-box.style-left .bottom-row .card-item:nth-child(2) {
                border-right: none;
            }
            .category-box.style-left .bottom-row .card-item:nth-child(3) {
                border-top: 1px solid #e0e0e0;
                padding-top: 15px;
                margin-top: 15px;
                grid-column: 1 / -1;
                border-right: none;
            }
        }
        
        @media (max-width: 480px) {
            .category-box.style-left .bottom-row {
                grid-template-columns: 1fr;
            }
            .category-box.style-left .bottom-row .card-item {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                padding: 0 0 15px 0;
                margin-bottom: 15px;
            }
            .category-box.style-left .bottom-row .card-item:last-child {
                border-bottom: none;
                padding-bottom: 0;
                margin-bottom: 0;
            }
        }
        
        /* Right Category - Same Layout as Left */
        .category-box.style-right .cat-content {
            display: flex;
            flex-direction: column;
            gap: 0;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
        }
        
        /* Top Row: Featured + Side Item */
        .category-box.style-right .top-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        /* Main Featured with Overlay */
        .category-box.style-right .main-featured {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
            border-right: 1px solid #e0e0e0;
            padding-right: 15px;
        }
        
        .category-box.style-right .main-featured img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        
        .category-box.style-right .main-featured .overlay-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 15px;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
        }
        
        .category-box.style-right .main-featured h3 {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            line-height: 1.4;
            margin: 0 0 8px 0;
        }
        
        .category-box.style-right .main-featured h3 a {
            color: #fff;
            text-decoration: none;
        }
        
        .category-box.style-right .main-featured h3 a:hover {
            color: #ffd700;
        }
        
        .category-box.style-right .main-featured p {
            font-size: 14px;
            color: #ddd;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Side Item */
        .category-box.style-right .side-item {
            display: flex;
            flex-direction: column;
            padding-left: 15px;
        }
        
        .category-box.style-right .side-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .category-box.style-right .side-item h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
        }
        
        .category-box.style-right .side-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .category-box.style-right .side-item h4 a:hover {
            color: #c00;
        }
        
        /* Bottom Row: 3 Cards Grid */
        .category-box.style-right .bottom-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            padding-top: 15px;
        }
        
        .category-box.style-right .bottom-row .card-item {
            padding: 0 15px;
            border-right: 1px solid #e0e0e0;
        }
        
        .category-box.style-right .bottom-row .card-item:first-child {
            padding-left: 0;
        }
        
        .category-box.style-right .bottom-row .card-item:last-child {
            border-right: none;
            padding-right: 0;
        }
        
        .category-box.style-right .bottom-row .card-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .category-box.style-right .bottom-row .card-item h4 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .category-box.style-right .bottom-row .card-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .category-box.style-right .bottom-row .card-item h4 a:hover {
            color: #c00;
        }
        
        /* Responsive for dual layout */
        @media (max-width: 1024px) {
            .dual-category-row {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .dual-category-row .category-divider {
                display: none;
            }
            .category-box {
                padding: 0;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .category-box.style-right .top-row {
                grid-template-columns: 1fr;
            }
            .category-box.style-right .main-featured {
                border-right: none;
                padding-right: 0;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            .category-box.style-right .main-featured .overlay-content {
                right: 0;
            }
            .category-box.style-right .side-item {
                padding-left: 0;
            }
            .category-box.style-right .bottom-row {
                grid-template-columns: repeat(2, 1fr);
            }
            .category-box.style-right .bottom-row .card-item:nth-child(2) {
                border-right: none;
            }
            .category-box.style-right .bottom-row .card-item:nth-child(3) {
                border-top: 1px solid #e0e0e0;
                padding-top: 15px;
                margin-top: 15px;
                grid-column: 1 / -1;
                border-right: none;
            }
        }
        
        @media (max-width: 480px) {
            .category-box.style-right .bottom-row {
                grid-template-columns: 1fr;
            }
            .category-box.style-right .bottom-row .card-item {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                padding: 0 0 15px 0;
                margin-bottom: 15px;
            }
            .category-box.style-right .bottom-row .card-item:last-child {
                border-bottom: none;
                padding-bottom: 0;
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 576px) {
            .category-box.style-left .main-featured img {
                height: 180px;
            }
        }
        
        @media (max-width: 768px) {
            .category-box .cat-content {
                grid-template-columns: 1fr;
            }
            .category-box .main-featured {
                grid-column: 1;
            }
            .category-box .side-news {
                grid-column: 1;
            }
            .category-box .bottom-news {
                grid-template-columns: 1fr;
            }
        }
        
        /* ========== ENTERTAINMENT STYLE - 3 COLUMN FULL WIDTH ========== */
        .entertainment-section {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 20px 15px;
        }
        
        .entertainment-section .section-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .entertainment-section .section-title::before,
        .entertainment-section .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #1a1a1a;
            max-width: 200px;
        }
        
        .entertainment-layout {
            display: grid;
            grid-template-columns: 180px 1fr 280px;
            gap: 20px;
        }
        
        /* Left Column */
        .entertainment-layout .left-col {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        
        .entertainment-layout .left-col .thumb-item {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .entertainment-layout .left-col .thumb-item img {
            width: 100%;
            height: 60px;
            object-fit: cover;
        }
        
        .entertainment-layout .left-col .thumb-item h4 {
            font-size: 11px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
            margin: 5px 0 0 0;
        }
        
        .entertainment-layout .left-col .thumb-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .entertainment-layout .left-col .text-item {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        
        .entertainment-layout .left-col .text-item h4 {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .entertainment-layout .left-col .text-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        /* Center Column */
        .entertainment-layout .center-col .main-featured {
            margin-bottom: 15px;
        }
        
        .entertainment-layout .center-col .main-featured img {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }
        
        .entertainment-layout .center-col .main-featured h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 15px 0 10px 0;
        }
        
        .entertainment-layout .center-col .main-featured h3 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .entertainment-layout .center-col .main-featured p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        .entertainment-layout .center-col .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .entertainment-layout .center-col .bottom-grid .grid-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }
        
        .entertainment-layout .center-col .bottom-grid .grid-item h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 10px 0 0 0;
        }
        
        .entertainment-layout .center-col .bottom-grid .grid-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        /* Right Column */
        .entertainment-layout .right-col .ad-banner {
            width: 100%;
            height: 120px;
            background: #f0f0f0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .entertainment-layout .right-col .ad-banner img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .entertainment-layout .right-col .small-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .entertainment-layout .right-col .small-grid .small-item img {
            width: 100%;
            height: 60px;
            object-fit: cover;
        }
        
        .entertainment-layout .right-col .small-grid .small-item h5 {
            font-size: 11px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
            margin: 5px 0 0 0;
        }
        
        .entertainment-layout .right-col .small-grid .small-item h5 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .entertainment-layout .right-col .highlight-item {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        
        .entertainment-layout .right-col .highlight-item h4 {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .entertainment-layout .right-col .highlight-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .entertainment-layout .right-col .side-item {
            display: flex;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .entertainment-layout .right-col .side-item img {
            width: 70px;
            height: 50px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .entertainment-layout .right-col .side-item h4 {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .entertainment-layout .right-col .side-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        /* Entertainment Responsive */
        @media (max-width: 1024px) {
            .entertainment-layout {
                grid-template-columns: 1fr 1fr;
            }
            .entertainment-layout .left-col {
                grid-column: 1 / -1;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }
            .entertainment-layout .left-col .thumb-item,
            .entertainment-layout .left-col .text-item {
                flex: 1 1 calc(50% - 10px);
            }
        }
        
        @media (max-width: 768px) {
            .entertainment-layout {
                grid-template-columns: 1fr;
            }
            .entertainment-layout .center-col .bottom-grid {
                grid-template-columns: 1fr;
            }
            .entertainment-layout .right-col .small-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        /* ========== HERO-STYLE CATEGORY LAYOUT ========== */
        .hero-style-section {
            max-width: 1300px;
            margin: 0 auto 50px;
            padding: 20px 15px;
            overflow: hidden;
            clear: both;
        }
        
        .hero-style-section .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
            background: transparent;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            padding: 0 0 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .hero-style-section .section-title .left-part {
            display: flex;
            align-items: center;
            gap: 0;
        }
        
        .hero-style-section .section-title .left-part .section-logo {
            height: 24px;
            width: auto;
            margin-right: 8px;
            padding-right: 10px;
            border-right: 2px solid #dc2626;
        }
        
        .hero-style-section .section-title .left-part .icon {
            display: none;
        }
        
        .hero-style-section .section-title .right-arrow {
            display: none;
        }
        
        .hero-style-layout {
            display: grid !important;
            grid-template-columns: 1fr 1fr 320px !important;
            gap: 20px !important;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            margin-top: 15px;
            padding: 20px;
            background: #fff;
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }
        
        .hero-style-layout.grid-layout {
            display: grid !important;
            grid-template-columns: 1fr 1fr 320px !important;
        }
        
        .hero-style-section .hero-style-main-article {
            padding: 0 !important;
            border-right: none !important;
            min-width: 0;
            overflow: hidden;
            display: block !important;
            float: none !important;
            width: auto !important;
        }
        
        .hero-style-section .hero-style-main-article > a > img {
            width: 100% !important;
            height: 220px !important;
            object-fit: cover;
            transition: transform 0.3s ease;
            border-radius: 0;
            display: block;
        }
        
        .hero-style-main-article:hover img {
            transform: scale(1.02);
        }
        
        .hero-style-main-article .content {
            padding: 10px 0 0 0;
        }
        
        .hero-style-main-article h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0 0 8px 0;
        }
        
        .hero-style-main-article h3 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .hero-style-main-article h3 a:hover {
            color: #8B0000;
        }
        
        .hero-style-main-article p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hero-style-main-article .meta {
            display: none;
        }
        
        .hero-style-section .hero-style-sidebar {
            display: flex !important;
            flex-direction: column !important;
            gap: 0 !important;
            border-left: 1px solid #e5e7eb;
            padding-left: 15px;
            min-width: 0;
            overflow: hidden;
            float: none !important;
            width: auto !important;
        }
        
        .hero-style-section .hero-style-sidebar-item {
            display: flex !important;
            flex-direction: row !important;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            align-items: flex-start;
        }
        
        .hero-style-sidebar-item:last-child {
            border-bottom: none;
        }
        
        .hero-style-sidebar-item:hover {
            background: #f9f9f9;
        }
        
        .hero-style-sidebar-item > a {
            flex-shrink: 0;
            display: block;
        }
        
        .hero-style-sidebar-item img {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
        }
        
        .hero-style-sidebar-item .content {
            flex: 1;
            display: flex;
            align-items: center;
            min-width: 0;
        }
        
        .hero-style-sidebar-item h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hero-style-sidebar-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .hero-style-sidebar-item h4 a:hover {
            color: #8B0000;
        }
        
        .hero-style-grid {
            display: none;
        }
        
        .hero-style-grid-item {
            display: none;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-style-layout,
            .hero-style-layout.grid-layout {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            
            .hero-style-sidebar {
                grid-column: span 2;
                flex-direction: row;
                flex-wrap: wrap;
                border-left: none;
                border-top: 1px solid #e5e7eb;
                padding-left: 0;
                padding-top: 15px;
            }
            
            .hero-style-sidebar-item {
                width: calc(50% - 10px);
                border-bottom: none;
                border-right: 1px solid #e5e7eb;
            }
            
            .hero-style-sidebar-item:nth-child(2n) {
                border-right: none;
            }
        }
        
        @media (max-width: 768px) {
            .hero-style-section {
                padding: 10px !important;
                margin: 0 0 20px 0 !important;
            }
            
            .hero-style-layout,
            .hero-style-layout.grid-layout {
                display: flex !important;
                flex-direction: column;
                gap: 0;
                padding: 0 !important;
                border: none !important;
                width: 100% !important;
                margin-top: 10px;
            }
            
            .hero-style-section .hero-style-main-article:first-child {
                display: block;
                margin-bottom: 15px;
                width: 100% !important;
            }
            
            .hero-style-section .hero-style-main-article:first-child img {
                width: 100%;
                height: 220px;
                object-fit: cover;
            }
            
            .hero-style-section .hero-style-main-article:first-child .content h3 {
                font-size: 20px;
                font-weight: 700;
                margin-bottom: 10px;
            }
            
            .hero-style-section .hero-style-main-article:first-child .content p {
                font-size: 14px;
                line-height: 1.6;
            }
            
            .hero-style-section .hero-style-main-article:nth-child(2) {
                display: none;
            }
            
            .hero-style-sidebar {
                border-left: none;
                padding-left: 0;
                padding-right: 0;
                border-top: 1px solid #e5e7eb;
                padding-top: 15px;
                width: 100% !important;
                box-sizing: border-box;
            }
            
            .hero-style-sidebar-item {
                display: flex !important;
                flex-direction: row !important;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
                align-items: flex-start;
                width: 100% !important;
                box-sizing: border-box;
            }
            
            .hero-style-sidebar-item:last-child {
                border-bottom: none;
            }
            
            .hero-style-sidebar-item > a {
                flex-shrink: 0;
                display: block;
            }
            
            .hero-style-sidebar-item img {
                width: 100px !important;
                height: 70px !important;
                object-fit: cover;
                border-radius: 4px;
                display: block;
            }
            
            .hero-style-sidebar-item .content {
                flex: 1;
                min-width: 0;
            }
            
            .hero-style-sidebar-item h4 {
                font-size: 14px;
                line-height: 1.5;
                margin: 0;
                -webkit-line-clamp: 3;
            }
        }
        
        @media (max-width: 480px) {
            .hero-style-section {
                padding: 0px;
            }
            
            .hero-style-section .section-title {
                font-size: 16px;
                padding: 10px;
            }
            
            .hero-style-section .hero-style-main-article:first-child img {
                height: 180px;
            }
            
            .hero-style-section .hero-style-main-article:first-child .content h3 {
                font-size: 18px;
            }
            
            .hero-style-section .hero-style-main-article:first-child .content p {
                font-size: 13px;
                -webkit-line-clamp: 3;
            }
            
            .hero-style-sidebar-item img {
                width: 90px;
                height: 60px;
            }
            
            .hero-style-sidebar-item h4 {
                font-size: 13px;
            }
        }
        
        .hero-style-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 15px;
            border-right: 1px solid #e5e7eb;
        }
        
        .hero-style-top {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }
        
        .hero-style-featured {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 400px;
        }
        
        .hero-style-featured img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .hero-style-featured .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
            padding: 80px 20px 20px;
        }
        
        .hero-style-featured .overlay h3 {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            line-height: 1.4;
            margin: 0 0 10px 0;
        }
        
        .hero-style-featured .overlay h3 a {
            color: #fff;
            text-decoration: none;
        }
        
        .hero-style-featured .overlay .meta {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }
        
        .hero-style-side {
            display: flex;
            flex-direction: column;
        }
        
        .hero-style-side .side-item {
            display: flex;
            gap: 12px;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .hero-style-side .side-item:first-child {
            padding-top: 0;
        }
        
        .hero-style-side .side-item:last-child {
            border-bottom: none;
        }
        
        .hero-style-side .side-item img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        
        .hero-style-side .side-item h4 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .hero-style-side .side-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        /* Hero-style Center Column - Same as hero-center */
        .hero-style-center {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-left: 1px solid #e5e7eb;
            padding: 15px;
            background: #fff;
        }
        
        .hero-style-center .top-card {
            background: white;
            padding: 0;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .hero-style-center .top-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            margin-bottom: 0;
        }
        
        .hero-style-center .top-card .card-content {
            padding: 12px;
        }
        
        .hero-style-center .top-card h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
        }
        
        .hero-style-center .top-card h4 a {
            color: #1f2937;
            text-decoration: none;
        }
        
        .hero-style-center .headline-list {
            background: white;
            padding: 0;
            flex: 1;
            overflow: hidden;
        }
        
        .hero-style-center .headline-list .h-item {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .hero-style-center .headline-list .h-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .hero-style-center .headline-list .h-item .dot {
            color: #dc2626;
            font-size: 10px;
            margin-top: 5px;
        }
        
        .hero-style-center .headline-list .h-item h5 {
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hero-style-center .headline-list .h-item h5 a {
            color: black;
            text-decoration: none;
        }
        
        .hero-style-center .headline-list .h-item h5 a:hover {
            color: #dc2626;
        }
        
        .hero-style-center .headline-list .h-item .h-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .hero-style-bottom {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .hero-style-bottom .bottom-card {
            background: #fff;
        }
        
        .hero-style-bottom .bottom-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .hero-style-bottom .bottom-card h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 12px 0 8px 0;
        }
        
        .hero-style-bottom .bottom-card h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .hero-style-bottom .bottom-card p {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin: 0;
        }
        
        /* ========== LIFESTYLE LAYOUT - Last Category ========== */
        .lifestyle-section {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 20px 15px;
        }
        
        .lifestyle-section .section-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .lifestyle-section .section-title::before,
        .lifestyle-section .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #1a1a1a;
            max-width: 200px;
        }
        
        .lifestyle-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 25px;
        }
        
        .lifestyle-layout .featured-image {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .lifestyle-layout .featured-image img {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }
        
        .lifestyle-layout .content-side {
            display: flex;
            flex-direction: column;
        }
        
        .lifestyle-layout .main-headline {
            margin-bottom: 15px;
        }
        
        .lifestyle-layout .main-headline h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0 0 12px 0;
        }
        
        .lifestyle-layout .main-headline h3 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .lifestyle-layout .main-headline h3 a:hover {
            color: #dc2626;
        }
        
        .lifestyle-layout .main-headline p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin: 0;
        }
        
        .lifestyle-layout .news-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .lifestyle-layout .news-grid .grid-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
        }
        
        .lifestyle-layout .news-grid .grid-item img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0;
        }
        
        .lifestyle-layout .news-grid .grid-item h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
        }
        
        .lifestyle-layout .news-grid .grid-item h4 a {
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .lifestyle-layout .news-grid .grid-item h4 a:hover {
            color: #dc2626;
        }
        
        /* Lifestyle Responsive */
        @media (max-width: 1024px) {
            .lifestyle-layout {
                grid-template-columns: 1fr;
            }
            .lifestyle-layout .featured-image img {
                height: 280px;
            }
        }
        
        @media (max-width: 768px) {
            .lifestyle-layout .news-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Hero-style Responsive */
        @media (max-width: 1024px) {
            .hero-style-layout {
                grid-template-columns: 1fr;
            }
            .hero-style-main {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }
            .hero-style-center {
                border-left: none;
                border-top: 1px solid #e5e7eb;
            }
        }
        
        @media (max-width: 768px) {
            .hero-style-main {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding: 10px;
            }
            .hero-style-top {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            .hero-style-featured {
                height: 200px;
            }
            .hero-style-featured .overlay {
                padding: 40px 10px 10px;
            }
            .hero-style-featured .overlay h3 {
                font-size: 14px;
            }
            .hero-style-side .side-item {
                padding: 8px 0;
            }
            .hero-style-side .side-item img {
                width: 70px;
                height: 55px;
            }
            .hero-style-side .side-item h4 {
                font-size: 12px;
            }
            .hero-style-bottom {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            .hero-style-bottom .bottom-card img {
                height: 80px;
            }
            .hero-style-bottom .bottom-card h4 {
                font-size: 12px;
                margin: 8px 0 0 0;
            }
            .hero-style-bottom .bottom-card p {
                display: none;
            }
            .hero-style-center {
                padding: 10px;
            }
            .hero-style-center .top-card img {
                height: 120px;
            }
            .hero-style-center .top-card h4 {
                font-size: 13px;
            }
            .hero-style-center .headline-list .h-item {
                padding: 8px 0;
            }
            .hero-style-center .headline-list .h-item h5 {
                font-size: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-style-section {
                padding: 0px;
            }
            .hero-style-section .section-title {
                font-size: 18px;
                margin-bottom: 15px;
            }
            .hero-style-layout {
                border-radius: 6px;
            }
            .hero-style-main {
                padding: 8px;
                gap: 10px;
            }
            .hero-style-top {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }
            .hero-style-featured {
                height: 180px !important;
            }
            .hero-style-featured .overlay {
                padding: 30px 10px 10px !important;
            }
            .hero-style-featured .overlay h3 {
                font-size: 14px !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .hero-style-featured .overlay .meta {
                font-size: 11px !important;
            }
            .hero-style-side {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }
            .hero-style-side .side-item {
                display: flex !important;
                flex-direction: column !important;
                gap: 8px !important;
                padding: 0 !important;
                border-bottom: none !important;
            }
            .hero-style-side .side-item img {
                width: 100% !important;
                height: 80px !important;
                border-radius: 4px !important;
                flex-shrink: 0 !important;
            }
            .hero-style-side .side-item h4 {
                font-size: 12px !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .hero-style-bottom {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 8px !important;
            }
            .hero-style-bottom .bottom-card img {
                height: 60px !important;
            }
            .hero-style-bottom .bottom-card h4 {
                font-size: 11px !important;
                margin: 6px 0 0 0 !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .hero-style-bottom .bottom-card p {
                display: none !important;
            }
            .hero-style-center {
                padding: 8px;
            }
            .hero-style-center .top-card {
                padding-bottom: 10px;
                margin-bottom: 10px;
            }
            .hero-style-center .top-card img {
                height: 100px;
            }
            .hero-style-center .top-card h4 {
                font-size: 12px;
            }
            .hero-style-center .headline-list .h-item {
                padding: 6px 0;
                gap: 8px;
            }
            .hero-style-center .headline-list .h-item .dot {
                font-size: 8px;
                margin-top: 3px;
            }
            .hero-style-center .headline-list .h-item h5 {
                font-size: 11px;
            }
            .hero-style-center .headline-list .h-item .h-meta {
                font-size: 10px;
            }
        }
        /* ===== END CATEGORY SECTION STYLES ===== */

        .p-all-70,
        .p-t-70,
        .p-tb-70 {
            padding-top: 0px !important;
        }

        /* Compact Opinion Section */
        .opinion-section {
            padding: 30px 20px;
            max-width: 1400px;
            margin: 0 auto;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .opinion-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #FF0000 50%, transparent 100%);
            opacity: 0.3;
        }

        /* Compact Header Design */
        .comment-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            position: relative;
            padding: 0;
        }

        .header-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #FF0000 0%, #E50000 50%, #CC0000 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 4px 15px rgba(255, 0, 0, 0.2),
                0 1px 4px rgba(255, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        .header-icon img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .header-text {
            position: relative;
            flex: 1;
        }

        .header-text h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .header-underline {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #FF0000 0%, #FF4444 50%, #FF6B6B 100%);
            border-radius: 2px;
            margin-top: 5px;
        }

        /* Full Width Slider Wrapper */
        .opinion-slider-wrapper {
            position: relative;
            width: 100%;
        }

        .opinion-slider-container {
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .opinion-slider {
            display: flex;
            gap: 15px;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Opinion Cards - 5 per view on desktop */
        .opinion-card {
            flex: 0 0 calc((100% - 60px) / 5);
            position: relative;
        }

        .opinion-card img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.1),
                0 2px 6px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .opinion-card:hover img {
            transform: translateY(-4px);
            box-shadow: 
                0 8px 24px rgba(0, 0, 0, 0.15),
                0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Navigation Arrows - Overlaid on cards */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            z-index: 10;
            padding: 0;
        }

        .slider-arrow-left {
            left: 15px;
        }

        .slider-arrow-right {
            right: 15px;
        }

        .slider-arrow svg {
            width: 24px;
            height: 24px;
            stroke: white;
            stroke-width: 2.5px;
            transition: all 0.3s ease;
        }

        .slider-arrow:hover {
            background: rgba(255, 0, 0, 0.9);
            transform: translateY(-50%) scale(1.15);
            box-shadow: 0 6px 24px rgba(255, 0, 0, 0.4);
        }

        .slider-arrow:hover svg {
            stroke: white;
        }

        .slider-arrow:active {
            transform: translateY(-50%) scale(0.95);
        }

        .slider-arrow:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        .no-opinions {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 18px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            /* 4 cards on medium screens */
            .opinion-card {
                flex: 0 0 calc((100% - 45px) / 4);
            }
        }

        @media (max-width: 1024px) {
            /* 3 cards on tablets */
            .opinion-card {
                flex: 0 0 calc((100% - 24px) / 3);
            }

            .opinion-slider {
                gap: 12px;
            }
        }

        @media (max-width: 768px) {
            .opinion-section {
                padding: 25px 15px;
            }

            .comment-header {
                margin-bottom: 20px;
                gap: 10px;
            }

            .header-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
            }

            .header-icon img {
                width: 22px;
                height: 22px;
            }

            .header-text h2 {
                font-size: 20px;
            }

            .header-underline {
                width: 50px;
                height: 2.5px;
                margin-top: 4px;
            }

            /* 2 cards on small tablets */
            .opinion-card {
                flex: 0 0 calc((100% - 10px) / 2);
            }

            .opinion-slider {
                gap: 10px;
            }

            .slider-arrow {
                width: 45px;
                height: 45px;
            }

            .slider-arrow-left {
                left: 10px;
            }

            .slider-arrow-right {
                right: 10px;
            }

            .slider-arrow svg {
                width: 22px;
                height: 22px;
            }
        }

        @media (max-width: 640px) {
            .opinion-section {
                padding: 20px 10px;
            }

            .comment-header {
                margin-bottom: 18px;
                gap: 8px;
            }

            .header-icon {
                width: 38px;
                height: 38px;
                border-radius: 9px;
            }

            .header-icon img {
                width: 20px;
                height: 20px;
            }

            .header-text h2 {
                font-size: 18px;
            }

            .header-underline {
                width: 45px;
                height: 2px;
            }

            /* 1 card on mobile - smaller size */
            .opinion-card {
                flex: 0 0 70%;
                max-width: 320px;
            }

            .opinion-slider {
                gap: 12px;
            }

            .opinion-card img {
                border-radius: 10px;
            }

            .slider-arrow {
                width: 42px;
                height: 42px;
            }

            .slider-arrow-left {
                left: 8px;
            }

            .slider-arrow-right {
                right: 8px;
            }

            .slider-arrow svg {
                width: 20px;
                height: 20px;
            }
        }

        @media (max-width: 480px) {
            .opinion-section {
                padding: 18px 8px;
            }

            /* 1 smaller card on small mobile */
            .opinion-card {
                flex: 0 0 75%;
                max-width: 280px;
            }

            .slider-arrow {
                width: 40px;
                height: 40px;
            }

            .slider-arrow svg {
                width: 19px;
                height: 19px;
            }
        }

        @media (max-width: 360px) {
            .header-icon {
                width: 35px;
                height: 35px;
            }

            .header-icon img {
                width: 18px;
                height: 18px;
            }

            .header-text h2 {
                font-size: 16px;
            }

            /* 1 smaller card on very small screens */
            .opinion-card {
                flex: 0 0 80%;
                max-width: 260px;
            }

            .slider-arrow {
                width: 38px;
                height: 38px;
            }

            .slider-arrow svg {
                width: 18px;
                height: 18px;
            }

            .slider-arrow-left {
                left: 6px;
            }

            .slider-arrow-right {
                right: 6px;
            }
        }

        .main-wrapper-content {
            max-width: 1400px;
            margin: 50px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .info-panel-box {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .panel-top-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #dc2626;
        }

        .panel-icon-holder {
            width: 40px;
            height: 40px;
            background: #dc2626;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .panel-heading-text {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
        }

        /* Donation Section */
        .charity-items-layout {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .charity-single-item {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .charity-single-item:hover {
            transform: translateY(-3px);
        }

        .charity-single-item:hover .charity-photo {
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }

        .charity-photo {
            width: 100%;
            height: 180px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .charity-info-text {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* News Area Section */
        .location-filter-wrapper {
            margin-bottom: 15px;
        }

        .filter-field-label {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 6px;
            font-weight: 500;
            display: block;
        }

        .location-selector {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-selector:focus {
            outline: none;
            border-color: #dc2626;
        }

        .news-search-action {
            width: 100%;
            padding: 12px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .news-search-action:hover {
            background: #b91c1c;
        }

        /* Quiz Section */
        .trivia-question-prompt {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            line-height: 1.6;
            text-align: center;
        }

        .trivia-answers-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .trivia-choice-btn {
            padding: 14px 20px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .trivia-choice-btn:hover {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .trivia-choice-btn.answer-picked {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        @media (max-width: 1024px) {
            .main-wrapper-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .charity-items-layout {
                grid-template-columns: 1fr;
            }
        }

        .p-all-35,
        .p-b-35,
        .p-tb-35 {
            padding-bottom: 0px !important;
        }

        .p-all-60,
        .p-t-60,
        .p-tb-60 {
            padding-top: 0px !important;
        }
    </style>


</head>

<body class="animsition">
    <header class="primary-header-wrapper">
        <div class="header-top-section">
            <div class="header-content-flex">
                <a href="index.php<?= $user_id_param; ?>" class="brand-logo-container">
                    <img src="<?= htmlspecialchars($basic_info['image'] ?? 'logo.jpg'); ?>" alt="<?= htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" class="logo-image-element">
                </a>
                
                <div class="header-action-group">
                    <div class="search-wrapper-container">
                        <div class="search-input-box">
                            <svg class="search-icon-svg" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <input type="text" placeholder="Search news..." class="search-field-input">
                        </div>
                    </div>
                    
                    <div class="date-info-display">
                        <svg class="date-icon-svg" viewBox="0 0 24 24">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        <span id="currentDateElement">Today</span>
                    </div>
                    
                    
                    
                    <div class="mobile-actions-group">
                        <button class="header-button-element" onclick="toggleMobileSearch()">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </button>
                        <button class="header-button-element">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7.5V9C15 12.18 12.18 15 9 15S3 12.18 3 9V7L9 6.5V9C9 10.66 10.34 12 12 12S15 10.66 15 9Z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <button class="mobile-menu-button" onclick="openSidebarPanel()">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <nav class="main-navigation-section">
            <div class="nav-content-container">
                <ul class="primary-nav-menu">
    <!-- Home is static -->
    <li class="nav-menu-item">
        <a href="index.php<?= $user_id_param; ?>" class="nav-link-element">প্রথম পাতা</a>
    </li>

    <!-- Load categories dynamically -->
    <?php
    include 'connection.php';

    $sql = "SELECT * FROM $tbl_category ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $category_id = $row["id"];
            $category_name = htmlspecialchars($row["name"]); // নিরাপদ করার জন্য

            echo "<li class='nav-menu-item'>
                    <a href='category.php?id=$category_id$user_id_suffix' class='nav-link-element'>
                        $category_name
                    </a>
                  </li>";
        }
    } else {
        echo "<li class='nav-menu-item'>No categories found</li>";
    }
    ?>
</ul>

               
            </div>
        </nav>
    </header>

    <!-- Mobile Sidebar -->
    <div class="sidebar-overlay-bg" id="sidebarOverlayBg" onclick="closeSidebarPanel()"></div>
    <div class="mobile-sidebar-panel" id="mobileSidebarPanel">
        <div class="sidebar-header-container">
            <div class="sidebar-title-text">Menu</div>
            <button class="sidebar-close-btn" onclick="closeSidebarPanel()">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
            </button>
        </div>
        <div class="sidebar-menu-container">
            <a href="#" class="sidebar-menu-link">প্রথম পাতা</a>
            <a href="#" class="sidebar-menu-link">রাজনীতি</a>
            <a href="#" class="sidebar-menu-link">বাংলাদেশ</a>
            <a href="#" class="sidebar-menu-link">আন্তর্জাতিক</a>
            <a href="#" class="sidebar-menu-link">ব্যবসা-বাণিজ্য</a>
            <a href="#" class="sidebar-menu-link">খেলা</a>
            <a href="#" class="sidebar-menu-link">বিনোদন</a>
            <a href="#" class="sidebar-menu-link">প্রযুক্তি</a>
            <a href="#" class="sidebar-menu-link">মতামত</a>
            <a href="#" class="sidebar-menu-link">জীবনধারা</a>
            <a href="#" class="sidebar-menu-link">স্বাস্থ্য</a>
            <a href="#" class="sidebar-menu-link">বিজ্ঞান</a>
            <a href="#" class="sidebar-menu-link">শিক্ষা</a>
            <a href="#" class="sidebar-menu-link">সংস্কৃতি</a>
            <a href="#" class="sidebar-menu-link">ভ্রমণ</a>
            <a href="#" class="sidebar-menu-link">খাদ্য</a>
            <a href="#" class="sidebar-menu-link">আবহাওয়া</a>
            <a href="#" class="sidebar-menu-link">অর্থনীতি</a>
            <a href="#" class="sidebar-menu-link">অর্থ-বাণিজ্য</a>
            <a href="#" class="sidebar-menu-link">বাজার</a>
            <a href="#" class="sidebar-menu-link">বিশ্ব সংবাদ</a>
            <a href="#" class="sidebar-menu-link">স্থানীয় সংবাদ</a>
            <a href="#" class="sidebar-menu-link">ব্রেকিং নিউজ</a>
            <a href="#" class="sidebar-menu-link">ছবির গ্যালারি</a>
            <a href="#" class="sidebar-menu-link">ভিডিও</a>
            <a href="#" class="sidebar-menu-link">পডকাস্ট</a>
            <a href="#" class="sidebar-menu-link">নিউজলেটার</a>
            <a href="#" class="sidebar-menu-link">যোগাযোগ</a>
            <a href="#" class="sidebar-menu-link">পরিচিতি</a>
            <a href="#" class="sidebar-menu-link">গোপনীয়তা নীতি</a>

        </div>
    </div>
    <br>

    <div class="news-ticker">
        <div class="ticker-label">শিরোনাম</div>
        <div class="ticker-container">
            <div class="ticker-move" id="tickerMove">
                <?php
            include 'connection.php';
            date_default_timezone_set('Asia/Dhaka');

            $sql = "SELECT news.id, news.headline, news.slug AS news_slug, category.slug AS category_slug
                    FROM $tbl_news news
                    JOIN $tbl_category category ON news.category_id = category.id
                    WHERE news.is_active = 1
                    ORDER BY news.created_at DESC
                    LIMIT 10";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $url = "news.php?id=" . $row['id'] . $user_id_suffix;
                    echo '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($row['headline']) . '</a>';
                }
            }

            // Don't close connection - needed for rest of page
            ?>
            </div>
        </div>
    </div> <br>



    <!-- ===== NEW HERO SECTION ===== -->
    <?php
    include 'connection.php';
    date_default_timezone_set('Asia/Dhaka');
    
    $uploadPath = 'Admin/img/';
    
    // Fetch latest 15 news for hero section (1 large + 4 side + 3 bottom + 1 top card + 6 headlines)
    $sql_hero = "SELECT 
        news.*, 
        news.news_1 AS news_content,
        category.name AS category_name, 
        category.slug AS category_slug,
        reporter.name AS reporter_name
    FROM $tbl_news news
    LEFT JOIN $tbl_category category ON news.category_id = category.id
    LEFT JOIN $tbl_reporter reporter ON news.reporter_id = reporter.id
    WHERE news.is_active = 1
    ORDER BY news.created_at DESC
    LIMIT 15";
    
    $result_hero = $conn->query($sql_hero);
    
    $heroNews = [];
    if ($result_hero && $result_hero->num_rows > 0) {
        while ($row = $result_hero->fetch_assoc()) {
            $heroNews[] = $row;
        }
    }
    ?>
    
    <section class="hero-section">
        <div class="hero-grid">
            <!-- Left Column - Main Featured + Bottom News -->
            <div class="hero-main">
                <!-- Top Row: Large Featured Left + Side News Right -->
                <div class="hero-top-row">
                    <!-- Large Featured News -->
                    <?php if (!empty($heroNews[0])): ?>
                    <div class="featured-large">
                        <div class="featured-large-image">
                            <a href="news.php?id=<?= $heroNews[0]['id']; ?><?= $user_id_suffix; ?>">
                                <img src="<?= $uploadPath . htmlspecialchars($heroNews[0]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[0]['headline']); ?>">
                            </a>
                        </div>
                        <div class="featured-large-content">
                            <h2 class="featured-large-headline">
                                <a href="news.php?id=<?= $heroNews[0]['id']; ?><?= $user_id_suffix; ?>">
                                    <?= htmlspecialchars($heroNews[0]['headline']); ?>
                                </a>
                            </h2>
                            <div class="featured-large-meta">
                                <i class="far fa-clock"></i> আপডেট <?= banglaDate($heroNews[0]['created_at']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Side News List - 4 Items -->
                    <div class="side-news-list">
                        <?php for ($i = 1; $i <= 4 && isset($heroNews[$i]); $i++): ?>
                        <div class="side-news-item">
                            <div class="side-news-thumb">
                                <a href="news.php?id=<?= $heroNews[$i]['id']; ?><?= $user_id_suffix; ?>">
                                    <img src="<?= $uploadPath . htmlspecialchars($heroNews[$i]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[$i]['headline']); ?>">
                                </a>
                            </div>
                            <div class="side-news-content">
                                <h4 class="side-news-title">
                                    <a href="news.php?id=<?= $heroNews[$i]['id']; ?><?= $user_id_suffix; ?>">
                                        <?= htmlspecialchars($heroNews[$i]['headline']); ?>
                                    </a>
                                </h4>
                                <div class="side-news-meta">
                                    <i class="far fa-clock"></i> আপডেট <?= banglaDate($heroNews[$i]['created_at']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Bottom News Row - 6 Cards with Image, Headline, Excerpt -->
                <div class="hero-bottom-news">
                    <?php for ($i = 5; $i <= 10 && isset($heroNews[$i]); $i++): ?>
                    <div class="bottom-news-card">
                        <div class="card-image">
                            <a href="news.php?id=<?= $heroNews[$i]['id']; ?><?= $user_id_suffix; ?>">
                                <img src="<?= $uploadPath . htmlspecialchars($heroNews[$i]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[$i]['headline']); ?>">
                            </a>
                        </div>
                        <h4 class="bottom-news-headline">
                            <a href="news.php?id=<?= $heroNews[$i]['id']; ?><?= $user_id_suffix; ?>">
                                <?= htmlspecialchars($heroNews[$i]['headline']); ?>
                            </a>
                        </h4>
                        <p class="bottom-news-excerpt">
                            <?php 
                            $excerpt = $heroNews[$i]['news_content'] ?? '';
                            $excerpt = strip_tags($excerpt);
                            $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
                            $excerpt = preg_replace('/\s+/', ' ', $excerpt);
                            $excerpt = trim($excerpt);
                            echo htmlspecialchars(mb_substr($excerpt, 0, 120));
                            ?>...
                        </p>
                        <div class="bottom-news-meta">
                            <i class="far fa-clock"></i> <?= banglaDate($heroNews[$i]['created_at']); ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Center Column - Banner + Tabs + News List -->
            <div class="hero-center">
                <!-- Banner Image -->
                <div class="hero-center-banner">
                    <a href="#">
                        <img src="img/template.jpeg" alt="ত্রয়োদশ জাতীয় সংসদ নির্বাচন, ২০২৬">
                    </a>
                </div>
                <br>
                <!-- Tabs -->
                <div class="hero-center-tabs">
                    <button class="hero-center-tab active" data-tab="latest">সর্বশেষ</button>
                    <button class="hero-center-tab" data-tab="popular">সর্বাধিক পঠিত</button>
                </div>
                
                <!-- Latest News List -->
                <div class="hero-center-list" id="latest-news">
                    <?php 
                    $numBn = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '১০'];
                    for ($i = 0; $i < 10 && isset($heroNews[$i]); $i++): 
                    ?>
                    <div class="hero-center-item">
                        <span class="hero-center-number"><?= $numBn[$i] ?></span>
                        <div class="hero-center-text">
                            <a href="news.php?id=<?= $heroNews[$i]['id']; ?><?= $user_id_suffix; ?>">
                                <?= htmlspecialchars($heroNews[$i]['headline']); ?>
                            </a>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <!-- Popular News List (Most Viewed) -->
                <div class="hero-center-list" id="popular-news" style="display: none;">
                    <?php 
                    $sql_popular = "SELECT n.headline, n.slug, n.views, c.slug AS category_slug 
                                    FROM $tbl_news n 
                                    JOIN $tbl_category c ON n.category_id = c.id 
                                    WHERE n.is_active = 1 
                                    ORDER BY n.views DESC 
                                    LIMIT 10";
                    $result_popular = $conn->query($sql_popular);
                    $popIndex = 0;
                    if ($result_popular && $result_popular->num_rows > 0):
                        while ($popNews = $result_popular->fetch_assoc()):
                    ?>
                    <div class="hero-center-item">
                        <span class="hero-center-number"><?= $numBn[$popIndex] ?></span>
                        <div class="hero-center-text">
                            <a href="news.php?id=<?= $popNews['id']; ?><?= $user_id_suffix; ?>">
                                <?= htmlspecialchars($popNews['headline']); ?>
                            </a>
                        </div>
                    </div>
                    <?php 
                        $popIndex++;
                        endwhile;
                    endif;
                    ?>
                </div>
                
                <!-- All News Button -->
                <a href="all-news.php<?= $user_id_param; ?>" class="hero-center-btn">সব খবর »</a>
                
                <!-- Category Tags -->
                <div class="hero-center-tags">
                    <div class="hero-center-tags-title">আরও দেখুন</div>
                    <div class="hero-center-tags-list">
                        <?php
                        $sql_cat = "SELECT name, slug FROM $tbl_category WHERE is_active = 1 ORDER BY id ASC LIMIT 10";
                        $result_cat = $conn->query($sql_cat);
                        if ($result_cat && $result_cat->num_rows > 0):
                            while ($cat = $result_cat->fetch_assoc()):
                        ?>
                        <a href="category.php?slug=<?= urlencode($cat['slug']); ?><?= $user_id_suffix; ?>" class="hero-center-tag">
                            <i class="far fa-play-circle"></i> <?= htmlspecialchars($cat['name']); ?>
                        </a>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== END HERO SECTION ===== -->


    <?php
function truncateWords($text, $limit = 6) {
    $words = explode(' ', $text);
    if(count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    } else {
        return $text;
    }
}

// Fetch news from the category with the most news
$sql = "SELECT n.headline, n.slug AS news_slug, n.image_url, n.created_at, 
               c.slug AS category_slug, c.name AS category_name
        FROM $tbl_news n
        JOIN $tbl_category c ON n.category_id = c.id
        WHERE n.category_id = (
            SELECT category_id 
            FROM $tbl_news 
            WHERE is_active = 1
            GROUP BY category_id 
            ORDER BY COUNT(*) DESC 
            LIMIT 1
        ) AND n.is_active = 1
        ORDER BY n.created_at DESC
        LIMIT 10";

$result = $conn->query($sql);
if($result->num_rows == 0) return;

$news = [];
while($row = $result->fetch_assoc()) {
    $news[] = $row;
}

$category_name = htmlspecialchars($news[0]['category_name']);
?>

    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="video-modal-content">
            <button class="video-modal-close" onclick="closeVideoModal()"><i class="fas fa-times"></i></button>
            <iframe id="videoIframe" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
        </div>
    </div>

    <!-- Modern Video Section -->
    <section class="video-section">
        <div class="video-section-inner">
            <div class="video-section-header">
                <div class="video-title">
                    <span class="video-title-icon"><i class="fas fa-video"></i></span>
                    <h2>ভিডিও</h2>
                </div>
                
            </div>
            
            <div class="video-grid">
                <?php
                // Fetch latest 8 videos from news_video table
                $sql_video = "SELECT id, title, subtitle, thumbnail, youtube_link
                              FROM $tbl_videos
                              WHERE is_active = 1
                              ORDER BY created_at DESC
                              LIMIT 8";
                $result_video = $conn->query($sql_video);
                
                if ($result_video && $result_video->num_rows > 0):
                    while ($video = $result_video->fetch_assoc()):
                        // Extract YouTube video ID from link
                        $ytLink = $video['youtube_link'];
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $ytLink, $matches);
                        $ytId = $matches[1] ?? '';
                        
                        $thumbnail = !empty($video['thumbnail']) ? $video['thumbnail'] : 'default.jpg';
                        $title = htmlspecialchars($video['title']);
                        $subtitle = htmlspecialchars($video['subtitle'] ?? '');
                ?>
                <div class="video-item" onclick="playVideo('<?= $ytId; ?>')">
                    <div class="video-thumbnail">
                        <img src="Admin/img/<?= $thumbnail; ?>" alt="<?= $title; ?>">
                        <div class="video-play-btn"><i class="fas fa-play"></i></div>
                    </div>
                    <div class="video-info">
                        <h4 class="video-headline"><?= mb_substr($title, 0, 60); ?><?= mb_strlen($title) > 60 ? '...' : ''; ?></h4>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p style="text-align: center; width: 100%; padding: 40px 0; color: #666;">কোন ভিডিও পাওয়া যায়নি</p>
                <?php
                endif;
                ?>
            </div>
            
         <!--   <div class="video-see-all">
                <a href="#">সকল ভিডিও দেখুন...</a>
            </div> -->
        </div>
    </section>
    
    <!-- Two Category News Sections (First 2 Categories by Serial Order) -->
    <?php
    // Fetch first 2 categories with their news for display between video and opinion sections (same order as dynamic sections)
    $midCategorySql = "SELECT c.id, c.name, c.slug 
                       FROM $tbl_category c
                       LEFT JOIN $tbl_settings s ON c.id = s.category_id
                       WHERE c.is_active = 1 
                       AND (s.is_active = 1 OR s.is_active IS NULL)
                       ORDER BY COALESCE(s.serial_order, 999) ASC, c.id ASC
                       LIMIT 2";
    $midCategoryResult = $conn->query($midCategorySql);
    
    if ($midCategoryResult && $midCategoryResult->num_rows > 0):
        while ($midCat = $midCategoryResult->fetch_assoc()):
            // Fetch 6 news for this category
            $midNewsSql = "SELECT n.id, n.headline, n.slug, n.image_url, n.news_1, n.created_at, c.slug as category_slug 
                          FROM $tbl_news n 
                          LEFT JOIN $tbl_category c ON n.category_id = c.id 
                          WHERE n.category_id = {$midCat['id']} AND n.is_active = 1 
                          ORDER BY n.created_at DESC LIMIT 6";
            $midNewsResult = $conn->query($midNewsSql);
            
            if ($midNewsResult && $midNewsResult->num_rows > 0):
                $midNews = [];
                while ($row = $midNewsResult->fetch_assoc()) {
                    $midNews[] = $row;
                }
    ?>
    <section class="hero-style-section">
        <h2 class="section-title">
            <div class="left-part">
              
                <?= htmlspecialchars($midCat['name']); ?>
            </div>
        </h2>
        <div class="hero-style-layout grid-layout">
            <!-- Left Column - First Featured Article -->
            <div class="hero-style-main-article">
                <a href="news.php?id=<?= $midNews[0]['id']; ?><?= $user_id_suffix; ?>">
                    <img src="Admin/img/<?= htmlspecialchars($midNews[0]['image_url']); ?>" alt="<?= htmlspecialchars($midNews[0]['headline']); ?>">
                </a>
                <div class="content">
                    <h3><a href="news.php?id=<?= $midNews[0]['id']; ?><?= $user_id_suffix; ?>"><?= htmlspecialchars($midNews[0]['headline']); ?></a></h3>
                    <p><?= htmlspecialchars(mb_substr(strip_tags($midNews[0]['news_1']), 0, 200)); ?>...</p>
                </div>
            </div>
            
            <!-- Middle Column - Second Featured Article -->
            <?php if (count($midNews) > 1): ?>
            <div class="hero-style-main-article">
                <a href="news.php?id=<?= $midNews[1]['id']; ?><?= $user_id_suffix; ?>">
                    <img src="Admin/img/<?= htmlspecialchars($midNews[1]['image_url']); ?>" alt="<?= htmlspecialchars($midNews[1]['headline']); ?>">
                </a>
                <div class="content">
                    <h3><a href="news.php?id=<?= $midNews[1]['id']; ?><?= $user_id_suffix; ?>"><?= htmlspecialchars($midNews[1]['headline']); ?></a></h3>
                    <p><?= htmlspecialchars(mb_substr(strip_tags($midNews[1]['news_1']), 0, 200)); ?>...</p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Right Sidebar - Horizontal List Items -->
            <div class="hero-style-sidebar">
                <?php for ($j = 2; $j < min(6, count($midNews)); $j++): ?>
                <div class="hero-style-sidebar-item">
                    <a href="news.php?id=<?= $midNews[$j]['id']; ?><?= $user_id_suffix; ?>">
                        <img src="Admin/img/<?= htmlspecialchars($midNews[$j]['image_url']); ?>" alt="<?= htmlspecialchars($midNews[$j]['headline']); ?>">
                    </a>
                    <div class="content">
                        <h4><a href="news.php?id=<?= $midNews[$j]['id']; ?><?= $user_id_suffix; ?>"><?= htmlspecialchars($midNews[$j]['headline']); ?></a></h4>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
    <?php 
            endif;
        endwhile;
    endif;
    ?>
    
    <!-- Opinion Slider Section -->
    <div class="opinion-section">
        <div class="comment-header">
            <div class="header-icon">
                <img src="comment.png" alt="মতামত">
            </div>
            <div class="header-text">
                <h2>মতামত</h2>
                <div class="header-underline"></div>
            </div>
        </div>

        <div class="opinion-slider-wrapper">
            <button class="slider-arrow slider-arrow-left" onclick="slideOpinions('prev')" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <div class="opinion-slider-container">
                <div class="opinion-slider" id="opinionSlider">
                    <?php if (!empty($opinions)): ?>
                        <?php foreach ($opinions as $opinion): ?>
                            <?php if (!empty($opinion['link'])): ?>
                                <a href="<?= htmlspecialchars($opinion['link']); ?>" target="_blank" class="opinion-card" style="cursor: pointer; text-decoration: none;">
                                    <img src="<?= htmlspecialchars($uploadPath . $opinion['image']); ?>" 
                                         alt="Opinion Card" 
                                         loading="lazy">
                                </a>
                            <?php else: ?>
                                <div class="opinion-card">
                                    <img src="<?= htmlspecialchars($uploadPath . $opinion['image']); ?>" 
                                         alt="Opinion Card" 
                                         loading="lazy">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-opinions">
                            <p>কোন মতামত পাওয়া যায়নি</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button class="slider-arrow slider-arrow-right" onclick="slideOpinions('next')" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>
    <!-- End Opinion Slider Section -->
    
    <script>
    function playVideo(videoId) {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoIframe');
        iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoIframe');
        iframe.src = '';
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    // Close modal on clicking outside
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVideoModal();
        }
    });
    </script>
    
    <!-- ===== DYNAMIC CATEGORY SECTIONS ===== -->
    <?php
    // Fetch all categories ordered by settings serial_order
    $sql_categories = "SELECT c.id, c.name, c.slug 
                       FROM $tbl_category c
                       LEFT JOIN $tbl_settings s ON c.id = s.category_id
                       WHERE c.is_active = 1 
                       AND (s.is_active = 1 OR s.is_active IS NULL)
                       ORDER BY COALESCE(s.serial_order, 999) ASC, c.id ASC";
    $result_categories = $conn->query($sql_categories);
    
    $categories = [];
    while ($cat = $result_categories->fetch_assoc()) {
        $categories[] = $cat;
    }
    
    // Helper function for news URL - uses ID-based URLs
    function getNewsLink($news_item) {
        global $user_id, $user_id_suffix;
        $news_id = $news_item['id'] ?? $news_item['news_id'] ?? 0;
        if ($user_id > 0) {
            return "news.php?id=" . $news_id . "&user_id=" . $user_id;
        }
        return "news.php?id=" . $news_id;
    }
    
    // Helper function for excerpt
    function getExcerptText($content, $length = 100) {
        $content = strip_tags($content ?? '');
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', trim($content));
        return mb_substr($content, 0, $length) . '...';
    }
    
    // Fetch news for all categories (more news for entertainment style)
    $allCategoryNews = [];
    foreach ($categories as $category) {
        $cat_id = $category['id'];
        $sql_news = "SELECT n.headline, n.slug AS news_slug, n.image_url, n.created_at, n.news_1,
                            c.slug AS category_slug, c.name AS category_name
                     FROM $tbl_news n
                     JOIN $tbl_category c ON n.category_id = c.id
                     WHERE n.category_id = $cat_id AND n.is_active = 1
                     ORDER BY n.created_at DESC
                     LIMIT 15";
        $result_news = $conn->query($sql_news);
        
        $news = [];
        while ($row = $result_news->fetch_assoc()) {
            $news[] = $row;
        }
        
        if (count($news) > 0) {
            $allCategoryNews[] = [
                'category' => $category,
                'news' => $news
            ];
        }
    }
    
    $layoutCounter = 0;
    $i = 2; // Skip first 2 categories (shown before opinion section)
    ?>
    
    <div class="categories-wrapper">
    <?php 
    $totalCategories = count($allCategoryNews);
    while ($i < $totalCategories): 
        // Check if this is the last category - use lifestyle layout
        $isLastCategory = ($i == $totalCategories - 1);
        
        // Layout pattern: 2 = Entertainment, 3 = Hero-style, 4+ = Dual
        // First category in this loop (index 2) uses Entertainment style
        $useEntertainment = ($i == 2 || $i == 3);
        $useHeroStyle = ($i == 4);
        
        if ($isLastCategory && count($allCategoryNews[$i]['news']) >= 5):
            // Lifestyle Layout - Last Category
            $cat = $allCategoryNews[$i];
            $news = $cat['news'];
    ?>
        <section class="lifestyle-section">
            <h2 class="section-title"><?= htmlspecialchars($cat['category']['name']); ?></h2>
            <div class="lifestyle-layout">
                <div class="featured-image">
                    <a href="<?= getNewsLink($news[0]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[0]['image_url']); ?>" alt="">
                    </a>
                </div>
                <div class="content-side">
                    <div class="main-headline">
                        <h3><a href="<?= getNewsLink($news[0]); ?>"><?= htmlspecialchars($news[0]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[0]['news_1'], 250); ?></p>
                    </div>
                    <div class="news-grid">
                        <?php for ($j = 1; $j < min(5, count($news)); $j++): ?>
                        <div class="grid-item">
                            <img src="Admin/img/<?= htmlspecialchars($news[$j]['image_url']); ?>" alt="">
                            <h4><a href="<?= getNewsLink($news[$j]); ?>"><?= htmlspecialchars($news[$j]['headline']); ?></a></h4>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php 
            $i++;
        elseif ($useEntertainment && count($allCategoryNews[$i]['news']) >= 8):
            // Entertainment Style - Full Width 3-Column Layout
            $cat = $allCategoryNews[$i];
            $news = $cat['news'];
    ?>
    </div><!-- Close categories-wrapper temporarily -->
        <section class="hero-style-section">
            <h2 class="section-title">
                <div class="left-part">
                    
                    <?= htmlspecialchars($cat['category']['name']); ?>
                </div>
            </h2>
            <div class="hero-style-layout grid-layout">
                <!-- Left Column - First Featured Article -->
                <div class="hero-style-main-article">
                    <a href="<?= getNewsLink($news[0]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[0]['image_url']); ?>" alt="<?= htmlspecialchars($news[0]['headline']); ?>">
                    </a>
                    <div class="content">
                        <h3><a href="<?= getNewsLink($news[0]); ?>"><?= htmlspecialchars($news[0]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[0]['news_1'], 200); ?></p>
                    </div>
                </div>
                
                <!-- Middle Column - Second Featured Article -->
                <?php if (count($news) > 1): ?>
                <div class="hero-style-main-article">
                    <a href="<?= getNewsLink($news[1]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[1]['image_url']); ?>" alt="<?= htmlspecialchars($news[1]['headline']); ?>">
                    </a>
                    <div class="content">
                        <h3><a href="<?= getNewsLink($news[1]); ?>"><?= htmlspecialchars($news[1]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[1]['news_1'], 200); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Right Sidebar - Horizontal List Items -->
                <div class="hero-style-sidebar">
                    <?php for ($j = 2; $j < min(6, count($news)); $j++): ?>
                    <div class="hero-style-sidebar-item">
                        <a href="<?= getNewsLink($news[$j]); ?>">
                            <img src="Admin/img/<?= htmlspecialchars($news[$j]['image_url']); ?>" alt="<?= htmlspecialchars($news[$j]['headline']); ?>">
                        </a>
                        <div class="content">
                            <h4><a href="<?= getNewsLink($news[$j]); ?>"><?= htmlspecialchars($news[$j]['headline']); ?></a></h4>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
    <div class="categories-wrapper"><!-- Reopen categories-wrapper -->
    <?php 
            $i++;
        elseif ($allCategoryNews[$i]['category']['id'] == 13):
            // Category ID 13 with Quiz Section
            $cat = $allCategoryNews[$i];
            $news = $cat['news'];
    ?>
    </div><!-- Close categories-wrapper for quiz layout -->
    <section class="quiz-category-section">
        <h2 class="section-title">
           
            <?= htmlspecialchars($cat['category']['name']); ?>
        </h2>
        <div class="quiz-layout-grid">
            <!-- News on Left - 2 Cards -->
            <div class="quiz-news-left">
                <?php for ($j = 0; $j < min(2, count($news)); $j++): ?>
                <div class="quiz-news-card">
                    <a href="<?= getNewsLink($news[$j]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[$j]['image_url']); ?>" alt="<?= htmlspecialchars($news[$j]['headline']); ?>">
                    </a>
                    <div class="quiz-news-card-content">
                        <h3><a href="<?= getNewsLink($news[$j]); ?>"><?= htmlspecialchars($news[$j]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[$j]['news_1'], 500); ?> <a href="<?= getNewsLink($news[$j]); ?>" style="color: #dc2626; font-weight: 600; text-decoration: none;">বিস্তারিত পড়ুন</a></p>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <!-- Quiz Box on Right -->
            <div class="quiz-box" id="quizBox">
                <div class="quiz-header">
                    <div class="quiz-title">
                        <span class="quiz-icon"><i class="fas fa-lightbulb"></i></span>
                        কুইজ
                    </div>
                    <div class="quiz-nav">
                        <button onclick="changeQuiz(-1)" id="quizPrevBtn" title="আগের কুইজ"><i class="fas fa-chevron-left"></i></button>
                        <button onclick="changeQuiz(1)" id="quizNextBtn" title="পরের কুইজ"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="quiz-counter" id="quizCounter">কুইজ ১/১</div>
                <div id="quizContent">
                    <div class="quiz-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        লোড হচ্ছে...
                    </div>
                </div>
                <div class="quiz-footer">
                    <div class="quiz-participants" id="quizParticipants">
                        <i class="fas fa-users"></i> <span>০</span> জন অংশগ্রহণ করেছেন
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="categories-wrapper"><!-- Reopen categories-wrapper -->
    <?php
            $i++;
        else:
            // Single Category Style - Full Width
            $cat = $allCategoryNews[$i];
            $news = $cat['news'];
    ?>
        <section class="hero-style-section">
            <h2 class="section-title">
                <div class="left-part">
                   
                    <?= htmlspecialchars($cat['category']['name']); ?>
                </div>
            </h2>
            <div class="hero-style-layout grid-layout">
                <!-- Left Column - First Featured Article -->
                <div class="hero-style-main-article">
                    <a href="<?= getNewsLink($news[0]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[0]['image_url']); ?>" alt="<?= htmlspecialchars($news[0]['headline']); ?>">
                    </a>
                    <div class="content">
                        <h3><a href="<?= getNewsLink($news[0]); ?>"><?= htmlspecialchars($news[0]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[0]['news_1'], 200); ?></p>
                    </div>
                </div>
                
                <!-- Middle Column - Second Featured Article -->
                <?php if (count($news) > 1): ?>
                <div class="hero-style-main-article">
                    <a href="<?= getNewsLink($news[1]); ?>">
                        <img src="Admin/img/<?= htmlspecialchars($news[1]['image_url']); ?>" alt="<?= htmlspecialchars($news[1]['headline']); ?>">
                    </a>
                    <div class="content">
                        <h3><a href="<?= getNewsLink($news[1]); ?>"><?= htmlspecialchars($news[1]['headline']); ?></a></h3>
                        <p><?= getExcerptText($news[1]['news_1'], 200); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Right Sidebar - Horizontal List Items -->
                <div class="hero-style-sidebar">
                    <?php for ($j = 2; $j < min(6, count($news)); $j++): ?>
                    <div class="hero-style-sidebar-item">
                        <a href="<?= getNewsLink($news[$j]); ?>">
                            <img src="Admin/img/<?= htmlspecialchars($news[$j]['image_url']); ?>" alt="<?= htmlspecialchars($news[$j]['headline']); ?>">
                        </a>
                        <div class="content">
                            <h4><a href="<?= getNewsLink($news[$j]); ?>"><?= htmlspecialchars($news[$j]['headline']); ?></a></h4>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
    <?php 
            $i++;
        endif;
        $layoutCounter++;
        
        // Show podcast section after 7 categories (index 6 = 7th category, counting first 2 before opinion)
        if ($i == 7):
    ?>
    </div><!-- Close categories-wrapper for podcast -->
    <!-- Podcast Section -->
    <section class="podcast-section">
        <div class="podcast-section-inner">
            <div class="podcast-section-header">
                <div class="podcast-title">
                    <span class="podcast-title-icon"><i class="fas fa-podcast"></i></span>
                    <h2>পডকাস্ট</h2>
                </div>
                <div class="podcast-date-picker">
                    <div class="podcast-date-nav">
                        <button onclick="changePodcastDate(-1)" title="আগের দিন"><i class="fas fa-chevron-left"></i></button>
                        <button onclick="changePodcastDate(1)" title="পরের দিন"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <input type="date" id="podcastDatePicker" onchange="filterPodcastsByDate(this.value)">
                    <button class="podcast-reset-btn" onclick="resetPodcastFilter()"><i class="fas fa-sync-alt"></i> সব দেখুন</button>
                </div>
            </div>
            
            <div class="podcast-grid" id="podcastGrid">
                <?php
                // Fetch latest 8 podcasts
                $sql_podcast = "SELECT id, title, subtitle, thumbnail, youtube_link, created_at
                              FROM $tbl_podcasts
                              WHERE is_active = 1
                              ORDER BY created_at DESC
                              LIMIT 8";
                $result_podcast = $conn->query($sql_podcast);
                
                if ($result_podcast && $result_podcast->num_rows > 0):
                    while ($podcast = $result_podcast->fetch_assoc()):
                        // Extract YouTube video ID from link
                        $ytLink = $podcast['youtube_link'];
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $ytLink, $matches);
                        $ytId = $matches[1] ?? '';
                        
                        $thumbnail = !empty($podcast['thumbnail']) ? $podcast['thumbnail'] : 'default.jpg';
                        $title = htmlspecialchars($podcast['title']);
                        $subtitle = htmlspecialchars($podcast['subtitle'] ?? '');
                ?>
                <div class="podcast-item" onclick="playVideo('<?= $ytId; ?>')">
                    <div class="podcast-thumbnail">
                        <img src="Admin/img/<?= $thumbnail; ?>" alt="<?= $title; ?>">
                        <div class="podcast-play-btn"><i class="fas fa-play"></i></div>
                    </div>
                    <div class="podcast-info">
                        <h4 class="podcast-headline"><?= mb_substr($title, 0, 50); ?><?= mb_strlen($title) > 50 ? '...' : ''; ?></h4>
                        <?php if (!empty($subtitle)): ?>
                        <p class="podcast-excerpt"><?= mb_substr($subtitle, 0, 70); ?><?= mb_strlen($subtitle) > 70 ? '...' : ''; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p style="text-align: center; width: 100%; padding: 40px 0; color: #999;">কোন পডকাস্ট পাওয়া যায়নি</p>
                <?php
                endif;
                ?>
            </div>
            <!-- Podcast Slider Dots (Mobile Only) -->
            <div class="podcast-slider-dots" id="podcastSliderDots"></div>
        </div>
    </section>
    
    <script>
    // Podcast Slider - Desktop & Mobile
    let podcastSlideIndex = 0;
    let podcastSliderInterval = null;
    
    function getVisibleItems() {
        const width = window.innerWidth;
        if (width <= 480) return 1;
        if (width <= 768) return 2;
        if (width <= 1024) return 3;
        return 4;
    }
    
    function getSlideInterval() {
        return window.innerWidth <= 480 ? 2000 : 1500;
    }
    
    function getMaxSlideIndex() {
        const grid = document.getElementById('podcastGrid');
        if (!grid) return 0;
        const items = grid.querySelectorAll('.podcast-item');
        const visible = getVisibleItems();
        return Math.max(0, items.length - visible);
    }
    
    function initPodcastSlider() {
        const grid = document.getElementById('podcastGrid');
        const dotsContainer = document.getElementById('podcastSliderDots');
        if (!grid || !dotsContainer) return;
        
        const items = grid.querySelectorAll('.podcast-item');
        if (items.length === 0) return;
        
        podcastSlideIndex = 0;
        updatePodcastDots();
        startPodcastAutoSlide();
    }
    
    function updatePodcastDots() {
        const dotsContainer = document.getElementById('podcastSliderDots');
        const maxIndex = getMaxSlideIndex();
        
        dotsContainer.innerHTML = '';
        for (let i = 0; i <= maxIndex; i++) {
            const dot = document.createElement('div');
            dot.className = 'podcast-slider-dot' + (i === podcastSlideIndex ? ' active' : '');
            dot.onclick = () => goToPodcastSlide(i);
            dotsContainer.appendChild(dot);
        }
    }
    
    function goToPodcastSlide(index) {
        const grid = document.getElementById('podcastGrid');
        const items = grid.querySelectorAll('.podcast-item');
        const dots = document.querySelectorAll('.podcast-slider-dot');
        const maxIndex = getMaxSlideIndex();
        
        if (items.length === 0) return;
        
        podcastSlideIndex = index;
        if (podcastSlideIndex > maxIndex) podcastSlideIndex = 0;
        if (podcastSlideIndex < 0) podcastSlideIndex = maxIndex;
        
        // Calculate translation based on item width
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const translateX = podcastSlideIndex * (itemWidth + gap);
        
        items.forEach(item => {
            item.style.transform = `translateX(-${translateX}px)`;
        });
        
        // Update dots
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === podcastSlideIndex);
        });
    }
    
    function nextPodcastSlide() {
        const maxIndex = getMaxSlideIndex();
        goToPodcastSlide((podcastSlideIndex + 1) > maxIndex ? 0 : podcastSlideIndex + 1);
    }
    
    function startPodcastAutoSlide() {
        if (podcastSliderInterval) clearInterval(podcastSliderInterval);
        podcastSliderInterval = setInterval(nextPodcastSlide, getSlideInterval());
    }
    
    function stopPodcastAutoSlide() {
        if (podcastSliderInterval) {
            clearInterval(podcastSliderInterval);
            podcastSliderInterval = null;
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initPodcastSlider);
    
    // Reinitialize on resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            podcastSlideIndex = 0;
            updatePodcastDots();
            goToPodcastSlide(0);
            startPodcastAutoSlide();
        }, 200);
    });
    
    // Pause on hover/touch
    document.addEventListener('DOMContentLoaded', function() {
        const podcastGrid = document.getElementById('podcastGrid');
        if (podcastGrid) {
            podcastGrid.addEventListener('mouseenter', stopPodcastAutoSlide);
            podcastGrid.addEventListener('mouseleave', startPodcastAutoSlide);
            podcastGrid.addEventListener('touchstart', stopPodcastAutoSlide);
            podcastGrid.addEventListener('touchend', function() {
                setTimeout(startPodcastAutoSlide, 1000);
            });
        }
    });
    
    // Podcast date filter functions
    let currentPodcastDate = null;
    
    function changePodcastDate(days) {
        const datePicker = document.getElementById('podcastDatePicker');
        let date = currentPodcastDate ? new Date(currentPodcastDate) : new Date();
        date.setDate(date.getDate() + days);
        
        const formattedDate = date.toISOString().split('T')[0];
        datePicker.value = formattedDate;
        filterPodcastsByDate(formattedDate);
    }
    
    function filterPodcastsByDate(dateValue) {
        currentPodcastDate = dateValue;
        const grid = document.getElementById('podcastGrid');
        grid.innerHTML = '<p style="text-align: center; width: 100%; padding: 40px 0; color: #999;"><i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...</p>';
        
        fetch('index.php?ajax_podcasts=1&date=' + encodeURIComponent(dateValue))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.podcasts.length > 0) {
                    let html = '';
                    data.podcasts.forEach(podcast => {
                        html += `
                        <div class="podcast-item" onclick="playVideo('${podcast.youtube_id}')">
                            <div class="podcast-thumbnail">
                                <img src="Admin/img/${podcast.thumbnail}" alt="${podcast.title}">
                                <div class="podcast-play-btn"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="podcast-info">
                                <h4 class="podcast-headline">${podcast.title.substring(0, 50)}${podcast.title.length > 50 ? '...' : ''}</h4>
                                ${podcast.subtitle ? `<p class="podcast-excerpt">${podcast.subtitle.substring(0, 70)}${podcast.subtitle.length > 70 ? '...' : ''}</p>` : ''}
                            </div>
                        </div>`;
                    });
                    grid.innerHTML = html;
                } else {
                    grid.innerHTML = '<p style="text-align: center; width: 100%; padding: 40px 0; color: #999;">এই তারিখে কোন পডকাস্ট পাওয়া যায়নি</p>';
                }
            })
            .catch(error => {
                grid.innerHTML = '<p style="text-align: center; width: 100%; padding: 40px 0; color: #f00;">লোড করতে সমস্যা হয়েছে</p>';
            });
    }
    
    function resetPodcastFilter() {
        currentPodcastDate = null;
        document.getElementById('podcastDatePicker').value = '';
        location.reload();
    }
    </script>
    <div class="categories-wrapper"><!-- Reopen categories-wrapper after podcast -->
    <?php
        endif;
    endwhile; ?>
    </div>
   
    <footer class="footer-wrapper">
        <!-- Main Footer Content -->
        <div class="footer-main">
            <!-- Logo & Social Section -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if (!empty($basic_info['image'])): ?>
                    <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" class="logo-image">
                    <?php else: ?>
                   
                    <?php endif; ?>
                </div>
                <!-- Social Media Icons -->
                <div class="footer-social">
                    <h4>অনুসরণ করুন</h4>
                    <div class="social-icons">
                        <?php if (!empty($basic_info['facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['facebook']); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($basic_info['twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['twitter']); ?>" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($basic_info['instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['instagram']); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($basic_info['youtube'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['youtube']); ?>" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($basic_info['whatsapp'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['whatsapp']); ?>" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($basic_info['tiktok'])): ?>
                        <a href="<?php echo htmlspecialchars($basic_info['tiktok']); ?>" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <?php if (!empty($basic_info['description'])): ?>
            <div class="footer-description-section">
                <p><?php echo htmlspecialchars($basic_info['description']); ?></p>
            </div>
            <?php endif; ?>

            <!-- Quick Links Row -->
            <div class="footer-links">
                <ul>
                    <li><a href="#" onclick="openAboutUsPopup(); return false;">আমাদের সম্পর্কে</a></li>
                    <li><a href="#" onclick="openAdsListPopup(); return false;">বিজ্ঞাপন</a></li>
                    <li><a href="#" onclick="openPrivacyPolicyPopup(); return false;">গোপনীয়তার নীতি</a></li>
                    <li><a href="#" onclick="openTermsPopup(); return false;">নীতি ও শর্ত</a></li>
                    <li><a href="#" onclick="openCommentPolicyPopup(); return false;">মন্তব্য নীতিমালা</a></li>
                    <li><a href="#" onclick="openAdsPolicyPopup(); return false;">বিজ্ঞাপন নীতিমালা</a></li>
                    <li><a href="#" onclick="openAboutUsPopup(); return false;">যোগাযোগ</a></li>
                </ul>
            </div>

            <!-- Contact Info Section -->
            <div class="footer-contact-row">
                <?php if (!empty($basic_info['mobile_number'])): ?>
                <span><i class="fas fa-phone"></i> <a href="tel:<?php echo htmlspecialchars($basic_info['mobile_number']); ?>"><?php echo htmlspecialchars($basic_info['mobile_number']); ?></a></span>
                <?php endif; ?>
                <?php if (!empty($basic_info['email'])): ?>
                <span><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($basic_info['email']); ?>"><?php echo htmlspecialchars($basic_info['email']); ?></a></span>
                <?php endif; ?>
            </div>

            <!-- Editor & Copyright Section -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>স্বত্ব © <?php echo date('Y'); ?> <?php echo htmlspecialchars($basic_info['news_portal_name'] ?? ''); ?></p>
                </div>
                <div class="footer-editor">
                    <?php if (!empty($basic_info['editor_in_chief'])): ?>
                    <p><strong>সম্পাদক ও প্রকাশক:</strong> <?php echo htmlspecialchars($basic_info['editor_in_chief']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($basic_info['media_info'])): ?>
                    <p><?php echo htmlspecialchars($basic_info['media_info']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!--===============================================================================================-->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="js/main.js"></script>
    <script>
        function openSidebarPanel() {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const overlay = document.getElementById('sidebarOverlayBg');

            sidebar.classList.add('sidebar-active');
            overlay.classList.add('overlay-active');

            // Prevent body scroll when sidebar is open
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarPanel() {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const overlay = document.getElementById('sidebarOverlayBg');

            sidebar.classList.remove('sidebar-active');
            overlay.classList.remove('overlay-active');
            document.body.style.overflow = 'auto';
        }

        function toggleMobileSearch() {
            // Mobile search functionality
            alert('Search functionality would be implemented here');
        }

        // Update date display
        function updateDateDisplay() {
            const dateElement = document.getElementById('currentDateElement');
            const now = new Date();

            // Bangla digits
            const banglaNumerals = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

            // Bangla months
            const banglaMonths = [
                'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
                'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'
            ];

            // Bangla weekdays
            const banglaWeekdays = [
                'রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'
            ];

            // Convert number to Bangla
            function toBanglaNumber(num) {
                return num.toString().split('').map(digit => banglaNumerals[digit]).join('');
            }

            const day = toBanglaNumber(now.getDate());
            const month = banglaMonths[now.getMonth()];
            const year = toBanglaNumber(now.getFullYear());
            const weekday = banglaWeekdays[now.getDay()];

            dateElement.textContent = `${weekday}, ${day} ${month} ${year}`;
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const menuToggle = document.querySelector('.mobile-menu-button');

            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                closeSidebarPanel();
            }
        });

        // Initialize
        updateDateDisplay();
    </script>
    <script>
        const tickerMove = document.getElementById('tickerMove');

        tickerMove.addEventListener('mouseenter', function () {
            this.classList.add('paused');
        });

        tickerMove.addEventListener('mouseleave', function () {
            this.classList.remove('paused');
        });
    </script>
    <script>
        function checkResponsive() {
            const rightSection = document.querySelector(".right-section");

            if (!rightSection) return;

            if (window.innerWidth <= 768) {
                rightSection.style.display = "none";   // Hide on mobile
            } else {
                rightSection.style.display = "flex";   // Show on desktop/tablet
            }
        }

        // Run on page load
        checkResponsive();

        // Run on screen resize
        window.addEventListener("resize", checkResponsive);
    </script>
    <script>
        const circle = document.getElementById("blinkCircle");

        function animateCircle() {
            circle.animate([
                { opacity: 1, transform: "scale(1)" }, // 0%
                { opacity: 0.3, transform: "scale(0.95)" }, // 25%
                { opacity: 1, transform: "scale(1)" }, // 50%
                { opacity: 0, transform: "scale(0.8)" }, // 75%
                { opacity: 1, transform: "scale(1)" }  // 100%
            ], {
                duration: 2000,
                iterations: Infinity,
                easing: "linear"
            });
        }

        animateCircle();
    </script>

</body>
<script>
    const videos = [
        "https://www.youtube.com/embed/Z3QUIu1eYeo",
        "https://www.youtube.com/embed/xFR7UnPKH6o",
        "https://www.youtube.com/embed/70Ovyx1GFpQ",
        "https://www.youtube.com/embed/bmSALkQMYsA",
        "https://www.youtube.com/embed/R3VzGW5Lz2U",
        "https://www.youtube.com/embed/FygZ26aBIZw"
    ];

    const container = document.getElementById("videoContainer");
    let index = 0;

    // Lazy load videos one by one
    function loadNextVideo() {
        if (index >= videos.length) return;

        const card = document.createElement("div");
        card.className = "video-card";
        card.innerHTML = `<iframe src="${videos[index]}" allowfullscreen loading="lazy"></iframe>`;
        container.appendChild(card);

        setTimeout(() => card.classList.add("show"), 100);

        index++;
        setTimeout(loadNextVideo, 500);
    }
    loadNextVideo();

    // Scroll button functionality
    const scrollAmount = 300; // pixels to scroll per click
    document.getElementById("scrollLeft").addEventListener("click", () => {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
    document.getElementById("scrollRight").addEventListener("click", () => {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
</script>

<script>
    // Multi-Card Opinion Slider with Infinite Loop
    let currentSlideIndex = 0;
    const opinionSlider = document.getElementById('opinionSlider');
    const opinionCards = document.querySelectorAll('.opinion-card');
    const totalCards = opinionCards.length;
    let isTransitioning = false;

    // Clone cards for infinite loop effect
    if (opinionSlider && totalCards > 0) {
        const visibleCount = 5; // Clone enough cards to fill the view
        for (let i = 0; i < Math.min(visibleCount, totalCards); i++) {
            const clone = opinionCards[i].cloneNode(true);
            clone.classList.add('cloned');
            opinionSlider.appendChild(clone);
        }
    }

    // Get number of visible cards based on screen width
    function getVisibleCards() {
        const width = window.innerWidth;
        if (width <= 360) return 1;
        if (width <= 480) return 1;
        if (width <= 640) return 1;
        if (width <= 768) return 2;
        if (width <= 1024) return 3;
        if (width <= 1200) return 4;
        return 5;
    }

    // Update slider position
    function updateSliderPosition(smooth = true) {
        if (totalCards === 0) return;
        
        const cardWidth = opinionCards[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(opinionSlider).gap) || 15;
        const offset = -(currentSlideIndex * (cardWidth + gap));
        
        if (smooth) {
            opinionSlider.style.transition = 'transform 0.5s ease';
        } else {
            opinionSlider.style.transition = 'none';
        }
        opinionSlider.style.transform = `translateX(${offset}px)`;
    }

    // Slide function
    function slideOpinions(direction) {
        if (totalCards === 0 || isTransitioning) return;

        if (direction === 'next') {
            currentSlideIndex++;
        } else if (direction === 'prev') {
            currentSlideIndex--;
            if (currentSlideIndex < 0) {
                currentSlideIndex = totalCards - 1;
                updateSliderPosition(false);
                setTimeout(() => updateSliderPosition(true), 50);
                return;
            }
        }

        updateSliderPosition();
        
        // Check if we've scrolled past the original cards
        if (currentSlideIndex >= totalCards) {
            isTransitioning = true;
            setTimeout(function() {
                currentSlideIndex = 0;
                updateSliderPosition(false);
                isTransitioning = false;
            }, 500);
        }
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            updateSliderPosition(false);
        }, 250);
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    if (opinionSlider) {
        opinionSlider.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        opinionSlider.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                slideOpinions('next');
            } else {
                slideOpinions('prev');
            }
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            slideOpinions('prev');
        } else if (e.key === 'ArrowRight') {
            slideOpinions('next');
        }
    });

    // Initialize slider position
    if (totalCards > 0) {
        updateSliderPosition(false);
    }

    // Auto-scroll functionality - infinite loop
    let autoScrollInterval;
    
    function startAutoScroll() {
        autoScrollInterval = setInterval(function() {
            slideOpinions('next');
        }, 2000);
    }
    
    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }
    
    // Start auto-scroll
    if (totalCards > 0) {
        startAutoScroll();
    }
    
    // Pause on hover
    const sliderWrapper = document.querySelector('.opinion-slider-wrapper');
    if (sliderWrapper) {
        sliderWrapper.addEventListener('mouseenter', stopAutoScroll);
        sliderWrapper.addEventListener('mouseleave', startAutoScroll);
    }
</script>
<script>
    function handleCharityClick(itemNumber) {
        alert('ধন্যবাদ! আপনি ' + itemNumber + ' নম্বর ডোনেশনে সাহায্য করতে চাচ্ছেন।');
    }

    function executeNewsSearch() {
        const divisionValue = document.getElementById('regional-division-picker').value;
        const districtValue = document.getElementById('district-zone-picker').value;
        const upazilaValue = document.getElementById('subdistrict-area-picker').value;

        if (!divisionValue || !districtValue || !upazilaValue) {
            alert('অনুগ্রহ করে সকল ক্ষেত্র নির্বাচন করুন।');
            return;
        }

        alert(`আপনার নির্বাচিত এলাকার সংবাদ:\nবিভাগ: ${divisionValue}\nজেলা: ${districtValue}\nউপজেলা: ${upazilaValue}`);
    }

    function pickTriviaAnswer(element, answerValue) {
        document.querySelectorAll('.trivia-choice-btn').forEach(opt => {
            opt.classList.remove('answer-picked');
        });

        element.classList.add('answer-picked');

        setTimeout(() => {
            if (answerValue === '108') {
                alert('🎉 সঠিক উত্তর! ভগবান শ্রী কৃষ্ণের ১০৮টি নাম রয়েছে।');
            } else {
                alert('❌ ভুল উত্তর! সঠিক উত্তর হল ১০৮।');
            }
        }, 300);
    }
</script>

<!-- Hero Center Tab Switching -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.hero-center-tab');
    const latestNews = document.getElementById('latest-news');
    const popularNews = document.getElementById('popular-news');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show/hide appropriate list
            if (this.dataset.tab === 'latest') {
                latestNews.style.display = 'block';
                popularNews.style.display = 'none';
            } else {
                latestNews.style.display = 'none';
                popularNews.style.display = 'block';
            }
        });
    });
});
</script>

<!-- Quiz Functionality -->
<script>
(function() {
    // Quiz state
    let currentQuizOffset = 0;
    let totalQuizzes = 0;
    let currentQuizId = null;
    
    // Generate unique device ID
    function getDeviceId() {
        let deviceId = localStorage.getItem('quiz_device_id');
        if (!deviceId) {
            deviceId = 'dev_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('quiz_device_id', deviceId);
        }
        return deviceId;
    }
    
    // Convert to Bengali numerals
    function toBengaliNum(num) {
        const bengaliNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return String(num).split('').map(d => bengaliNums[parseInt(d)] || d).join('');
    }
    
    // Load quiz
    window.loadQuiz = function(offset = 0) {
        const quizContent = document.getElementById('quizContent');
        if (!quizContent) return;
        
        quizContent.innerHTML = '<div class="quiz-loading"><i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...</div>';
        
        fetch(`api/quiz.php?action=get_quiz&offset=${offset}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentQuizId = data.quiz.id;
                    totalQuizzes = data.total;
                    currentQuizOffset = offset;
                    
                    // Update counter
                    document.getElementById('quizCounter').textContent = `কুইজ ${toBengaliNum(data.current)}/${toBengaliNum(data.total)}`;
                    
                    // Update nav buttons
                    document.getElementById('quizPrevBtn').disabled = offset <= 0;
                    document.getElementById('quizNextBtn').disabled = offset >= totalQuizzes - 1;
                    
                    // Check if already voted
                    checkVoted(data.quiz);
                } else {
                    quizContent.innerHTML = '<div class="quiz-loading">কোনো কুইজ পাওয়া যায়নি</div>';
                }
            })
            .catch(err => {
                quizContent.innerHTML = '<div class="quiz-loading">লোড করতে সমস্যা হয়েছে</div>';
            });
    };
    
    // Check if user already voted
    function checkVoted(quiz) {
        const deviceId = getDeviceId();
        
        fetch(`api/quiz.php?action=check_voted&quiz_id=${quiz.id}&device_id=${deviceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.voted) {
                    renderQuizWithResults(quiz, data.results, data.correct_option, data.selected_option);
                } else {
                    renderQuiz(quiz);
                }
            })
            .catch(() => renderQuiz(quiz));
    }
    
    // Render quiz without results
    function renderQuiz(quiz) {
        const options = [
            { key: 'a', text: quiz.option_a },
            { key: 'b', text: quiz.option_b },
            { key: 'c', text: quiz.option_c },
            { key: 'd', text: quiz.option_d }
        ];
        
        let html = `<div class="quiz-question">${quiz.question}</div><div class="quiz-options">`;
        
        options.forEach(opt => {
            html += `
                <div class="quiz-option" onclick="submitQuizVote('${opt.key}')" data-option="${opt.key}">
                    <div class="quiz-progress-bar" style="width: 0%;"></div>
                    <div class="quiz-option-letter">${opt.key.toUpperCase()}</div>
                    <div class="quiz-option-text">${opt.text}</div>
                    <div class="quiz-option-count" style="visibility: hidden;">০ জন</div>
                </div>
            `;
        });
        
        html += '</div>';
        document.getElementById('quizContent').innerHTML = html;
        updateParticipants(0);
    }
    
    // Render quiz with results
    function renderQuizWithResults(quiz, results, correctOption, selectedOption) {
        const options = [
            { key: 'a', text: quiz.option_a },
            { key: 'b', text: quiz.option_b },
            { key: 'c', text: quiz.option_c },
            { key: 'd', text: quiz.option_d }
        ];
        
        let html = `<div class="quiz-question">${quiz.question}</div><div class="quiz-options">`;
        
        options.forEach(opt => {
            const isCorrect = opt.key === correctOption;
            const isSelected = opt.key === selectedOption;
            const voteCount = results.votes[opt.key] || 0;
            const percent = results.percentages[opt.key] || 0;
            
            let classes = 'quiz-option voted';
            if (isCorrect) {
                classes += ' correct';
            } else {
                classes += ' wrong';
            }
            if (isSelected) classes += ' selected';
            
            const selectedTag = isSelected ? '<span class="quiz-selected-tag">আপনার উত্তর</span>' : '';
            
            html += `
                <div class="${classes}" data-option="${opt.key}">
                    <div class="quiz-progress-bar" style="width: ${percent}%"></div>
                    <div class="quiz-option-letter">${opt.key.toUpperCase()}</div>
                    <div class="quiz-option-text">${opt.text}${selectedTag}</div>
                    <div class="quiz-option-count">${toBengaliNum(voteCount)} জন</div>
                </div>
            `;
        });
        
        html += '</div>';
        document.getElementById('quizContent').innerHTML = html;
        updateParticipants(results.total);
    }
    
    // Submit vote
    window.submitQuizVote = function(option) {
        if (!currentQuizId) return;
        
        const deviceId = getDeviceId();
        const formData = new FormData();
        formData.append('action', 'vote');
        formData.append('quiz_id', currentQuizId);
        formData.append('option', option);
        formData.append('device_id', deviceId);
        
        // Disable all options immediately
        document.querySelectorAll('.quiz-option').forEach(el => {
            el.classList.add('voted');
            el.onclick = null;
        });
        
        fetch('api/quiz.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showResults(option, data.correct_option, data.results);
            } else if (data.already_voted) {
                loadQuiz(currentQuizOffset);
            }
        })
        .catch(err => console.error('Vote error:', err));
    };
    
    // Show results after voting
    function showResults(selectedOption, correctOption, results) {
        const options = document.querySelectorAll('.quiz-option');
        
        options.forEach(el => {
            const optKey = el.dataset.option;
            const voteCount = results.votes[optKey] || 0;
            const percent = results.percentages[optKey] || 0;
            const isCorrect = optKey === correctOption;
            const isSelected = optKey === selectedOption;
            
            // Update existing progress bar (already in DOM)
            const progressBar = el.querySelector('.quiz-progress-bar');
            
            // Update existing count element (make visible and update text)
            const countEl = el.querySelector('.quiz-option-count');
            if (countEl) {
                countEl.style.visibility = 'visible';
                countEl.textContent = toBengaliNum(voteCount) + ' জন';
            }
            
            // Add classes - correct is green, ALL others are red
            if (isCorrect) {
                el.classList.add('correct');
            } else {
                el.classList.add('wrong');
            }
            if (isSelected) {
                el.classList.add('selected');
                // Add selected tag
                const textEl = el.querySelector('.quiz-option-text');
                if (textEl) {
                    textEl.innerHTML += '<span class="quiz-selected-tag">আপনার উত্তর</span>';
                }
            }
            
            // Animate progress bar
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = percent + '%';
                }, 100);
            }
        });
        
        updateParticipants(results.total);
    }
    
    // Update participants count
    function updateParticipants(count) {
        const el = document.getElementById('quizParticipants');
        if (el) {
            el.innerHTML = `<i class="fas fa-users"></i> <span>${toBengaliNum(count)}</span> জন অংশগ্রহণ করেছেন`;
        }
    }
    
    // Change quiz (prev/next)
    window.changeQuiz = function(direction) {
        const newOffset = currentQuizOffset + direction;
        if (newOffset >= 0 && newOffset < totalQuizzes) {
            loadQuiz(newOffset);
        }
    };
    
    // Initialize quiz on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('quizBox')) {
            loadQuiz(0);
        }
    });
})();
</script>

<!-- Privacy Policy Popup -->
<div id="privacyPopup" class="policy-popup-overlay" onclick="closePrivacyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-shield-alt"></i> Privacy Policy for Hindus News</h2>
            <button class="policy-popup-close" onclick="closePrivacyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <p class="policy-website"><i class="fas fa-globe"></i> Website: <a href="http://hindus-news.com" target="_blank">http://hindus-news.com</a></p>
            
            <p>Your privacy is important to us. It is Hindus News's policy to respect your privacy regarding any information we may collect from you across our website, http://hindus-news.com and other sites we own and operate.</p>
            
            <p>This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>
            
            <p>We use your data to provide and improve the Service. By using the Service, you agree to the collection and use of information in accordance with this policy.</p>
            
            <h3><i class="fas fa-database"></i> Information We Collect</h3>
            <p>Hindus News collects the information on a user upon the user's access to the Hindus News website –</p>
            <ul>
                <li>by registering to the site or Apps,</li>
                <li>subscribing to the newsletter,</li>
                <li>responding to a survey or participating in a competition,</li>
                <li>logging-in to a site or page, etc.</li>
            </ul>
            
            <h3><i class="fas fa-user-circle"></i> What personal information do we collect?</h3>
            <p>When ordering or registering on our site, as appropriate, you may be asked to enter your name, email address, phone number or other details to help you with your experience.</p>
            
            <h3><i class="fas fa-lock"></i> How Do We Protect Your Information?</h3>
            <p>We do not use vulnerability scanning and/or scanning to PCI standards. We do not use Malware Scanning. Your personal information is contained behind secured networks and is only accessible by a limited number of persons who have special access rights to such systems, and are required to keep the information confidential.</p>
            
            <p>In addition, all sensitive/credit information you supply is encrypted via Secure Socket Layer (SSL) technology. We implement a variety of security measures when a user enters, submits, or accesses their information to maintain the safety of your personal information. All transactions are processed through a gateway provider and are not stored or processed on our servers.</p>
            
            <h3><i class="fas fa-exclamation-triangle"></i> Special Precaution</h3>
            <p>There lies, in the name of Hindus News, multiple fake websites and facebook pages and unauthorized/ unwanted facebook groups in online. Hindus News shall hold no responsibility for any content generated, published or shared in those fake websites, unauthorized pages & groups in social media.</p>
            
            <h3><i class="fas fa-cookie-bite"></i> Do We Use 'Cookies'?</h3>
            <p>Hindus News does not collect any user data based on cookies, nor does it store any sort of user information that may be personal to the user. If a third party associated with the Hindus News website collects user cookies upon your visit to the Hindus News website, Hindus News does not control the use of these cookies therefore you should check the relevant third-party website.</p>
            
            <h3><i class="fas fa-share-alt"></i> Third-Party Disclosure</h3>
            <p>We do not sell, trade, or otherwise transfer to outside parties your Personally Identifiable Information unless we provide users with advance notice. This does not include website hosting partners and other parties who assist us in operating our website, conducting our business, or serving our users, so long as those parties agree to keep this information confidential. We may also release information when it's release is appropriate to comply with the law, enforce our site policies, or protect ours or others' rights, property or safety. However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.</p>
            
            <h3><i class="fas fa-external-link-alt"></i> Third-Party Links</h3>
            <p>Occasionally, at our discretion, we may include or offer third-party products or services on our website. These third-party sites have separate and independent privacy policies. We therefore have no responsibility or liability for the content and activities of these linked sites. Nonetheless, we seek to protect the integrity of our site and welcome any feedback about these sites.</p>
            
            <h3><i class="fab fa-google"></i> Google</h3>
            <p>Google's advertising requirements can be summed up by Google's Advertising Principles. They are put in place to provide a positive experience for users. <a href="https://support.google.com/adwordspolicy/answer/1316548?hl=en" target="_blank">Learn more</a></p>
            
            <h3><i class="fas fa-chart-line"></i> Google Analytics</h3>
            <p>Google Analytics is a web analytics service offered by Google that tracks and reports website traffic. Google uses the data collected to track and monitor the use of our Service. This data is shared with other Google services. Google may use the collected data to contextualise and personalise the ads of its own advertising network.</p>
            
            <p>You can opt-out of having made your activity on the Service available to Google Analytics by installing the Google Analytics opt-out browser add-on. The add-on prevents the Google Analytics JavaScript (ga.js, analytics.js, and dc.js) from sharing information with Google Analytics about visits activity.</p>
            
            <p>For more information on the privacy practices of Google, please visit the Google Privacy & Terms web page: <a href="https://policies.google.com/privacy?hl=en" target="_blank">https://policies.google.com/privacy?hl=en</a></p>
            
            <h3><i class="fas fa-envelope"></i> Contact Information</h3>
            <p>For privacy concerns, the contact point is:</p>
            <p><strong>Email:</strong> <a href="mailto:hindusnewsbd@gmail.com">hindusnewsbd@gmail.com</a></p>
        </div>
    </div>
</div>

<!-- Ads Popup -->
<div id="adsPopup" class="policy-popup-overlay" onclick="closeAdsPopup(event)">
    <div class="policy-popup-container" style="max-width: 900px;" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-ad"></i> বিজ্ঞাপন তালিকা</h2>
            <button class="policy-popup-close" onclick="closeAdsPopup()">&times;</button>
        </div>
        <div class="policy-popup-content" style="padding: 15px; text-align: center;">
            <img src="https://hindus-news.com/img/ads.jpeg" alt="বিজ্ঞাপন তালিকা" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
    </div>
</div>

<!-- Ekadashi Popup -->
<div id="ekadashiPopup" class="policy-popup-overlay" onclick="closeEkadashiPopup(event)">
    <div class="policy-popup-container" style="max-width: 900px;" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-calendar-alt"></i> একাদশী ২০২৬</h2>
            <button class="policy-popup-close" onclick="closeEkadashiPopup()">&times;</button>
        </div>
        <div class="policy-popup-content" style="padding: 15px; text-align: center;">
            <img src="https://hindus-news.com/img/akadishi.jpeg" alt="একাদশী ২০২৬" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
    </div>
</div>

<!-- Scriptures Popup -->
<div id="scripturesPopup" class="policy-popup-overlay" onclick="closeScripturesPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-book-open"></i> ধর্মীয় গ্রন্থ</h2>
            <button class="policy-popup-close" onclick="closeScripturesPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <p style="background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff6600;">আপনার জন্য বাংলায় হিন্দু ধর্মগ্রন্থের কিছু নির্ভরযোগ্য ওয়েবসাইট এবং পিডিএফ লিংক নিচে দেওয়া হলো। এখানে আপনি গীতা, রামায়ণ, মহাভারত, বেদ এবং পুরান পড়তে বা ডাউনলোড করতে পারবেন।</p>
            
            <h3><i class="fas fa-om"></i> ১. শ্রীমদ্ভগবদ্গীতা (Srimad Bhagavad Gita)</h3>
            <p>গীতা হলো হিন্দু ধর্মের মূল গ্রন্থ। বাংলায় গীতা পড়ার জন্য নিচের লিংকগুলো দেখুন:</p>
            <ul>
                <li><strong>গীতা সুপ্রীম (ইস্কন):</strong> এখানে আপনি শ্লোক এবং তাৎপর্যসহ সম্পূর্ণ গীতা বাংলায় পড়তে পারবেন।<br><a href="https://vedabase.io/bn/library/bg/" target="_blank"><i class="fas fa-external-link-alt"></i> এখানে পড়ুন</a></li>
                <li><strong>গীতা পিডিএফ (Gita Press):</strong> গীতা প্রেসের আসল বাংলা অনুবাদ।<br><a href="https://archive.org/details/SrimadBhagavadGita_201607/mode/2up" target="_blank"><i class="fas fa-download"></i> পিডিএফ ডাউনলোড করুন</a></li>
            </ul>
            
            <h3><i class="fas fa-feather-alt"></i> ২. রামায়ণ ও মহাভারত (Ramayana & Mahabharata)</h3>
            <p>বাংলায় কৃত্তিবাসী রামায়ণ এবং কাশীরাম দাসের মহাভারত সবচেয়ে জনপ্রিয়।</p>
            <ul>
                <li><strong>বাল্মীকি রামায়ণ (বাংলা অনুবাদ):</strong><br><a href="https://archive.org/details/ValmikiRamayanBengali/mode/2up" target="_blank"><i class="fas fa-external-link-alt"></i> অনলাইন পড়ুন বা ডাউনলোড করুন</a></li>
                <li><strong>কাশীরাম দাসের মহাভারত (সমগ্র):</strong><br><a href="https://archive.org/details/MahabharataKashiramDas" target="_blank"><i class="fas fa-download"></i> মহাভারত পিডিএফ ডাউনলোড</a></li>
            </ul>
            
            <h3><i class="fas fa-scroll"></i> ৩. বেদ ও উপনিষদ (Vedas & Upanishads)</h3>
            <p>চার বেদ এবং প্রধান উপনিষদগুলো বাংলায় পড়ার জন্য:</p>
            <ul>
                <li><strong>বেদ সমগ্র (ঋগ্বেদ, সামবেদ, যজুর্ভেদ, অথর্ববেদ):</strong> হরফ প্রকাশনীর বিখ্যাত অনুবাদ।<br><a href="https://archive.org/details/RigVedaSamhita_Bengali" target="_blank"><i class="fas fa-download"></i> ঋগ্বেদ সংহিতা ডাউনলোড</a></li>
                <li><strong>উপনিষদ গ্রন্থাবলী (অখণ্ড):</strong> স্বামী গম্ভীরানন্দ (রামকৃষ্ণ মঠ) অনূদিত।<br><a href="https://archive.org/details/UpanishadGranthavaliVol1SwamiGambhiriananda" target="_blank"><i class="fas fa-external-link-alt"></i> উপনিষদ পড়ুন</a></li>
            </ul>
            
            <h3><i class="fas fa-praying-hands"></i> ৪. শ্রীরামকৃষ্ণ ও বিবেকানন্দ সাহিত্য</h3>
            <p>বাঙালি হিন্দুদের জন্য এই গ্রন্থগুলো অত্যন্ত গুরুত্বপূর্ণ:</p>
            <ul>
                <li><strong>শ্রীশ্রীরামকৃষ্ণকথামৃত:</strong> শ্রী ম -এর লেখা মূল বই।<br><a href="https://archive.org/details/SriSriRamakrishnaKathamrita" target="_blank"><i class="fas fa-download"></i> কথামৃত পিডিএফ</a></li>
                <li><strong>স্বামী বিবেকানন্দের বাণী ও রচনা:</strong><br><a href="https://archive.org/details/SwamiVivekanandaBaniORachanaVol1" target="_blank"><i class="fas fa-external-link-alt"></i> সম্পূর্ণ রচনাবলী</a></li>
            </ul>
            
            <h3><i class="fas fa-archive"></i> ৫. অন্যান্য পুরাণ ও ধর্মগ্রন্থের সংগ্রহ</h3>
            <p>যদি আপনি এক জায়গায় সব বই পেতে চান, তবে এই সাইটগুলো দেখতে পারেন:</p>
            <ul>
                <li><strong>শ্রীমদ ভাগবত মহাপুরাণ:</strong><br><a href="https://archive.org/details/SrimadBhagavatamMahapurana-BengaliTranslation" target="_blank"><i class="fas fa-download"></i> ডাউনলোড লিংক</a></li>
                <li><strong>আর্কাইভ ডট অর্গ (Archive.org):</strong> এখানে সার্চ বক্সে "Bangla Hindu Book" লিখলে হাজার হাজার বই পাবেন।</li>
            </ul>
            
            <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p style="margin: 0;"><i class="fas fa-mobile-alt" style="color: #4CAF50;"></i> <strong>মোবাইল অ্যাপ পরামর্শ:</strong><br>আপনি যদি মোবাইলে পড়তে পছন্দ করেন, তবে Google Play Store এ গিয়ে <strong>"Bengali Gita"</strong> বা <strong>"Sanatan Dharma Books"</strong> লিখে সার্চ দিলে অনেক ভালো অ্যাপ পাবেন যেখানে ইন্টারনেট ছাড়াও পড়া যায়।</p>
            </div>
        </div>
    </div>
</div>

<!-- About Us Popup -->
<div id="aboutPopup" class="policy-popup-overlay" onclick="closeAboutPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-info-circle"></i> আমাদের সম্পর্কে (About Us)</h2>
            <button class="policy-popup-close" onclick="closeAboutPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <div style="text-align: center; margin-bottom: 25px;">
                <h3 style="font-size: 24px; color: #ff0000; margin-bottom: 5px;"><i>Voice of Hinduism</i></h3>
                <p style="font-size: 16px; color: #666;">স্বাগতম হিন্দুস নিউজ (Hindus News)-এ।</p>
            </div>
            
            <h3><i class="fas fa-users"></i> আমাদের পরিচয়</h3>
            <p>"হিন্দুস নিউজ" একটি সম্পূর্ণ অরাজনৈতিক এবং মানবাধিকারের ভিত্তিতে পরিচালিত অনলাইন নিউজ পোর্টাল। আমরা কোনো বিশেষ রাজনৈতিক দল বা মতাদর্শের মুখপাত্র নই। আমাদের অবস্থান সর্বদা সত্য, ন্যায় এবং মানবাধিকারের পক্ষে।</p>
            
            <h3><i class="fas fa-bullseye"></i> আমাদের লক্ষ্য ও উদ্দেশ্য</h3>
            <p>আমাদের মূল লক্ষ্য অত্যন্ত সুস্পষ্ট—বাংলাদেশের সংখ্যালঘু হিন্দু সম্প্রদায়ের বাস্তব সমস্যা, তাদের ওপর হওয়া বিভিন্ন নির্যাতন, বৈষম্য এবং তাদের ন্যায্য অধিকারের বিষয়গুলো সাহসিকতার সাথে তুলে ধরা।</p>
            <p>আমরা বিশ্বাস করি, সমাজের পিছিয়ে পড়া বা নিপীড়িত জনগোষ্ঠীর কণ্ঠস্বর হয়ে ওঠাই সংবাদমাধ্যমের অন্যতম প্রধান দায়িত্ব। আমরা সেই দায়িত্ব পালনে প্রতিশ্রুতিবদ্ধ। আমরা কেবল সংবাদ পরিবেশন করি না, বরং সংবাদের পেছনের সত্য ঘটনাটি বস্তুনিষ্ঠভাবে পাঠকের সামনে উপস্থাপন করার চেষ্টা করি।</p>
            
            <h3><i class="fas fa-handshake"></i> আমাদের অঙ্গীকার</h3>
            <p>আমরা সংবাদ পরিবেশনে নিরপেক্ষতা এবং সর্বোচ্চ পেশাদারিত্ব বজায় রাখতে বদ্ধপরিকর। আবেগের ঊর্ধ্বে উঠে সঠিক তথ্য ও প্রমাণভিত্তিক সংবাদ প্রচারই আমাদের নীতি। সাম্প্রদায়িক সম্প্রীতি বজায় রাখা এবং সংখ্যালঘুদের অধিকার রক্ষায় আমরা সর্বদা সোচ্চার থাকব।</p>
            
            <p style="background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff0000; font-style: italic; text-align: center;">সত্য ও ন্যায়ের পথে আমাদের এই যাত্রায় আপনারা আমাদের সঙ্গী হবেন, এটাই আমাদের প্রত্যাশা।</p>
            
            <h3><i class="fas fa-address-book"></i> যোগাযোগ</h3>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <p style="margin-bottom: 10px;"><i class="fas fa-globe" style="color: #ff0000; width: 25px;"></i> <strong>ওয়েবসাইট:</strong> <a href="http://www.hindus-news.com" target="_blank">www.hindus-news.com</a></p>
                <p style="margin-bottom: 10px;"><i class="fas fa-envelope" style="color: #ff0000; width: 25px;"></i> <strong>ইমেইল:</strong> <a href="mailto:hindusnewsbd@gmail.com">hindusnewsbd@gmail.com</a></p>
                <p style="margin-bottom: 0;"><i class="fas fa-phone" style="color: #ff0000; width: 25px;"></i> <strong>ফোন:</strong> <a href="tel:+8801890890920">+880 1890890920</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Ads Policy Popup -->
<div id="adsPolicyPopup" class="policy-popup-overlay" onclick="closeAdsPolicyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-bullhorn"></i> বিজ্ঞাপন প্রকাশের নীতিমালা</h2>
            <button class="policy-popup-close" onclick="closeAdsPolicyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <p class="policy-website"><i class="fas fa-newspaper"></i> <strong>হিন্দুস নিউজ (Hindus News)</strong></p>
            
            <p style="font-style: italic; background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #ff0000;">"হিন্দুস নিউজ" তার পাঠক এবং বিজ্ঞাপনদাতা উভয়ের প্রতি শ্রদ্ধাশীল। আমাদের অনলাইন পোর্টালে বিজ্ঞাপন প্রকাশের ক্ষেত্রে একটি স্বচ্ছ ও পেশাদার সম্পর্ক বজায় রাখার লক্ষ্যে আমরা নিম্নলিখিত নীতিমালা অনুসরণ করি। যেকোনো ব্যক্তি বা প্রতিষ্ঠান আমাদের সাইটে বিজ্ঞাপন প্রদানে ইচ্ছুক হলে, তাদের এই শর্তাবলী মেনে চলতে হবে।</p>
            
            <h3><i class="fas fa-balance-scale"></i> ১. সাধারণ নীতিমালা ও সম্পাদকীয় স্বাধীনতা</h3>
            <ul>
                <li><strong>সম্পাদকীয় স্বাধীনতা:</strong> হিন্দুস নিউজের সম্পাদকীয় নীতি সম্পূর্ণ স্বাধীন। বিজ্ঞাপনের সাথে সংবাদ বা সম্পাদকীয় মতামতের কোনো সম্পর্ক নেই। কোনো বিজ্ঞাপনদাতা অর্থের বিনিময়ে সংবাদের বিষয়বস্তু বা সংবাদ পরিবেশনকে প্রভাবিত করতে পারবেন না।</li>
                <li><strong>সংবাদ বনাম বিজ্ঞাপন:</strong> সংবাদ এবং বিজ্ঞাপনের মধ্যে স্পষ্ট পার্থক্য বজায় রাখা হবে। যদি কোনো কনটেন্ট অর্থের বিনিময়ে প্রকাশিত হয় (Advertorial বা Sponsored Content), তবে তা স্পষ্টভাবে 'বিজ্ঞাপন' (Advertisement), 'প্রযোজিত' (Sponsored), বা 'সৌজন্যে' (Powered by) হিসেবে চিহ্নিত থাকবে।</li>
            </ul>
            
            <h3><i class="fas fa-ban"></i> ২. নিষিদ্ধ বিজ্ঞাপনসমূহ</h3>
            <p>আমরা যেকোনো বিজ্ঞাপন গ্রহণ বা প্রত্যাখ্যান করার অধিকার সংরক্ষণ করি। নিম্নলিখিত ধরনের বিজ্ঞাপন আমাদের সাইটে প্রকাশ করা হবে না:</p>
            <ul>
                <li><strong>বেআইনি ও নিষিদ্ধ পণ্য:</strong> বাংলাদেশের প্রচলিত আইনে নিষিদ্ধ কোনো পণ্য বা সেবার বিজ্ঞাপন (যেমন: মাদক, অবৈধ অস্ত্র, চোরাচালানকৃত পণ্য ইত্যাদি)।</li>
                <li><strong>অশ্লীলতা ও কুরুচি:</strong> অশ্লীল, কুরুচিপূর্ণ, যৌন উদ্দীপক ছবি বা বার্তা সম্বলিত বিজ্ঞাপন।</li>
                <li><strong>সাম্প্রদায়িক সম্প্রীতি:</strong> কোনো নির্দিষ্ট ধর্ম, বর্ণ, গোষ্ঠী বা সম্প্রদায়ের প্রতি বিদ্বেষ ছড়ায় বা সাম্প্রদায়িক সম্প্রীতি বিনষ্ট করতে পারে এমন বিজ্ঞাপন।</li>
                <li><strong>জুয়া ও বাজি:</strong> যেকোনো ধরনের জুয়া, বেটিং সাইট বা লটারির বিজ্ঞাপন (সরকারি অনুমোদন ব্যতীত)।</li>
                <li><strong>ভুল তথ্য ও প্রতারণা:</strong> পাঠকদের বিভ্রান্ত করতে পারে এমন মিথ্যা তথ্য, অবৈজ্ঞানিক দাবি বা প্রতারণামূলক আর্থিক স্কিমের বিজ্ঞাপন।</li>
                <li><strong>মানহানিকর:</strong> কোনো ব্যক্তি বা প্রতিষ্ঠানের সম্মানহানি করে এমন বিজ্ঞাপন।</li>
                <li><strong>রাজনৈতিক আক্রমণ:</strong> উসকানিমূলক বা আক্রমণাত্মক রাজনৈতিক বিজ্ঞাপন।</li>
            </ul>
            
            <h3><i class="fas fa-user-tie"></i> ৩. বিজ্ঞাপনদাতার দায়বদ্ধতা</h3>
            <ul>
                <li><strong>তথ্যের সত্যতা:</strong> বিজ্ঞাপনে ব্যবহৃত ছবি, তথ্য, দাবি বা বিবৃতির সম্পূর্ণ দায়ভার বিজ্ঞাপনদাতার। বিজ্ঞাপনের বিষয়বস্তু কোনো কপিরাইট বা ট্রেডমার্ক আইন লঙ্ঘন করছে কিনা, তা নিশ্চিত করার দায়িত্ব বিজ্ঞাপনদাতার।</li>
                <li><strong>আইনি ঝামেলা:</strong> প্রকাশিত কোনো বিজ্ঞাপনের কারণে যদি কোনো আইনি জটিলতা সৃষ্টি হয়, তবে তার সমস্ত দায় বিজ্ঞাপনদাতাকে বহন করতে হবে। এ বিষয়ে হিন্দুস নিউজ কর্তৃপক্ষ কোনোভাবেই দায়ী থাকবে না।</li>
            </ul>
            
            <h3><i class="fas fa-edit"></i> ৪. বিজ্ঞাপন বাতিল ও সংশোধনের অধিকার</h3>
            <p>হিন্দুস নিউজ কর্তৃপক্ষ যেকোনো সময়, কোনো কারণ দর্শানো ব্যতিরেকে যেকোনো বিজ্ঞাপন বাতিল, স্থগিত বা সংশোধন করার পূর্ণ অধিকার সংরক্ষণ করে। যদি কোনো বিজ্ঞাপন আমাদের নীতিমালার পরিপন্থী বলে মনে হয়, তবে তা প্রকাশের পরেও সরিয়ে নেওয়া হতে পারে।</p>
            
            <h3><i class="fas fa-credit-card"></i> ৫. মূল্য পরিশোধ ও অন্যান্য শর্ত</h3>
            <p>বিজ্ঞাপন প্রকাশের পূর্বে নির্ধারিত মূল্য (রেট কার্ড অনুযায়ী বা আলোচনার ভিত্তিতে) অগ্রিম পরিশোধ করতে হবে। বিজ্ঞাপনের মূল্য তালিকা (Rate Card) বাজারের পরিস্থিতির ওপর ভিত্তি করে যেকোনো সময় পরিবর্তন হতে পারে।</p>
            
            <h3><i class="fas fa-exclamation-triangle"></i> ৬. দাবিত্যাগ (Disclaimer)</h3>
            <p style="background: #fff5f5; padding: 15px; border-radius: 8px; border-left: 4px solid #ff0000;">হিন্দুস নিউজ শুধুমাত্র বিজ্ঞাপন প্রকাশের একটি মাধ্যম। প্রকাশিত বিজ্ঞাপনের কোনো পণ্যের গুণমান, সেবার নিশ্চয়তা বা বিজ্ঞাপনদাতার দাবির সত্যতা সম্পর্কে হিন্দুস নিউজ কর্তৃপক্ষ কোনো প্রকার জামানত বা নিশ্চয়তা প্রদান করে না। বিজ্ঞাপনের ওপর ভিত্তি করে কোনো লেনদেন করার আগে পাঠকদের নিজ দায়িত্বে যাচাই-বাছাই করার পরামর্শ দেওয়া হলো।</p>
            
            <p style="text-align: center; margin-top: 20px; font-weight: 600; color: #666;"><i class="fas fa-info-circle"></i> এই নীতিমালা যেকোনো সময় পরিবর্তন বা পরিমার্জনের অধিকার হিন্দুস নিউজ কর্তৃপক্ষ সংরক্ষণ করে।</p>
        </div>
    </div>
</div>

<!-- Comment Policy Popup -->
<div id="commentPolicyPopup" class="policy-popup-overlay" onclick="closeCommentPolicyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-comments"></i> মন্তব্য প্রকাশের নীতিমালা</h2>
            <button class="policy-popup-close" onclick="closeCommentPolicyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <p class="policy-website" style="border-left-color: #ff6b00;"><i class="fas fa-newspaper"></i> <strong>হিন্দুস নিউজ (Hindus News)</strong></p>
            
            <p style="font-style: italic; background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff6b00;">হিন্দুস নিউজ (Hindus News) পাঠকদের মতামতকে গুরুত্ব দেয়। তবে একটি সুস্থ ও নিরাপদ আলোচনার পরিবেশ বজায় রাখার জন্য, আমাদের সাইটে মন্তব্য করার ক্ষেত্রে নিম্নলিখিত নীতিমালা অনুসরণ করা বাধ্যতামূলক:</p>
            
            <h3><i class="fas fa-list-check"></i> নীতিমালা সমূহ</h3>
            <ul class="comment-rules">
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> পাঠকদের মন্তব্য সংবাদের বিষয় বা বিষয়বস্তুর সাথে প্রাসঙ্গিক হতে হবে।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> বাংলাদেশের প্রচলিত আইন লঙ্ঘন করে কোনো মন্তব্য করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কোনো ব্যক্তি, জাতি, গোষ্ঠী বা ভাষার প্রতি অবমাননামূলক মন্তব্য করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কোনো ধর্ম বা কারও অনুভূতিতে আঘাত দিতে পারে এমন কোনো মন্তব্য করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কোনো ব্যক্তিকে হেয় করতে অবমাননামূলকভাবে কোনো প্রাণীবাচক নাম দেওয়া যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কোনো ব্যক্তির নাম বিকৃত করে মন্তব্য করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> মন্তব্যে অশ্লীল ও অশালীন ইঙ্গিতপূর্ণ শব্দ ব্যবহার করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কাউকে ব্যক্তিগতভাবে আক্রমণ করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> কাউকে ভয় দেখানো বা হুমকি দেওয়া যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> উদ্দেশ্যমূলক, আপত্তিকর বা ইঙ্গিতপূর্ণ নাম বা ছদ্মনাম ব্যবহার করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> নিউজের মন্তব্যে কোনো লিংক শেয়ার করা যাবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> বাংলা হরফে বাংলায় মন্তব্য করতে হবে। ইংরেজি হরফে বাংলা মন্তব্য করলে (Banglish) তা বাতিল করা হবে।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> অসংলগ্ন বাক্যের মন্তব্য প্রকাশ করা হবে না।</li>
                <li><i class="fas fa-check-circle" style="color: #ff6b00;"></i> দৃষ্টিকটু ভুল বা অসম্পূর্ণ বাক্যের মন্তব্য সম্পাদনা সাপেক্ষে প্রকাশ হতে পারে।</li>
            </ul>
            
            <h3><i class="fas fa-exclamation-circle"></i> দাবিত্যাগ ও অধিকার</h3>
            <ul class="disclaimer-list">
                <li><i class="fas fa-angle-right" style="color: #ff0000;"></i> পাঠকদের দ্বারা প্রকাশিত কোনো মতামতের জন্য হিন্দুস নিউজ (Hindus News) কর্তৃপক্ষ দায়বদ্ধ থাকবে না।</li>
                <li><i class="fas fa-angle-right" style="color: #ff0000;"></i> হিন্দুস নিউজ (Hindus News) কর্তৃপক্ষ যেকোনো মন্তব্য বাতিলের অধিকার রাখে।</li>
                <li><i class="fas fa-angle-right" style="color: #ff0000;"></i> কোনো ইস্যুতে পাঠকের মন্তব্য মুছে দেওয়ার অধিকার সংরক্ষণ করে হিন্দুস নিউজ (Hindus News)।</li>
            </ul>
        </div>
    </div>
</div>

<!-- Terms of Use Popup -->
<div id="termsPopup" class="policy-popup-overlay" onclick="closeTermsPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-file-contract"></i> Terms of Use</h2>
            <button class="policy-popup-close" onclick="closeTermsPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <p class="policy-website"><i class="fas fa-calendar-alt"></i> Last Updated: 27-1-2026 | <i class="fas fa-globe"></i> Website: <a href="http://hindus-news.com" target="_blank">http://hindus-news.com</a></p>
            
            <h3><i class="fas fa-check-circle"></i> 1. Acceptance of Terms</h3>
            <p>Welcome to Hindus News. Please read these Terms of Use ("Terms", "Terms of Use") carefully before using the http://hindus-news.com website (the "Service") operated by Hindus News ("us", "we", or "our").</p>
            <p>By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service. These Terms apply to all visitors, users, and others who access or use the Service.</p>
            
            <h3><i class="fas fa-edit"></i> 2. Changes to Terms</h3>
            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. What constitutes a material change will be determined at our sole discretion.</p>
            <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. It is your responsibility to check this page periodically for changes.</p>
            
            <h3><i class="fas fa-door-open"></i> 3. Access and Use of Service</h3>
            <p><strong>3.1. Age Restriction</strong><br>By using this Site, you represent that you are at least 18 years of age, or if you are under 18, you are at least 13 years of age and are using the site with the permission and supervision of a parent or legal guardian.</p>
            <p><strong>3.2. User Accounts</strong><br>If you create an account to use parts of our Service (such as commenting or newsletter subscription), you are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account or password. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
            
            <h3><i class="fas fa-copyright"></i> 4. Intellectual Property Rights</h3>
            <p>The Service and its original content, features, and functionality are and will remain the exclusive property of Hindus News and its licensors. The content published on this website (articles, photographs, graphics, logos) is protected by copyright, trademark, and other laws of both Bangladesh and foreign countries. Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of Hindus News.</p>
            <p><strong>You may not:</strong></p>
            <ul>
                <li>Republish material from Hindus News without attribution and a link back to the original source.</li>
                <li>Sell, rent, or sub-license material from the website.</li>
                <li>Reproduce, duplicate, copy or otherwise exploit material on our website for a commercial purpose.</li>
            </ul>
            
            <h3><i class="fas fa-users"></i> 5. User-Generated Content and Conduct</h3>
            <p>If the Service allows users to post comments or submit content:</p>
            <p><strong>5.1. Responsibility</strong><br>You are solely responsible for the content that you post on or through the Service, and any consequences of submitting and posting same.</p>
            <p><strong>5.2. Prohibited Content</strong><br>You agree not to post content that:</p>
            <ul>
                <li>Is unlawful, harassing, defamatory, abusive, threatening, harmful, obscene, profane, sexually oriented, or racially offensive.</li>
                <li>Encourages conduct that could constitute a criminal offense, give rise to civil liability, or otherwise violate any applicable law or regulation.</li>
                <li>Infringes on any third party's copyright, trademark, patent, trade secret, or other proprietary rights.</li>
                <li>Contains spam, advertising, commercial solicitations, or promotional materials.</li>
            </ul>
            <p>We reserve the right, but have no obligation, to monitor and remove any content that we determine, in our sole discretion, violates these Terms.</p>
            
            <h3><i class="fas fa-exclamation-triangle"></i> 6. Official Channels Warning</h3>
            <p>As stated in our Privacy Policy, there are multiple fake websites, Facebook pages, and unauthorized groups posing as "Hindus News." Hindus News shall hold no responsibility for any content generated, published, or shared on these fake websites or unauthorized social media pages. We are only responsible for content published on our official domain: http://hindus-news.com.</p>
            
            <h3><i class="fas fa-external-link-alt"></i> 7. Third-Party Links</h3>
            <p>Our Service may contain links to third-party web sites or services that are not owned or controlled by Hindus News (e.g., Google advertising links, external news sources).</p>
            <p>Hindus News has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third-party web sites or services. You further acknowledge and agree that Hindus News shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>
            <p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit.</p>
            
            <h3><i class="fas fa-shield-alt"></i> 8. Disclaimer of Warranties</h3>
            <p>YOUR USE OF THE SERVICE IS AT YOUR SOLE RISK. THE SERVICE IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. THE SERVICE IS PROVIDED WITHOUT WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, NON-INFRINGEMENT, OR COURSE OF PERFORMANCE.</p>
            <p>Hindus News does not warrant that a) the Service will function uninterrupted, secure, or available at any particular time or location; b) any errors or defects will be corrected; c) the Service is free of viruses or other harmful components; or d) the results of using the Service will meet your requirements.</p>
            
            <h3><i class="fas fa-balance-scale"></i> 9. Limitation of Liability</h3>
            <p>IN NO EVENT SHALL HINDUS NEWS, NOR ITS DIRECTORS, EMPLOYEES, PARTNERS, AGENTS, SUPPLIERS, OR AFFILIATES, BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, INCLUDING WITHOUT LIMITATION, LOSS OF PROFITS, DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES, RESULTING FROM (I) YOUR ACCESS TO OR USE OF OR INABILITY TO ACCESS OR USE THE SERVICE; (II) ANY CONDUCT OR CONTENT OF ANY THIRD PARTY ON THE SERVICE; (III) ANY CONTENT OBTAINED FROM THE SERVICE; AND (IV) UNAUTHORIZED ACCESS, USE OR ALTERATION OF YOUR TRANSMISSIONS OR CONTENT.</p>
            
            <h3><i class="fas fa-gavel"></i> 10. Governing Law</h3>
            <p>The laws that govern "Terms of Use" of Hindus News and its relationship with the user is the laws of Bangladesh and any dispute regarding the use, retention, disclosure, leakage or dissemination of the information or date can only be raised in arbitration in accordance with the Arbitration Act, 2001. The place of arbitration shall be Dhaka, Bangladesh and the arbitral tribunal shall consist of three members. The courts of Bangladesh shall have exclusive jurisdiction on this matter.</p>
            
            <h3><i class="fas fa-envelope"></i> 11. Contact Us</h3>
            <p>If you have any questions about these Terms, please contact us at: <a href="mailto:hindusnewsbd@gmail.com">hindusnewsbd@gmail.com</a></p>
        </div>
    </div>
</div>

<style>
/* Privacy Policy Popup Styles */
.policy-popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    justify-content: center;
    align-items: center;
    padding: 20px;
    box-sizing: border-box;
    backdrop-filter: blur(5px);
}

.policy-popup-overlay.active {
    display: flex;
}

.policy-popup-container {
    background: #fff;
    border-radius: 16px;
    max-width: 800px;
    width: 100%;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    animation: popupSlideIn 0.3s ease;
}

@keyframes popupSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.policy-popup-header {
    background: linear-gradient(135deg, #ff0000, #cc0000);
    color: #fff;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
}

.policy-popup-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.policy-popup-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    font-size: 28px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.policy-popup-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.policy-popup-content {
    padding: 25px 30px;
    overflow-y: auto;
    max-height: calc(85vh - 80px);
    line-height: 1.8;
    color: #333;
}

.policy-popup-content .policy-website {
    background: #f8f9fa;
    padding: 12px 18px;
    border-radius: 8px;
    border-left: 4px solid #ff0000;
    margin-bottom: 20px;
}

.policy-popup-content .policy-website a {
    color: #ff0000;
    text-decoration: none;
    font-weight: 500;
}

.policy-popup-content h3 {
    color: #1a1a1a;
    font-size: 18px;
    margin: 25px 0 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.policy-popup-content h3 i {
    color: #ff0000;
    font-size: 16px;
}

.policy-popup-content p {
    margin-bottom: 15px;
    font-size: 15px;
}

.policy-popup-content ul {
    margin: 10px 0 20px 20px;
    padding-left: 20px;
}

.policy-popup-content ul li {
    margin-bottom: 8px;
    font-size: 15px;
}

.policy-popup-content a {
    color: #ff0000;
    text-decoration: none;
    transition: color 0.3s;
}

.policy-popup-content a:hover {
    text-decoration: underline;
}

/* Comment Policy List Styles */
.comment-rules, .disclaimer-list {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.comment-rules li, .disclaimer-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 15px;
    margin-bottom: 8px;
    background: #fafafa;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
}

.comment-rules li i, .disclaimer-list li i {
    margin-top: 3px;
    flex-shrink: 0;
}

.disclaimer-list li {
    background: #fff5f5;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .policy-popup-overlay {
        padding: 10px;
    }
    
    .policy-popup-container {
        max-height: 90vh;
        border-radius: 12px;
    }
    
    .policy-popup-header {
        padding: 15px 18px;
    }
    
    .policy-popup-header h2 {
        font-size: 16px;
    }
    
    .policy-popup-close {
        width: 35px;
        height: 35px;
        font-size: 24px;
    }
    
    .policy-popup-content {
        padding: 18px 20px;
        max-height: calc(90vh - 70px);
    }
    
    .policy-popup-content h3 {
        font-size: 16px;
    }
    
    .policy-popup-content p,
    .policy-popup-content ul li {
        font-size: 14px;
    }
}
</style>

<script>
function openPrivacyPopup() {
    document.getElementById('privacyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePrivacyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('privacyPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openTermsPopup() {
    document.getElementById('dbTermsPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeTermsPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbTermsPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openCommentPolicyPopup() {
    document.getElementById('dbCommentPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCommentPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbCommentPolicyPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openAdsPopup() {
    document.getElementById('adsPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdsPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('adsPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openAdsPolicyPopup() {
    document.getElementById('dbAdsPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdsPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbAdsPolicyPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openAboutPopup() {
    document.getElementById('aboutPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAboutPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('aboutPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openScripturesPopup() {
    document.getElementById('scripturesPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeScripturesPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('scripturesPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openEkadashiPopup() {
    document.getElementById('ekadashiPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEkadashiPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('ekadashiPopup').classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrivacyPopup();
        closeTermsPopup();
        closeCommentPolicyPopup();
        closeAdsPopup();
        closeAdsPolicyPopup();
        closeAboutPopup();
        closeScripturesPopup();
        closeEkadashiPopup();
        closePrivacyPolicyPopup();
        closeAboutUsPopup();
        closeAdsListPopup();
    }
});

// Dynamic popups from database
function openPrivacyPolicyPopup() {
    document.getElementById('dbPrivacyPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePrivacyPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbPrivacyPolicyPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openAboutUsPopup() {
    document.getElementById('dbAboutUsPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAboutUsPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbAboutUsPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openAdsListPopup() {
    document.getElementById('dbAdsListPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdsListPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbAdsListPopup').classList.remove('active');
    document.body.style.overflow = '';
}
</script>

<!-- Dynamic Database Popups -->
<!-- Privacy Policy Popup (from DB) -->
<div id="dbPrivacyPolicyPopup" class="policy-popup-overlay" onclick="closePrivacyPolicyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-shield-alt"></i> গোপনীয়তার নীতি</h2>
            <button class="policy-popup-close" onclick="closePrivacyPolicyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <?php if (!empty($basic_info['privacy_policy'])): ?>
                <?php echo $basic_info['privacy_policy']; ?>
            <?php else: ?>
                <p>গোপনীয়তার নীতি এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Terms Popup (from DB) -->
<div id="dbTermsPopup" class="policy-popup-overlay" onclick="closeTermsPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-file-contract"></i> শর্তাবলী</h2>
            <button class="policy-popup-close" onclick="closeTermsPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <?php if (!empty($basic_info['terms'])): ?>
                <?php echo $basic_info['terms']; ?>
            <?php else: ?>
                <p>শর্তাবলী এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- About Us Popup (from DB) -->
<div id="dbAboutUsPopup" class="policy-popup-overlay" onclick="closeAboutUsPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-info-circle"></i> আমাদের সম্পর্কে</h2>
            <button class="policy-popup-close" onclick="closeAboutUsPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <?php if (!empty($basic_info['about_us'])): ?>
                <?php echo $basic_info['about_us']; ?>
            <?php else: ?>
                <p>আমাদের সম্পর্কে তথ্য এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Comment Policy Popup (from DB) -->
<div id="dbCommentPolicyPopup" class="policy-popup-overlay" onclick="closeCommentPolicyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-comments"></i> মন্তব্য প্রকাশের নীতিমালা</h2>
            <button class="policy-popup-close" onclick="closeCommentPolicyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <?php if (!empty($basic_info['comment_policy'])): ?>
                <?php echo $basic_info['comment_policy']; ?>
            <?php else: ?>
                <p>মন্তব্য প্রকাশের নীতিমালা এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ads Policy Popup (from DB) -->
<div id="dbAdsPolicyPopup" class="policy-popup-overlay" onclick="closeAdsPolicyPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-bullhorn"></i> বিজ্ঞাপন প্রকাশের নীতিমালা</h2>
            <button class="policy-popup-close" onclick="closeAdsPolicyPopup()">&times;</button>
        </div>
        <div class="policy-popup-content">
            <?php if (!empty($basic_info['advertisement_policy'])): ?>
                <?php echo $basic_info['advertisement_policy']; ?>
            <?php else: ?>
                <p>বিজ্ঞাপন প্রকাশের নীতিমালা এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ads List Popup (from DB) -->
<div id="dbAdsListPopup" class="policy-popup-overlay" onclick="closeAdsListPopup(event)">
    <div class="policy-popup-container" style="max-width: 900px;" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-ad"></i> বিজ্ঞাপন তালিকা</h2>
            <button class="policy-popup-close" onclick="closeAdsListPopup()">&times;</button>
        </div>
        <div class="policy-popup-content" style="text-align: center;">
            <?php if (!empty($basic_info['advertisement_list'])): ?>
                <?php echo $basic_info['advertisement_list']; ?>
            <?php else: ?>
                <p>বিজ্ঞাপন তালিকা এখনো আপডেট করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</html>