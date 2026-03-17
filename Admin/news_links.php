<?php
include 'auth_check.php';
include 'connection.php'; // PDO connection

// Get user_id for links
$user_id = $_SESSION['current_user_id'] ?? 0;
$user_id_param = $user_id > 0 ? "?user_id=$user_id" : "";
$user_id_suffix = $user_id > 0 ? "&user_id=$user_id" : "";

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Total news count
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM news WHERE is_active = 1");
$total = $totalStmt->fetch()['total'];
$totalPages = ceil($total / $limit);

// Fetch news with category info
$stmt = $pdo->prepare("SELECT n.id, n.headline, n.slug AS news_slug, c.slug AS category_slug
                       FROM news n
                       JOIN category c ON n.category_id = c.id
                       WHERE n.is_active = 1
                       ORDER BY n.created_at DESC
                       LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$newsList = $stmt->fetchAll();

// Dynamic domain based on current server
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$domain = $protocol . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . '/';
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>সংবাদ লিংক তালিকা</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
    background: #f5f7fa;
    color: #2c3e50;
    line-height: 1.6;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 24px;
}

/* Section Card */
.section {
    background: #ffffff;
    border: 1px solid #e8eaed;
    border-radius: 8px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.section-header {
    background: #308e87;
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    font-size: 1.1rem;
}

/* News Item */
.news-list {
    padding: 0;
}

.news-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e8eaed;
    transition: all 0.2s;
    gap: 20px;
}

.news-item:last-child {
    border-bottom: none;
}

.news-item:hover {
    background: #f8f9fa;
}

.news-info {
    flex: 1;
    min-width: 0;
}

.news-title {
    font-weight: 600;
    font-size: 1.05rem;
    color: #2c3e50;
    margin-bottom: 8px;
    line-height: 1.4;
}

.news-link {
    font-size: 0.875rem;
    color: #308e87;
    text-decoration: none;
    word-break: break-all;
    display: block;
    margin-top: 6px;
}

.news-link:hover {
    color: #267872;
    text-decoration: underline;
}

.news-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.copy-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #308e87;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.copy-btn:hover {
    background: #267872;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(48, 142, 135, 0.3);
}

.copy-btn i {
    font-size: 14px;
}

/* Pagination */
.pagination-container {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e8eaed;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pagination-info {
    font-size: 0.875rem;
    color: #5f6368;
}

.pagination {
    display: flex;
    gap: 6px;
    list-style: none;
}

.page-item {
    display: inline-block;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    border: 1px solid #e8eaed;
    border-radius: 6px;
    background: #ffffff;
    color: #5f6368;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.page-link:hover {
    background: #f8f9fa;
    border-color: #308e87;
    color: #308e87;
}

.page-item.active .page-link {
    background: #308e87;
    color: #ffffff;
    border-color: #308e87;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #80868b;
}

.empty-state i {
    font-size: 3rem;
    color: #dadce0;
    margin-bottom: 16px;
}

.empty-state p {
    font-size: 1rem;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 16px;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .news-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .news-info {
        width: 100%;
    }

    .news-actions {
        width: 100%;
    }

    .copy-btn {
        width: 100%;
        justify-content: center;
    }

    .pagination-container {
        flex-direction: column;
        gap: 12px;
    }

    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }

    .page-link {
        min-width: 32px;
        height: 32px;
        font-size: 0.813rem;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 1.125rem;
    }

    .news-title {
        font-size: 0.938rem;
    }

    .news-link {
        font-size: 0.813rem;
    }
}
</style>
</head>
<body>

<?php include 'navigation.php'; ?>

<div class="container">
    <div class="section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-link"></i>
                সংবাদ লিংক তালিকা
            </div>
        </div>

        <div class="news-list">
            <?php if(!empty($newsList)): ?>
                <?php foreach($newsList as $news): 
                    $link = $domain . "news.php?id={$news['id']}" . $user_id_suffix;
                ?>
                    <div class="news-item">
                        <div class="news-info">
                            <div class="news-title"><?php echo htmlspecialchars($news['headline']); ?></div>
                            <a class="news-link" href="<?php echo $link; ?>" target="_blank" title="লিংকে যান"><?php echo $link; ?></a>
                        </div>
                        <div class="news-actions">
                            <button class="copy-btn" onclick="copyToClipboard('<?php echo addslashes($link); ?>', this)" title="লিংক কপি করুন">
                                <i class="fas fa-copy"></i>
                                <span>কপি</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>কোনো সংবাদ পাওয়া যায়নি</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($totalPages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    মোট <?php echo $total; ?> টি সংবাদ, পৃষ্ঠা <?php echo $page; ?> এর <?php echo $totalPages; ?>
                </div>
                <ul class="pagination">
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1" title="প্রথম পৃষ্ঠা">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>" title="পূর্ববর্তী">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for($i=$start; $i<=$end; $i++): 
                    ?>
                        <li class="page-item <?php echo $i==$page?'active':''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>" title="পরবর্তী">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?>" title="শেষ পৃষ্ঠা">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> <span>কপি হয়েছে!</span>';
        btn.style.background = '#28a745';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#308e87';
        }, 1500);
    }).catch(err => {
        alert('কপি করতে ব্যর্থ। অনুগ্রহ করে ম্যানুয়ালি কপি করুন।');
    });
}
</script>

</body>
</html>
