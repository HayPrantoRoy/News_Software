<?php
// Master Database Connection
// This connects to the master database that stores all tenant information

$master_host = 'localhost';
$master_dbname = 'master_news_software_db';
$master_username = 'root';
$master_password = '';

try {
    $master_pdo = new PDO("mysql:host=$master_host;dbname=$master_dbname;charset=utf8mb4", $master_username, $master_password);
    $master_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $master_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Master Database Connection failed: " . $e->getMessage());
}

// Also create mysqli connection for master database
$master_conn = new mysqli($master_host, $master_username, $master_password, $master_dbname);
if ($master_conn->connect_error) {
    die("Master Database Connection failed: " . $master_conn->connect_error);
}
$master_conn->set_charset("utf8mb4");

/**
 * Get user database info from master database
 * @param int $user_id
 * @return array|null
 */
function getUserDatabaseInfo($user_id) {
    global $master_pdo;
    
    $stmt = $master_pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

/**
 * Get user database name by user ID
 * @param int $user_id
 * @return string|null
 */
function getUserDatabaseName($user_id) {
    $user = getUserDatabaseInfo($user_id);
    return $user ? $user['database_name'] : null;
}
?>
