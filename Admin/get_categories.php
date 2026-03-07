<?php
header('Content-Type: application/json');
require_once 'connection.php';

try {
    $stmt = $pdo->prepare("SELECT id, name FROM category WHERE is_active = 1 ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>