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

// Fetch news
$sql = "SELECT * FROM $tbl_news WHERE is_active = 1";
$result = $conn->query($sql);

$news = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
}

// Folder path for images
$uploadPath = 'Post_News/';



// Helper function for Bangla date
function banglaDate($date) {
    $engDATE = array(0,1,2,3,4,5,6,7,8,9,'January','February','March','April','May','June','July','August','September','October','November','December');
    $bangDATE = array('০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর');
    
    $format = date('d F', strtotime($date));
    $format = str_replace($engDATE, $bangDATE, $format);
    return $format;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>HINDUS NEWS</title>
	<meta charset="UTF-8">
     <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <script src="https://cdn.tiny.cloud/1/xik9chst9hfu1krjwmjep37qmvt8zvlco7x5sextpnr90jas/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<?php if (!empty($basic_info['image'])): ?>
	<link rel="icon" type="image/png" href="<?php echo htmlspecialchars($basic_info['image']); ?>"/>
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
	<link rel="stylesheet" type="text/css" href="css/index.css">
<!--===============================================================================================-->

<style>
@import url('https://fonts.maateen.me/solaiman-lipi/font.css');
        body{
            font-family: 'SolaimanLipi', sans-serif !important;
}
        
     .primary-header-wrapper {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }

        .header-top-section {
            padding: 10px 80px;
            border-bottom: 1px solid #e9ecef;
        }

        .header-content-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .brand-logo-container:hover {
            transform: scale(1.02);
        }

        .logo-image-element {
            height: 80px;
            width: auto;
            max-width: 300px;
            object-fit: contain;
        }

        .header-action-group {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .search-wrapper-container {
            position: relative;
        }

        .search-input-box {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input-box:focus-within {
            border-color: #495057;
            box-shadow: 0 0 0 3px rgba(73, 80, 87, 0.1);
        }

        .search-icon-svg {
            width: 16px;
            height: 16px;
            margin-right: 10px;
            fill: #6c757d;
        }

        .search-field-input {
            border: none;
            background: none;
            outline: none;
            padding: 0;
            font-size: 14px;
            width: 200px;
            color: #495057;
        }

        .search-field-input::placeholder {
            color: #6c757d;
        }

        .date-info-display {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .date-icon-svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            fill: #6c757d;
        }

        .header-button-element {
            background-color: white;
            color: black;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-button-element svg {
            width: 16px;
            height: 16px;
            fill: black;
        }

        .mobile-menu-button {
            display: none;
            background: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-menu-button svg {
            width: 40px;
            height: 40px;
            fill: black;
        }

        .main-navigation-section {
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        


        .nav-content-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* allow the navigation to overflow horizontally on small screens */
            overflow: visible;
        }

        /* Keep nav items on one line and enable horizontal scrolling when needed */
        .primary-nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            flex-grow: 1;
            white-space: nowrap; /* prevent wrapping */
            overflow-x: auto; /* allow horizontal scroll when items overflow */
            -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
            scrollbar-width: thin; /* Firefox: make scrollbar thin */
            scrollbar-color: #cfcfcf transparent; /* Firefox colors */
            position: relative; /* required for overlay thumb positioning */
        }

        .nav-menu-item {
            margin-right: 35px;
            flex: 0 0 auto; /* prevent flex items from shrinking/wrapping */
        }

        .nav-link-element {
            text-decoration: none;
            color: #495057;
            font-size: 17px;
            font-weight: 500;
            padding: 8px 0;
            display: block;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link-element::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #495057;
            transition: width 0.3s ease;
        }

        /* WebKit scrollbar styling for the horizontal nav */
        /* WebKit scrollbar styling for the horizontal nav */
        .primary-nav-menu::-webkit-scrollbar {
            height: 8px;
        }

        /* Hide scrollbar visuals by default (transparent) */
        .primary-nav-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .primary-nav-menu::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0); /* hidden */
            border-radius: 6px;
            transition: background-color 0.18s ease;
        }

        /* Show the thumb when hovering or when `.scrolling` class is present */
        .primary-nav-menu:hover::-webkit-scrollbar-thumb,
        .primary-nav-menu.scrolling::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.12);
        }

        .primary-nav-menu:hover::-webkit-scrollbar-thumb:hover,
        .primary-nav-menu.scrolling::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.18);
        }

        /* Firefox: hide by default, show when scrolling/hover */
        .primary-nav-menu {
            scrollbar-color: transparent transparent;
        }

        .primary-nav-menu.scrolling {
            scrollbar-color: #cfcfcf transparent;
        }

        /* Overlay thumb (mobile-like) - appears while interacting */
        .nav-scroll-thumb {
            position: absolute;
            bottom: 6px; /* same area as the hidden native scrollbar */
            left: 0;
            height: 3px; /* slimmer, half-bar look */
            width: 40px; /* will be updated via JS */
            background: rgba(0, 0, 0, 0.10); /* low opacity */
            border-radius: 999px;
            transform: translateX(0);
            opacity: 0;
            pointer-events: none; /* don't block pointer/touch */
            transition: opacity 0.18s ease, transform 0.12s linear, width 0.12s linear;
            will-change: transform, width, opacity;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08); /* subtle shadow */
        }

        .nav-scroll-thumb.show {
            opacity: 0.22; /* low, subtle visible state */
        }

        /* Slightly larger thumb on small screens for easier touch */
        @media (max-width: 768px) {
            .nav-scroll-thumb { height: 5px; }
        }

        .nav-link-element:hover {
            color: #212529;
        }

        .nav-link-element:hover::after {
            width: 100%;
        }

        .nav-actions-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-actions-group {
            display: none;
        }

        /* Mobile Sidebar */
        .mobile-sidebar-panel {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100vh;
            background: white;
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        .mobile-sidebar-panel.sidebar-active {
            right: 0;
        }

        .sidebar-header-container {
            padding: 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .sidebar-title-text {
            color: #495057;
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-close-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-close-btn:hover {
            background: #e9ecef;
            transform: rotate(90deg);
        }

        .sidebar-close-btn svg {
            width: 18px;
            height: 18px;
            fill: #495057;
        }

        .sidebar-menu-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
        }

        .sidebar-menu-container::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .sidebar-menu-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 3px;
        }

        .sidebar-menu-container::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }

        .sidebar-menu-link {
            display: block;
            color: #495057;
            text-decoration: none;
            padding: 15px 25px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu-link:hover {
            background: #f8f9fa;
            border-left-color: #495057;
            color: #212529;
        }

        .sidebar-overlay-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay-bg.overlay-active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .header-top-section {
                padding: 5px 30px;
            }

            .header-content-flex {
                flex-wrap: wrap;
            }

            .logo-image-element {
                height: 70px;
                max-width: 150px;
            }

            .header-action-group {
                gap: 15px;
            }

            .search-input-box {
                display: none;
            }

            .date-info-display {
                display: none;
            }

            .header-button-element {
                display: none;
            }

            .mobile-actions-group {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .mobile-menu-button {
                display: block;
            }

            .mobile-sidebar-panel {
                width: 300px;
                right: -300px;
            }

            .main-navigation-section {
                padding: 0 20px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav-content-container {
                min-width: max-content;
            }

            .primary-nav-menu {
                min-width: max-content;
            }

            .nav-menu-item {
                margin-right: 25px;
            }

            .nav-link-element {
                padding: 8px 0;
                font-size: 14px;
                white-space: nowrap;
            }

            .nav-actions-container {
                min-width: max-content;
                padding-left: 20px;
            }

            .main-navigation-section::-webkit-scrollbar {
                height: 3px;
            }

            .main-navigation-section::-webkit-scrollbar-track {
                background: #f8f9fa;
            }

            .main-navigation-section::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 2px;
            }

            .main-navigation-section::-webkit-scrollbar-thumb:hover {
                background: #adb5bd;
            }
        }

        @media (max-width: 480px) {
            .logo-image-element {
                height: 70px;
                max-width: 120px;
            }

            .mobile-sidebar-panel {
                width: 280px;
                right: -280px;
            }

            .nav-menu-item {
                margin-right: 20px;
            }

            .nav-link-element {
                font-size: 13px;
            }
        }
         .news-ticker {
  display: flex;
  align-items: center;
  width: 100%;
  height: 50px;
  overflow: hidden;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}

.ticker-label {
  padding: 0 10px;
  background-color: white;
  color: black;
  font-size: 20px;
  height: 100%;
  display: flex;
  align-items: center;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
  z-index: 2;
}

.ticker-container {
  flex: 1;
  overflow: hidden;
  position: relative;
}

.ticker-move {
  display: inline-block;
  white-space: nowrap;
  padding-left: 100%;
  animation: scroll-left 80s linear infinite; /* much slower speed */
}

.ticker-move a {
  margin: 0 10px;
  color: #000000; /* black text */
  text-decoration: none;
  padding: 10px;
  border-left: 2px solid black;
  transition: color 0.3s;
  font-size: 16px;
}

.ticker-move a:hover {
  color: #4f46e5;
}

@keyframes scroll-left {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}

.paused {
  animation-play-state: paused !important;
}


        .footer-wrapper {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Decorative Border */
        .decorative-border {
            width: 100%;
            height: 15px;
            background-image: repeating-linear-gradient(
                45deg,
                #ff0000 0px,
                #ff0000 12px,
                #ffffff 12px,
                #ffffff 24px
            );
            border: 1px solid #ff0000;
        }
         .news-ticker {
  display: flex;
  align-items: center;
  width: 100%;
  height: 50px;
  overflow: hidden;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}

.ticker-label {
  padding: 0 10px;
  background-color: white;
  color: black;
  font-size: 20px;
  height: 100%;
  display: flex;
  align-items: center;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
  z-index: 2;
}

.ticker-container {
  flex: 1;
  overflow: hidden;
  position: relative;
}

.ticker-move {
  display: inline-block;
  white-space: nowrap;
  padding-left: 100%;
  animation: scroll-left 80s linear infinite; /* much slower speed */
}

.ticker-move a {
  margin: 0 10px;
  color: #000000; /* black text */
  text-decoration: none;
  padding: 10px;
  border-left: 2px solid black;
  transition: color 0.3s;
  font-size: 16px;
}

.ticker-move a:hover {
  color: #4f46e5;
}

@keyframes scroll-left {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}

.paused {
  animation-play-state: paused !important;
}
/* Right Section */
        .right-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-icon {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-icon:hover {
            background: red;
            transform: scale(1.05);
        }

        .search-icon i {
            font-size: 20px;
            color: red;
        }

        .bangla-text {
            color: white;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
        }

/* ===== HERO SECTION STYLES ===== */
.hero-section { max-width: 1200px; margin: 0 auto; padding: 20px 15px; }
.hero-grid { display: grid; grid-template-columns: 1fr 280px; gap: 25px; }
.hero-main { display: flex; flex-direction: column; gap: 25px; }
.hero-top-row { display: grid; grid-template-columns: 1fr 350px; gap: 20px; }
.featured-large { position: relative; border-radius: 8px; overflow: hidden; height: 400px; }
.featured-large img { width: 100%; height: 100%; object-fit: cover; }
.featured-large-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.85)); padding: 80px 20px 20px; }
.featured-large-headline { font-size: 26px; font-weight: 700; color: white; line-height: 1.4; margin-bottom: 10px; }
.featured-large-headline a { color: white; text-decoration: none; }
.featured-large-meta { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.8); font-size: 13px; }
.side-news-list { display: flex; flex-direction: column; }
.side-news-item { display: flex; gap: 12px; padding: 15px 0; border-bottom: 1px solid #e5e7eb; }
.side-news-item:first-child { padding-top: 0; }
.side-news-item:last-child { border-bottom: none; padding-bottom: 0; }
.side-news-thumb { width: 100px; height: 75px; flex-shrink: 0; border-radius: 6px; overflow: hidden; }
.side-news-thumb img { width: 100%; height: 100%; object-fit: cover; }
.side-news-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
.side-news-title { font-size: 15px; font-weight: 600; color: #1f2937; line-height: 1.4; margin-bottom: 6px; }
.side-news-title a { color: #1f2937; text-decoration: none; }
.side-news-title a:hover { color: #dc2626; }
.hero-bottom-news { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
.bottom-news-card .card-image { width: 100%; height: 180px; overflow: hidden; border-radius: 8px; }
.bottom-news-card .card-image img { width: 100%; height: 100%; object-fit: cover; }
.bottom-news-headline { font-size: 17px; font-weight: 700; color: #1f2937; line-height: 1.4; margin-top: 12px; margin-bottom: 8px; }
.bottom-news-headline a { color: #1f2937; text-decoration: none; }
.bottom-news-excerpt { font-size: 14px; color: #6b7280; line-height: 1.6; }
.hero-center { display: flex; flex-direction: column; border-left: 1px solid #e5e7eb; padding-left: 20px; }
.govt-emblem-box { border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 15px; border-radius: 8px; overflow: hidden; }
.govt-emblem-box img { width: 100%; height: 160px; object-fit: cover; }
.govt-emblem-box .card-content { padding: 12px; }
.govt-emblem-box h4 { font-size: 14px; font-weight: 600; color: #1f2937; line-height: 1.5; margin: 0; }
.govt-emblem-box h4 a { color: #1f2937; text-decoration: none; }
.news-headlines-box { flex: 1; overflow: hidden; }
.headline-item { padding: 12px 0; border-bottom: 1px solid #e5e7eb; display: flex; align-items: flex-start; gap: 10px; }
.headline-item:last-child { border-bottom: none; }
.headline-icon { color: #dc2626; font-size: 10px; margin-top: 5px; }
.headline-text { font-size: 14px; color: #1f2937; line-height: 1.5; font-weight: 500; }
.headline-text a { color: black; text-decoration: none; }
.headline-text a:hover { color: #dc2626; }
.headline-meta { font-size: 12px; color: #6b7280; margin-top: 4px; }
@media (max-width: 1100px) { .hero-grid { grid-template-columns: 1fr; } .hero-center { border-left: none; padding-left: 0; border-top: 1px solid #e5e7eb; padding-top: 20px; } .hero-top-row { grid-template-columns: 1fr 300px; } }
@media (max-width: 900px) { .hero-top-row { grid-template-columns: 1fr; } .featured-large { height: 300px; } }
@media (max-width: 768px) { .hero-bottom-news { grid-template-columns: 1fr; } }

/* ========== ENTERTAINMENT STYLE LAYOUT ========== */
.entertainment-section { max-width: 1200px; margin: 0 auto 50px; padding: 20px 15px; }
.entertainment-section .section-title { text-align: center; font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 25px; display: flex; align-items: center; justify-content: center; gap: 20px; }
.entertainment-section .section-title::before, .entertainment-section .section-title::after { content: ''; flex: 1; height: 1px; background: #1a1a1a; max-width: 200px; }
.entertainment-layout { display: grid; grid-template-columns: 180px 1fr 280px; gap: 20px; }
.entertainment-layout .left-col { display: flex; flex-direction: column; }
.entertainment-layout .left-col .thumb-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
.entertainment-layout .left-col .thumb-item img { width: 60px; height: 45px; object-fit: cover; flex-shrink: 0; }
.entertainment-layout .left-col .thumb-item h4 { font-size: 13px; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0; }
.entertainment-layout .left-col .thumb-item h4 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .left-col .text-item { padding: 10px 0; border-bottom: 1px solid #ccc; }
.entertainment-layout .left-col .text-item h4 { font-size: 13px; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0; }
.entertainment-layout .left-col .text-item h4 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .center-col .main-featured { margin-bottom: 15px; }
.entertainment-layout .center-col .main-featured img { width: 100%; height: 320px; object-fit: cover; }
.entertainment-layout .center-col .main-featured h3 { font-size: 22px; font-weight: 700; color: #1a1a1a; line-height: 1.4; margin: 15px 0 10px 0; }
.entertainment-layout .center-col .main-featured h3 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .center-col .main-featured p { font-size: 14px; color: #666; line-height: 1.6; }
.entertainment-layout .center-col .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
.entertainment-layout .center-col .bottom-grid .grid-item img { width: 100%; height: 140px; object-fit: cover; }
.entertainment-layout .center-col .bottom-grid .grid-item h4 { font-size: 14px; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 10px 0 0 0; }
.entertainment-layout .center-col .bottom-grid .grid-item h4 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .right-col .small-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 15px; }
.entertainment-layout .right-col .small-grid .small-item img { width: 100%; height: 60px; object-fit: cover; }
.entertainment-layout .right-col .small-grid .small-item h5 { font-size: 11px; font-weight: 600; color: #1a1a1a; line-height: 1.3; margin: 5px 0 0 0; }
.entertainment-layout .right-col .small-grid .small-item h5 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .right-col .highlight-item { padding: 10px 0; border-bottom: 1px solid #ccc; }
.entertainment-layout .right-col .highlight-item h4 { font-size: 13px; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0; }
.entertainment-layout .right-col .highlight-item h4 a { color: #1a1a1a; text-decoration: none; }
.entertainment-layout .right-col .side-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
.entertainment-layout .right-col .side-item img { width: 70px; height: 50px; object-fit: cover; flex-shrink: 0; }
.entertainment-layout .right-col .side-item h4 { font-size: 12px; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0; }
.entertainment-layout .right-col .side-item h4 a { color: #1a1a1a; text-decoration: none; }
@media (max-width: 1024px) { .entertainment-layout { grid-template-columns: 1fr 1fr; } .entertainment-layout .left-col { grid-column: 1 / -1; flex-direction: row; flex-wrap: wrap; gap: 10px; } }
@media (max-width: 768px) { .entertainment-layout { grid-template-columns: 1fr; } .entertainment-layout .center-col { order: -1; } .entertainment-layout .center-col .bottom-grid { grid-template-columns: 1fr; } .entertainment-layout .right-col .small-grid { grid-template-columns: 1fr 1fr; } }

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
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
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

    <!-- ===== HERO SECTION ===== -->
    <?php
    include 'connection.php';
    date_default_timezone_set('Asia/Dhaka');
    
    $uploadPath = 'Admin/img/';
    
    // Get category info
    $category_id = 0;
    $category_name = "সর্বশেষ সংবাদ";
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $category_id = intval($_GET['id']);
        $cat_sql = "SELECT name FROM $tbl_category WHERE id = $category_id LIMIT 1";
        $cat_result = $conn->query($cat_sql);
        if ($cat_result && $cat_result->num_rows > 0) {
            $cat_row = $cat_result->fetch_assoc();
            $category_name = htmlspecialchars($cat_row['name']);
        }
    }
    
    // Fetch news for hero section (15 items)
    $category_condition = $category_id > 0 ? "WHERE n.category_id = $category_id AND n.is_active = 1" : "WHERE n.is_active = 1";
    $sql = "SELECT n.id, n.headline, n.slug AS news_slug, n.image_url, n.created_at, n.news_1,
                   c.slug AS category_slug, c.name AS category_name
            FROM $tbl_news n
            JOIN $tbl_category c ON n.category_id = c.id
            $category_condition
            ORDER BY n.created_at DESC
            LIMIT 15";
    $result = $conn->query($sql);
    
    $heroNews = [];
    while ($row = $result->fetch_assoc()) {
        $heroNews[] = $row;
    }
    
    function getNewsLinkCat($news) {
        global $user_id_suffix;
        return "news.php?id=" . ($news['id'] ?? $news['news_id'] ?? 0) . $user_id_suffix;
    }
    
    function getExcerptCat($content, $length = 100) {
        $content = strip_tags($content ?? '');
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', trim($content));
        return mb_substr($content, 0, $length) . '...';
    }
    ?>

    <section class="entertainment-section">
        <div class="entertainment-layout">
            <div class="left-col">
                <?php for ($j = 5; $j < min(7, count($heroNews)); $j++): ?>
                <div class="thumb-item">
                    <img src="<?= $uploadPath . htmlspecialchars($heroNews[$j]['image_url']); ?>" alt="">
                    <h4><a href="<?= getNewsLinkCat($heroNews[$j]); ?>"><?= htmlspecialchars($heroNews[$j]['headline']); ?></a></h4>
                </div>
                <?php endfor; ?>
                <?php for ($j = 7; $j < min(14, count($heroNews)); $j++): ?>
                <div class="text-item">
                    <h4><a href="<?= getNewsLinkCat($heroNews[$j]); ?>"><?= htmlspecialchars($heroNews[$j]['headline']); ?></a></h4>
                </div>
                <?php endfor; ?>
            </div>
            
            <div class="center-col">
                <?php if (!empty($heroNews[0])): ?>
                <div class="main-featured">
                    <a href="<?= getNewsLinkCat($heroNews[0]); ?>">
                        <img src="<?= $uploadPath . htmlspecialchars($heroNews[0]['image_url']); ?>" alt="">
                    </a>
                    <h3><a href="<?= getNewsLinkCat($heroNews[0]); ?>"><?= htmlspecialchars($heroNews[0]['headline']); ?></a></h3>
                    <p><?= getExcerptCat($heroNews[0]['news_1'] ?? '', 150); ?></p>
                </div>
                <?php endif; ?>
                <div class="bottom-grid">
                    <?php for ($j = 1; $j < min(3, count($heroNews)); $j++): ?>
                    <div class="grid-item">
                        <a href="<?= getNewsLinkCat($heroNews[$j]); ?>">
                            <img src="<?= $uploadPath . htmlspecialchars($heroNews[$j]['image_url']); ?>" alt="">
                        </a>
                        <h4><a href="<?= getNewsLinkCat($heroNews[$j]); ?>"><?= htmlspecialchars($heroNews[$j]['headline']); ?></a></h4>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="right-col">
                <div class="small-grid">
                    <?php for ($j = 3; $j < min(6, count($heroNews)); $j++): ?>
                    <div class="small-item">
                        <img src="<?= $uploadPath . htmlspecialchars($heroNews[$j]['image_url']); ?>" alt="">
                        <h5><a href="<?= getNewsLinkCat($heroNews[$j]); ?>"><?= htmlspecialchars($heroNews[$j]['headline']); ?></a></h5>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php for ($j = 6; $j < min(15, count($heroNews)); $j++): ?>
                <div class="highlight-item">
                    <h4><a href="<?= getNewsLinkCat($heroNews[$j]); ?>"><?= htmlspecialchars($heroNews[$j]['headline']); ?></a></h4>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ===== HERO SECTION ===== -->
    <?php
    $sql2 = "SELECT n.id, n.headline, n.slug AS news_slug, n.image_url, n.created_at, n.news_1,
                    c.slug AS category_slug, c.name AS category_name
             FROM $tbl_news n JOIN $tbl_category c ON n.category_id = c.id
             $category_condition ORDER BY n.created_at DESC LIMIT 15 OFFSET 15";
    $result2 = $conn->query($sql2);
    $entNews = [];
    while ($row = $result2->fetch_assoc()) { $entNews[] = $row; }
    ?>
    
    <?php if (count($entNews) >= 8): ?>
    <section class="hero-section">
        <div class="hero-grid">
            <div class="hero-main">
                <div class="hero-top-row">
                    <?php if (!empty($entNews[0])): ?>
                    <div class="featured-large">
                        <a href="<?= getNewsLinkCat($entNews[0]); ?>">
                            <img src="<?= $uploadPath . htmlspecialchars($entNews[0]['image_url']); ?>" alt="">
                        </a>
                        <div class="featured-large-overlay">
                            <h2 class="featured-large-headline">
                                <a href="<?= getNewsLinkCat($entNews[0]); ?>"><?= htmlspecialchars($entNews[0]['headline']); ?></a>
                            </h2>
                            <div class="featured-large-meta">
                                <i class="far fa-clock"></i> <?= banglaDate($entNews[0]['created_at']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="side-news-list">
                        <?php for ($i = 1; $i <= 4 && isset($entNews[$i]); $i++): ?>
                        <div class="side-news-item">
                            <div class="side-news-thumb">
                                <a href="<?= getNewsLinkCat($entNews[$i]); ?>">
                                    <img src="<?= $uploadPath . htmlspecialchars($entNews[$i]['image_url']); ?>" alt="">
                                </a>
                            </div>
                            <div class="side-news-content">
                                <h4 class="side-news-title">
                                    <a href="<?= getNewsLinkCat($entNews[$i]); ?>"><?= htmlspecialchars($entNews[$i]['headline']); ?></a>
                                </h4>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="hero-bottom-news">
                    <?php for ($i = 5; $i <= 7 && isset($entNews[$i]); $i++): ?>
                    <div class="bottom-news-card">
                        <div class="card-image">
                            <a href="<?= getNewsLinkCat($entNews[$i]); ?>">
                                <img src="<?= $uploadPath . htmlspecialchars($entNews[$i]['image_url']); ?>" alt="">
                            </a>
                        </div>
                        <h4 class="bottom-news-headline">
                            <a href="<?= getNewsLinkCat($entNews[$i]); ?>"><?= htmlspecialchars($entNews[$i]['headline']); ?></a>
                        </h4>
                        <p class="bottom-news-excerpt"><?= getExcerptCat($entNews[$i]['news_1'] ?? '', 80); ?></p>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="hero-center">
                <?php if (!empty($entNews[8])): ?>
                <div class="govt-emblem-box">
                    <a href="<?= getNewsLinkCat($entNews[8]); ?>">
                        <img src="<?= $uploadPath . htmlspecialchars($entNews[8]['image_url']); ?>" alt="">
                    </a>
                    <div class="card-content">
                        <h4><a href="<?= getNewsLinkCat($entNews[8]); ?>"><?= htmlspecialchars($entNews[8]['headline']); ?></a></h4>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="news-headlines-box">
                    <?php for ($i = 9; $i <= 14 && isset($entNews[$i]); $i++): ?>
                    <div class="headline-item">
                        <span class="headline-icon"><i class="fas fa-circle"></i></span>
                        <div>
                            <div class="headline-text">
                                <a href="<?= getNewsLinkCat($entNews[$i]); ?>"><?= htmlspecialchars($entNews[$i]['headline']); ?></a>
                            </div>
                            <div class="headline-meta"><?= htmlspecialchars($entNews[$i]['category_name']); ?></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <!-- Footer -->
    <footer class="footer-wrapper">
        <div class="footer-main">
            <!-- Logo & Social Section -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if (!empty($basic_info['image'])): ?>
                    <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" class="logo-image">
                    <?php else: ?>
                    <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Hindus News Logo" class="logo-image">
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
                    <li><a href="#" onclick="openDbTermsPopup(); return false;">নীতি ও শর্ত</a></li>
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
                    <p>স্বত্ব  <?php echo date('Y'); ?> <?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'HINDUS NEWS'); ?></p>
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
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            dateElement.textContent = now.toLocaleDateString('en-US', options);
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
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
// Copy current URL to clipboard
function copyPageURL() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert("Link copied to clipboard!");
    }).catch(err => {
        alert("Failed to copy link");
    });
}

// Generate and download current page as PDF (simple method)
function downloadAsPDF() {
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Download PDF</title></head><body>');
    printWindow.document.write(document.body.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
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
<script>
        function checkResponsive() {
            const rightSection = document.querySelector(".right-section");

            if (!rightSection) return;

            if (window.innerWidth <= 768) {
                rightSection.style.display = "none";   // Hide on mobile
            } else {
                rightSection.style.display = "flex";   // Show on desktop/tablet
            }

        // Run on page load
        checkResponsive();

        // Run on screen resize
        window.addEventListener("resize", checkResponsive);
    </script>

<script>
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

function openDbTermsPopup() {
    document.getElementById('dbTermsPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDbTermsPopup(event) {
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

function openAdsPolicyPopup() {
    document.getElementById('dbAdsPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdsPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('dbAdsPolicyPopup').classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrivacyPolicyPopup();
        closeAboutUsPopup();
        closeAdsListPopup();
        closeDbTermsPopup();
        closeCommentPolicyPopup();
        closeAdsPolicyPopup();
    }
});
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
<div id="dbTermsPopup" class="policy-popup-overlay" onclick="closeDbTermsPopup(event)">
    <div class="policy-popup-container" onclick="event.stopPropagation()">
        <div class="policy-popup-header">
            <h2><i class="fas fa-file-contract"></i> শর্তাবলী</h2>
            <button class="policy-popup-close" onclick="closeDbTermsPopup()">&times;</button>
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

<style>
.policy-popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
.policy-popup-overlay.active {
    display: flex;
}
.policy-popup-container {
    background: #fff;
    max-width: 700px;
    width: 90%;
    max-height: 80vh;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
.policy-popup-header {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.policy-popup-header h2 {
    margin: 0;
    font-size: 18px;
}
.policy-popup-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
}
.policy-popup-content {
    padding: 20px;
    overflow-y: auto;
    max-height: calc(80vh - 60px);
    line-height: 1.8;
}
</style>

</body>
</html>