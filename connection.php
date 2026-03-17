<?php
// Prevent multiple includes
if (defined('CONNECTION_INCLUDED')) {
    return;
}
define('CONNECTION_INCLUDED', true);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";

// Default database name (fallback)
$dbname = "news_software";

// Check if user_id is provided for multi-tenant support
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_SESSION['current_user_id']) ? intval($_SESSION['current_user_id']) : 0);

// Store user_id in session for persistence across pages
if ($user_id > 0) {
    $_SESSION['current_user_id'] = $user_id;
    // Database name follows pattern: news_software_id_{user_id}
    $dbname = "news_software_id_" . $user_id;
}

// Create MySQLi connection to the appropriate database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Important: set charset for Bangla/Unicode support
$conn->set_charset("utf8mb4");

// Also create PDO connection for compatibility
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // PDO is optional, continue with mysqli
}

// Table names (no suffix needed - each tenant has own database)
$tbl_basic_info = "basic_info";
$tbl_category = "category";
$tbl_news = "news";
$tbl_reporter = "reporter";
$tbl_videos = "news_video";
$tbl_podcasts = "podcasts";
$tbl_opinions = "opinions";
$tbl_menus = "menus";
$tbl_settings = "settings";
?>
