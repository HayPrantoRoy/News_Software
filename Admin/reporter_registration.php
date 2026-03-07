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
    case 'login_reporter':
        loginReporter($pdo);
        break;
    case 'update_reporter':
        updateReporter($pdo);
        break;
    case 'delete_reporter':
        deleteReporter($pdo);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid action: " . $action]);
        break;
}

// ==================== LOGIN REPORTER ====================
function loginReporter($pdo) {
    header("Content-Type: application/json");
    
    // Check if phone number and password are provided
    if (empty($_POST['phone_number']) || empty($_POST['password'])) {
        echo json_encode([
            "success" => false, 
            "message" => "Phone number and password are required"
        ]);
        return;
    }
    
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    
    try {
        // Get reporter by phone number including is_active status
        $stmt = $pdo->prepare("SELECT id, name, email, phone_number, password, is_active FROM reporter WHERE phone_number = ?");
        $stmt->execute([$phone_number]);
        $reporter = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if reporter exists
        if (!$reporter) {
            echo json_encode([
                "success" => false, 
                "message" => "Phone number not found"
            ]);
            return;
        }
        
        // Simple password comparison
        if ($password === $reporter['password']) {
            // Check if reporter is active (approved by admin)
            if (isset($reporter['is_active']) && $reporter['is_active'] == 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "Your account is pending admin approval. Please wait for approval to login."
                ]);
                return;
            }
            
            // Password is correct and account is active
            echo json_encode([
                "success" => true, 
                "message" => "Login successful",
                "reporter_id" => $reporter['id'],
                "reporter_name" => $reporter['name'],
                "reporter_email" => $reporter['email']
            ]);
        } else {
            // Password is incorrect
            echo json_encode([
                "success" => false, 
                "message" => "Incorrect password"
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false, 
            "message" => "Login failed: " . $e->getMessage()
        ]);
    }
}

// ==================== GET ALL REPORTERS ====================
function getReporters($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name, email, phone_number, id_card, address, photo, id_card_photo, created_at FROM reporter ORDER BY id DESC");
        $reporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true, 
            "data" => $reporters,
            "message" => "Reporters retrieved successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve reporters: " . $e->getMessage()]);
    }
}

// ==================== GET SINGLE REPORTER ====================
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

// ==================== CREATE REPORTER ====================
function createReporter($pdo) {
    // Required fields
    $required = ['name', 'email', 'phone_number', 'password', 'id_card', 'address'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode([
                "success" => false,
                "message" => "All fields are required"
            ]);
            exit;
        }
    }

    // Files
    if (!isset($_FILES['photo'], $_FILES['id_card_photo'])) {
        echo json_encode([
            "success" => false,
            "message" => "Photos are required"
        ]);
        exit;
    }

    $upload_dir = "uploads/reporters/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $photo_path = $upload_dir . uniqid() . "_" . $_FILES['photo']['name'];
    $id_card_path = $upload_dir . uniqid() . "_" . $_FILES['id_card_photo']['name'];

    move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    move_uploaded_file($_FILES['id_card_photo']['tmp_name'], $id_card_path);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO reporter 
            (name, email, phone_number, password, id_card, address, photo, id_card_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone_number'],
            $_POST['password'],
            $_POST['id_card'],
            $_POST['address'],
            $photo_path,
            $id_card_path
        ]);

        $user_id = $pdo->lastInsertId();

        // Redirect to login page with success message
        echo json_encode([
            "success" => true,
            "message" => "Registration successful! Please login to continue.",
            "redirect" => "../Reporter/index.php"
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Registration failed: " . $e->getMessage()
        ]);
        exit;
    }
}

// ==================== UPDATE REPORTER ====================
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
        
        // Check if password is being updated
        if (!empty($_POST['password'])) {
            $stmt = $pdo->prepare("UPDATE reporter SET name = ?, email = ?, phone_number = ?, password = ?, id_card = ?, address = ?, photo = ?, id_card_photo = ? WHERE id = ?");
            $stmt->execute([
                trim($_POST['name']),
                trim($_POST['email']),
                trim($_POST['phone_number']),
                $_POST['password'],
                trim($_POST['id_card']),
                trim($_POST['address']),
                $photo_path,
                $id_card_photo_path,
                $id
            ]);
        } else {
            // Update without changing password
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
        }
        
        echo json_encode([
            "success" => true, 
            "message" => "Reporter updated successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to update reporter: " . $e->getMessage()]);
    }
}

// ==================== DELETE REPORTER ====================
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
?>