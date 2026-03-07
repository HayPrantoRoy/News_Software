<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include your database connection
require_once 'connection.php';

// Check if connection is established properly
if (!isset($pdo) || $pdo === null) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Get the requested action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Process the request
switch ($action) {
    case 'get_reporters':
        getReporters($pdo);
        break;
    case 'get_reporter':
        getReporter($pdo);
        break;
    case 'create_reporter':
        createReporter($pdo);
        break;
    case 'update_reporter':
        updateReporter($pdo);
        break;
    case 'delete_reporter':
        deleteReporter($pdo);
        break;
    case 'toggle_reporter_status':
        toggleReporterStatus($pdo);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid action: " . $action]);
        break;
}

function getReporters($pdo) {
    try {
        try {
            $stmt = $pdo->query("SELECT id, name, email, phone_number, id_card, address, photo, id_card_photo, created_at, COALESCE(is_active, 1) as is_active FROM reporter ORDER BY id DESC");
            $reporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $stmt = $pdo->query("SELECT id, name, email, phone_number, id_card, address, photo, id_card_photo, created_at, 1 as is_active FROM reporter ORDER BY id DESC");
            $reporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode([
            "success" => true, 
            "data" => $reporters,
            "message" => "Reporters retrieved successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve reporters: " . $e->getMessage()]);
    }
}

function getReporter($pdo) {
    if (!isset($_GET['id'])) {
        echo json_encode(["success" => false, "message" => "Reporter ID is required"]);
        return;
    }
    
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, phone_number, id_card, address, photo, id_card_photo, created_at FROM reporter WHERE id = ?");
        $stmt->execute([$id]);
        $reporter = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reporter) {
            echo json_encode([
                "success" => true, 
                "data" => $reporter,
                "message" => "Reporter retrieved successfully"
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Reporter not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve reporter: " . $e->getMessage()]);
    }
}

function createReporter($pdo) {
    // Validate required fields
    $required_fields = ['name', 'email', 'phone_number', 'id_card', 'address'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(["success" => false, "message" => "Field $field is required"]);
            return;
        }
    }
    
    // Validate files
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "Photo is required"]);
        return;
    }
    
    if (!isset($_FILES['id_card_photo']) || $_FILES['id_card_photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "ID card photo is required"]);
        return;
    }
    
    // Process file uploads
    $upload_dir = 'uploads/reporters/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
    $id_card_photo_name = uniqid() . '_' . basename($_FILES['id_card_photo']['name']);
    
    $photo_path = $upload_dir . $photo_name;
    $id_card_photo_path = $upload_dir . $id_card_photo_name;
    
    // Move uploaded files
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
        echo json_encode(["success" => false, "message" => "Failed to upload photo"]);
        return;
    }
    
    if (!move_uploaded_file($_FILES['id_card_photo']['tmp_name'], $id_card_photo_path)) {
        // Clean up the first file if second upload fails
        unlink($photo_path);
        echo json_encode(["success" => false, "message" => "Failed to upload ID card photo"]);
        return;
    }
    
    try {
        // Insert new reporter
        $stmt = $pdo->prepare("INSERT INTO reporter (name, email, phone_number, id_card, address, photo, id_card_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['email']),
            trim($_POST['phone_number']),
            trim($_POST['id_card']),
            trim($_POST['address']),
            $photo_path,
            $id_card_photo_path
        ]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Reporter created successfully",
            "id" => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        // Clean up uploaded files if database operation fails
        unlink($photo_path);
        unlink($id_card_photo_path);
        echo json_encode(["success" => false, "message" => "Failed to create reporter: " . $e->getMessage()]);
    }
}

function updateReporter($pdo) {
    if (!isset($_POST['id'])) {
        echo json_encode(["success" => false, "message" => "Reporter ID is required"]);
        return;
    }
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone_number', 'id_card', 'address'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(["success" => false, "message" => "Field $field is required"]);
            return;
        }
    }
    
    try {
        // Get current reporter data
        $stmt = $pdo->prepare("SELECT photo, id_card_photo FROM reporter WHERE id = ?");
        $stmt->execute([$id]);
        $current_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_data) {
            echo json_encode(["success" => false, "message" => "Reporter not found"]);
            return;
        }
        
        $photo_path = $current_data['photo'];
        $id_card_photo_path = $current_data['id_card_photo'];
        
        // Process file uploads if provided
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/reporters/';
            $new_photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
            $new_photo_path = $upload_dir . $new_photo_name;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $new_photo_path)) {
                // Delete old photo
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
                $photo_path = $new_photo_path;
            }
        }
        
        if (isset($_FILES['id_card_photo']) && $_FILES['id_card_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/reporters/';
            $new_id_card_photo_name = uniqid() . '_' . basename($_FILES['id_card_photo']['name']);
            $new_id_card_photo_path = $upload_dir . $new_id_card_photo_name;
            
            if (move_uploaded_file($_FILES['id_card_photo']['tmp_name'], $new_id_card_photo_path)) {
                // Delete old ID card photo
                if (file_exists($id_card_photo_path)) {
                    unlink($id_card_photo_path);
                }
                $id_card_photo_path = $new_id_card_photo_path;
            }
        }
        
        // Update reporter
        $stmt = $pdo->prepare("UPDATE reporter SET name = ?, email = ?, phone_number = ?, id_card = ?, address = ?, photo = ?, id_card_photo = ? WHERE id = ?");
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['email']),
            trim($_POST['phone_number']),
            trim($_POST['id_card']),
            trim($_POST['address']),
            $photo_path,
            $id_card_photo_path,
            $id
        ]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Reporter updated successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to update reporter: " . $e->getMessage()]);
    }
}

function deleteReporter($pdo) {
    if (!isset($_POST['id'])) {
        echo json_encode(["success" => false, "message" => "Reporter ID is required"]);
        return;
    }
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    
    try {
        // Get reporter data to delete associated files
        $stmt = $pdo->prepare("SELECT photo, id_card_photo FROM reporter WHERE id = ?");
        $stmt->execute([$id]);
        $reporter = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reporter) {
            echo json_encode(["success" => false, "message" => "Reporter not found"]);
            return;
        }
        
        // Delete associated files
        if (file_exists($reporter['photo'])) {
            unlink($reporter['photo']);
        }
        
        if (file_exists($reporter['id_card_photo'])) {
            unlink($reporter['id_card_photo']);
        }
        
        // Delete reporter
        $stmt = $pdo->prepare("DELETE FROM reporter WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Reporter deleted successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to delete reporter: " . $e->getMessage()]);
    }
}

function toggleReporterStatus($pdo) {
    if (!isset($_POST['id']) || !isset($_POST['is_active'])) {
        echo json_encode(["success" => false, "message" => "Reporter ID and status are required"]);
        return;
    }
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $is_active = filter_var($_POST['is_active'], FILTER_VALIDATE_INT);
    
    if ($is_active !== 0 && $is_active !== 1) {
        echo json_encode(["success" => false, "message" => "Invalid status value"]);
        return;
    }
    
    try {
        try {
            $stmt = $pdo->prepare("UPDATE reporter SET is_active = ? WHERE id = ?");
            $stmt->execute([$is_active, $id]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'is_active') !== false) {
                $pdo->exec("ALTER TABLE reporter ADD COLUMN is_active TINYINT(1) DEFAULT 1");
                $stmt = $pdo->prepare("UPDATE reporter SET is_active = ? WHERE id = ?");
                $stmt->execute([$is_active, $id]);
            } else {
                throw $e;
            }
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Reporter status updated successfully"
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Reporter not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to update status: " . $e->getMessage()]);
    }
}
?>