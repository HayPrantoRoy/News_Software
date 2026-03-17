<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

// Get database name from session (set during login)
if (isset($_SESSION['tenant_database']) && !empty($_SESSION['tenant_database'])) {
    $dbname = $_SESSION['tenant_database'];
} else {
    // Check if this is an API call (JSON expected)
    $isApiCall = (strpos($_SERVER['PHP_SELF'], 'api.php') !== false) || 
                 (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    
    if ($isApiCall) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'সেশন শেষ হয়ে গেছে। পুনরায় লগইন করুন।', 'redirect' => 'index.php']);
        exit;
    }
    // Redirect to login if no tenant database in session
    header('Location: index.php');
    exit;
}

try {
    // Create PDO connection to the appropriate database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO attributes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Also create MySQLi connection for compatibility
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

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