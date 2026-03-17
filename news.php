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

// Set proper headers for Facebook crawler
header('Content-Type: text/html; charset=UTF-8');
header('HTTP/1.1 200 OK');

include 'connection.php';

if (!function_exists('mb_substr')) {
    function mb_substr($string, $start, $length = null, $encoding = null) {
        if ($length === null) {
            return substr($string, $start);
        }
        return substr($string, $start, $length);
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = null) {
        return strlen($string);
    }
}

$gdAvailable = function_exists('imagecreatefromstring') && function_exists('imagecreatetruecolor') && function_exists('imagejpeg') && function_exists('imagesx') && function_exists('imagesy');

$stmtFetchAssoc = function ($stmt) {
    $meta = $stmt->result_metadata();
    if (!$meta) {
        return null;
    }
    $fields = $meta->fetch_fields();
    $row = [];
    $bindArgs = [];
    foreach ($fields as $field) {
        $row[$field->name] = null;
        $bindArgs[] = &$row[$field->name];
    }
    call_user_func_array([$stmt, 'bind_result'], $bindArgs);
    if ($stmt->fetch()) {
        $out = [];
        foreach ($row as $k => $v) {
            $out[$k] = $v;
        }
        return $out;
    }
    return null;
};

// Ensure database connection uses UTF-8
if (isset($conn)) {
    $conn->set_charset("utf8mb4");
}

// Fetch basic_info for footer
$tbl_basic_info = 'basic_info';
$basic_info = [];
$sql_basic_info = "SELECT id, news_portal_name, image, description, editor_in_chief, media_info, 
                   privacy_policy, about_us, comment_policy, advertisement_policy, terms, 
                   advertisement_list, facebook, youtube, whatsapp, twitter, tiktok, instagram, 
                   mobile_number, email FROM $tbl_basic_info LIMIT 1";
$result_basic_info = $conn->query($sql_basic_info);
if ($result_basic_info && $result_basic_info->num_rows > 0) {
    $basic_info = $result_basic_info->fetch_assoc();
}

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set default values in case of errors
$error_occurred = false;
$error_message = '';

// Get and split the path - handle Bengali characters properly
$newsId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$path = isset($_GET['path']) ? $_GET['path'] : '';
// Don't double-decode - PHP automatically decodes GET parameters
$parts = explode('/', $path);

// Encode each segment but keep '/' literal (better compatibility than encoding '/' as %2F)
$encodedPathForUrl = implode('/', array_map('rawurlencode', $parts));

if (count($parts) === 2) {
    $encodedPathForUrl = rawurlencode($parts[0]) . '/' . rawurlencode($parts[1]);
}

// Initialize default variables
$row = ['headline' => '', 'category_name' => 'News'];
$meta = [];
$views_count = 0;

if ($newsId > 0) {
    $sql = "SELECT n.*, c.name AS category_name, c.slug AS category_slug, r.name AS reporter_name, r.image AS reporter_photo
            FROM news n
            JOIN category c ON n.category_id = c.id
            LEFT JOIN reporter r ON n.reporter_id = r.id
            WHERE n.id = ? AND n.is_active = 1
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $sql = "SELECT n.*, c.name AS category_name, c.slug AS category_slug, r.name AS reporter_name, r.image AS reporter_photo
                FROM news n
                JOIN category c ON n.category_id = c.id
                LEFT JOIN reporter r ON n.reporter_id = r.id
                WHERE n.id = ?
                LIMIT 1";
        $stmt = $conn->prepare($sql);
    }

    if (!$stmt) {
        $error_occurred = true;
        $error_message = "Database query failed";
    } else {
        $stmt->bind_param("i", $newsId);
        $stmt->execute();

        $foundRow = null;
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $foundRow = $result->fetch_assoc();
            }
        } else {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $foundRow = $stmtFetchAssoc($stmt);
            }
        }

        if (!empty($foundRow)) {
            $row = $foundRow;
            $meta = $row;

            if (!empty($row['category_slug']) && !empty($row['slug'])) {
                $path = $row['category_slug'] . '/' . $row['slug'];
                $parts = explode('/', $path);
                $encodedPathForUrl = rawurlencode($parts[0]) . '/' . rawurlencode($parts[1]);
                if (empty($_GET['path'])) {
                    $_GET['path'] = $path;
                }
            }
            
            // Increment view count for id-based URL
            try {
                $update_views_sql = "UPDATE news SET views = views + 1 WHERE id = ?";
                $update_views_stmt = $conn->prepare($update_views_sql);
                $update_views_stmt->bind_param("i", $newsId);
                $update_views_stmt->execute();
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'views') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {
                    try {
                        $conn->query("ALTER TABLE news ADD COLUMN views INT DEFAULT 0");
                        $update_views_stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
                        $update_views_stmt->bind_param("i", $newsId);
                        $update_views_stmt->execute();
                    } catch (Exception $inner_e) {}
                }
            }
            
            // Get current view count for id-based URL
            try {
                $views_sql = "SELECT views FROM news WHERE id = ? AND is_active = 1 LIMIT 1";
                $views_stmt = $conn->prepare($views_sql);
                if (!$views_stmt) {
                    $views_sql = "SELECT views FROM news WHERE id = ? LIMIT 1";
                    $views_stmt = $conn->prepare($views_sql);
                }
                if ($views_stmt) {
                    $views_stmt->bind_param("i", $newsId);
                    $views_stmt->execute();
                    if (method_exists($views_stmt, 'get_result')) {
                        $views_result = $views_stmt->get_result();
                        if ($views_result && ($views_row = $views_result->fetch_assoc())) {
                            $views_count = $views_row['views'] ?? 0;
                        }
                    } else {
                        $views_stmt->store_result();
                        if ($views_stmt->num_rows > 0) {
                            $views_row = $stmtFetchAssoc($views_stmt);
                            if (!empty($views_row)) {
                                $views_count = $views_row['views'] ?? 0;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $views_count = 0;
            }
        } else {
            $error_occurred = true;
            $error_message = "News not found";
        }
    }
} elseif (count($parts) === 2) {
    $categorySlug = $parts[0];
    $newsSlug = $parts[1];
    
    // Simplified query: search by news slug only (more flexible)
    $sql = "SELECT n.*, c.name AS category_name, c.slug AS category_slug
            FROM news n
            JOIN category c ON n.category_id = c.id
            WHERE n.slug = ? AND n.is_active = 1
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $sql = "SELECT n.*, c.name AS category_name, c.slug AS category_slug
                FROM news n
                JOIN category c ON n.category_id = c.id
                WHERE n.slug = ?
                LIMIT 1";
        $stmt = $conn->prepare($sql);
    }

    if (!$stmt) {
        $error_occurred = true;
        $error_message = "Database query failed";
    } else {
        $stmt->bind_param("s", $newsSlug);
        $stmt->execute();

        $foundRow = null;
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $foundRow = $result->fetch_assoc();
            }
        } else {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $foundRow = $stmtFetchAssoc($stmt);
            }
        }

        if (!empty($foundRow)) {
            $row = $foundRow;
            $meta = $row; // Use the same result for meta data
        
        // Increment view count
        try {
            $update_views_sql = "UPDATE news SET views = views + 1 WHERE slug = ?";
            $update_views_stmt = $conn->prepare($update_views_sql);
            $update_views_stmt->bind_param("s", $newsSlug);
            $update_views_stmt->execute();
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'views') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {
                try {
                    $conn->query("ALTER TABLE news ADD COLUMN views INT DEFAULT 0");
                    $update_views_stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE slug = ?");
                    $update_views_stmt->bind_param("s", $newsSlug);
                    $update_views_stmt->execute();
                } catch (Exception $inner_e) {}
            }
        }
        
        // Get current view count
        try {
            $views_sql = "SELECT views FROM news WHERE slug = ? AND is_active = 1 LIMIT 1";
            $views_stmt = $conn->prepare($views_sql);
            if (!$views_stmt) {
                $views_sql = "SELECT views FROM news WHERE slug = ? LIMIT 1";
                $views_stmt = $conn->prepare($views_sql);
            }
            if ($views_stmt) {
                $views_stmt->bind_param("s", $newsSlug);
                $views_stmt->execute();
                if (method_exists($views_stmt, 'get_result')) {
                    $views_result = $views_stmt->get_result();
                    if ($views_result && ($views_row = $views_result->fetch_assoc())) {
                        $views_count = $views_row['views'] ?? 0;
                    }
                } else {
                    $views_stmt->store_result();
                    if ($views_stmt->num_rows > 0) {
                        $views_row = $stmtFetchAssoc($views_stmt);
                        if (!empty($views_row)) {
                            $views_count = $views_row['views'] ?? 0;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $views_count = 0;
        }
        } else {
            $error_occurred = true;
            $error_message = "News not found";
        }
    }
} else {
    $error_occurred = true;
    $error_message = "Invalid news path";
}

// ============ META TAGS CONFIGURATION ============
$site_name = 'Hindus News';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'hindus-news.com';
$site_url = $scheme . '://' . $host;

// Prepare meta variables with proper sanitization and strong fallbacks
if (!empty($meta['headline'])) {
    $meta_headline = htmlspecialchars($meta['headline'], ENT_QUOTES, 'UTF-8');
} else {
    $meta_headline = 'Hindus News';
}

if (!empty($meta['short_description'])) {
    $descSource = $meta['short_description'];
} elseif (!empty($meta['news_1'])) {
    $descSource = $meta['news_1'];
} elseif (!empty($row['news_1'])) {
    $descSource = $row['news_1'];
} else {
    $descSource = '';
}

if ($descSource !== '') {
    $descSource = htmlspecialchars_decode((string)$descSource, ENT_QUOTES);
    $descSource = strip_tags($descSource);
    $descSource = preg_replace('/\s+/', ' ', trim($descSource));

    $meta_description = htmlspecialchars(
        mb_substr($descSource, 0, 160),
        ENT_QUOTES,
        'UTF-8'
    );
} else {
    $meta_description = 'Read the latest news and updates from Hindus News. Breaking news, Bangladesh news, and more.';
}

// Category and author information
$meta_category = !empty($row['category_name']) ? htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8') : 'News';
$meta_author = !empty($meta['reporter_name']) ? htmlspecialchars($meta['reporter_name'], ENT_QUOTES, 'UTF-8') : $site_name;

// Generate absolute image URL
if (!empty($meta['image_url'])) {
    $imageRel = ltrim($meta['image_url'], '/');
    $meta_image_original = $site_url . '/Admin/img/' . str_replace('%2F', '/', rawurlencode($imageRel));
} else {
    $meta_image_original = $site_url . '/logo.png';
}

// Generate watermarked image URL for social media (static cached file)
$meta_image = $meta_image_original;
if (!empty($meta['image_url']) && $gdAvailable) {
    $cacheDir = __DIR__ . '/social_cache';
    if (!file_exists($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFileName = md5($meta['image_url']) . '.jpg';
    $cachePath = $cacheDir . '/' . $cacheFileName;
    
    if (!file_exists($cachePath) || (time() - filemtime($cachePath)) > 86400) {
        $newsImagePath = __DIR__ . '/Admin/img/' . $meta['image_url'];
        $framePath = __DIR__ . '/Admin/img/frame.jpg';
        
        if (file_exists($newsImagePath)) {
            $newsImage = @imagecreatefromstring(file_get_contents($newsImagePath));
            
            if ($newsImage) {
                $newsWidth = imagesx($newsImage);
                $newsHeight = imagesy($newsImage);
                $targetWidth = 1200;
                $targetHeight = 630;
                $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
                
                $newsAspect = $newsWidth / $newsHeight;
                $targetAspect = $targetWidth / $targetHeight;
                
                if ($newsAspect > $targetAspect) {
                    $newWidth = $targetWidth;
                    $newHeight = (int)($targetWidth / $newsAspect);
                    $offsetX = 0;
                    $offsetY = (int)(($targetHeight - $newHeight) / 2);
                } else {
                    $newHeight = $targetHeight;
                    $newWidth = (int)($targetHeight * $newsAspect);
                    $offsetX = (int)(($targetWidth - $newWidth) / 2);
                    $offsetY = 0;
                }
                
                $white = imagecolorallocate($canvas, 255, 255, 255);
                imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
                imagecopyresampled($canvas, $newsImage, $offsetX, $offsetY, 0, 0, $newWidth, $newHeight, $newsWidth, $newsHeight);
                
                if (file_exists($framePath)) {
                    $frameImage = @imagecreatefromstring(file_get_contents($framePath));
                    if ($frameImage) {
                        $frameWidth = imagesx($frameImage);
                        $frameHeight = imagesy($frameImage);
                        $frameTargetHeight = (int)($targetHeight * 0.15);
                        $frameTargetWidth = $targetWidth;
                        $frameY = $targetHeight - $frameTargetHeight;
                        imagecopyresampled($canvas, $frameImage, 0, $frameY, 0, 0, $frameTargetWidth, $frameTargetHeight, $frameWidth, $frameHeight);
                        imagedestroy($frameImage);
                    }
                }
                
                @imagejpeg($canvas, $cachePath, 90);
                imagedestroy($newsImage);
                imagedestroy($canvas);
            }
        }
    }
    
    if (file_exists($cachePath)) {
        $meta_image = $site_url . '/social_cache/' . $cacheFileName;
    }
}

// Generate canonical URL - use actual URL format with news.php?path=
if (!empty($row['id'])) {
    $meta_url = $site_url . '/news.php?id=' . rawurlencode((string)$row['id']);
} else {
    $meta_url = $site_url . '/news.php?path=' . $encodedPathForUrl;
}

$meta_image_secure = $meta_image;
if ($scheme === 'https' && strpos($meta_image_secure, 'http://') === 0) {
    $meta_image_secure = 'https://' . substr($meta_image_secure, 7);
}

$meta_image_type = 'image/jpeg';
$meta_image_path = parse_url($meta_image, PHP_URL_PATH);
$meta_image_ext = strtolower(pathinfo($meta_image_path ?? '', PATHINFO_EXTENSION));
if ($meta_image_ext === 'png') {
    $meta_image_type = 'image/png';
} elseif ($meta_image_ext === 'webp') {
    $meta_image_type = 'image/webp';
}

// Article metadata
$meta_published = !empty($meta['created_at']) ? date('c', strtotime($meta['created_at'])) : date('c');
$meta_modified = !empty($meta['updated_at']) ? date('c', strtotime($meta['updated_at'])) : $meta_published;

// Keywords generation
$meta_keywords = $meta_category . ', ' . $site_name . ', Latest News, Breaking News, Bangladesh News, ' . 
                 str_replace([',', '.', '!', '?'], '', mb_substr($meta_headline, 0, 50));
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6023610381338539" crossorigin="anonymous"></script>
        <script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php if (!empty($basic_info['image'])): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($basic_info['image']); ?>">
        <?php endif; ?>

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
        <meta property="fb:app_id" content="3906305243002966">
        <meta property="og:site_name" content="<?php echo $site_name; ?>">
        <meta property="og:title" content="<?php echo $meta_headline; ?>">
        <meta property="og:description" content="<?php echo $meta_description; ?>">
        <meta property="og:url" content="<?php echo $meta_url; ?>">
        <meta property="og:image" content="<?php echo $meta_image; ?>">
        <meta property="og:image:secure_url" content="<?php echo $meta_image_secure; ?>">
        <meta property="og:image:type" content="<?php echo $meta_image_type; ?>">
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
        
        

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@100;600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Font Awesome 6.5.1 (stable version with correct integrity) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <!-- Removed: local fontawesome-5.0.8 (using CDN version above) -->
    <!--===============================================================================================-->
    <!-- Removed: material-design-iconic-font (not found, using Font Awesome instead) -->
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
    /* Video Section Styles */
    .video-section {
        background: #1a1a1a;
        padding: 30px 0;
        margin: 0px 0;
        margin-top: -50px;
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
        font-size: 13px;
        font-weight: 600;
        color: #f59e0b;
        line-height: 1.4;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .video-excerpt {
        font-size: 11px;
        color: #ffffff;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Latest News Section Styles */
    .latest-news-section {
        padding: 40px 0;
        margin: 0px 0;
    }
    
    .latest-news-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .latest-news-header {
        text-align: center;
        margin-bottom: 35px;
    }
    
    .latest-news-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
        font-family: 'SolaimanLipi', sans-serif;
    }
    
    .header-line {
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, #dc2626, #b91c1c);
        margin: 0 auto;
        border-radius: 2px;
    }
    
    .latest-news-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }
    
    .latest-news-card {
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .latest-news-card:hover {
        transform: translateY(-5px);
    }
    
    .latest-news-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .latest-news-image {
        width: 100%;
        height: 180px;
        overflow: hidden;
        position: relative;
    }
    
    .latest-news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .latest-news-card:hover .latest-news-image img {
        transform: scale(1.1);
    }
    
    .latest-news-content {
        padding: 10px 0;
    }
    
    .latest-news-headline {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.5;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-family: 'SolaimanLipi', sans-serif;
        min-height: 63px;
    }
    
    .latest-news-date {
        font-size: 11px;
        color: #666;
        font-family: 'SolaimanLipi', sans-serif;
    }

    /* Responsive Styles */
    @media (max-width: 1200px) {
        .video-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .latest-news-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .video-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .latest-news-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .video-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .latest-news-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .video-section {
           padding: 20px 0;
            margin: 0px 0;
            margin-top: -100px;
        }
        .latest-news-section {
            padding: 20px 0;
            margin: 0px 0;
        }
        
        .latest-news-header h2 {
            font-size: 22px;
        }
    }
    
    @media (max-width: 576px) {
        .latest-news-grid {
            grid-template-columns: 1fr;
        }
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
        transition: all 0.3s ease;
    }
    
    .video-modal-close:hover {
        color: #FF0000;
    }
    
    .video-modal iframe {
        width: 100%;
        height: 500px;
        border-radius: 12px;
    }
    
    @media (max-width: 768px) {
        .video-modal iframe {
            height: 300px;
        }
    }

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
    /* ==== COPY PROTECTION STYLES ==== */
    * {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }
    
    body {
        -webkit-touch-callout: none !important;
    }
    
    img {
        pointer-events: none !important;
        -webkit-user-drag: none !important;
        -khtml-user-drag: none !important;
        -moz-user-drag: none !important;
        -o-user-drag: none !important;
        user-drag: none !important;
    }
    
    /* Screenshot Warning Overlay */
    #screenshotWarning {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(220, 38, 38, 0.95);
        z-index: 999999;
        display: none;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        animation: warningPulse 1s infinite;
    }
    
    @keyframes warningPulse {
        0%, 100% { opacity: 0.95; }
        50% { opacity: 1; }
    }
    
    #screenshotWarning .warning-content {
        padding: 40px;
        max-width: 600px;
    }
    
    #screenshotWarning .warning-icon {
        font-size: 80px;
        margin-bottom: 20px;
    }
    </style>

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/head.css" rel="stylesheet">
        <style>
            
            @import url('https://fonts.maateen.me/solaiman-lipi/font.css');
             body{
            font-family: 'SolaimanLipi', sans-serif !important;
            color: #000 !important;
}
h6, .h6, h5, .h5, h4, .h4, h3, .h3, h2, .h2, h1, .h1 {
    font-family: 'SolaimanLipi', sans-serif !important;

}
h2{
    line-height: 1.4 !important;
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

/* Force all news paragraph text to black */
p {
    color: #000 !important;
}

/* Specific targeting for news content paragraphs */
.my-4,
article p,
.full-news-article p {
    color: #000 !important;
}

/* Override any Bootstrap or other styles */
.container p,
.col-lg-8 p {
    color: #000 !important;
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
        
        /* Hide right section on mobile */
        @media (max-width: 768px) {
            .right-section {
                display: none !important;
            }
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
        <amp-auto-ads type="adsense" data-ad-client="ca-pub-6023610381338539"></amp-auto-ads>
        
        <!-- Screenshot Warning Overlay -->
        <div id="screenshotWarning">
            <div class="warning-content">
                <div class="warning-icon">⚠️</div>
                <h2>SCREENSHOT DETECTED!</h2>
                <p>Screenshots are strictly prohibited on this page.</p>
                <p>This action has been logged with your IP address.</p>
            </div>
        </div>

        <!-- Logo Loading Animation Start -->
        <div id="spinner" class="logo-loader">
            <div class="logo-loader-content">
                <img src="" alt="" class="loader-logo">
            </div>
        </div>
        
        <style>
            .logo-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 99999;
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }
            
            .logo-loader.fade-out {
                opacity: 0;
                pointer-events: none;
            }
            
            .logo-loader-content {
                text-align: center;
            }
            
            .loader-logo {
                width: 200px;
                animation: logoAppear 1.2s ease-in-out;
            }
            
            @keyframes logoAppear {
                0% {
                    opacity: 0;
                    transform: scale(0.5) rotate(-10deg);
                }
                60% {
                    opacity: 1;
                    transform: scale(1.1) rotate(5deg);
                }
                100% {
                    opacity: 1;
                    transform: scale(1) rotate(0deg);
                }
            }
            
            @media (max-width: 768px) {
                .loader-logo {
                    width: 150px;
                }
            }
        </style>
        <!-- Logo Loading Animation End -->


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
        $query = "SELECT id, name FROM $tbl_category ORDER BY id ASC";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category_id = $row['id'];
                $categoryName = htmlspecialchars($row['name']); // sanitize output
                echo '<a href="category.php?id=' . $category_id . $user_id_suffix . '" class="sidebar-menu-link">' . $categoryName . '</a>';
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
// Display error if news not found
if ($error_occurred) {
    echo '<div class="alert alert-warning text-center p-5">';
    echo '<h2>' . htmlspecialchars($error_message) . '</h2>';
    echo '<p>The requested news article could not be found.</p>';
    echo '<a href="index.php' . $user_id_param . '" class="btn btn-primary mt-3">Go to Homepage</a>';
    echo '</div>';
    echo '</div></div>'; // Close containers
    echo '</body></html>';
    exit;
}

// Use data already fetched at top of file (supports both ID and path URLs)
if (!empty($meta)) {
?>
        <ol class="breadcrumb justify-content-start mb-4">
            <li class="breadcrumb-item"><a href="index.php<?= $user_id_param; ?>">হোম</a></li>
            <li class="breadcrumb-item"><a><?php echo htmlspecialchars($meta['category_name'] ?? ''); ?></a></li>
        </ol>
<?php
} else {
    echo "<h2>News not found</h2>";
}
?>



                <div class="row g-4">
                    <div class="col-lg-8">
                        <?php
// Use data already fetched at top of file (supports both ID and path URLs)
$row = $meta; // Restore $row from saved meta data
if (!empty($row)) {
?>
        <div class="mb-4" style="margin-top: -15px;">
            <h2><?php echo htmlspecialchars($row['headline']); ?></h2>
            <p><?php echo htmlspecialchars($row['short_description'] ?? ''); ?></p>
        </div>
<?php
} else {
    echo "<h2>News not found</h2>";
}
?>



                        <!-- Heading and Read Time -->
<div style="margin-bottom: 12px; font-family: 'SolaimanLipi', sans-serif !important;">


<!-- Reporter Card Section -->
<?php if (!empty($row['reporter_name'])): 
    $fullName = $row['reporter_name'];
    $displayName = $fullName;
    $subtitle = '';
    
    if (preg_match('/^(.+?)\s*\((.+?)\)\s*$/', $fullName, $matches)) {
        $displayName = trim($matches[1]);
        $subtitle = trim($matches[2]);
    }
?>
<div style="background: #f5f5f5; border-radius: 6px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; margin: 10px 0;">
    <img src="Admin/<?php echo htmlspecialchars(!empty($row['reporter_photo']) ? $row['reporter_photo'] : 'commentator.png'); ?>" 
         alt="<?php echo htmlspecialchars($displayName); ?>"
         loading="lazy"
         style="width: 45px; height: 45px; border-radius: 10px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
    <div style="display: flex; flex-direction: column; gap: 1px;">
        <span style="font-size: 15px; font-weight: 700; color: #222;">
            <?php echo htmlspecialchars($displayName); ?>
        </span>
        <?php if (!empty($subtitle)): ?>
        <span style="font-size: 12px; color: #666; font-weight: 500;">
            <?php echo htmlspecialchars($subtitle); ?>
        </span>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div style="background: #f5f5f5; border-radius: 6px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; margin: 10px 0;">
    <i class="fas fa-user-edit" style="font-size: 32px; color: #666; width: 45px; text-align: center;"></i>
    <div style="display: flex; flex-direction: column; gap: 1px;">
        <span style="font-size: 15px; font-weight: 700; color: #222;">
            নিজস্ব প্রতিবেদক
        </span>
        <span style="font-size: 12px; color: #666; font-weight: 500;">
            <?php echo htmlspecialchars($basic_info['news_portal_name'] ?? ''); ?>
        </span>
    </div>
</div>
<?php endif; ?>

<!-- Published Date -->
<div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #555; margin-bottom: 15px;">
    <span style="font-weight: 600;">প্রকাশিত:</span>
    <span>
        <?php
        $months = [
            'January' => 'জানুয়ারি',
            'February' => 'ফেব্রুয়ারি',
            'March' => 'মার্চ',
            'April' => 'এপ্রিল',
            'May' => 'মে',
            'June' => 'জুন',
            'July' => 'জুলাই',
            'August' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর',
            'November' => 'নভেম্বর',
            'December' => 'ডিসেম্বর'
        ];

        $numbers = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];

        $englishDate = date("d F Y, H:i", strtotime($row['created_at']));
        $banglaDate = strtr($englishDate, $months);
        $banglaDate = strtr($banglaDate, $numbers);

        echo $banglaDate;
        ?>
    </span>
    
</div>

</div>



<div class="main-image-container">
    <!-- Main News Image -->
    <img 
        src="Admin/img/<?php echo htmlspecialchars($row['image_url']); ?>" 
        alt="<?php echo !empty($row['image_url_title']) ? htmlspecialchars($row['image_url_title']) : 'News'; ?>" 
        class="main-news-image"
        loading="eager"
    >
    
    <!-- Frame Below Image 
    <img 
        src="img/frame.jpg" 
        alt="Frame"
        class="frame-image"
        loading="lazy"
    >-->
    
    <!-- Image Title After Frame -->
    <?php if (!empty($row['image_url_title'])): ?>
    <div class="main-image-title">
        <?php echo htmlspecialchars($row['image_url_title']); ?>
    </div>
    <?php endif; ?>
</div>









<!-- Views Display -->
<div style="display: flex; align-items: center; gap: 6px; margin-top: -5px; border-radius: 5px;">
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
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode($meta_url); ?>" target="_blank"
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
    color: #000 !important;
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
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_2']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_2_title']) ? htmlspecialchars($row['image_2_title']) : ''; ?>">
                <?php if (!empty($row['image_2_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_2_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
        <div class="col-6">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_3']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_3_title']) ? htmlspecialchars($row['image_3_title']) : ''; ?>">
                <?php if (!empty($row['image_3_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_3_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php elseif (!empty($row['image_2'])): ?>
        <div class="col-12">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_2']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_2_title']) ? htmlspecialchars($row['image_2_title']) : ''; ?>">
                <?php if (!empty($row['image_2_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_2_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php elseif (!empty($row['image_3'])): ?>
        <div class="col-12">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_3']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_3_title']) ? htmlspecialchars($row['image_3_title']) : ''; ?>">
                <?php if (!empty($row['image_3_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_3_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php endif; ?>
</div>
<br>
<?php endif; ?>


                        <p class="my-4" style="color: #000 !important; "><?php echo renderBoldTags(htmlspecialchars_decode($row['news_3'])); ?>
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
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_4']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_4_title']) ? htmlspecialchars($row['image_4_title']) : ''; ?>">
                <?php if (!empty($row['image_4_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_4_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
        <div class="col-6">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_5']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_5_title']) ? htmlspecialchars($row['image_5_title']) : ''; ?>">
                <?php if (!empty($row['image_5_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_5_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php elseif (!empty($row['image_4'])): ?>
        <div class="col-12">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_4']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_4_title']) ? htmlspecialchars($row['image_4_title']) : ''; ?>">
                <?php if (!empty($row['image_4_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_4_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php elseif (!empty($row['image_5'])): ?>
        <div class="col-12">
            <figure class="news-image-figure mb-0">
                <img src="Admin/img/<?php echo htmlspecialchars($row['image_5']); ?>" class="img-zoomin img-fluid rounded-top w-100" loading="lazy" alt="<?php echo !empty($row['image_5_title']) ? htmlspecialchars($row['image_5_title']) : ''; ?>">
                <?php if (!empty($row['image_5_title'])): ?>
                <figcaption class="news-image-caption small-caption">
                    <?php echo htmlspecialchars($row['image_5_title']); ?>
                </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php endif; ?>
</div>
<br>
<?php endif; ?>


                        <p class="my-4" style="color: #000 !important;"><?php echo renderBoldTags(htmlspecialchars_decode($row['news_4'])); ?>
                        </p>


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
// Don't re-include connection - already included at top
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

// Fetch top 10 most viewed news from entire database (highest views first)
$popular_news_query = "SELECT n.id, n.headline, n.slug, n.image_url, n.created_at, 
                       IFNULL(n.views, 0) as views,
                       c.name as category_name, c.slug AS category_slug 
                       FROM news n 
                       INNER JOIN category c ON n.category_id = c.id 
                       WHERE n.is_active = 1 
                       ORDER BY IFNULL(n.views, 0) DESC, n.created_at DESC 
                       LIMIT 10";

$popular_news_result = mysqli_query($conn, $popular_news_query);

if (!$popular_news_result) {
    echo "<!-- Error: " . mysqli_error($conn) . " -->";
} else {
    echo "<!-- Popular News Query Executed Successfully. Total rows: " . mysqli_num_rows($popular_news_result) . " -->";
}

while ($pnews = mysqli_fetch_assoc($popular_news_result)):
    $view_count = isset($pnews['views']) && $pnews['views'] !== null ? intval($pnews['views']) : 0;
    echo "<!-- News ID: {$pnews['id']} | Headline: {$pnews['headline']} | Views: {$view_count} -->";
    
    $dateObj = new DateTime($pnews['created_at']);
    $day = en2bnNumberSidebar($dateObj->format('d'));
    $month = en2bnMonthSidebar($dateObj->format('F'));
    $year = en2bnNumberSidebar($dateObj->format('Y'));
    $bangla_date = $day . ' ' . $month . ' ' . $year;
?>
                                    <div class="popular-news-card">
                                        <div class="popular-news-thumb">
                                            <a href="news.php?path=<?= rawurlencode($pnews['category_slug']) . '/' . rawurlencode($pnews['slug']); ?><?= $user_id_suffix; ?>">
                                                <img src="Admin/img/<?php echo $pnews['image_url']; ?>" loading="lazy" alt="<?php echo $pnews['headline']; ?>">
                                            </a>
                                        </div>
                                        <div class="popular-news-info">
                                            <h6><a href="news.php?path=<?= rawurlencode($pnews['category_slug']) . '/' . rawurlencode($pnews['slug']); ?><?= $user_id_suffix; ?>"><?php echo $pnews['headline']; ?></a></h6>
                                            <div class="popular-news-meta">
                                                <span><i class="fas fa-eye"></i> <?php echo en2bnNumberSidebar(number_format($view_count)); ?></span>
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

        <!-- Video Section Start -->
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
                                  FROM news_video
                                  WHERE is_active = 1
                                  ORDER BY created_at DESC
                                  LIMIT 8";
                    $result_video = $conn->query($sql_video);
                    
                    if ($result_video && $result_video->num_rows > 0):
                        while ($video = $result_video->fetch_assoc()):
                            // Extract YouTube video ID from link
                            $ytLink = $video['youtube_link'];
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)([^"&?\/ ]{11})/', $ytLink, $matches);
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
                            <h4 class="video-headline"><?= mb_substr($title, 0, 50); ?><?= mb_strlen($title) > 50 ? '...' : ''; ?></h4>
                            <?php if (!empty($subtitle)): ?>
                            
                            <?php endif; ?>
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
            </div>
        </section>
        <!-- Video Section End -->

        <!-- Latest News Section Start -->
        <section class="latest-news-section">
            <div class="latest-news-container">
                <div class="latest-news-header">
                    <h2>সর্বশেষ সংবাদ</h2>
                    <div class="header-line"></div>
                </div>
                
                <div class="latest-news-grid">
                    <?php
                    // Fetch latest 10 news
                    $sql_latest = "SELECT n.id, n.headline, n.slug, n.image_url, n.created_at,
                                   c.name as category_name, c.slug as category_slug
                                   FROM news n
                                   INNER JOIN category c ON n.category_id = c.id
                                   WHERE n.is_active = 1
                                   ORDER BY n.created_at DESC
                                   LIMIT 10";
                    $result_latest = $conn->query($sql_latest);
                    
                    if ($result_latest && $result_latest->num_rows > 0):
                        while ($latest = $result_latest->fetch_assoc()):
                            $image = !empty($latest['image_url']) ? 'Admin/img/' . $latest['image_url'] : 'img/default-news.jpg';
                            $headline = htmlspecialchars($latest['headline']);
                            $newsUrl = rawurlencode($latest['category_slug']) . '/' . rawurlencode($latest['slug']);
                            
                            // Format date in Bangla
                            $dateObj = new DateTime($latest['created_at']);
                            $day = en2bnNumberSidebar($dateObj->format('d'));
                            $month = en2bnMonthSidebar($dateObj->format('F'));
                            $year = en2bnNumberSidebar($dateObj->format('Y'));
                            $banglaDate = $day . ' ' . $month . ' ' . $year;
                    ?>
                    <div class="latest-news-card">
                        <a href="news.php?path=<?= $newsUrl; ?><?= $user_id_suffix; ?>" class="latest-news-link">
                            <div class="latest-news-image">
                                <img src="<?= $image; ?>" alt="<?= $headline; ?>">
                            </div>
                            <div class="latest-news-content">
                                <h3 class="latest-news-headline"><?= $headline; ?></h3>
                                <div class="latest-news-date">
                                    <?= $banglaDate; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <p style="text-align: center; width: 100%; padding: 40px 0; color: #666;">কোন সংবাদ পাওয়া যায়নি</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </section>
        <!-- Latest News Section End -->

        <!-- Video Modal -->
        <div class="video-modal" id="videoModal">
            <div class="video-modal-content">
                <button class="video-modal-close" onclick="closeVideoModal()"><i class="fas fa-times"></i></button>
                <iframe id="videoIframe" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
            </div>
        </div>

        <footer class="footer-wrapper">
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
                        <p>স্বত্ব <?php echo date('Y'); ?> <?php echo htmlspecialchars($basic_info['news_portal_name'] ?? ''); ?></p>
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
    const url = <?php echo json_encode($meta_url); ?>;
    navigator.clipboard.writeText(url).then(() => {
        // Link copied silently
    }).catch(err => {
        // Copy failed silently
    });
}

// Normalize address bar to encoded canonical URL (helps manual copy/paste for Facebook)
(function() {
    try {
        const canonical = <?php echo json_encode($meta_url); ?>;
        if (canonical && typeof history !== 'undefined' && history.replaceState) {
            history.replaceState(null, document.title, canonical);
        }
    } catch (e) {}
})();
</script>
<script>
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
    
    <script>
    // Hide initial spinner when page loads
    window.addEventListener('load', function() {
        const spinner = document.getElementById('spinner');
        if (spinner) {
            setTimeout(function() {
                spinner.classList.remove('show');
                spinner.style.display = 'none';
            }, 100);
        }
    });
    
    // Also hide on DOMContentLoaded as fallback for faster perceived load
    document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.getElementById('spinner');
        if (spinner) {
            setTimeout(function() {
                spinner.classList.add('fade-out');
            }, 300);
        }
    });
    </script>
    
    <script>
    // Video Modal Functions
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
    
    // Close modal when clicking outside
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
    </script>
    
    <script>
    // ==== COMPREHENSIVE COPY PROTECTION ====
    (function() {
        'use strict';
        
        // Function to show screenshot warning overlay
        function showScreenshotWarning() {
            const warningDiv = document.getElementById('screenshotWarning');
            if (warningDiv) {
                warningDiv.style.display = 'flex';
                setTimeout(function() {
                    warningDiv.style.display = 'none';
                }, 3000);
            }
        }
        
        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable text selection via mouse
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable copy event
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable cut event
        document.addEventListener('cut', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable paste event (prevents paste-based content extraction)
        document.addEventListener('paste', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+C, Ctrl+X, Ctrl+A, Ctrl+S, Ctrl+U, Ctrl+P
            if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || 
                              e.key === 'x' || e.key === 'X' || 
                              e.key === 'a' || e.key === 'A' ||
                              e.key === 's' || e.key === 'S' ||
                              e.key === 'u' || e.key === 'U' ||
                              e.key === 'p' || e.key === 'P')) {
                e.preventDefault();
                return false;
            }
            
            // Ctrl+Shift+C, Ctrl+Shift+I, Ctrl+Shift+J (Developer Tools)
            if (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.key === 'I' || e.key === 'J')) {
                e.preventDefault();
                return false;
            }
            
            // F12 (Developer Tools)
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            
            // Ctrl+Shift+K (Firefox Console)
            if (e.ctrlKey && e.shiftKey && (e.key === 'K' || e.keyCode === 75)) {
                e.preventDefault();
                return false;
            }
            
            // Cmd+C, Cmd+X, Cmd+A, Cmd+S, Cmd+U, Cmd+P (Mac)
            if (e.metaKey && (e.key === 'c' || e.key === 'C' || 
                              e.key === 'x' || e.key === 'X' || 
                              e.key === 'a' || e.key === 'A' ||
                              e.key === 's' || e.key === 'S' ||
                              e.key === 'u' || e.key === 'U' ||
                              e.key === 'p' || e.key === 'P')) {
                e.preventDefault();
                return false;
            }
            
            // Print Screen key - Show visual warning only
            if (e.key === 'PrintScreen' || e.keyCode === 44) {
                e.preventDefault();
                showScreenshotWarning();
                return false;
            }
            
            // Windows Snipping Tool shortcuts (Win+Shift+S)
            if (e.shiftKey && e.key === 'S' && (e.metaKey || e.key === 'Meta')) {
                showScreenshotWarning();
                e.preventDefault();
                return false;
            }
        }, false);
        
        // Disable drag and drop for images and text
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        }, false);
        
        // Disable long press on mobile (prevents text selection)
        let touchTimer;
        document.addEventListener('touchstart', function(e) {
            touchTimer = setTimeout(function() {
                e.preventDefault();
            }, 500);
        }, false);
        
        document.addEventListener('touchend', function() {
            clearTimeout(touchTimer);
        }, false);
        
        document.addEventListener('touchmove', function() {
            clearTimeout(touchTimer);
        }, false);
        
        // Additional protection: Clear clipboard periodically
        setInterval(function() {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText('').catch(function() {});
            }
        }, 1000);
        
        // Detect developer tools opening (basic detection) with alert
        let devtoolsOpen = false;
        const threshold = 160;
        
        setInterval(function() {
            if (window.outerWidth - window.innerWidth > threshold || 
                window.outerHeight - window.innerHeight > threshold) {
                if (!devtoolsOpen) {
                    devtoolsOpen = true;
                }
            } else {
                devtoolsOpen = false;
            }
        }, 500);
        
        // Detect page visibility change (possible screenshot tool)
        let visibilityAlertShown = false;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                setTimeout(function() {
                    if (document.hidden && !visibilityAlertShown) {
                        visibilityAlertShown = true;
                        setTimeout(function() {
                            visibilityAlertShown = false;
                        }, 5000);
                    }
                }, 100);
            }
        });
        
        // Detect window blur (possible screenshot tool activation)
        let blurWarningCount = 0;
        window.addEventListener('blur', function() {
            blurWarningCount++;
            if (blurWarningCount >= 3) {
                blurWarningCount = 0;
            }
        });
        
        // Disable iOS callout (text selection on long press)
        document.body.style.webkitTouchCallout = 'none';
        
        // Clear any selection that might have been made
        setInterval(function() {
            if (window.getSelection) {
                const selection = window.getSelection();
                if (selection.toString().length > 0) {
                    selection.removeAllRanges();
                }
            }
        }, 100);
        
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
/* Simple Image Container Styles */
.news-image-figure {
    margin: 0;
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.news-image-figure img {
    border-radius: 8px 8px 0 0;
}

/* Simple Gray Caption */
.news-image-caption {
    background: #f5f5f5;
    color: #555;
    padding: 10px 14px;
    font-size: 13px;
    font-family: 'SolaimanLipi', sans-serif;
    line-height: 1.5;
    border-radius: 0 0 8px 8px;
    border-top: 1px solid #e0e0e0;
}

/* Small Caption for Secondary Images */
.news-image-caption.small-caption {
    padding: 8px 12px;
    font-size: 12px;
}

/* Main Image Title After Frame */
.main-image-title {
    background: #f0f0f0;
    color: #444;
    padding: 12px 16px;
    font-size: 14px;
    font-family: 'SolaimanLipi', sans-serif;
    line-height: 1.5;
    border-radius: 0 0 8px 8px;
    border-top: 1px solid #ddd;
}

/* Main Image Container */
.main-image-container {
    position: relative;
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
}

.main-image-container img.main-news-image {
    width: 100%;
    height: auto;
    display: block;
}

.main-image-container img.frame-image {
    width: 100%;
    height: auto;
    display: block;
    margin-top: -5px;
}

@media (max-width: 768px) {
    .news-image-caption {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .news-image-caption.small-caption {
        padding: 6px 10px;
        font-size: 11px;
    }
    
    .main-image-title {
        padding: 10px 14px;
        font-size: 13px;
    }
}

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
    document.getElementById('termsPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeTermsPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('termsPopup').classList.remove('active');
    document.body.style.overflow = '';
}

function openCommentPolicyPopup() {
    document.getElementById('commentPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCommentPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('commentPolicyPopup').classList.remove('active');
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
    document.getElementById('adsPolicyPopup').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdsPolicyPopup(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('adsPolicyPopup').classList.remove('active');
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
        closeDbTermsPopup();
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

    </body>

</html>