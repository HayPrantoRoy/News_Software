<!DOCTYPE html>

<html lang="en">



    <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" type="image/x-icon" href="logo.jpg">



        <!-- Google Web Fonts -->

        <link rel="preconnect" href="https://fonts.googleapis.com">

        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@100;600;800&display=swap" rel="stylesheet"> 



        <!-- Icon Font Stylesheet -->

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

    

    <style>

    /* News Page Professional Layout */

    .news-page-wrapper {

        display: flex;

        gap: 30px;

        max-width: 1200px;

        margin: 0 auto;

        padding: 20px;

    }

    

    .news-main-content {

        flex: 1;

        min-width: 0;

    }

    

    .news-sidebar {

        width: 350px;

        flex-shrink: 0;

    }

    

    /* Desktop: Sticky sidebar */

    @media (min-width: 992px) {

        .row.g-4 {

            align-items: flex-start;

        }

        

        .col-lg-4 {

            position: -webkit-sticky;

            position: sticky;

            top: 100px;

            align-self: flex-start;

        }

    }

    

    /* Mobile styles */

    @media (max-width: 991px) {

        .col-lg-4 {

            margin-bottom: 20px;

        }

        

        .news-sidebar {

            width: 100% !important;

        }

    }

    

    /* Popular News Section */

    .popular-section {

        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);

        border-radius: 16px;

        padding: 20px;

        box-shadow: 0 4px 20px rgba(0,0,0,0.08);

        border: 1px solid #e5e7eb;

    }

    

    .popular-header {

        display: flex;

        align-items: center;

        gap: 12px;

        margin-bottom: 20px;

        padding-bottom: 15px;

        border-bottom: 2px solid #dc2626;

    }

    

    .popular-header-icon {

        width: 40px;

        height: 40px;

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        border-radius: 10px;

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 18px;

    }

    

    .popular-header h4 {

        margin: 0;

        font-size: 20px;

        font-weight: 700;

        color: #1a1a1a;

        font-family: 'SolaimanLipi', sans-serif;

    }

    

    .popular-news-card {

        display: flex;

        gap: 12px;

        padding: 15px 0;

        border-bottom: 1px solid #e5e7eb;

        transition: all 0.3s ease;

        cursor: pointer;

    }

    

    .popular-news-card:last-child {

        border-bottom: none;

        padding-bottom: 0;

    }

    

    .popular-news-card:hover {

        background: #f8f9fa;

        margin: 0 -10px;

        padding-left: 10px;

        padding-right: 10px;

        border-radius: 8px;

    }

    

    .popular-news-thumb {

        width: 80px;

        height: 60px;

        border-radius: 8px;

        overflow: hidden;

        flex-shrink: 0;

    }

    

    .popular-news-thumb img {

        width: 100%;

        height: 100%;

        object-fit: cover;

        transition: transform 0.3s ease;

    }

    

    .popular-news-card:hover .popular-news-thumb img {

        transform: scale(1.1);

    }

    

    .popular-news-info {

        flex: 1;

        min-width: 0;

    }

    

    .popular-news-info h6 {

        margin: 0 0 6px 0;

        font-size: 14px;

        font-weight: 600;

        line-height: 1.4;

        color: #1a1a1a;

        display: -webkit-box;

        -webkit-line-clamp: 2;

        -webkit-box-orient: vertical;

        overflow: hidden;

    }

    

    .popular-news-info h6 a {

        color: inherit;

        text-decoration: none;

    }

    

    .popular-news-info h6 a:hover {

        color: #dc2626;

    }

    

    .popular-news-meta {

        font-size: 11px;

        color: #6b7280;

        display: flex;

        align-items: center;

        gap: 8px;

    }

    

    .popular-news-meta i {

        color: #dc2626;

    }

    

    /* Infinite Scroll News Container */

    .infinite-news-container {

        margin-top: 40px;

    }

    

    .infinite-news-header {

        display: flex;

        align-items: center;

        gap: 12px;

        margin-bottom: 25px;

        padding-bottom: 15px;

        border-bottom: 3px solid #dc2626;

    }

    

    .infinite-news-header-icon {

        width: 45px;

        height: 45px;

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        border-radius: 12px;

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 20px;

    }

    

    .infinite-news-header h3 {

        margin: 0;

        font-size: 24px;

        font-weight: 700;

        color: #1a1a1a;

    }

    

    .next-news-article {

        background: white;

        border-radius: 16px;

        padding: 25px;

        margin-bottom: 30px;

        box-shadow: 0 4px 20px rgba(0,0,0,0.08);

        border: 1px solid #e5e7eb;

        animation: slideInUp 0.5s ease;

    }

    

    @keyframes slideInUp {

        from {

            opacity: 0;

            transform: translateY(30px);

        }

        to {

            opacity: 1;

            transform: translateY(0);

        }

    }

    

    .next-news-article .article-category {

        display: inline-block;

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        color: white;

        padding: 5px 15px;

        border-radius: 20px;

        font-size: 12px;

        font-weight: 600;

        margin-bottom: 15px;

    }

    

    .next-news-article .article-image {

        width: 100%;

        border-radius: 12px;

        overflow: hidden;

        margin-bottom: 20px;

    }

    

    .next-news-article .article-image img {

        width: 100%;

        height: auto;

        display: block;

    }

    

    .next-news-article .article-title {

        font-size: 22px;

        font-weight: 700;

        color: #1a1a1a;

        margin-bottom: 12px;

        line-height: 1.4;

    }

    

    .next-news-article .article-title a {

        color: inherit;

        text-decoration: none;

    }

    

    .next-news-article .article-title a:hover {

        color: #dc2626;

    }

    

    .next-news-article .article-meta {

        display: flex;

        align-items: center;

        gap: 15px;

        font-size: 13px;

        color: #6b7280;

        margin-bottom: 15px;

        flex-wrap: wrap;

    }

    

    .next-news-article .article-meta span {

        display: flex;

        align-items: center;

        gap: 5px;

    }

    

    .next-news-article .article-meta i {

        color: #dc2626;

    }

    

    .next-news-article .article-excerpt {

        font-size: 15px;

        color: #4b5563;

        line-height: 1.7;

        text-align: justify;

    }

    

    .next-news-article .read-more-btn {

        display: inline-flex;

        align-items: center;

        gap: 8px;

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        color: white;

        padding: 10px 20px;

        border-radius: 25px;

        font-size: 14px;

        font-weight: 600;

        text-decoration: none;

        margin-top: 15px;

        transition: all 0.3s ease;

    }

    

    .next-news-article .read-more-btn:hover {

        transform: translateX(5px);

        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);

        color: white;

    }

    

    /* Loading Spinner */

    .loading-spinner {

        display: flex;

        justify-content: center;

        align-items: center;

        padding: 30px;

    }

    

    .loading-spinner .spinner {

        width: 40px;

        height: 40px;

        border: 4px solid #f3f3f3;

        border-top: 4px solid #dc2626;

        border-radius: 50%;

        animation: spin 1s linear infinite;

    }

    

    @keyframes spin {

        0% { transform: rotate(0deg); }

        100% { transform: rotate(360deg); }

    }

    

    .end-of-news {

        text-align: center;

        padding: 30px;

        color: #6b7280;

        font-size: 14px;

    }

    

    /* Full News Article Styles */

    .full-news-article {

        animation: slideInUp 0.5s ease;

        padding-top: 30px;

    }

    

    .article-separator {

        display: flex;

        align-items: center;

        margin-bottom: 25px;

        gap: 15px;

    }

    

    .article-separator::before,

    .article-separator::after {

        content: '';

        flex: 1;

        height: 3px;

        background: linear-gradient(90deg, #dc2626, #f87171);

        border-radius: 2px;

    }

    

    .article-separator span {

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        color: white;

        padding: 8px 20px;

        border-radius: 25px;

        font-size: 14px;

        font-weight: 600;

        white-space: nowrap;

    }

    

    .article-main-headline {

        font-size: 28px;

        font-weight: 700;

        color: #1a1a1a;

        line-height: 1.4;

        margin-bottom: 15px;

        font-family: 'SolaimanLipi', sans-serif;

    }

    

    .article-short-desc {

        font-size: 16px;

        color: #4b5563;

        line-height: 1.6;

        margin-bottom: 20px;

        font-style: italic;

    }

    

    /* Responsive */

    @media (max-width: 991px) {

        .news-page-wrapper {

            flex-direction: column;

        }

        

        .news-sidebar {

            width: 100%;

            order: -1;

        }

        

        .sticky-sidebar {

            position: relative;

            top: 0;

            max-height: none;

            overflow: visible;

        }

        

        .popular-section {

            margin-bottom: 30px;

        }

        

        .popular-news-card {

            flex-wrap: wrap;

        }

    }

    

    @media (max-width: 576px) {

        .news-page-wrapper {

            padding: 10px;

        }

        

        .popular-news-thumb {

            width: 70px;

            height: 50px;

        }

        

        .next-news-article {

            padding: 15px;

        }

        

        .next-news-article .article-title {

            font-size: 18px;

        }

    }

    </style>



        <!-- Libraries Stylesheet -->

        <link href="lib/animate/animate.min.css" rel="stylesheet">

        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

      <?php

include 'connection.php';



// Enable error reporting (for debugging)

error_reporting(E_ALL);

ini_set('display_errors', 1);



// Get and split the path

$path = $_GET['path'] ?? '';

$parts = explode('/', $path);

if (count($parts) !== 2) {

    echo "<div class='container p-5'><h2>Invalid news path</h2></div>";

    exit;

}



$categorySlug = $parts[0];

$newsSlug = $parts[1];



// Main query: fetch headline, category

$sql = "SELECT n.headline, c.name AS category_name

        FROM news n

        JOIN category c ON n.category_id = c.id

        WHERE n.slug = ? AND c.slug = ? AND n.is_active = 1

        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $newsSlug, $categorySlug);

$stmt->execute();

$result = $stmt->get_result();



if ($result->num_rows === 0) {

    echo "<div class='container p-5'><h2>News not found</h2></div>";

    exit;

}



$row = $result->fetch_assoc();



// Separate query for meta data

$meta_sql = "SELECT * FROM news WHERE slug = ? AND is_active = 1 LIMIT 1";

$meta_stmt = $conn->prepare($meta_sql);

$meta_stmt->bind_param("s", $newsSlug);

$meta_stmt->execute();

$meta_result = $meta_stmt->get_result();

$meta = $meta_result->fetch_assoc() ?? [];



// Increment view count

try {

    // First, try to update views (increment by 1)

    $update_views_sql = "UPDATE news SET views = views + 1 WHERE slug = ?";

    $update_views_stmt = $conn->prepare($update_views_sql);

    $update_views_stmt->bind_param("s", $newsSlug);

    $update_views_stmt->execute();

} catch (Exception $e) {

    // If views column doesn't exist, add it

    if (strpos($e->getMessage(), 'views') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {

        try {

            $conn->query("ALTER TABLE news ADD COLUMN views INT DEFAULT 0");

            // Try update again

            $update_views_stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE slug = ?");

            $update_views_stmt->bind_param("s", $newsSlug);

            $update_views_stmt->execute();

        } catch (Exception $inner_e) {

            // Silently fail if unable to add column

        }

    }

}



// Get current view count

$views_count = 0;

try {

    $views_sql = "SELECT views FROM news WHERE slug = ? AND is_active = 1 LIMIT 1";

    $views_stmt = $conn->prepare($views_sql);

    $views_stmt->bind_param("s", $newsSlug);

    $views_stmt->execute();

    $views_result = $views_stmt->get_result();

    if ($views_row = $views_result->fetch_assoc()) {

        $views_count = $views_row['views'] ?? 0;

    }

} catch (Exception $e) {

    // If views column doesn't exist yet, default to 0

    $views_count = 0;

}



// ============ META TAGS CONFIGURATION ============

// Site configuration

$site_name = 'Hindus News';

$site_url = 'http://hindus-news.com';



// Prepare meta variables with proper sanitization

$meta_headline = htmlspecialchars($meta['headline'] ?? 'Latest News', ENT_QUOTES, 'UTF-8');

$meta_description = htmlspecialchars(

    mb_substr(strip_tags($meta['short_description'] ?? 'Read the latest news and updates from Hindus News.'), 0, 160),

    ENT_QUOTES, 

    'UTF-8'

);



// Category and author information

$meta_category = htmlspecialchars($row['category_name'] ?? 'News', ENT_QUOTES, 'UTF-8');

$meta_author = htmlspecialchars($meta['reporter_name'] ?? $site_name, ENT_QUOTES, 'UTF-8');



// Generate absolute image URL

$meta_image = !empty($meta['image_url'])

    ? $site_url . '/Admin/img/' . ltrim($meta['image_url'], '/')

    : $site_url . '/img/default-news.jpg';



// Generate canonical URL

$meta_url = $site_url . '/' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8');



// Article metadata

$meta_published = !empty($meta['created_at']) ? date('c', strtotime($meta['created_at'])) : date('c');

$meta_modified = !empty($meta['updated_at']) ? date('c', strtotime($meta['updated_at'])) : $meta_published;



// Keywords generation

$meta_keywords = $meta_category . ', ' . $site_name . ', Latest News, Breaking News, Bangladesh News, ' . 

                 str_replace([',', '.', '!', '?'], '', mb_substr($meta_headline, 0, 50));



?>



        <!-- Primary Meta Tags -->

        <title><?php echo $meta_headline; ?> | <?php echo $site_name; ?></title>

        <meta name="title" content="<?php echo $meta_headline; ?> | <?php echo $site_name; ?>">

        <meta name="description" content="<?php echo $meta_description; ?>">

        <meta name="keywords" content="<?php echo $meta_keywords; ?>">

        <meta name="author" content="<?php echo $meta_author; ?>">

        <meta name="robots" content="index, follow">

        <meta name="language" content="Bengali">

        <meta name="revisit-after" content="1 days">

        

        <!-- Canonical URL -->

        <link rel="canonical" href="<?php echo $meta_url; ?>">

        

        <!-- Open Graph / Facebook Meta Tags -->

        <meta property="og:type" content="article">

        <meta property="og:site_name" content="<?php echo $site_name; ?>">

        <meta property="og:title" content="<?php echo $meta_headline; ?>">

        <meta property="og:description" content="<?php echo $meta_description; ?>">

        <meta property="og:url" content="<?php echo $meta_url; ?>">

        <meta property="og:image" content="<?php echo $meta_image; ?>">

        <meta property="og:image:secure_url" content="<?php echo str_replace('http://', 'https://', $meta_image); ?>">

        <meta property="og:image:type" content="image/jpeg">

        <meta property="og:image:width" content="1200">

        <meta property="og:image:height" content="630">

        <meta property="og:image:alt" content="<?php echo $meta_headline; ?>">

        <meta property="og:locale" content="bn_BD">

        <meta property="article:published_time" content="<?php echo $meta_published; ?>">

        <meta property="article:modified_time" content="<?php echo $meta_modified; ?>">

        <meta property="article:author" content="<?php echo $meta_author; ?>">

        <meta property="article:section" content="<?php echo $meta_category; ?>">

        <meta property="article:tag" content="<?php echo $meta_category; ?>">

        

        <!-- Twitter Card Meta Tags -->

        <meta name="twitter:card" content="summary_large_image">

        <meta name="twitter:site" content="@HindusNews">

        <meta name="twitter:creator" content="@HindusNews">

        <meta name="twitter:title" content="<?php echo $meta_headline; ?>">

        <meta name="twitter:description" content="<?php echo $meta_description; ?>">

        <meta name="twitter:image" content="<?php echo $meta_image; ?>">

        <meta name="twitter:image:alt" content="<?php echo $meta_headline; ?>">

        

        <!-- Structured Data (JSON-LD) for Rich Snippets -->

        <script type="application/ld+json">

        {

            "@context": "https://schema.org",

            "@type": "NewsArticle",

            "headline": "<?php echo addslashes($meta_headline); ?>",

            "description": "<?php echo addslashes($meta_description); ?>",

            "image": {

                "@type": "ImageObject",

                "url": "<?php echo $meta_image; ?>",

                "width": 1200,

                "height": 630

            },

            "datePublished": "<?php echo $meta_published; ?>",

            "dateModified": "<?php echo $meta_modified; ?>",

            "author": {

                "@type": "Person",

                "name": "<?php echo addslashes($meta_author); ?>"

            },

            "publisher": {

                "@type": "Organization",

                "name": "<?php echo $site_name; ?>",

                "logo": {

                    "@type": "ImageObject",

                    "url": "<?php echo $site_url; ?>/logo.png",

                    "width": 250,

                    "height": 60

                }

            },

            "mainEntityOfPage": {

                "@type": "WebPage",

                "@id": "<?php echo $meta_url; ?>"

            },

            "articleSection": "<?php echo addslashes($meta_category); ?>",

            "inLanguage": "bn-BD"

        }

        </script>



        <!-- Customized Bootstrap Stylesheet -->

        <link href="css/bootstrap.min.css" rel="stylesheet">



        <!-- Template Stylesheet -->

        <link href="css/style.css" rel="stylesheet">

        <link href="css/head.css" rel="stylesheet">

        <style>

            

            @import url('https://fonts.maateen.me/solaiman-lipi/font.css');

             body{

            font-family: 'SolaimanLipi', sans-serif !important;

}

h6, .h6, h5, .h5, h4, .h4, h3, .h3, h2, .h2, h1, .h1 {

    font-family: 'SolaimanLipi', sans-serif !important;

}

            .py-5 {

    padding-top: 0.8rem !important;

    padding-bottom: 3rem !important;

    .my-4 {

    margin-top: 0rem !important;

    margin-bottom: 1rem !important;

}

}



.right-shadow {

    box-shadow: none !important;

}



.ps-4 {

    padding-left: 1rem !important;

}



.breadcrumb {

        background-color: white !important;

}



.breadcrumb-item+.breadcrumb-item {

    padding-left: 0rem !important;

}

.breadcrumb-item+.breadcrumb-item::before {

    padding-right: .3rem !important;

    padding-left: .3rem !important;

}



.breadcrumb-item+.breadcrumb-item::before {

  font-size: 18px !important;

}

  .primary-header-wrapper {

            background: red;

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

            height: 3px;

            background: red;

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

                max-width: 200px;

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

                max-width: 200px;

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

        </style>

    </head>



    <body>



        <!-- Spinner Start -->

        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">

            <div class="spinner-grow text-primary" role="status"></div>

        </div>

        <!-- Spinner End -->





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

    <?php include "connection.php"; ?>

    <div class="sidebar-menu-container">

        <?php

        $query = "SELECT id, name FROM category ORDER BY id ASC";

        $result = $conn->query($query);



        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                $category_id = $row['id'];

                $categoryName = htmlspecialchars($row['name']); // sanitize output

                echo '<a href="index.php?category_id=' . $category_id . '" class="sidebar-menu-link">' . $categoryName . '</a>';

            }

        } else {

            echo '<p class="sidebar-menu-link">No categories found.</p>';

        }

        ?>

    </div>

</div>

        <!-- Navbar End -->





        <!-- Modal Search Start -->

        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-fullscreen">

                <div class="modal-content rounded-0">

                    <div class="modal-header">

                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>

                    <div class="modal-body d-flex align-items-center">

                        <div class="input-group w-75 mx-auto d-flex">

                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">

                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Modal Search End -->





        <!-- Single Product Start -->

        <div class="container-fluid py-5">

            <div class="container py-5">

                <?php

include 'connection.php';



// Get path like "International/Pashupatinath-Temple"

$path = $_GET['path'] ?? '';

$parts = explode('/', $path);



// Validate and extract category and slug

if (count($parts) == 2) {

    $category = $parts[0];

    $slug = $parts[1];



    // Query to fetch news by category slug and news slug, including category name

    $stmt = $conn->prepare("SELECT news.*, category.slug AS category_slug, category.name AS category_name

                            FROM news 

                            JOIN category ON news.category_id = category.id 

                            WHERE category.slug = ? AND news.slug = ? AND news.is_active = 1");

    $stmt->bind_param("ss", $category, $slug);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($row = $result->fetch_assoc()) {

        ?>

        <ol class="breadcrumb justify-content-start mb-4">

            <li class="breadcrumb-item"><a href="index.php">হোম</a></li>

            <li class="breadcrumb-item"><a ><?php echo htmlspecialchars($row['category_name']) ; ?></a></li>

        </ol>



        <?php

    } else {

        echo "<h2>News not found</h2>";

    }



    $stmt->close();

} else {

    echo "<h2>Invalid URL format</h2>";

}

?>







                <div class="row g-4">

                    <div class="col-lg-8">

                        <?php

include 'connection.php';



// Get path like "International/Pashupatinath-Temple"

$path = $_GET['path'] ?? '';

$parts = explode('/', $path);



// Validate and extract category and slug

if (count($parts) == 2) {

    $category = $parts[0];

    $slug = $parts[1];



    // Query to fetch news by category slug and news slug

    $stmt = $conn->prepare("SELECT news.*, category.slug AS category_slug 

                            FROM news 

                            JOIN category ON news.category_id = category.id 

                            WHERE category.slug = ? AND news.slug = ? AND news.is_active = 1");

    $stmt->bind_param("ss", $category, $slug);

    $stmt->execute();

    $result = $stmt->get_result();



    // If found, display headline and short description

    if ($row = $result->fetch_assoc()) {

        ?>

        <div class="mb-4">

            <h2><?php echo htmlspecialchars($row['headline']); ?></h2>

            <p><?php echo htmlspecialchars($row['short_description']); ?></p>

        </div>

        <?php

    } else {

        echo "<h2>News not found</h2>";

    }



    $stmt->close();

} else {

    echo "<h2>Invalid URL format</h2>";

}

?>





                        <!-- Heading and Read Time -->

<div style="border-left: 3px solid #e63946; padding-left: 10px; margin-bottom: 12px;  font-family: 'SolaimanLipi', sans-serif !important;">



<?php

include 'connection.php';



// Get path like "International/Pashupatinath-Temple"

$path = $_GET['path'] ?? '';

$parts = explode('/', $path);



// Validate and extract category and slug

if (count($parts) == 2) {

    $category = $parts[0];

    $slug = $parts[1];



    // Query to fetch news by category slug and news slug, including category name

    $stmt = $conn->prepare("

        SELECT news.*, category.slug AS category_slug, category.name AS category_name,

               reporter.name AS reporter_name, reporter.photo AS reporter_photo

        FROM news

        JOIN category ON news.category_id = category.id

        LEFT JOIN reporter ON news.reporter_id = reporter.id

        WHERE category.slug = ? AND news.slug = ? AND news.is_active = 1

    ");

    $stmt->bind_param("ss", $category, $slug);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($row = $result->fetch_assoc()) {

        ?>

        



        <!-- Category Title with "Desk" -->

        <h2 style="font-size: 25px; font-weight: 600; margin: 0 0 6px 0; color: #1d1f20; line-height: 1.1;">

            <?php echo htmlspecialchars($row['category_name']) ; ?> ডেস্ক

        </h2>

        <?php

    } else {

        echo "<h2>News not found</h2>";

    }



    $stmt->close();

} else {

    echo "<h2>Invalid URL format</h2>";

}

?>





  <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; color: #555;">



    <!-- Reporter -->

    <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; color: #555;">

    <?php if (!empty($row['reporter_name'])): ?>

        <!-- Reporter -->

        <div style="display: flex; align-items: center; gap: 6px; white-space: nowrap;">

            <img src="Admin/<?php echo htmlspecialchars(!empty($row['reporter_photo']) ? $row['reporter_photo'] : 'commentator.png'); ?>" 

                 alt="Reporter Icon" 

                 style="width: 22px; height: 22px; border-radius: 20%; object-fit: cover;">

            <span style="font-weight: 600;">প্রতিবেদন <?php echo htmlspecialchars($row['reporter_name']); ?></span>

        </div>

    <?php else: ?>

        <!-- Default text if no reporter found -->

        <div style="white-space: nowrap; font-weight: 600;">

            নিজস্ব প্রতিবেদন

        </div>

    <?php endif; ?>

</div>





    <!-- Separator dot -->



  







  </div>



</div>













<div style="position: relative; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">

    <div style="position: relative;">

        

        <!-- Main Image -->

        <img 

            src="Admin/img/<?php echo htmlspecialchars($row['image_url']); ?>" 

            alt="News" 

            style="width: 100%; height: auto; display: block; border-radius: 8px;"

        >



        <!-- Frame Overlay -->

        <img 

            src="img/frame.png" 

            alt="Frame"

            style="

                position: absolute;

                top: 0;

                left: 0;

                width: 100%;

                height: 100%;

                object-fit: cover;

                pointer-events: none;

            "

        >



    </div>

</div>





















<!-- Views Display -->

<div style="display: flex; align-items: center; gap: 4px; margin-top: -5px; border-radius: 5px;">

    <i class="fas fa-eye" style="font-size: 13px; color: #6c757d;"></i>

    <span style="font-size: 12px; font-weight: 500; color: #6c757d;">

        <strong><?php echo number_format($views_count); ?></strong>  জন পড়েছেন 

    </span>

</div>



<!-- Share Features -->

<div style="display: flex; align-items: center; gap: 0px; flex-wrap: wrap;">

    <!-- "Share on" text -->

    <span style="font-size: 16px; margin-right: 8px;">Share on:</span>



    <!-- Facebook Share -->

    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank"

       style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">

        <img src="img/facebook.png" alt="Facebook" style="width: 24px; height: 24px;">

    </a>



    <!-- PDF Download (triggers JS function) -->

    <a href="javascript:void(0);" onclick="downloadAsPDF()" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">

        <img src="img/pdf.png" alt="PDF" style="width: 24px; height: 24px;">

    </a>



    <!-- Copy Link -->

    <a href="javascript:void(0);" onclick="copyPageURL()" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">

        <img src="img/copy.png" alt="Copy" style="width: 24px; height: 24px;">

    </a>



    <!-- Print -->

    <a href="javascript:window.print()" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">

        <img src="img/printer.png" alt="Print" style="width: 24px; height: 24px;">

    </a>



    <!-- More (placeholder link) -->

    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">

        <img src="img/next.png" alt="More" style="width: 24px; height: 24px;">

    </a>

</div>











                        

<?php



$news = $row['news_1'] ?? '';

$firstLetter = '';

$restOfNews = '';



// Replace <bold>...</bold> with <strong>...</strong> for HTML rendering

function renderBoldTags($text) {

    // Replace <bold>...</bold> with <strong>...</strong>

    return preg_replace('/<bold>(.*?)<\/bold>/is', '<strong>$1</strong>', $text);

}



// Check if news is not empty and extract first letter and rest

if ($news !== '') {

    $firstLetter = mb_substr($news, 0, 1, 'UTF-8');  // get first character (Unicode-safe)

    $restOfNews = mb_substr($news, 1, null, 'UTF-8'); // get rest of string

}

?>



<p style="

    text-align: justify;

    margin: 20px 0;

">

    <span style="

        float: left;

        font-size: 48px;

        line-height: 1;

        font-weight: bold;

        color: #000;

        margin-right: 12px;

         font-family: 'SolaimanLipi', sans-serif !important;

    "></span>

    <?php echo renderBoldTags(htmlspecialchars_decode($news)); ?>

</p>











                      <!--  <p class="my-4">

                            <?php echo renderBoldTags(htmlspecialchars_decode($row['news_2'])); ?>

                        </p> -->

                       <?php if (!empty($row['quote_1'])): ?>

<div class="bg-light p-4 mb-4 rounded border-start border-5 border-primary shadow-sm" style="position: relative;">

  <div style="position: absolute; left: 1rem; top: 1.2rem; font-size: 2.5rem; color: #0d6efd; font-weight: bold; line-height: 1;">

    &#8220;

  </div>

  <h1 class="mb-0 ps-4" style="color: black; font-weight: 500; font-size: 1rem; font-family: 'SolaimanLipi', sans-serif;"> 

    <?php echo htmlspecialchars($row['quote_1']); ?>

  </h1>

  <div class="text-end mt-3" style="font-style: normal; font-weight: 600; color: #6c757d; font-size: 1rem;">

    — <?php echo htmlspecialchars($row['auture_1'] ?? ''); ?>

  </div>

</div>

<?php endif; ?>





                       <?php if (!empty($row['image_2']) || !empty($row['image_3'])): ?>

<div class="row g-4">

    <?php if (!empty($row['image_2']) && !empty($row['image_3'])): ?>

        <div class="col-6">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_2']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

        <div class="col-6">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_3']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php elseif (!empty($row['image_2'])): ?>

        <div class="col-12">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_2']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php elseif (!empty($row['image_3'])): ?>

        <div class="col-12">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_3']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php endif; ?>

</div>

<br>

<?php endif; ?>



                        <p class="my-4"><?php echo renderBoldTags(htmlspecialchars_decode($row['news_3'])); ?>

                        </p>

                        <?php if (!empty($row['quote_2'])): ?>

<div class="bg-light p-4 mb-4 rounded border-start border-5 border-primary shadow-sm" style="position: relative;">

  <div style="position: absolute; left: 1rem; top: 1.2rem; font-size: 2.5rem; color: #0d6efd; font-weight: bold; line-height: 1;">

    &#8220;

  </div>

  <h1 class="mb-0 ps-4" style="color: black; font-weight: 500; font-size: 1rem; font-family: 'SolaimanLipi', sans-serif;"> 

    <?php echo htmlspecialchars($row['quote_2']); ?>

  </h1>

  <div class="text-end mt-3" style="font-style: normal; font-weight: 600; color: #6c757d; font-size: 1rem;">

    — <?php echo htmlspecialchars($row['auture_2'] ?? ''); ?>

  </div>

</div>

<?php endif; ?>





<?php if (!empty($row['image_4']) || !empty($row['image_5'])): ?>

<div class="row g-4">

    <?php if (!empty($row['image_4']) && !empty($row['image_5'])): ?>

        <div class="col-6">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_4']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

        <div class="col-6">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_5']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php elseif (!empty($row['image_4'])): ?>

        <div class="col-12">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_4']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php elseif (!empty($row['image_5'])): ?>

        <div class="col-12">

            <div class="rounded overflow-hidden">

                <img src="Admin/img/<?php echo htmlspecialchars($row['image_5']); ?>" class="img-zoomin img-fluid rounded w-100" alt="">

            </div>

        </div>

    <?php endif; ?>

</div>

<br>

<?php endif; ?>

            

                        <!-- Infinite Scroll News Container -->

    <div class="infinite-news-container" id="infinite-news-container">

        <div class="infinite-news-header">

            <div class="infinite-news-header-icon">

                <i class="fas fa-newspaper"></i>

            </div>

            <h3>আরও সংবাদ পড়ুন</h3>

        </div>

        

        <div id="infinite-news-list">

            <!-- News articles will be loaded here via AJAX -->

        </div>

        

        <div class="loading-spinner" id="loading-spinner" style="display: none;">

            <div class="spinner"></div>

        </div>

        

        <div class="end-of-news" id="end-of-news" style="display: none;">

            <i class="fas fa-check-circle"></i> সকল সংবাদ দেখা হয়েছে

        </div>

    </div>



                    </div><!-- End col-lg-8 -->

                    

                    <!-- Sticky Sidebar -->

                    <div class="col-lg-4">

                        <div class="news-sidebar">

                            <div class="sticky-sidebar">

                                <div class="popular-section">

                                    <div class="popular-header">

                                        <div class="popular-header-icon">

                                            <i class="fas fa-fire"></i>

                                        </div>

                                        <h4>জনপ্রিয় সংবাদ</h4>

                                    </div>

                                    

<?php

include 'connection.php';

date_default_timezone_set('Asia/Dhaka');



// Bangla conversion helpers for sidebar

if (!function_exists('en2bnNumberSidebar')) {

    function en2bnNumberSidebar($number) {

        $en = ['0','1','2','3','4','5','6','7','8','9'];

        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

        return str_replace($en, $bn, $number);

    }

}



if (!function_exists('en2bnMonthSidebar')) {

    function en2bnMonthSidebar($dateStr) {

        $enMonths = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        $bnMonths = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];

        return str_replace($enMonths, $bnMonths, $dateStr);

    }

}



// Fetch popular news (by views or recent)

$popular_news_query = "SELECT n.*, c.name as category_name, c.slug AS category_slug 

                       FROM news n 

                       JOIN category c ON n.category_id = c.id 

                       WHERE n.is_active = 1 

                       ORDER BY n.views DESC, n.created_at DESC 

                       LIMIT 10";



$popular_news_result = mysqli_query($conn, $popular_news_query);



while ($pnews = mysqli_fetch_assoc($popular_news_result)):

    $dateObj = new DateTime($pnews['created_at']);

    $datePart = $dateObj->format('d M');

    $bangla_date = en2bnNumberSidebar(en2bnMonthSidebar($datePart));



    $pimage = !empty($pnews['image_url']) ? $pnews['image_url'] : 'default.jpg';

    $pheadline = htmlspecialchars($pnews['headline']);

    $pslug = urlencode($pnews['slug']);

    $pcategory_slug = urlencode($pnews['category_slug']);

    $plink = "news.php?path={$pcategory_slug}/{$pslug}";

    $pviews = isset($pnews['views']) ? number_format($pnews['views']) : '0';

?>

                                    <div class="popular-news-card">

                                        <div class="popular-news-thumb">

                                            <a href="<?php echo $plink; ?>">

                                                <img src="Admin/img/<?php echo $pimage; ?>" alt="<?php echo $pheadline; ?>">

                                            </a>

                                        </div>

                                        <div class="popular-news-info">

                                            <h6><a href="<?php echo $plink; ?>"><?php echo $pheadline; ?></a></h6>

                                            <div class="popular-news-meta">

                                                <span><i class="fas fa-eye"></i> <?php echo $pviews; ?></span>

                                                <span><i class="fas fa-calendar"></i> <?php echo $bangla_date; ?></span>

                                            </div>

                                        </div>

                                    </div>

<?php 

endwhile; 

?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Single Product End -->





         <!-- Footer Start -->

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

                    হিন্দু'স নিউজ একটি সম্পূর্ণ অরাজনৈতিক ও মানবাধিকারভিত্তিক নিউজ পোর্টাল। দেশের সংখ্যালঘু হিন্দু সম্প্রদায়ের বাস্তব সমস্যা, নির্যাতন ও অধিকার নিয়ে বস্তুনিষ্ঠ সংবাদ উপস্থাপন করাই আমাদের মূল লক্ষ্য।

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





        <!-- Copyright Start 

        <div class="container-fluid copyright bg-dark py-4">

            <div class="container">

                <div class="row">

                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">

                        <span class="text-light"><i class="fas fa-copyright text-light me-2"></i>STV24 All right reserved.</span>

                    </div>

                    

                </div>

            </div>

        </div>

      Copyright End -->

         





        <!-- Back to Top -->

        <a href="#" class="btn btn-primary border-2 border-white rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   



        

        <!-- JavaScript Libraries -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

        <script src="lib/easing/easing.min.js"></script>

        <script src="lib/waypoints/waypoints.min.js"></script>

        <script src="lib/owlcarousel/owl.carousel.min.js"></script>



        <!-- Template Javascript -->

        <script src="js/main.js"></script>

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

        }



        // Run on page load

        checkResponsive();



        // Run on screen resize

        window.addEventListener("resize", checkResponsive);

    </script>

    <!-- Infinite Scroll Script -->

    <script>

    (function() {

        let currentPage = 0;

        let loading = false;

        let allLoaded = false;

        const currentNewsSlug = '<?php echo addslashes($newsSlug); ?>';

        

        // Build full article HTML

        function buildFullArticle(news) {

            let imagesRow = '';

            if (news.image_2 && news.image_3) {

                imagesRow = `

                    <div class="row g-4 my-3">

                        <div class="col-6"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_2}" class="img-fluid rounded w-100" alt=""></div></div>

                        <div class="col-6"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_3}" class="img-fluid rounded w-100" alt=""></div></div>

                    </div>`;

            } else if (news.image_2) {

                imagesRow = `<div class="my-3"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_2}" class="img-fluid rounded w-100" alt=""></div></div>`;

            }

            

            let imagesRow2 = '';

            if (news.image_4 && news.image_5) {

                imagesRow2 = `

                    <div class="row g-4 my-3">

                        <div class="col-6"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_4}" class="img-fluid rounded w-100" alt=""></div></div>

                        <div class="col-6"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_5}" class="img-fluid rounded w-100" alt=""></div></div>

                    </div>`;

            } else if (news.image_4) {

                imagesRow2 = `<div class="my-3"><div class="rounded overflow-hidden"><img src="Admin/img/${news.image_4}" class="img-fluid rounded w-100" alt=""></div></div>`;

            }

            

            let quote1 = '';

            if (news.quote_1) {

                quote1 = `

                    <div class="bg-light p-4 mb-4 rounded border-start border-5 border-primary shadow-sm" style="position: relative;">

                        <div style="position: absolute; left: 1rem; top: 1.2rem; font-size: 2.5rem; color: #0d6efd; font-weight: bold; line-height: 1;">&#8220;</div>

                        <h1 class="mb-0 ps-4" style="color: black; font-weight: 500; font-size: 1rem; font-family: 'SolaimanLipi', sans-serif;">${news.quote_1}</h1>

                        <div class="text-end mt-3" style="font-style: normal; font-weight: 600; color: #6c757d; font-size: 1rem;">— ${news.auture_1}</div>

                    </div>`;

            }

            

            let quote2 = '';

            if (news.quote_2) {

                quote2 = `

                    <div class="bg-light p-4 mb-4 rounded border-start border-5 border-primary shadow-sm" style="position: relative;">

                        <div style="position: absolute; left: 1rem; top: 1.2rem; font-size: 2.5rem; color: #0d6efd; font-weight: bold; line-height: 1;">&#8220;</div>

                        <h1 class="mb-0 ps-4" style="color: black; font-weight: 500; font-size: 1rem; font-family: 'SolaimanLipi', sans-serif;">${news.quote_2}</h1>

                        <div class="text-end mt-3" style="font-style: normal; font-weight: 600; color: #6c757d; font-size: 1rem;">— ${news.auture_2}</div>

                    </div>`;

            }

            

            let reporterSection = news.reporter_name ? 

                `<div style="display: flex; align-items: center; gap: 6px; white-space: nowrap;">

                    <img src="Admin/${news.reporter_photo || 'commentator.png'}" alt="Reporter" style="width: 22px; height: 22px; border-radius: 20%; object-fit: cover;">

                    <span style="font-weight: 600;">প্রতিবেদন ${news.reporter_name}</span>

                </div>` : 

                `<div style="white-space: nowrap; font-weight: 600;">নিজস্ব প্রতিবেদন</div>`;

            

            return `

                <article class="full-news-article" data-news-id="${news.id}">

                    <div class="article-separator">

                        <span>পরবর্তী সংবাদ</span>

                    </div>

                    

                    <div style="border-left: 3px solid #e63946; padding-left: 10px; margin-bottom: 12px;">

                        <h2 style="font-size: 25px; font-weight: 600; margin: 0 0 6px 0; color: #1d1f20; line-height: 1.1;">

                            ${news.category_name} ডেস্ক

                        </h2>

                        <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; color: #555;">

                            ${reporterSection}

                            <div style="display: flex; align-items: center; gap: 4px; white-space: nowrap; font-weight: 500; color: #666;">

                                <i class="fa-solid fa-calendar"></i>

                                <span>${news.date}</span>

                            </div>

                        </div>

                    </div>

                    

                    <h1 class="article-main-headline">${news.headline}</h1>

                    <p class="article-short-desc">${news.short_description}</p>

                    

                    <div style="position: relative; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">

                        <img src="Admin/img/${news.image_url}" alt="News" style="width: 100%; height: auto; display: block; border-radius: 8px;">

                        <img src="img/frame.png" alt="Frame" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">

                    </div>

                    

                    <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 10px;">

                        <i class="fas fa-eye" style="font-size: 13px; color: #6c757d;"></i>

                        <span style="font-size: 12px; font-weight: 500; color: #6c757d;"><strong>${news.views}</strong> জন পড়েছেন</span>

                    </div>

                    

                    <p style="text-align: justify; margin: 20px 0;">${news.news_1}</p>

                    

                    ${quote1}

                    ${imagesRow}

                    

                    <p class="my-4">${news.news_3}</p>

                    

                    ${quote2}

                    ${imagesRow2}

                    

                    <p class="my-4">${news.news_4}</p>

                </article>

            `;

        }

        

        // Load more news when scrolling

        function loadMoreNews() {

            if (loading || allLoaded) return;

            

            loading = true;

            const spinner = document.getElementById('loading-spinner');

            spinner.style.display = 'flex';

            

            // Small delay to ensure spinner is visible

            setTimeout(function() {

                fetch('ajax_load_news.php?page=' + currentPage + '&exclude=' + encodeURIComponent(currentNewsSlug))

                    .then(response => response.json())

                    .then(data => {

                        if (data.news && data.news.length > 0) {

                            data.news.forEach(news => {

                                const articleHTML = buildFullArticle(news);

                                document.getElementById('infinite-news-list').insertAdjacentHTML('beforeend', articleHTML);

                            });

                            currentPage++;

                        }

                        

                        if (!data.hasMore) {

                            allLoaded = true;

                            document.getElementById('end-of-news').style.display = 'block';

                        }

                        

                        loading = false;

                        spinner.style.display = 'none';

                    })

                    .catch(error => {

                        console.error('Error loading news:', error);

                        loading = false;

                        spinner.style.display = 'none';

                    });

            }, 300);

        }

        

        // Check if user scrolled near bottom of content

        function checkScroll() {

            if (loading || allLoaded) return;

            

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

            const scrollHeight = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);

            const clientHeight = window.innerHeight || document.documentElement.clientHeight;

            

            // Load more when user is 300px from bottom

            if (scrollTop + clientHeight >= scrollHeight - 300) {

                loadMoreNews();

            }

        }

        

        // Start when DOM is ready

        document.addEventListener('DOMContentLoaded', function() {

            // Listen for scroll events (works on both desktop and mobile)

            window.addEventListener('scroll', checkScroll, { passive: true });

            

            // Also check on touch move for mobile

            window.addEventListener('touchmove', checkScroll, { passive: true });

            

            // Initial check in case page is short

            setTimeout(checkScroll, 500);

        });

    })();

    </script>

    </body>



</html>