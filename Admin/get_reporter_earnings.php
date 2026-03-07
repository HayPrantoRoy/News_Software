<?php
include 'connection.php';
header('Content-Type: application/json');

try {
    // Get date filters from request
    $input = json_decode(file_get_contents('php://input'), true);
    $fromDate = isset($input['from_date']) && !empty($input['from_date']) ? $input['from_date'] : null;
    $toDate = isset($input['to_date']) && !empty($input['to_date']) ? $input['to_date'] : null;
    
    // Build query with date filtering
    if ($fromDate && $toDate) {
        // Query with date filters - combine news and video_earning directly
        $query = "
            SELECT 
                r.id as reporter_id,
                r.name as reporter_name,
                COUNT(CASE WHEN source = 'news' THEN 1 END) as news_count,
                COUNT(CASE WHEN source = 'video' THEN 1 END) as video_count,
                COALESCE(SUM(earning), 0) as total_earning,
                COALESCE(SUM(web_earning), 0) as web_earning,
                COALESCE(SUM(youtube_earning), 0) as youtube_earning
            FROM (
                SELECT 
                    n.reporter_id,
                    COALESCE(n.earning, 0) as earning,
                    COALESCE(n.web_earning, 0) as web_earning,
                    0 as youtube_earning,
                    'news' as source
                FROM news n
                WHERE n.reporter_id IS NOT NULL 
                    AND n.reporter_id != 0
                    AND DATE(n.created_at) BETWEEN :from_date1 AND :to_date1
                
                UNION ALL
                
                SELECT 
                    ve.reporter_id,
                    COALESCE(ve.earning, 0) as earning,
                    0 as web_earning,
                    COALESCE(ve.youtube_earning, 0) as youtube_earning,
                    'video' as source
                FROM video_earning ve
                WHERE ve.reporter_id IS NOT NULL 
                    AND ve.reporter_id != 0
                    AND DATE(ve.created_at) BETWEEN :from_date2 AND :to_date2
            ) as combined
            LEFT JOIN reporter r ON combined.reporter_id = r.id
            GROUP BY r.id, r.name
            ORDER BY (COALESCE(SUM(earning), 0) + COALESCE(SUM(web_earning), 0) + COALESCE(SUM(youtube_earning), 0)) DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':from_date1', $fromDate);
        $stmt->bindParam(':to_date1', $toDate);
        $stmt->bindParam(':from_date2', $fromDate);
        $stmt->bindParam(':to_date2', $toDate);
        $stmt->execute();
    } else {
        // Query without date filters - use view
        $query = "
            SELECT 
                reporter_name,
                news_count,
                video_count,
                total_earning,
                web_earning,
                youtube_earning
            FROM reporter_total_earnings
            ORDER BY grand_total DESC
        ";
        $stmt = $pdo->query($query);
    }
    
    $reporters = [];
    
    while ($row = $stmt->fetch()) {
        $reporters[] = [
            'reporter_name' => $row['reporter_name'],
            'news_count' => $row['news_count'],
            'video_count' => $row['video_count'],
            'total_earning' => number_format((float)$row['total_earning'], 2, '.', ''),
            'web_earning' => number_format((float)$row['web_earning'], 2, '.', ''),
            'youtube_earning' => number_format((float)$row['youtube_earning'], 2, '.', '')
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reporters' => $reporters,
        'count' => count($reporters)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
