<?php
/**
 * Views API
 * Handles view tracking and statistics for news articles
 */

header('Content-Type: application/json');
include 'connection.php';

// Get action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_views':
        getViews($pdo);
        break;
    case 'increment_view':
        incrementView($pdo);
        break;
    case 'get_top_viewed':
        getTopViewed($pdo);
        break;
    case 'get_news_stats':
        getNewsStats($pdo);
        break;
    case 'reset_views':
        resetViews($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Get view count for a specific news article
 */
function getViews($pdo) {
    if (!isset($_GET['news_id']) && !isset($_GET['slug'])) {
        echo json_encode(['success' => false, 'message' => 'news_id or slug is required']);
        return;
    }
    
    try {
        if (isset($_GET['news_id'])) {
            $news_id = (int)$_GET['news_id'];
            $stmt = $pdo->prepare("SELECT id, headline, views FROM news WHERE id = ?");
            $stmt->execute([$news_id]);
        } else {
            $slug = $_GET['slug'];
            $stmt = $pdo->prepare("SELECT id, headline, views FROM news WHERE slug = ?");
            $stmt->execute([$slug]);
        }
        
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($news) {
            echo json_encode([
                'success' => true,
                'data' => $news
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'News not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Increment view count for a news article
 */
function incrementView($pdo) {
    if (!isset($_POST['news_id']) && !isset($_POST['slug'])) {
        echo json_encode(['success' => false, 'message' => 'news_id or slug is required']);
        return;
    }
    
    try {
        // Check if views column exists, if not create it
        try {
            if (isset($_POST['news_id'])) {
                $news_id = (int)$_POST['news_id'];
                $stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
                $stmt->execute([$news_id]);
            } else {
                $slug = $_POST['slug'];
                $stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE slug = ?");
                $stmt->execute([$slug]);
            }
        } catch (PDOException $e) {
            // If views column doesn't exist, add it
            if (strpos($e->getMessage(), 'views') !== false) {
                $pdo->exec("ALTER TABLE news ADD COLUMN views INT DEFAULT 0");
                // Try update again
                if (isset($_POST['news_id'])) {
                    $news_id = (int)$_POST['news_id'];
                    $stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
                    $stmt->execute([$news_id]);
                } else {
                    $slug = $_POST['slug'];
                    $stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE slug = ?");
                    $stmt->execute([$slug]);
                }
            } else {
                throw $e;
            }
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'View count incremented successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'News not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Get top viewed news articles
 */
function getTopViewed($pdo) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    
    try {
        // Ensure views column exists
        try {
            if ($category_id) {
                $stmt = $pdo->prepare("
                    SELECT n.id, n.headline, n.slug, n.image_url, n.views, n.created_at,
                           c.name as category_name, c.slug as category_slug
                    FROM news n
                    LEFT JOIN category c ON n.category_id = c.id
                    WHERE n.is_active = 1 AND n.category_id = ?
                    ORDER BY n.views DESC
                    LIMIT ?
                ");
                $stmt->execute([$category_id, $limit]);
            } else {
                $stmt = $pdo->prepare("
                    SELECT n.id, n.headline, n.slug, n.image_url, n.views, n.created_at,
                           c.name as category_name, c.slug as category_slug
                    FROM news n
                    LEFT JOIN category c ON n.category_id = c.id
                    WHERE n.is_active = 1
                    ORDER BY n.views DESC
                    LIMIT ?
                ");
                $stmt->execute([$limit]);
            }
            
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $news,
                'count' => count($news)
            ]);
        } catch (PDOException $e) {
            // If views column doesn't exist, return empty array
            if (strpos($e->getMessage(), 'views') !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'count' => 0,
                    'message' => 'Views column not yet created'
                ]);
            } else {
                throw $e;
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Get statistics about news views
 */
function getNewsStats($pdo) {
    try {
        // Ensure views column exists
        try {
            // Total views across all news
            $stmt = $pdo->query("SELECT SUM(views) as total_views, AVG(views) as avg_views, MAX(views) as max_views FROM news WHERE is_active = 1");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // News with views count
            $stmt = $pdo->query("SELECT COUNT(*) as news_with_views FROM news WHERE is_active = 1 AND views > 0");
            $viewed_count = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Total news count
            $stmt = $pdo->query("SELECT COUNT(*) as total_news FROM news WHERE is_active = 1");
            $total_count = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'total_views' => (int)($stats['total_views'] ?? 0),
                    'average_views' => round($stats['avg_views'] ?? 0, 2),
                    'max_views' => (int)($stats['max_views'] ?? 0),
                    'news_with_views' => (int)($viewed_count['news_with_views'] ?? 0),
                    'total_news' => (int)($total_count['total_news'] ?? 0)
                ]
            ]);
        } catch (PDOException $e) {
            // If views column doesn't exist
            if (strpos($e->getMessage(), 'views') !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'total_views' => 0,
                        'average_views' => 0,
                        'max_views' => 0,
                        'news_with_views' => 0,
                        'total_news' => 0
                    ],
                    'message' => 'Views column not yet created'
                ]);
            } else {
                throw $e;
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Reset views for a specific news or all news (Admin function)
 */
function resetViews($pdo) {
    // Security check - you may want to add admin authentication here
    
    try {
        if (isset($_POST['news_id'])) {
            $news_id = (int)$_POST['news_id'];
            $stmt = $pdo->prepare("UPDATE news SET views = 0 WHERE id = ?");
            $stmt->execute([$news_id]);
            $message = 'Views reset for news ID: ' . $news_id;
        } else {
            // Reset all views
            $pdo->exec("UPDATE news SET views = 0");
            $message = 'All views reset successfully';
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
