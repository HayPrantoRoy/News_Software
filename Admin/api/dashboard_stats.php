<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database connection
require_once '../connection.php';

try {
    // Check if this is a filtered request
    $chart = $_GET['chart'] ?? '';
    $table = $_GET['table'] ?? '';
    $period = isset($_GET['period']) ? (int)$_GET['period'] : 10;
    $fromDate = $_GET['from'] ?? '';
    $toDate = $_GET['to'] ?? '';
    
    // Get total news count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM news");
    $total_news = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total reporters
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reporter");
    $total_reporters = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active reporters
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM reporter WHERE is_active = 1");
        $active_reporters = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
        // If is_active column doesn't exist, count all reporters as active
        $active_reporters = $total_reporters;
    }
    
    // Get total views
    try {
        $stmt = $pdo->query("SELECT SUM(views) as total FROM news");
        $total_views = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    } catch (PDOException $e) {
        $total_views = 0;
    }
    
    // Get unpublished news (is_active = 0)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM news WHERE is_active = 0");
    $unpublished_news = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get news this month
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM news WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $news_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get views today (if views column exists)
    try {
        $stmt = $pdo->query("SELECT SUM(views) as total FROM news WHERE DATE(created_at) = CURRENT_DATE()");
        $views_today = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    } catch (PDOException $e) {
        $views_today = 0;
    }
    
    // Get news data with filtering
    $newsDays = 10; // Default 10 days
    if ($chart == 'news' || $chart == 'views') {
        $newsDays = $period;
    }
    
    // Build date condition for news chart
    if ($fromDate && $toDate) {
        $dateCondition = "DATE(created_at) BETWEEN '$fromDate' AND '$toDate'";
    } else {
        $dateCondition = "created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL $newsDays DAY)";
    }
    
    $stmt = $pdo->query("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as count
        FROM news
        WHERE $dateCondition
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $weekly_news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get views trend data
    try {
        $stmt = $pdo->query("
            SELECT 
                DATE(created_at) as date,
                COALESCE(SUM(views), 0) as views
            FROM news
            WHERE $dateCondition
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $views_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback if views column doesn't exist
        $views_data = [];
    }
    
    // Fill in missing days with 0 for both news and views
    $last_days = [];
    $views_trend = [];
    
    if ($fromDate && $toDate) {
        $start = strtotime($fromDate);
        $end = strtotime($toDate);
        $currentDate = $start;
        while ($currentDate <= $end) {
            $dateStr = date('Y-m-d', $currentDate);
            $dateLabel = date('M j', strtotime($dateStr));
            
            // News count
            $found = false;
            foreach ($weekly_news as $day) {
                if ($day['date'] == $dateStr) {
                    $last_days[] = [
                        'date' => $dateLabel,
                        'count' => (int)$day['count']
                    ];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $last_days[] = [
                    'date' => $dateLabel,
                    'count' => 0
                ];
            }
            
            // Views
            $foundViews = false;
            foreach ($views_data as $day) {
                if ($day['date'] == $dateStr) {
                    $views_trend[] = [
                        'date' => $dateLabel,
                        'views' => (int)$day['views']
                    ];
                    $foundViews = true;
                    break;
                }
            }
            if (!$foundViews) {
                $views_trend[] = [
                    'date' => $dateLabel,
                    'views' => 0
                ];
            }
            
            $currentDate = strtotime('+1 day', $currentDate);
        }
    } else {
        for ($i = $newsDays - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateLabel = date('M j', strtotime($date));
            
            // News count
            $found = false;
            foreach ($weekly_news as $day) {
                if ($day['date'] == $date) {
                    $last_days[] = [
                        'date' => $dateLabel,
                        'count' => (int)$day['count']
                    ];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $last_days[] = [
                    'date' => $dateLabel,
                    'count' => 0
                ];
            }
            
            // Views
            $foundViews = false;
            foreach ($views_data as $day) {
                if ($day['date'] == $date) {
                    $views_trend[] = [
                        'date' => $dateLabel,
                        'views' => (int)$day['views']
                    ];
                    $foundViews = true;
                    break;
                }
            }
            if (!$foundViews) {
                $views_trend[] = [
                    'date' => $dateLabel,
                    'views' => 0
                ];
            }
        }
    }
    
    // Get category distribution
    $stmt = $pdo->query("
        SELECT 
            c.name as category,
            COUNT(n.id) as count
        FROM category c
        LEFT JOIN news n ON n.category_id = c.id
        WHERE c.is_active = 1
        GROUP BY c.id, c.name
        HAVING count > 0
        ORDER BY count DESC
        LIMIT 7
    ");
    $category_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get reporter performance with filtering
    $perfPeriod = 30; // Default 30 days
    if ($table == 'performance') {
        $perfPeriod = $period;
    }
    
    // Build date condition for performance
    if ($table == 'performance' && $fromDate && $toDate) {
        $perfDateCondition = "DATE(n.created_at) BETWEEN '$fromDate' AND '$toDate'";
    } else {
        $perfDateCondition = "n.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL $perfPeriod DAY)";
    }
    
    try {
        $stmt = $pdo->query("
            SELECT 
                r.name,
                COUNT(n.id) as published_news,
                COALESCE(SUM(n.views), 0) as total_views,
                COALESCE(SUM(n.earning), 0) as earnings
            FROM reporter r
            LEFT JOIN news n ON n.reporter_id = r.id 
                AND n.is_active = 1 
                AND $perfDateCondition
            GROUP BY r.id, r.name
            HAVING published_news > 0
            ORDER BY published_news DESC, total_views DESC
        ");
        $reporter_performance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback without views and earning if columns don't exist
        $stmt = $pdo->query("
            SELECT 
                r.name,
                COUNT(n.id) as published_news,
                0 as total_views,
                0 as earnings
            FROM reporter r
            LEFT JOIN news n ON n.reporter_id = r.id 
                AND n.is_active = 1 
                AND $perfDateCondition
            GROUP BY r.id, r.name
            HAVING published_news > 0
            ORDER BY published_news DESC
        ");
        $reporter_performance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get recent news (last 10)
    try {
        $stmt = $pdo->query("
            SELECT 
                n.id,
                n.headline,
                n.created_at,
                COALESCE(n.views, 0) as views,
                n.is_active,
                r.name as reporter_name
            FROM news n
            LEFT JOIN reporter r ON n.reporter_id = r.id
            ORDER BY n.created_at DESC
            LIMIT 10
        ");
        $recent_news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback without views if column doesn't exist
        $stmt = $pdo->query("
            SELECT 
                n.id,
                n.headline,
                n.created_at,
                0 as views,
                n.is_active,
                r.name as reporter_name
            FROM news n
            LEFT JOIN reporter r ON n.reporter_id = r.id
            ORDER BY n.created_at DESC
            LIMIT 10
        ");
        $recent_news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get top reporters by views with filtering
    $reportersPeriod = 30; // Default 30 days
    $reportersLimit = 10; // Default limit
    
    if ($chart == 'reporters') {
        $reportersPeriod = $period;
        $reportersLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        // Cap at 1000 to prevent excessive queries
        $reportersLimit = min($reportersLimit, 1000);
    }
    
    // Build date condition for top reporters
    if ($chart == 'reporters' && $fromDate && $toDate) {
        $reportersDateCondition = "DATE(n.created_at) BETWEEN '$fromDate' AND '$toDate'";
    } else {
        $reportersDateCondition = "n.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL $reportersPeriod DAY)";
    }
    
    try {
        $stmt = $pdo->query("
            SELECT 
                r.name,
                COALESCE(SUM(n.views), 0) as total_views,
                COUNT(n.id) as news_count
            FROM reporter r
            LEFT JOIN news n ON n.reporter_id = r.id 
                AND n.is_active = 1 
                AND $reportersDateCondition
            GROUP BY r.id, r.name
            HAVING total_views > 0
            ORDER BY total_views DESC
            LIMIT $reportersLimit
        ");
        $top_reporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback without views if column doesn't exist
        $top_reporters = [];
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'total_news' => (int)$total_news,
        'total_reporters' => (int)$total_reporters,
        'active_reporters' => (int)$active_reporters,
        'total_views' => (int)$total_views,
        'unpublished_news' => (int)$unpublished_news,
        'news_this_month' => (int)$news_this_month,
        'views_today' => (int)$views_today,
        'weekly_news' => $last_days,
        'views_trend' => $views_trend,
        'category_distribution' => $category_distribution,
        'reporter_performance' => $reporter_performance,
        'top_reporters' => $top_reporters
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
