<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'connection.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'get_categories':
        getCategories($pdo);
        break;
    case 'get_reporters':
        getReporters($pdo);
        break;
    case 'get_news':
        getNews($pdo);
        break;
    case 'get_reporter_earnings':
        getReporterEarnings($pdo);
        break;
    case 'create_news':
        createNews($pdo);
        break;
    case 'update_news':
        updateNews($pdo);
        break;
    case 'delete_news':
        deleteNews($pdo);
        break;
    case 'toggle_status':
        toggleStatus($pdo);
        break;
    case 'update_earning':
        updateEarning($pdo);
        break;
    case 'get_news_detail':
        getNewsDetail($pdo);
        break;
    case 'get_video_earnings':
        getVideoEarnings($pdo);
        break;
    case 'add_video_earning':
        addVideoEarning($pdo);
        break;
    case 'update_video_earning':
        updateVideoEarning($pdo);
        break;
    case 'delete_video_earning':
        deleteVideoEarning($pdo);
        break;
    case 'get_reporter_video_earnings':
        getReporterVideoEarnings($pdo);
        break;
    case 'get_all_earnings':
        getAllEarnings($pdo);
        break;
    case 'update_news_earning_field':
        updateNewsEarningField($pdo);
        break;
    case 'update_video_earning_field':
        updateVideoEarningField($pdo);
        break;
    case 'get_reporter_payment_history':
        getReporterPaymentHistory($pdo);
        break;
    default:
        echo json_encode(['success'=>false,'message'=>'Invalid action']);
        break;
}

// Toggle is_active status for news
function toggleStatus($pdo){
    try{
        $id = $_POST['id'] ?? null;
        $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : null;
        if(!$id || ($is_active !== 0 && $is_active !== 1)){
            echo json_encode(['success'=>false,'message'=>'Invalid parameters']);
            return;
        }
        $stmt = $pdo->prepare("UPDATE news SET is_active=? WHERE id=?");
        $stmt->execute([$is_active, $id]);
        echo json_encode(['success'=>true,'message'=>'Status updated']);
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Fetch earnings per news for a reporter
function getReporterEarnings($pdo) {
    try {
        if (!isset($_GET['reporter_id']) || !is_numeric($_GET['reporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'reporter_id is required']);
            return;
        }

        $reporterId = (int)$_GET['reporter_id'];

        $rows = [];
        try {
            // Select all earning columns, filter out paid items
            $stmt = $pdo->prepare("SELECT id, headline, created_at, COALESCE(earning, 0) AS earning, COALESCE(web_earning, 0) AS web_earning FROM news WHERE reporter_id = ? AND (is_paid = 0 OR is_paid IS NULL) ORDER BY id DESC");
            $stmt->execute([$reporterId]);
            $rows = $stmt->fetchAll();
        } catch (PDOException $inner) {
            // Fallback when columns don't exist
            $stmt = $pdo->prepare("SELECT id, headline, created_at, 0 AS earning, 0 AS web_earning FROM news WHERE reporter_id = ? ORDER BY id DESC");
            $stmt->execute([$reporterId]);
            $rows = $stmt->fetchAll();
        }

        // Compute total
        $total = 0;
        foreach ($rows as $r) {
            $total += (float)$r['earning'];
        }

        echo json_encode([
            'success' => true,
            'data' => $rows,
            'total' => $total
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Fetch categories
function getCategories($pdo){
    try{
        $stmt = $pdo->query("SELECT id,name,slug FROM category WHERE is_active=1 ORDER BY name");
        echo json_encode($stmt->fetchAll());
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Fetch reporters
function getReporters($pdo){
    try{
        $stmt = $pdo->query("SELECT id,name,email,phone_number FROM reporter ORDER BY name");
        echo json_encode($stmt->fetchAll());
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Fetch news
function getNews($pdo){
    try{
        if(isset($_GET['id'])){
            $stmt = $pdo->prepare("SELECT * FROM news WHERE id=?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        }else{
            $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, 
                           r.name as reporter_name, r.email as reporter_email
                    FROM news n
                    LEFT JOIN category c ON n.category_id=c.id
                    LEFT JOIN reporter r ON n.reporter_id=r.id
                    WHERE 1=1";
            
            $params = [];
            
            // Add reporter filter if reporter_id is provided
            if (isset($_GET['reporter_id']) && is_numeric($_GET['reporter_id'])) {
                $sql .= " AND n.reporter_id = ?";
                $params[] = $_GET['reporter_id'];
            }
            
            $sql .= " ORDER BY n.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll());
        }
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Create news
function createNews($pdo){
    try{
        $required = ['category_id','headline','slug','news_1'];
        foreach($required as $r){
            if(empty($_POST[$r])) {
                echo json_encode(['success'=>false,'message'=>"Field $r is required"]);
                return;
            }
        }

        // Check slug unique
        $stmt = $pdo->prepare("SELECT id FROM news WHERE slug=?");
        $stmt->execute([$_POST['slug']]);
        if($stmt->fetch()){
            echo json_encode(['success'=>false,'message'=>'Slug already exists']);
            return;
        }

        $image_fields = ['image_url','image_2','image_3','image_4','image_5'];
        $images = [];
        foreach($image_fields as $field){
            if(isset($_FILES[$field]) && $_FILES[$field]['error']===UPLOAD_ERR_OK){
                $images[$field] = uploadImage($_FILES[$field],$field);
            }
        }

        $sql = "INSERT INTO news (
            category_id, reporter_id, headline, short_description, slug,
            news_1, image_url, image_url_title, quote_1, auture_1, news_2, image_2, image_2_title, image_3, image_3_title,
            news_3, quote_2, auture_2, image_4, image_4_title, image_5, image_5_title, news_4, is_active, created_at
        ) VALUES (
            :category_id,:reporter_id,:headline,:short_description,:slug,
            :news_1,:image_url,:image_url_title,:quote_1,:auture_1,:news_2,:image_2,:image_2_title,:image_3,:image_3_title,
            :news_3,:quote_2,:auture_2,:image_4,:image_4_title,:image_5,:image_5_title,:news_4,0,NOW()
        )";

        $stmt = $pdo->prepare($sql);
        $params = [
            ':category_id'=>$_POST['category_id'],
            ':reporter_id'=>$_POST['reporter_id'],
            ':headline'=>$_POST['headline'],
            ':short_description'=>$_POST['short_description'],
            ':slug'=>$_POST['slug'],
            ':news_1'=>$_POST['news_1'],
            ':image_url'=>$images['image_url']??null,
            ':image_url_title'=>$_POST['image_url_title']??null,
            ':quote_1'=>$_POST['quote_1']??null,
            ':auture_1'=>$_POST['auture_1']??null,
            ':news_2'=>$_POST['news_2']??null,
            ':image_2'=>$images['image_2']??null,
            ':image_2_title'=>$_POST['image_2_title']??null,
            ':image_3'=>$images['image_3']??null,
            ':image_3_title'=>$_POST['image_3_title']??null,
            ':news_3'=>$_POST['news_3']??null,
            ':quote_2'=>$_POST['quote_2']??null,
            ':auture_2'=>$_POST['auture_2']??null,
            ':image_4'=>$images['image_4']??null,
            ':image_4_title'=>$_POST['image_4_title']??null,
            ':image_5'=>$images['image_5']??null,
            ':image_5_title'=>$_POST['image_5_title']??null,
            ':news_4'=>$_POST['news_4']??null
        ];
        $stmt->execute($params);
        echo json_encode(['success'=>true,'message'=>'News created','id'=>$pdo->lastInsertId()]);
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Update news
function updateNews($pdo){
    try{
        if(empty($_POST['id'])){
            echo json_encode(['success'=>false,'message'=>'News ID required']);
            return;
        }
        $stmt = $pdo->prepare("SELECT id FROM news WHERE id=?");
        $stmt->execute([$_POST['id']]);
        if(!$stmt->fetch()){
            echo json_encode(['success'=>false,'message'=>'News not found']);
            return;
        }

        $image_fields = ['image_url','image_2','image_3','image_4','image_5'];
        $images = [];
        foreach($image_fields as $field){
            if(isset($_FILES[$field]) && $_FILES[$field]['error']===UPLOAD_ERR_OK){
                $images[$field] = uploadImage($_FILES[$field],$field);
            }
        }

        // Handle image deletions - set to NULL if marked for deletion
        $deleteImages = [];
        $imageFieldMap = [
            'delete_image_url' => 'image_url',
            'delete_image_2' => 'image_2',
            'delete_image_3' => 'image_3',
            'delete_image_4' => 'image_4',
            'delete_image_5' => 'image_5'
        ];
        foreach ($imageFieldMap as $deleteFlag => $imageField) {
            if (isset($_POST[$deleteFlag]) && $_POST[$deleteFlag] === '1') {
                $deleteImages[$imageField] = true;
            }
        }

        $sql = "UPDATE news SET 
            category_id=:category_id,
            reporter_id=:reporter_id,
            headline=:headline,
            short_description=:short_description,
            news_1=:news_1,
            quote_1=:quote_1,
            auture_1=:auture_1,
            news_2=:news_2,
            news_3=:news_3,
            quote_2=:quote_2,
            auture_2=:auture_2,
            news_4=:news_4,
            image_url_title=:image_url_title,
            image_2_title=:image_2_title,
            image_3_title=:image_3_title,
            image_4_title=:image_4_title,
            image_5_title=:image_5_title";

        // Add image fields to SQL - either new upload or set to NULL for deletion
        foreach($images as $field=>$filename){
            $sql.=", $field=:$field";
        }
        foreach($deleteImages as $field=>$shouldDelete){
            if (!isset($images[$field])) { // Only if not uploading a new image
                $sql.=", $field=NULL";
            }
        }

        $sql.=" WHERE id=:id";
        $stmt = $pdo->prepare($sql);

        $params = [
            ':id'=>$_POST['id'],
            ':category_id'=>$_POST['category_id'],
            ':reporter_id'=>$_POST['reporter_id'],
            ':headline'=>$_POST['headline'],
            ':short_description'=>$_POST['short_description'],
            ':news_1'=>$_POST['news_1'],
            ':quote_1'=>$_POST['quote_1']??null,
            ':auture_1'=>$_POST['auture_1']??null,
            ':news_2'=>$_POST['news_2']??null,
            ':news_3'=>$_POST['news_3']??null,
            ':quote_2'=>$_POST['quote_2']??null,
            ':auture_2'=>$_POST['auture_2']??null,
            ':news_4'=>$_POST['news_4']??null,
            ':image_url_title'=>$_POST['image_url_title']??null,
            ':image_2_title'=>$_POST['image_2_title']??null,
            ':image_3_title'=>$_POST['image_3_title']??null,
            ':image_4_title'=>$_POST['image_4_title']??null,
            ':image_5_title'=>$_POST['image_5_title']??null
        ];
        foreach($images as $field=>$filename){
            $params[":$field"] = $filename;
        }

        $stmt->execute($params);
        echo json_encode(['success'=>true,'message'=>'News updated']);
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// Delete news
function deleteNews($pdo){
    try{
        if(empty($_POST['id'])){
            echo json_encode(['success'=>false,'message'=>'News ID required']);
            return;
        }

        // Delete news images
        $stmt = $pdo->prepare("SELECT image_url,image_2,image_3,image_4,image_5 FROM news WHERE id=?");
        $stmt->execute([$_POST['id']]);
        $news = $stmt->fetch();
        if($news){
            foreach($news as $img){
                if($img && file_exists('img/'.$img)) unlink('img/'.$img);
            }
            $stmt = $pdo->prepare("DELETE FROM news WHERE id=?");
            $stmt->execute([$_POST['id']]);
            echo json_encode(['success'=>true,'message'=>'News deleted']);
        }else{
            echo json_encode(['success'=>false,'message'=>'News not found']);
        }
    }catch(PDOException $e){
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}


function uploadImage($file,$field){
    $upload_dir = 'img/';
    if(!is_dir($upload_dir)) mkdir($upload_dir,0755,true);

    $ext = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp','avif'];
    if(!in_array($ext,$allowed)) throw new Exception('Invalid file type');
    if($file['size']>5*1024*1024) throw new Exception('File too large');

    $filename = uniqid($field.'_').'.'.$ext;
    $filepath = $upload_dir.$filename;
    if(move_uploaded_file($file['tmp_name'],$filepath)){
        return $filename; // only filename stored in DB
    }
    throw new Exception('Failed to upload image');
}

// Update earning for a news item
function updateEarning($pdo) {
    try {
        if (!isset($_POST['news_id']) || !is_numeric($_POST['news_id'])) {
            echo json_encode(['success' => false, 'message' => 'news_id is required']);
            return;
        }
        
        if (!isset($_POST['earning']) || !is_numeric($_POST['earning'])) {
            echo json_encode(['success' => false, 'message' => 'earning is required and must be numeric']);
            return;
        }
        
        $newsId = (int)$_POST['news_id'];
        $earning = (float)$_POST['earning'];
        
        // First check if earning column exists, if not create it
        try {
            $stmt = $pdo->prepare("UPDATE news SET earning = ? WHERE id = ?");
            $stmt->execute([$earning, $newsId]);
        } catch (PDOException $e) {
            // If column doesn't exist, add it
            if (strpos($e->getMessage(), 'earning') !== false) {
                $pdo->exec("ALTER TABLE news ADD COLUMN earning DECIMAL(10,2) DEFAULT 0.00");
                // Try update again
                $stmt = $pdo->prepare("UPDATE news SET earning = ? WHERE id = ?");
                $stmt->execute([$earning, $newsId]);
            } else {
                throw $e;
            }
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Earning updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'News not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Get single news detail for editing
function getNewsDetail($pdo) {
    try {
        if (!isset($_GET['news_id']) || !is_numeric($_GET['news_id'])) {
            echo json_encode(['success' => false, 'message' => 'news_id is required']);
            return;
        }
        
        $newsId = (int)$_GET['news_id'];
        
        // Try to select with earning column
        try {
            $stmt = $pdo->prepare("
                SELECT id, headline, created_at, COALESCE(earning, 0) AS earning 
                FROM news 
                WHERE id = ?
            ");
            $stmt->execute([$newsId]);
            $news = $stmt->fetch();
        } catch (PDOException $inner) {
            // Fallback if earning column doesn't exist
            $stmt = $pdo->prepare("
                SELECT id, headline, created_at, 0 AS earning 
                FROM news 
                WHERE id = ?
            ");
            $stmt->execute([$newsId]);
            $news = $stmt->fetch();
        }
        
        if ($news) {
            echo json_encode([
                'success' => true,
                'news' => $news
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'News not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Get all video earnings with reporter info
function getVideoEarnings($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT ve.*, r.name as reporter_name 
            FROM video_earning ve
            LEFT JOIN reporter r ON ve.reporter_id = r.id
            ORDER BY ve.created_at DESC
        ");
        $earnings = $stmt->fetchAll();
        echo json_encode([
            'success' => true,
            'data' => $earnings
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Add new video earning
function addVideoEarning($pdo) {
    try {
        if (!isset($_POST['reporter_id']) || !is_numeric($_POST['reporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'Reporter ID is required']);
            return;
        }
        
        if (empty($_POST['video_headline'])) {
            echo json_encode(['success' => false, 'message' => 'Video headline is required']);
            return;
        }
        
        $reporterId = (int)$_POST['reporter_id'];
        $videoHeadline = trim($_POST['video_headline']);
        $earning = isset($_POST['earning']) ? (float)$_POST['earning'] : 0.00;
        
        $stmt = $pdo->prepare("
            INSERT INTO video_earning (reporter_id, video_headline, earning, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$reporterId, $videoHeadline, $earning]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Video earning added successfully',
            'id' => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Update video earning
function updateVideoEarning($pdo) {
    try {
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Video earning ID is required']);
            return;
        }
        
        if (!isset($_POST['earning']) || !is_numeric($_POST['earning'])) {
            echo json_encode(['success' => false, 'message' => 'Earning amount is required']);
            return;
        }
        
        $id = (int)$_POST['id'];
        $earning = (float)$_POST['earning'];
        
        $stmt = $pdo->prepare("
            UPDATE video_earning 
            SET earning = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$earning, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Video earning updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Video earning not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Delete video earning
function deleteVideoEarning($pdo) {
    try {
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Video earning ID is required']);
            return;
        }
        
        $id = (int)$_POST['id'];
        
        $stmt = $pdo->prepare("DELETE FROM video_earning WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Video earning deleted successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Video earning not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Get video earnings for a reporter
function getReporterVideoEarnings($pdo) {
    try {
        if (!isset($_GET['reporter_id']) || !is_numeric($_GET['reporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'reporter_id is required']);
            return;
        }

        $reporterId = (int)$_GET['reporter_id'];

        $stmt = $pdo->prepare("
            SELECT id, video_headline, COALESCE(earning, 0) AS earning, COALESCE(youtube_earning, 0) AS youtube_earning, created_at 
            FROM video_earning 
            WHERE reporter_id = ? AND (is_paid = 0 OR is_paid IS NULL)
            ORDER BY created_at DESC
        ");
        $stmt->execute([$reporterId]);
        $rows = $stmt->fetchAll();

        // Compute total
        $total = 0;
        foreach ($rows as $r) {
            $total += (float)$r['earning'];
        }

        echo json_encode([
            'success' => true,
            'data' => $rows,
            'total' => $total
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Get payment history for a reporter (paid items only)
function getReporterPaymentHistory($pdo) {
    try {
        if (!isset($_GET['reporter_id']) || !is_numeric($_GET['reporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'reporter_id is required']);
            return;
        }

        $reporterId = (int)$_GET['reporter_id'];
        $data = [];

        // Get paid news
        try {
            $stmt = $pdo->prepare("
                SELECT id, headline, COALESCE(earning, 0) AS earning, 0 AS video_earning, paid_at, created_at, 'news' AS type
                FROM news 
                WHERE reporter_id = ? AND is_paid = 1 AND paid_at IS NOT NULL
                ORDER BY paid_at DESC
            ");
            $stmt->execute([$reporterId]);
            while ($row = $stmt->fetch()) {
                $data[] = $row;
            }
        } catch (PDOException $e) {
            // Column might not exist yet
        }

        // Get paid videos
        try {
            $stmt = $pdo->prepare("
                SELECT id, video_headline AS headline, COALESCE(earning, 0) AS earning, COALESCE(earning, 0) AS video_earning, paid_at, created_at, 'video' AS type
                FROM video_earning 
                WHERE reporter_id = ? AND is_paid = 1 AND paid_at IS NOT NULL
                ORDER BY paid_at DESC
            ");
            $stmt->execute([$reporterId]);
            while ($row = $stmt->fetch()) {
                $data[] = $row;
            }
        } catch (PDOException $e) {
            // Column might not exist yet
        }

        // Sort by paid_at descending
        usort($data, function($a, $b) {
            return strtotime($b['paid_at']) - strtotime($a['paid_at']);
        });

        // Compute total
        $total = 0;
        foreach ($data as $r) {
            $total += (float)$r['earning'];
        }

        echo json_encode([
            'success' => true,
            'data' => $data,
            'total' => $total
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Get all earnings (news + video) combined
function getAllEarnings($pdo) {
    try {
        $allEarnings = [];
        
        // Get news earnings with existing earning and web_earning (no youtube_earning for news)
        try {
            $stmt = $pdo->query("
                SELECT 
                    n.id,
                    n.headline,
                    n.created_at,
                    COALESCE(n.earning, 0) AS earning,
                    COALESCE(n.web_earning, 0) AS web_earning,
                    0 AS youtube_earning,
                    r.name as reporter_name,
                    r.id as reporter_id,
                    'news' as type
                FROM news n
                LEFT JOIN reporter r ON n.reporter_id = r.id
                WHERE n.reporter_id IS NOT NULL AND n.reporter_id != 0
                ORDER BY n.created_at DESC
            ");
            $newsEarnings = $stmt->fetchAll();
            $allEarnings = array_merge($allEarnings, $newsEarnings);
        } catch (PDOException $inner) {
            // Fallback if earning columns don't exist
            $stmt = $pdo->query("
                SELECT 
                    n.id,
                    n.headline,
                    n.created_at,
                    0 AS earning,
                    0 AS web_earning,
                    0 AS youtube_earning,
                    r.name as reporter_name,
                    r.id as reporter_id,
                    'news' as type
                FROM news n
                LEFT JOIN reporter r ON n.reporter_id = r.id
                WHERE n.reporter_id IS NOT NULL AND n.reporter_id != 0
                ORDER BY n.created_at DESC
            ");
            $newsEarnings = $stmt->fetchAll();
            $allEarnings = array_merge($allEarnings, $newsEarnings);
        }
        
        // Get video earnings with existing earning and youtube_earning (no web_earning for videos)
        $stmt = $pdo->query("
            SELECT 
                ve.id,
                ve.video_headline as headline,
                ve.created_at,
                COALESCE(ve.earning, 0) AS earning,
                0 AS web_earning,
                COALESCE(ve.youtube_earning, 0) AS youtube_earning,
                r.name as reporter_name,
                r.id as reporter_id,
                'video' as type
            FROM video_earning ve
            LEFT JOIN reporter r ON ve.reporter_id = r.id
            WHERE ve.reporter_id IS NOT NULL AND ve.reporter_id != 0
            ORDER BY ve.created_at DESC
        ");
        $videoEarnings = $stmt->fetchAll();
        $allEarnings = array_merge($allEarnings, $videoEarnings);
        
        // Sort by created_at DESC
        usort($allEarnings, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        echo json_encode([
            'success' => true,
            'data' => $allEarnings
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Update news earning field (earning or web_earning only)
function updateNewsEarningField($pdo) {
    try {
        if (!isset($_POST['news_id']) || !is_numeric($_POST['news_id'])) {
            echo json_encode(['success' => false, 'message' => 'news_id is required']);
            return;
        }
        
        if (!isset($_POST['field']) || !in_array($_POST['field'], ['earning', 'web_earning'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid field']);
            return;
        }
        
        if (!isset($_POST['value']) || !is_numeric($_POST['value'])) {
            echo json_encode(['success' => false, 'message' => 'value is required and must be numeric']);
            return;
        }
        
        $newsId = (int)$_POST['news_id'];
        $field = $_POST['field'];
        $value = (float)$_POST['value'];
        
        // Try to update, if column doesn't exist, add it first
        try {
            $stmt = $pdo->prepare("UPDATE news SET $field = ? WHERE id = ?");
            $stmt->execute([$value, $newsId]);
        } catch (PDOException $e) {
            // If column doesn't exist, add it
            if (strpos($e->getMessage(), $field) !== false) {
                $pdo->exec("ALTER TABLE news ADD COLUMN $field DECIMAL(10,2) DEFAULT 0.00");
                // Try update again
                $stmt = $pdo->prepare("UPDATE news SET $field = ? WHERE id = ?");
                $stmt->execute([$value, $newsId]);
            } else {
                throw $e;
            }
        }
        
        if ($stmt->rowCount() > 0 || $stmt->rowCount() === 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Earning updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'News not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Update video earning field (earning or youtube_earning only)
function updateVideoEarningField($pdo) {
    try {
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'id is required']);
            return;
        }
        
        if (!isset($_POST['field']) || !in_array($_POST['field'], ['earning', 'youtube_earning'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid field']);
            return;
        }
        
        if (!isset($_POST['value']) || !is_numeric($_POST['value'])) {
            echo json_encode(['success' => false, 'message' => 'value is required and must be numeric']);
            return;
        }
        
        $id = (int)$_POST['id'];
        $field = $_POST['field'];
        $value = (float)$_POST['value'];
        
        // Try to update, if column doesn't exist, add it first
        try {
            $stmt = $pdo->prepare("UPDATE video_earning SET $field = ? WHERE id = ?");
            $stmt->execute([$value, $id]);
        } catch (PDOException $e) {
            // If column doesn't exist, add it
            if (strpos($e->getMessage(), $field) !== false) {
                $pdo->exec("ALTER TABLE video_earning ADD COLUMN $field DECIMAL(10,2) DEFAULT 0.00");
                // Try update again
                $stmt = $pdo->prepare("UPDATE video_earning SET $field = ? WHERE id = ?");
                $stmt->execute([$value, $id]);
            } else {
                throw $e;
            }
        }
        
        if ($stmt->rowCount() > 0 || $stmt->rowCount() === 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Earning updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Video earning not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
