<?php
header('Content-Type: application/json');
include 'connection.php';
date_default_timezone_set('Asia/Dhaka');

// Bangla conversion helpers
function en2bnNum($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return str_replace($en, $bn, $number);
}

function en2bnMon($dateStr) {
    $enMonths = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $bnMonths = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    return str_replace($enMonths, $bnMonths, $dateStr);
}

function renderBold($text) {
    return preg_replace('/<bold>(.*?)<\/bold>/is', '<strong>$1</strong>', $text);
}

// Get parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$exclude = isset($_GET['exclude']) ? $_GET['exclude'] : '';
$limit = 1; // Load 1 article at a time for visible loading effect
$offset = $page * $limit;

// Build exclude condition
$excludeCondition = '';
if (!empty($exclude)) {
    $excludeSlug = $conn->real_escape_string($exclude);
    $excludeCondition = "AND n.slug != '$excludeSlug'";
}

// Fetch news with reporter info
$sql = "SELECT n.*, c.name as category_name, c.slug AS category_slug,
               r.name AS reporter_name, r.photo AS reporter_photo
        FROM news n 
        JOIN category c ON n.category_id = c.id 
        LEFT JOIN reporter r ON n.reporter_id = r.id
        WHERE n.is_active = 1 $excludeCondition
        ORDER BY n.created_at DESC 
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$newsItems = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format date in Bangla
        $dateObj = new DateTime($row['created_at']);
        $datePart = $dateObj->format('d F Y, h:i');
        $banglaDate = en2bnNum(en2bnMon($datePart));
        
        // Build link
        $link = "news.php?path=" . urlencode($row['category_slug']) . "/" . urlencode($row['slug']);
        
        // Get views
        $views = isset($row['views']) ? number_format($row['views']) : '0';
        
        // Process news content
        $news1 = renderBold(htmlspecialchars_decode($row['news_1'] ?? ''));
        $news3 = renderBold(htmlspecialchars_decode($row['news_3'] ?? ''));
        $news4 = renderBold(htmlspecialchars_decode($row['news_4'] ?? ''));
        
        $newsItems[] = [
            'id' => $row['id'],
            'headline' => htmlspecialchars($row['headline']),
            'short_description' => htmlspecialchars($row['short_description'] ?? ''),
            'image_url' => $row['image_url'] ?? 'default.jpg',
            'image_2' => $row['image_2'] ?? '',
            'image_3' => $row['image_3'] ?? '',
            'image_4' => $row['image_4'] ?? '',
            'image_5' => $row['image_5'] ?? '',
            'news_1' => $news1,
            'news_3' => $news3,
            'news_4' => $news4,
            'quote_1' => htmlspecialchars($row['quote_1'] ?? ''),
            'auture_1' => htmlspecialchars($row['auture_1'] ?? ''),
            'quote_2' => htmlspecialchars($row['quote_2'] ?? ''),
            'auture_2' => htmlspecialchars($row['auture_2'] ?? ''),
            'category_name' => htmlspecialchars($row['category_name']),
            'category_slug' => $row['category_slug'],
            'date' => $banglaDate,
            'link' => $link,
            'views' => $views,
            'reporter_name' => htmlspecialchars($row['reporter_name'] ?? ''),
            'reporter_photo' => $row['reporter_photo'] ?? ''
        ];
    }
}

// Check if there are more news
$countSql = "SELECT COUNT(*) as total FROM news n WHERE n.is_active = 1 $excludeCondition";
$countResult = $conn->query($countSql);
$totalCount = $countResult->fetch_assoc()['total'];
$hasMore = ($offset + $limit) < $totalCount;

// Return JSON response
echo json_encode([
    'news' => $newsItems,
    'hasMore' => $hasMore,
    'page' => $page
]);

$conn->close();
?>
