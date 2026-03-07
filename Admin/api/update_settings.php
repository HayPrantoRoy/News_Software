<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Fix path - API is in Admin/api/, connection.php is in root
$connectionPath = dirname(dirname(dirname(__FILE__))) . '/connection.php';
if (!file_exists($connectionPath)) {
    echo json_encode(['success' => false, 'error' => 'Connection file not found: ' . $connectionPath]);
    exit;
}
include $connectionPath;

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Create settings table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `serial_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// Update order
if ($action === 'update_order') {
    $ordersJson = isset($_POST['orders']) ? $_POST['orders'] : '';
    $orders = json_decode($ordersJson, true);
    
    if (!$orders || !is_array($orders)) {
        echo json_encode(['success' => false, 'error' => 'Invalid orders data']);
        exit;
    }
    
    $success = true;
    $errors = [];
    
    foreach ($orders as $item) {
        $categoryId = (int)$item['category_id'];
        $serialOrder = (int)$item['serial_order'];
        $isActive = isset($item['is_active']) ? (int)$item['is_active'] : 1;
        
        // Check if exists
        $check = $conn->query("SELECT id FROM settings WHERE category_id = $categoryId");
        
        if ($check && $check->num_rows > 0) {
            // Update existing
            $sql = "UPDATE settings SET serial_order = $serialOrder, is_active = $isActive WHERE category_id = $categoryId";
            if (!$conn->query($sql)) {
                $success = false;
                $errors[] = "Failed to update category $categoryId: " . $conn->error;
            }
        } else {
            // Insert new
            $sql = "INSERT INTO settings (category_id, serial_order, is_active) VALUES ($categoryId, $serialOrder, $isActive)";
            if (!$conn->query($sql)) {
                $success = false;
                $errors[] = "Failed to insert category $categoryId: " . $conn->error;
            }
        }
    }
    
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Settings updated successfully' : 'Some updates failed',
        'errors' => $errors
    ]);
    exit;
}

// Toggle active status
if ($action === 'toggle_active') {
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    if ($categoryId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid category ID']);
        exit;
    }
    
    $check = $conn->query("SELECT id FROM settings WHERE category_id = $categoryId");
    
    if ($check && $check->num_rows > 0) {
        $sql = "UPDATE settings SET is_active = $isActive WHERE category_id = $categoryId";
    } else {
        $sql = "INSERT INTO settings (category_id, serial_order, is_active) VALUES ($categoryId, 999, $isActive)";
    }
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
