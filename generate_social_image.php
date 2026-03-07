<?php
// generate_social_image.php - Generate watermarked images for social media sharing
header('Content-Type: image/jpeg');

// Get image path from query parameter
$imagePath = $_GET['img'] ?? '';

if (empty($imagePath)) {
    // Return a default image if no path provided
    header('Location: img/default-news.jpg');
    exit;
}

// Build full paths
$newsImagePath = __DIR__ . '/Admin/img/' . $imagePath;
$framePath = __DIR__ . '/Admin/img/frame.jpg';

// Check if files exist
if (!file_exists($newsImagePath)) {
    header('Location: img/default-news.jpg');
    exit;
}

if (!file_exists($framePath)) {
    // If frame doesn't exist, just return original image
    header('Content-Type: image/jpeg');
    readfile($newsImagePath);
    exit;
}

// Create image resources
$newsImage = @imagecreatefromstring(file_get_contents($newsImagePath));
$frameImage = @imagecreatefromstring(file_get_contents($framePath));

if (!$newsImage || !$frameImage) {
    // If image creation fails, return original
    readfile($newsImagePath);
    exit;
}

// Get dimensions
$newsWidth = imagesx($newsImage);
$newsHeight = imagesy($newsImage);
$frameWidth = imagesx($frameImage);
$frameHeight = imagesy($frameImage);

// Calculate optimal dimensions for social media (1200x630 for Facebook)
$targetWidth = 1200;
$targetHeight = 630;

// Create canvas for final image
$canvas = imagecreatetruecolor($targetWidth, $targetHeight);

// Resize news image to fit target dimensions
$newsAspect = $newsWidth / $newsHeight;
$targetAspect = $targetWidth / $targetHeight;

if ($newsAspect > $targetAspect) {
    // Image is wider - fit to width
    $newWidth = $targetWidth;
    $newHeight = (int)($targetWidth / $newsAspect);
    $offsetX = 0;
    $offsetY = (int)(($targetHeight - $newHeight) / 2);
} else {
    // Image is taller - fit to height
    $newHeight = $targetHeight;
    $newWidth = (int)($targetHeight * $newsAspect);
    $offsetX = (int)(($targetWidth - $newWidth) / 2);
    $offsetY = 0;
}

// Fill canvas with white background
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);

// Copy resized news image to canvas
imagecopyresampled($canvas, $newsImage, $offsetX, $offsetY, 0, 0, $newWidth, $newHeight, $newsWidth, $newsHeight);

// Calculate frame position (bottom of image)
$frameTargetHeight = (int)($targetHeight * 0.15); // Frame takes 15% of height
$frameTargetWidth = $targetWidth;
$frameY = $targetHeight - $frameTargetHeight;

// Resize and overlay frame at bottom
imagecopyresampled($canvas, $frameImage, 0, $frameY, 0, 0, $frameTargetWidth, $frameTargetHeight, $frameWidth, $frameHeight);

// Output image
imagejpeg($canvas, null, 90);

// Clean up
imagedestroy($newsImage);
imagedestroy($frameImage);
imagedestroy($canvas);
?>
