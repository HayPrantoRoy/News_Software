<?php
header('Content-Type: application/json');

$url = $_GET['url'] ?? '';

if (empty($url)) {
    echo json_encode(['success' => false, 'message' => 'No URL provided']);
    exit;
}

// Handle relative URLs
if (strpos($url, 'http') !== 0) {
    // Remove any leading slashes
    $url = ltrim($url, '/');
    
    // Try different paths
    $possiblePaths = [
        '../' . $url,
        $url,
        dirname(__DIR__) . '/' . $url
    ];
    
    $filePath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $filePath = $path;
            break;
        }
    }
    
    if ($filePath) {
        $imageData = file_get_contents($filePath);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Map extension to MIME type
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml'
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'image/png';
        $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        
        echo json_encode(['success' => true, 'base64' => $base64]);
        exit;
    }
}

// For external URLs, use file_get_contents with context
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

try {
    $imageData = @file_get_contents($url, false, $context);
    
    if ($imageData === false) {
        echo json_encode(['success' => false, 'message' => 'Could not fetch image']);
        exit;
    }
    
    // Detect image type from content
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);
    
    if (strpos($mimeType, 'image/') !== 0) {
        $mimeType = 'image/png';
    }
    
    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    
    echo json_encode(['success' => true, 'base64' => $base64]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
