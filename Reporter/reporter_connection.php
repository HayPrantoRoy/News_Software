<?php
// Reporter Connection - connects to tenant DB using user_id from URL/session
// Similar pattern to root connection.php but with master DB lookup

if (defined('REPORTER_CONNECTION_INCLUDED')) {
    return;
}
define('REPORTER_CONNECTION_INCLUDED', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$master_db = "master_news_software_db";

// Get user_id from URL or session
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_SESSION['reporter_user_id']) ? intval($_SESSION['reporter_user_id']) : 0);

if ($user_id <= 0) {
    // No user_id - show error or redirect
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header('Location: index.php');
        exit;
    }
}

// Store in session for persistence
if ($user_id > 0) {
    $_SESSION['reporter_user_id'] = $user_id;
}

// Lookup database_name from master DB
$tenant_database = '';
if ($user_id > 0) {
    $master_conn = new mysqli($servername, $db_username, $db_password, $master_db);
    if (!$master_conn->connect_error) {
        $master_conn->set_charset("utf8mb4");
        $stmt = $master_conn->prepare("SELECT database_name FROM users WHERE id = ? AND is_active = 1");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $tenant_database = $row['database_name'];
            }
            $stmt->close();
        }
        $master_conn->close();
    }
}

if (empty($tenant_database) && $user_id > 0) {
    // Fallback: use naming convention
    $tenant_database = "news_software_id_" . $user_id;
}

// Connect to tenant database
$conn = null;
$pdo = null;

if (!empty($tenant_database)) {
    // Store for other pages/APIs
    $_SESSION['reporter_database'] = $tenant_database;
    $_SESSION['tenant_database'] = $tenant_database;

    $conn = new mysqli($servername, $db_username, $db_password, $tenant_database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$tenant_database;charset=utf8mb4", $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // PDO is optional
    }
}

// Get reporter_id from URL or session
$reporter_id = isset($_GET['reporter_id']) ? intval($_GET['reporter_id']) : (isset($_SESSION['reporter_id']) ? intval($_SESSION['reporter_id']) : 0);

// Helper: build URL with user_id and optional reporter_id
function reporter_url($page, $extra_params = []) {
    global $user_id, $reporter_id;
    $params = ['user_id' => $user_id];
    if ($reporter_id > 0) {
        $params['reporter_id'] = $reporter_id;
    }
    $params = array_merge($params, $extra_params);
    return $page . '?' . http_build_query($params);
}
?>
