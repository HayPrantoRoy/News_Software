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
    case 'get_categories':
        getCategories($pdo);
        break;
    case 'get_category':
        getCategory($pdo);
        break;
    case 'create_category':
        createCategory($pdo);
        break;
    case 'update_category':
        updateCategory($pdo);
        break;
    case 'delete_category':
        deleteCategory($pdo);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid action: " . $action]);
        break;
}

function getCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name, slug, is_active FROM category ORDER BY id DESC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true, 
            "data" => $categories,
            "message" => "Categories retrieved successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve categories: " . $e->getMessage()]);
    }
}

function getCategory($pdo) {
    if (!isset($_GET['id'])) {
        echo json_encode(["success" => false, "message" => "Category ID is required"]);
        return;
    }
    
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, slug, is_active FROM category WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            echo json_encode([
                "success" => true, 
                "data" => $category,
                "message" => "Category retrieved successfully"
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Category not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to retrieve category: " . $e->getMessage()]);
    }
}

function createCategory($pdo) {
    if (!isset($_POST['name']) || !isset($_POST['slug']) || !isset($_POST['is_active'])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        return;
    }
    
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $is_active = filter_var($_POST['is_active'], FILTER_VALIDATE_INT);
    
    // Validate inputs
    if (empty($name) || empty($slug)) {
        echo json_encode(["success" => false, "message" => "Name and slug are required"]);
        return;
    }
    
    try {
        // Check if slug already exists
        $stmt = $pdo->prepare("SELECT id FROM category WHERE slug = ?");
        $stmt->execute([$slug]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "Slug already exists"]);
            return;
        }
        
        // Insert new category
        $stmt = $pdo->prepare("INSERT INTO category (name, slug, is_active) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $is_active]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Category created successfully",
            "id" => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to create category: " . $e->getMessage()]);
    }
}

function updateCategory($pdo) {
    if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['slug']) || !isset($_POST['is_active'])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        return;
    }
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $is_active = filter_var($_POST['is_active'], FILTER_VALIDATE_INT);
    
    // Validate inputs
    if (empty($id) || empty($name) || empty($slug)) {
        echo json_encode(["success" => false, "message" => "Valid ID, name and slug are required"]);
        return;
    }
    
    try {
        // Check if category exists
        $stmt = $pdo->prepare("SELECT id FROM category WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(["success" => false, "message" => "Category not found"]);
            return;
        }
        
        // Check if slug already exists for another category
        $stmt = $pdo->prepare("SELECT id FROM category WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "Slug already exists for another category"]);
            return;
        }
        
        // Update category
        $stmt = $pdo->prepare("UPDATE category SET name = ?, slug = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $is_active, $id]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Category updated successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to update category: " . $e->getMessage()]);
    }
}

function deleteCategory($pdo) {
    if (!isset($_POST['id'])) {
        echo json_encode(["success" => false, "message" => "Category ID is required"]);
        return;
    }
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    
    try {
        // Check if category exists
        $stmt = $pdo->prepare("SELECT id FROM category WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(["success" => false, "message" => "Category not found"]);
            return;
        }
        
        // Delete category
        $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            "success" => true, 
            "message" => "Category deleted successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to delete category: " . $e->getMessage()]);
    }
}
?>