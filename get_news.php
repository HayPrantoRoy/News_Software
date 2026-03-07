<?php
include 'connection.php';
date_default_timezone_set('Asia/Dhaka');

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    $news_query = "SELECT n.*, c.name as category_name 
                   FROM news n 
                   JOIN category c ON n.category_id = c.id 
                   WHERE n.category_id = $category_id AND n.is_active = 1 
                   ORDER BY n.created_at DESC";
    $news_result = mysqli_query($conn, $news_query);
    
    if ($news_result && mysqli_num_rows($news_result) > 0) {
        $featured_news = mysqli_fetch_assoc($news_result);
        mysqli_data_seek($news_result, 0);
?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="position-relative rounded overflow-hidden">
                    <img src="img/<?php echo htmlspecialchars($featured_news['image_url']); ?>" 
                         class="img-zoomin img-fluid rounded w-100" alt="<?php echo htmlspecialchars($featured_news['headline']); ?>">
                    <div class="position-absolute text-white px-4 py-2 bg-primary rounded" 
                         style="top: 20px; right: 20px;">
                        <?php echo htmlspecialchars($featured_news['category_name']); ?>
                    </div>
                </div>
                <div class="my-4">
                    <a href="#" class="h4"><?php echo htmlspecialchars($featured_news['headline']); ?></a>
                </div>
                <p class="my-4">
                    <?php echo htmlspecialchars(substr($featured_news['news_1'], 0, 200)) . '...'; ?>
                </p>
            </div>
            
            <div class="col-lg-4">
                <div class="row g-4">
                    <?php 
                    $count = 0;
                    while($news = mysqli_fetch_assoc($news_result)): 
                        if ($count >= 5) break;
                        $count++;
                    ?>
                    <div class="col-12">
                        <div class="row g-4 align-items-center">
                            <div class="col-5">
                                <div class="overflow-hidden rounded">
                                    <img src="img/<?php echo htmlspecialchars($news['image_url']); ?>" 
                                         class="img-zoomin img-fluid rounded w-100" alt="<?php echo htmlspecialchars($news['headline']); ?>">
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="features-content d-flex flex-column">
                                    <p class="text-uppercase mb-2"><?php echo htmlspecialchars($news['category_name']); ?></p>
                                    <a href="#" class="h6"><?php echo htmlspecialchars(substr($news['headline'], 0, 50)) . '...'; ?></a>
                                    <small class="text-body d-block">
                                        <i class="fas fa-calendar-alt me-1"></i> 
                                        <?php 
                                        if ($news['created_at'] && $news['created_at'] != '0000-00-00 00:00:00') {
                                            echo date('M d, Y', strtotime($news['created_at']));
                                        } else {
                                            echo 'No date';
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
<?php 
    } else {
?>
        <div class="row g-4">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4>No news available</h4>
                    <p>There are no active news articles in this category yet.</p>
                </div>
            </div>
        </div>
<?php 
    }
}
mysqli_close($conn);
?>