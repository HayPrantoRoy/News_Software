<?php
header('Content-Type: application/json');
require_once '../connection.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle JSON POST data
$jsonInput = json_decode(file_get_contents('php://input'), true);
if ($jsonInput) {
    $action = $jsonInput['action'] ?? $action;
}

switch ($action) {
    case 'get_reporters':
        getReporters($pdo);
        break;
    case 'get_pending':
        getPendingPayments($pdo);
        break;
    case 'get_history':
        getPaymentHistory($pdo);
        break;
    case 'mark_paid':
        markAsPaid($pdo, $jsonInput['items'] ?? []);
        break;
    case 'mark_unpaid':
        markAsUnpaid($pdo, $jsonInput['type'] ?? '', $jsonInput['id'] ?? 0);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getReporters($pdo) {
    $sql = "SELECT id, name, email FROM reporter ORDER BY name";
    
    $stmt = $pdo->query($sql);
    $reporters = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $reporters]);
}

function getPendingPayments($pdo) {
    $data = [];
    
    // Get unpaid news earnings
    $sql = "SELECT n.id, n.headline, n.earning, n.web_earning, n.created_at, 
                   r.id as reporter_id, r.name as reporter_name, r.email as reporter_email, 'news' as type
            FROM news n
            LEFT JOIN reporter r ON n.reporter_id = r.id
            WHERE (n.earning > 0 OR n.web_earning > 0)
            AND (n.is_paid = 0 OR n.is_paid IS NULL)
            ORDER BY n.created_at DESC";
    
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch()) {
        $row['youtube_earning'] = 0;
        $data[] = $row;
    }
    
    // Get unpaid video earnings
    $sql = "SELECT v.id, v.video_headline as headline, v.earning, v.youtube_earning, v.created_at,
                   r.id as reporter_id, r.name as reporter_name, r.email as reporter_email, 'video' as type
            FROM video_earning v
            LEFT JOIN reporter r ON v.reporter_id = r.id
            WHERE (v.earning > 0 OR v.youtube_earning > 0)
            AND (v.is_paid = 0 OR v.is_paid IS NULL)
            ORDER BY v.created_at DESC";
    
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch()) {
        $row['web_earning'] = 0;
        $data[] = $row;
    }
    
    // Sort by created_at descending
    usort($data, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    echo json_encode(['success' => true, 'data' => $data]);
}

function getPaymentHistory($pdo) {
    $data = [];
    
    // Get paid news
    $sql = "SELECT n.id, n.headline, n.earning, n.web_earning, n.paid_at, n.created_at,
                   r.id as reporter_id, r.name as reporter_name, 'news' as type
            FROM news n
            LEFT JOIN reporter r ON n.reporter_id = r.id
            WHERE n.is_paid = 1 AND n.paid_at IS NOT NULL
            ORDER BY n.paid_at DESC";
    
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch()) {
        $row['youtube_earning'] = 0;
        $data[] = $row;
    }
    
    // Get paid videos
    $sql = "SELECT v.id, v.video_headline as headline, v.earning, v.youtube_earning, v.paid_at, v.created_at,
                   r.id as reporter_id, r.name as reporter_name, 'video' as type
            FROM video_earning v
            LEFT JOIN reporter r ON v.reporter_id = r.id
            WHERE v.is_paid = 1 AND v.paid_at IS NOT NULL
            ORDER BY v.paid_at DESC";
    
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch()) {
        $row['web_earning'] = 0;
        $data[] = $row;
    }
    
    // Sort by paid_at descending
    usort($data, function($a, $b) {
        return strtotime($b['paid_at']) - strtotime($a['paid_at']);
    });
    
    echo json_encode(['success' => true, 'data' => $data]);
}

function markAsPaid($pdo, $items) {
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'No items provided']);
        return;
    }
    
    $updated = 0;
    $now = date('Y-m-d H:i:s');
    
    foreach ($items as $item) {
        $type = $item['type'];
        $id = intval($item['id']);
        
        if ($type === 'news') {
            $stmt = $pdo->prepare("UPDATE news SET is_paid = 1, paid_at = ? WHERE id = ?");
            $stmt->execute([$now, $id]);
        } else if ($type === 'video') {
            $stmt = $pdo->prepare("UPDATE video_earning SET is_paid = 1, paid_at = ? WHERE id = ?");
            $stmt->execute([$now, $id]);
        } else {
            continue;
        }
        
        $updated++;
    }
    
    echo json_encode(['success' => true, 'updated' => $updated]);
}

function markAsUnpaid($pdo, $type, $id) {
    $id = intval($id);
    
    if ($type === 'news') {
        $stmt = $pdo->prepare("UPDATE news SET is_paid = 0, paid_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    } else if ($type === 'video') {
        $stmt = $pdo->prepare("UPDATE video_earning SET is_paid = 0, paid_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        return;
    }
    
    echo json_encode(['success' => true]);
}
?>
