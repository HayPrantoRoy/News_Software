<?php
header('Content-Type: application/json');
require_once 'connection.php';

try {
    $stmt = $pdo->prepare("SELECT id, name FROM reporter  ORDER BY name");
    $stmt->execute();
    $reporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reporters);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>