<?php
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
                                    FROM podcasts 
                                    WHERE is_active = 1 
                                    AND DATE(created_at) = ? 
                                    ORDER BY created_at DESC 
                                    LIMIT 8");
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT id, title, subtitle, thumbnail, youtube_link, created_at 
                                    FROM podcasts 
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
               FROM news n
               JOIN category c ON n.category_id = c.id
               WHERE n.is_active = 1
               ORDER BY RAND()
               LIMIT 3";

$result_random = $conn->query($sql_random);

// Fetch opinions for slider (last 10 only)
$sql_opinions = "SELECT id, image, link
                 FROM opinions 
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>HINDUS NEWS</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="logo.png" />
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
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .podcast-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .podcast-thumbnail {
            position: relative;
            height: 180px;
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
        
        @media (max-width: 1024px) {
            .podcast-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .podcast-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .podcast-thumbnail {
                height: 150px;
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
            .podcast-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .podcast-thumbnail {
                height: 120px;
            }
            .podcast-headline {
                font-size: 12px;
            }
            .podcast-excerpt {
                display: none;
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
<amp-auto-ads type="adsense"
        data-ad-client="ca-pub-8181164230956209">
</amp-auto-ads>
    <header class="primary-header-wrapper">
       
        <div class="header-top-section">
            <div class="header-content-flex">
                <a href="#" class="brand-logo-container" style="display:inline-flex;align-items:center;gap:8px;">

                    <!-- White circle -->
                    <span id="blinkCircle" style="
        width:55px;
        height:55px;
        background:white;
        border-radius:50%;
        display:inline-block;
        transform:scale(1);
        opacity:1;
    "></span>

                    <img src="logo.png" alt="News Portal Logo" class="logo-image-element">
                </a>


                <div class="right-section">
                    <div class="search-icon"><i class="fas fa-search"></i></div>
                    <div class="bangla-text">BANGLA</div>
                </div>





                <div class="mobile-actions-group">
                    <button class="header-button-element" onclick="toggleMobileSearch()">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </button>
                    <button class="header-button-element">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7.5V9C15 12.18 12.18 15 9 15S3 12.18 3 9V7L9 6.5V9C9 10.66 10.34 12 12 12S15 10.66 15 9Z" />
                        </svg>
                    </button>
                </div>


            </div>
        </div>
        </div>

        <nav class="main-navigation-section">
            <div class="nav-content-container">
                <ul class="primary-nav-menu">
                    <!-- Home is static -->
                    <li class="nav-menu-item">
                        <a href="index.php" class="nav-link-element">প্রথম পাতা</a>
                    </li>

                    <!-- Load categories dynamically -->
                    <?php
    include 'connection.php';

    $sql = "SELECT * FROM category ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $category_id = $row["id"];
            $category_name = htmlspecialchars($row["name"]); // নিরাপদ করার জন্য

            echo "<li class='nav-menu-item'>
                    <a href='category.php?id=$category_id' class='nav-link-element'>
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

            $sql = "SELECT news.headline, news.slug AS news_slug, category.slug AS category_slug
                    FROM news
                    JOIN category ON news.category_id = category.id
                    WHERE news.is_active = 1
                    ORDER BY news.created_at DESC
                    LIMIT 15";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $url = "news.php?path=" . urlencode($row['category_slug'] . '/' . $row['news_slug']);
                    echo '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($row['headline']) . '</a>';
                }
            }

            $conn->close();
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
    FROM news
    LEFT JOIN category ON news.category_id = category.id
    LEFT JOIN reporter ON news.reporter_id = reporter.id
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
                            <a href="news.php?path=<?= urlencode($heroNews[0]['category_slug'] . '/' . $heroNews[0]['slug']); ?>">
                                <img src="<?= $uploadPath . htmlspecialchars($heroNews[0]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[0]['headline']); ?>">
                            </a>
                        </div>
                        <div class="featured-large-content">
                            <h2 class="featured-large-headline">
                                <a href="news.php?path=<?= urlencode($heroNews[0]['category_slug'] . '/' . $heroNews[0]['slug']); ?>">
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
                                <a href="news.php?path=<?= urlencode($heroNews[$i]['category_slug'] . '/' . $heroNews[$i]['slug']); ?>">
                                    <img src="<?= $uploadPath . htmlspecialchars($heroNews[$i]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[$i]['headline']); ?>">
                                </a>
                            </div>
                            <div class="side-news-content">
                                <h4 class="side-news-title">
                                    <a href="news.php?path=<?= urlencode($heroNews[$i]['category_slug'] . '/' . $heroNews[$i]['slug']); ?>">
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
                            <a href="news.php?path=<?= urlencode($heroNews[$i]['category_slug'] . '/' . $heroNews[$i]['slug']); ?>">
                                <img src="<?= $uploadPath . htmlspecialchars($heroNews[$i]['image_url']); ?>" alt="<?= htmlspecialchars($heroNews[$i]['headline']); ?>">
                            </a>
                        </div>
                        <h4 class="bottom-news-headline">
                            <a href="news.php?path=<?= urlencode($heroNews[$i]['category_slug'] . '/' . $heroNews[$i]['slug']); ?>">
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
                        <img src="https://www.hindus-news.com/img/hindusnews_add_slide_img.jpeg" alt="ত্রয়োদশ জাতীয় সংসদ নির্বাচন, ২০২৬">
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
                            <a href="news.php?path=<?= urlencode($heroNews[$i]['category_slug'] . '/' . $heroNews[$i]['slug']); ?>">
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
                                    FROM news n 
                                    JOIN category c ON n.category_id = c.id 
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
                            <a href="news.php?path=<?= urlencode($popNews['category_slug'] . '/' . $popNews['slug']); ?>">
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
                <a href="all-news.php" class="hero-center-btn">সব খবর »</a>
                
                <!-- Category Tags -->
                <div class="hero-center-tags">
                    <div class="hero-center-tags-title">আরও দেখুন</div>
                    <div class="hero-center-tags-list">
                        <?php
                        $sql_cat = "SELECT name, slug FROM category WHERE is_active = 1 ORDER BY id ASC LIMIT 10";
                        $result_cat = $conn->query($sql_cat);
                        if ($result_cat && $result_cat->num_rows > 0):
                            while ($cat = $result_cat->fetch_assoc()):
                        ?>
                        <a href="category.php?slug=<?= urlencode($cat['slug']); ?>" class="hero-center-tag">
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
        FROM news n
        JOIN category c ON n.category_id = c.id
        WHERE n.category_id = (
            SELECT category_id 
            FROM news 
            WHERE is_active = 1
            GROUP BY category_id 
            ORDER BY COUNT(*) DESC 
            LIMIT 1
        ) AND n.is_active = 1
        ORDER BY n.created_at DESC
        LIMIT 4";

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
                <div class="video-tabs">
                    <button class="video-tab active">Hindus News</button>
                </div>
            </div>
            
            <div class="video-grid">
                <?php
                // Fetch latest 8 videos from news_video table
                $sql_video = "SELECT id, title, subtitle, thumbnail, youtube_link
                              FROM news_video
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
    // Fetch all categories ordered by ID (serial order)
    $sql_categories = "SELECT id, name, slug FROM category ORDER BY id ASC";
    $result_categories = $conn->query($sql_categories);
    
    $categories = [];
    while ($cat = $result_categories->fetch_assoc()) {
        $categories[] = $cat;
    }
    
    // Helper function for news URL
    function getNewsLink($news_item) {
        return "news.php?path=" . urlencode($news_item['category_slug'] . '/' . $news_item['news_slug']);
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
                     FROM news n
                     JOIN category c ON n.category_id = c.id
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
    $i = 0;
    ?>
    
    <div class="categories-wrapper">
    <?php 
    $totalCategories = count($allCategoryNews);
    while ($i < $totalCategories): 
        // Check if this is the last category - use lifestyle layout
        $isLastCategory = ($i == $totalCategories - 1);
        
        // Layout pattern: 0,1 = Entertainment, 2 = Hero-style, 3+ = Dual
        // First 2 categories use Entertainment style
        $useEntertainment = ($i == 0 || $i == 1);
        $useHeroStyle = ($i == 2);
        
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
                    <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Logo" class="section-logo">
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
            // Show podcast section after first category (i=0)
            if ($i == 0):
    ?>
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
                              FROM podcasts
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
        </div>
    </section>
    
    <script>
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
    <?php
            endif;
            $i++;
        else:
            // Single Category Style - Full Width
            $cat = $allCategoryNews[$i];
            $news = $cat['news'];
    ?>
        <section class="hero-style-section">
            <h2 class="section-title">
                <div class="left-part">
                    <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Logo" class="section-logo">
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
    endwhile; ?>
    </div>
    <!-- ===== END DYNAMIC CATEGORY SECTIONS ===== -->


    <div class="main-wrapper-content">
        <!-- Donation Section -->
        <div class="info-panel-box">
            <div class="panel-top-section">
                <div class="panel-icon-holder">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="panel-heading-text">ডোনেশন</div>
            </div>
            <div class="charity-items-layout">
                <div class="charity-single-item" onclick="handleCharityClick('১')">
                    <img src="https://www.hindus.news/_ipx/w_960/newsImages/1764693626488_YxV79t.png" alt="ডোনেশন"
                        class="charity-photo">
                    <div class="charity-info-text">ডোনেশন প্রয়োজন, হিন্দু সম্প্রদায়ের সাহায্যের জন্য সকলকে এগিয়ে আসার
                        আহব্বান!</div>
                </div>
                <div class="charity-single-item" onclick="handleCharityClick('২')">
                    <img src="https://www.hindus.news/_ipx/w_960/newsImages/1764692198071_pLdQcw.jpg" alt="ডোনেশন"
                        class="charity-photo">
                    <div class="charity-info-text">ডোনেশন প্রয়োজন, হিন্দু সম্প্রদায়ের সাহায্যের জন্য সকলকে এগিয়ে আসার
                        আহব্বান!</div>
                </div>
                <div class="charity-single-item" onclick="handleCharityClick('৩')">
                    <img src="https://www.hindus.news/_ipx/w_960/newsImages/1764574564540_pGXi5d.jpg" alt="ডোনেশন"
                        class="charity-photo">
                    <div class="charity-info-text">ডোনেশন প্রয়োজন, হিন্দু সম্প্রদায়ের সাহায্যের জন্য সকলকে এগিয়ে আসার
                        আহব্বান!</div>
                </div>

            </div>
        </div>

        <!-- News Area Section -->
        <div class="info-panel-box">
            <div class="panel-top-section">
                <div class="panel-icon-holder">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="panel-heading-text">এলাকার সংবাদ</div>
            </div>
            <div class="location-filters-area">
                <div class="location-filter-wrapper">
                    <label class="filter-field-label">বিভাগ</label>
                    <select class="location-selector" id="regional-division-picker">
                        <option value="">নির্বাচন করুন</option>
                        <option value="ঢাকা">ঢাকা</option>
                        <option value="চট্টগ্রাম">চট্টগ্রাম</option>
                        <option value="রাজশাহী">রাজশাহী</option>
                        <option value="খুলনা">খুলনা</option>
                        <option value="বরিশাল">বরিশাল</option>
                        <option value="সিলেট">সিলেট</option>
                        <option value="রংপুর">রংপুর</option>
                        <option value="ময়মনসিংহ">ময়মনসিংহ</option>
                    </select>
                </div>

                <div class="location-filter-wrapper">
                    <label class="filter-field-label">জেলা</label>
                    <select class="location-selector" id="district-zone-picker">
                        <option value="">নির্বাচন করুন</option>
                        <option value="ঢাকা">ঢাকা</option>
                        <option value="গাজীপুর">গাজীপুর</option>
                        <option value="নারায়ণগঞ্জ">নারায়ণগঞ্জ</option>
                    </select>
                </div>

                <div class="location-filter-wrapper">
                    <label class="filter-field-label">উপজেলা</label>
                    <select class="location-selector" id="subdistrict-area-picker">
                        <option value="">নির্বাচন করুন</option>
                        <option value="মিরপুর">মিরপুর</option>
                        <option value="মোহাম্মদপুর">মোহাম্মদপুর</option>
                        <option value="উত্তরা">উত্তরা</option>
                    </select>
                </div>

                <button class="news-search-action" onclick="executeNewsSearch()">
                    <i class="fas fa-search"></i>
                    অনুসন্ধান করুন
                </button>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="info-panel-box">
            <div class="panel-top-section">
                <div class="panel-icon-holder">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="panel-heading-text">আজকের প্রশ্ন</div>
            </div>
            <div class="trivia-question-prompt">
                ভগবান শ্রী কৃষ্ণের কয়েকটি নাম?
            </div>
            <div class="trivia-answers-list">
                <div class="trivia-choice-btn" onclick="pickTriviaAnswer(this, '100')">100</div>
                <div class="trivia-choice-btn" onclick="pickTriviaAnswer(this, '108')">108</div>
                <div class="trivia-choice-btn" onclick="pickTriviaAnswer(this, '200')">200</div>
                <div class="trivia-choice-btn" onclick="pickTriviaAnswer(this, '105')">105</div>
            </div>
        </div>
    </div>
    <!-- Latest -->
    <section class="bg0 p-t-60 p-b-35">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 p-b-20">
                    <?php
include 'connection.php';
date_default_timezone_set('Asia/Dhaka');

$uploadPath = 'Admin/img/';

// Fetch last 6 news with category and reporter
$sql = "
  SELECT 
    news.*, 
    category.name AS category_name, 
    category.slug AS category_slug,
    reporter.name AS reporter_name
FROM news
LEFT JOIN category ON news.category_id = category.id
LEFT JOIN reporter ON news.reporter_id = reporter.id
WHERE news.is_active = 1
ORDER BY news.created_at DESC
LIMIT 4 OFFSET 4
";

$result = $conn->query($sql);
?>

                    <div class="how2 how2-cl4 flex-s-c m-r-10 m-r-0-sr991" style=" padding-left: 15px;">
                        <h3 class="f1-m-2 tab01-title" style="color:#222; font-weight:700;">
                            সর্বশেষ সংবাদ
                        </h3>
                    </div>

                    <div class="row p-t-35">
                        <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $newsUrl = "news.php?path=" . urlencode($row['category_slug'] . '/' . $row['slug']);
        ?>
                        <div class="col-sm-6 p-r-25 p-r-15-sr991">
                            <div class="m-b-45">
                                <a href="<?= $newsUrl; ?>" class="wrap-pic-w hov1 trans-03">
                                    <img src="<?= $uploadPath . htmlspecialchars($row['image_url']); ?>" alt="IMG">
                                </a>
                                <div class="p-t-16">
                                    <h5 class="p-b-5">
                                        <a href="<?= $newsUrl; ?>" class="f1-m-3 cl2 hov-cl10 trans-03">
                                            <?= htmlspecialchars($row['headline']); ?>
                                        </a>
                                    </h5>
                                    <span class="cl8">
                                        <span class="f1-s-3">
                                            <?= banglaDate($row['created_at']); ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
    }
}
?>

                    </div>

                </div>


                <div class="col-md-10 col-lg-4">
                    <div class="p-l-10 p-rl-0-sr991 p-b-20">
                        <!-- Video -->
                        <div class="p-b-55">
                            <div class="how2 how2-cl4 flex-s-c m-b-35">
                                <h3 class="f1-m-2 cl3 tab01-title">
                                    প্রতিবেদন ভিডিও
                                </h3>
                            </div>

                            <div>
                                <div class="wrap-pic-w pos-relative">
                                    <!-- YouTube iframe embed -->
                                    <iframe width="100%" height="180"
                                        src="https://www.youtube.com/embed/xFR7UnPKH6o?si=kpyp8On6uE4QDqR2"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen style="display: block;">
                                    </iframe>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for video -->



                        <!-- Subscribe -->


                        <!-- Tag -->
                        <div class="p-b-55">
                            <div class="how2 how2-cl4 flex-s-c m-b-30">
                                <h3 class="f1-m-2 cl3 tab01-title">
                                    ক্যাটাগরি
                                </h3>
                            </div>

                            <div class="flex-wr-s-s m-rl--5">
                                <?php
        include 'connection.php';

        $sql = "SELECT * FROM category ORDER BY id ASC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category_id = $row["id"];
                $category_name = htmlspecialchars($row["name"]); 

                echo "<a href='category.php?id=$category_id' 
                          class='flex-c-c size-h-2 bo-1-rad-20 bocl12 f1-s-1 cl8 hov-btn2 trans-03 
                                 p-rl-20 p-tb-5 m-all-5'>
                          $category_name
                      </a>";
            }
        } else {
            echo "<p>No categories found</p>";
        }
        ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>






    <!-- Back to top -->
    <div class="btn-back-to-top" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <span class="fas fa-angle-up"></span>
        </span>
    </div>

    <!-- Modal Video 01-->
    <div class="modal fade" id="modal-video-01" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" data-dismiss="modal">
            <div class="close-mo-video-01 trans-0-4" data-dismiss="modal" aria-label="Close">&times;</div>

            <div class="wrap-video-mo-01">
                <div class="video-mo-01">
                    <iframe src="https://www.youtube.com/embed/wJnBTPUQS5A?rel=0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-wrapper">
        <!-- Decorative Border -->
        <div class="decorative-border"></div>

        <!-- Main Footer Content -->
        <div class="footer-main">
            <!-- Logo Section -->
            <div class="footer-logo-section">
                <div class="logo-box">
                    <!-- Replace src with your actual logo image URL -->
                    <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Hindus News Logo"
                        class="logo-image">
                    <div class="logo-tagline">Voice of Hinduism</div>
                </div>
                <p class="footer-description">
                   হিন্দু’স নিউজ একটি সম্পূর্ণ অরাজনৈতিক ও মানবাধিকারভিত্তিক নিউজ পোর্টাল। দেশের সংখ্যালঘু হিন্দু সম্প্রদায়ের বাস্তব সমস্যা, নির্যাতন ও অধিকার নিয়ে বস্তুনিষ্ঠ সংবাদ উপস্থাপন করাই আমাদের মূল লক্ষ্য।
                </p>
            </div>

            <!-- Menu Columns -->
            <div class="footer-menus">
                <div class="menu-column">
                    <a href="#">গোপনীয়তার নীতি</a>
                    <a href="#">শর্তাবলী</a>
                    <a href="#">মতবা প্রকাশে নীতিমালা</a>
                    <a href="#">বিজ্ঞাপন তালিকা</a>
                </div>
                <div class="menu-column">
                    <a href="#">একাদশী</a>
                    <a href="#">পূজাপর্বন</a>
                    <a href="#">ধর্মীয় গ্রন্থ</a>
                    <a href="#">সংগঠন সংবাদ</a>
                </div>
                <div class="menu-column">
                    <a href="#">মঠ-মন্দির</a>
                    <a href="#">লাভ-জিহাদ</a>
                    <a href="#">ধর্মীয় গ্রন্থ</a>
                    <a href="#">সংগঠন সংবাদ</a>
                </div>
            </div>

            <!-- Contact Links -->
            <div class="footer-contact">
                <div class="contact-column">
                    <a href="#">একাদশী</a>
                    <a href="#">পূজাপর্বন</a>
                    <a href="#">ধর্মীয় গ্রন্থ</a>
                    <a href="#">সংগঠন সংবাদ</a>
                </div>
            </div>
        </div>

        <!-- Red Bar Section -->
        <div class="footer-red-section">
            <!-- Editor Info -->
            <div class="editor-section">
                <h3>প্রকাশক & সম্পাদক: শুভ চন্দ্র দে।</h3>
                <p>HINDUS NEWS মিডিয়া লিমিটেডের পক্ষে</p>
                <p>প্রকাশক কর্তৃক ঢাকা থেকে প্রকাশিত</p>
            </div>

            <!-- Social Media -->
            <div class="social-media-section">
                <h3 class="social-title">সোশ্যাল মিডিয়া</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="#1877f2">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="#ff0000">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="#E4405F">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Pinterest">
                        <svg viewBox="0 0 24 24" fill="#E60023">
                            <path
                                d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="contact-info-section">
                <h3 class="contact-info-title">যোগাযোগ</h3>
                <p class="contact-info-item">ফোন নম্বর: <a href="tel:+8801890890920">+88 01890890920</a></p>
                <p class="contact-info-item">ই-মেইল: <a href="mailto:news@hindus.news">news@hindus.news</a></p>
                <p class="contact-info-item">বিজ্ঞাপন: <a href="mailto:ads@hindus.news">ads@hindus.news</a></p>
            </div>
        </div>

        <!-- Copyright Section -->
        <div class="footer-copyright">
            <p>স্বত্ব © HINDUS NEWS মিডিয়া লিমিটেড ২০২৫ — ওয়েবসাইটের কোনো লেখা, ছবি, ভিডিও অনুমতি ছাড়া ব্যবহার বেআইনি।
            </p>
        </div>
    </div>

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

<script async custom-element="amp-auto-ads"
        src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js">
</script>
</html>