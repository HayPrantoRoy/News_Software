<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Function to generate unique filename
function generateUniqueFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $basename = pathinfo($originalName, PATHINFO_FILENAME);
    $basename = preg_replace('/[^a-zA-Z0-9]/', '_', $basename);
    return $basename . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
}

// Function to upload image (simple version without compression)
function uploadImage($fileInput, $uploadDir = 'img/') {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $file = $_FILES[$fileInput];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array('image/' . $fileExtension, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']) && 
        !in_array($file['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        throw new Exception("Invalid file type for $fileInput. Only JPEG, PNG, GIF, and WebP are allowed.");
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File $fileInput is too large. Maximum size is 5MB. Please compress the image before uploading.");
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory.");
        }
    }
    
    // Generate unique filename
    $filename = generateUniqueFilename($file['name']);
    $destination = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    } else {
        throw new Exception("Failed to upload image $fileInput.");
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    // Validate required fields (reporter_id removed)
    $requiredFields = ['category_id', 'headline', 'slug', 'news_1'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    
    // Validate slug length (8-10 words)
    $slugWords = explode('-', $_POST['slug']);
    if (count($slugWords) > 10) {
        throw new Exception('Slug must be maximum 10 words');
    }
    
    // Check if slug already exists
    $stmt = $pdo->prepare("SELECT id FROM news WHERE slug = ?");
    $stmt->execute([$_POST['slug']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Slug already exists. Please modify the headline to generate a unique slug.');
    }
    
    // Validate category exists
    $stmt = $pdo->prepare("SELECT id FROM category WHERE id = ? AND is_active = 1");
    $stmt->execute([$_POST['category_id']]);
    if ($stmt->rowCount() === 0) {
        throw new Exception('Selected category does not exist or is inactive.');
    }
    
    // Validate reporter exists

    
    // Upload images
    $imageFields = ['image_url', 'image_2', 'image_3', 'image_4', 'image_5'];
    $uploadedImages = [];
    
    foreach ($imageFields as $field) {
        try {
            $uploadedImages[$field] = uploadImage($field);
        } catch (Exception $e) {
            // If it's the main image (image_url), it's required
            if ($field === 'image_url' && isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                throw $e;
            }
            $uploadedImages[$field] = null;
        }
    }
    
    // Prepare SQL statement
    $sql = "INSERT INTO news (
        category_id, reporter_id, headline, short_description, slug, 
        news_1, image_url, quote_1, auture_1, news_2, image_2, image_3, 
        news_3, quote_2, auture_2, image_4, image_5, news_4, is_active, created_at
    ) VALUES (
        :category_id, :reporter_id, :headline, :short_description, :slug,
        :news_1, :image_url, :quote_1, :auture_1, :news_2, :image_2, :image_3,
        :news_3, :quote_2, :auture_2, :image_4, :image_5, :news_4, 1, NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $params = [
        ':category_id' => (int)$_POST['category_id'],
        ':reporter_id' => (int)$_POST['reporter_id'],
        ':headline' => trim($_POST['headline']),
        ':short_description' => trim($_POST['short_description']),
        ':slug' => trim($_POST['slug']),
        ':news_1' => trim($_POST['news_1']),
        ':image_url' => $uploadedImages['image_url'],
        ':quote_1' => !empty($_POST['quote_1']) ? trim($_POST['quote_1']) : null,
        ':auture_1' => !empty($_POST['auture_1']) ? trim($_POST['auture_1']) : null,
        ':news_2' => !empty($_POST['news_2']) ? trim($_POST['news_2']) : null,
        ':image_2' => $uploadedImages['image_2'],
        ':image_3' => $uploadedImages['image_3'],
        ':news_3' => !empty($_POST['news_3']) ? trim($_POST['news_3']) : null,
        ':quote_2' => !empty($_POST['quote_2']) ? trim($_POST['quote_2']) : null,
        ':auture_2' => !empty($_POST['auture_2']) ? trim($_POST['auture_2']) : null,
        ':image_4' => $uploadedImages['image_4'],
        ':image_5' => $uploadedImages['image_5'],
        ':news_4' => !empty($_POST['news_4']) ? trim($_POST['news_4']) : null
    ];
    
    // Execute the statement
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'News article created successfully! Note: Images were uploaded without compression. For best performance, please compress images to under 100KB before uploading.',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        throw new Exception('Failed to insert news article into database');
    }
    
} catch (Exception $e) {
    // Clean up uploaded files if database insert fails
    if (isset($uploadedImages)) {
        foreach ($uploadedImages as $filename) {
            if ($filename && file_exists('img/' . $filename)) {
                unlink('img/' . $filename);
            }
        }
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    // Clean up uploaded files if database error occurs
    if (isset($uploadedImages)) {
        foreach ($uploadedImages as $filename) {
            if ($filename && file_exists('img/' . $filename)) {
                unlink('img/' . $filename);
            }
        }
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>