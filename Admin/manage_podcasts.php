<?php
include 'auth_check.php';
include '../connection.php';

// Check if user has permission to view this page
if (!$can_view) {
    header("Location: dashboard.php");
    exit();
}

// Handle Add/Edit/Delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $subtitle = $_POST['subtitle'] ?? '';
        $youtube_link = $_POST['youtube_link'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle thumbnail upload
        $thumbnail = $_POST['existing_thumbnail'] ?? 'default.jpg';
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['thumbnail']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = 'podcast_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = 'img/' . $newname;
                
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                    $thumbnail = $newname;
                }
            }
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO podcasts (title, subtitle, thumbnail, youtube_link, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $subtitle, $thumbnail, $youtube_link, $is_active);
            $message = "Podcast added successfully!";
            
            if ($stmt->execute()) {
                $_SESSION['success'] = $message;
            } else {
                $_SESSION['error'] = "Operation failed: " . $conn->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE podcasts SET title=?, subtitle=?, thumbnail=?, youtube_link=?, is_active=? WHERE id=?");
            $stmt->bind_param("ssssii", $title, $subtitle, $thumbnail, $youtube_link, $is_active, $id);
            $message = "Podcast updated successfully!";
            
            if ($stmt->execute()) {
                $_SESSION['success'] = $message;
            } else {
                $_SESSION['error'] = "Operation failed: " . $conn->error;
            }
            $stmt->close();
        }
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM podcasts WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Podcast deleted successfully!";
        }
        $stmt->close();
    }
    
    header("Location: manage_podcasts.php");
    exit();
}

// Pagination settings
$items_per_page = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as total FROM podcasts");
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch podcasts for current page (latest first)
$podcasts = [];
$result = $conn->query("SELECT * FROM podcasts ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $podcasts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Podcasts - Hindus News</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: #308e87;
            color: white;
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(48, 142, 135, 0.2);
        }
        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }
        
        /* Form Section */
        .form-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-section h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2d3748;
            padding-bottom: 12px;
            border-bottom: 2px solid #308e87;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group input[type="file"],
        .form-group textarea {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #308e87;
            color: white;
        }
        .btn-primary:hover {
            background: #267872;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(48, 142, 135, 0.4);
        }
        .btn-secondary {
            background: #718096;
            color: white;
        }
        .btn-secondary:hover {
            background: #4a5568;
        }
        .btn-danger {
            background: #f56565;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }
        .btn-warning {
            background: #ed8936;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }
        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }
        
        /* Table Section */
        .table-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow-x: auto;
        }
        .table-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #f7fafc;
        }
        th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #2d3748;
        }
        tr:hover {
            background: #f7fafc;
        }
        .podcast-thumb {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }
        .badge-inactive {
            background: #e2e8f0;
            color: #4a5568;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .thumbnail-preview {
            max-width: 200px;
            max-height: 120px;
            margin-top: 10px;
            border-radius: 6px;
            display: none;
        }
        .thumbnail-preview.show {
            display: block;
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            padding: 20px 0;
        }
        .pagination a,
        .pagination span {
            padding: 10px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            text-decoration: none;
            color: #4a5568;
            font-weight: 600;
            transition: all 0.3s;
        }
        .pagination a:hover {
            background: #308e87;
            color: white;
            border-color: #308e87;
        }
        .pagination .active {
            background: #308e87;
            color: white;
            border-color: #308e87;
        }
        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .page-header { padding: 20px; }
            .page-header h1 { font-size: 22px; }
            .form-section, .table-section { padding: 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .table-section { overflow-x: scroll; }
            table { min-width: 800px; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-podcast"></i> Manage Podcasts</h1>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div style="background: #ebf4ff; border-left: 4px solid #4299e1; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <p style="margin: 0; color: #2c5282; font-size: 14px; line-height: 1.6;">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> Podcasts will be displayed with latest first on the website.
            </p>
            <p style="margin: 10px 0 0 0; color: #2c5282; font-size: 14px;">
                <i class="fas fa-podcast"></i> <strong>Total Podcasts:</strong> <strong><?= $total_items; ?></strong> | Last 8 uploaded podcasts will be shown in the podcast section.
            </p>
        </div>

        <!-- Add/Edit Form -->
        <div class="form-section">
            <h2><i class="fas fa-plus-circle"></i> <span id="formTitle">Add New Podcast</span></h2>
            <form method="POST" enctype="multipart/form-data" id="podcastForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="podcastId">
                <input type="hidden" name="existing_thumbnail" id="existingThumbnail">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Title <span style="color: #f56565;">*</span></label>
                        <input type="text" name="title" id="podcastTitle" required placeholder="Enter podcast title">
                    </div>
                    
                    <div class="form-group">
                        <label>YouTube Link <span style="color: #f56565;">*</span></label>
                        <input type="url" name="youtube_link" id="youtubeLink" required placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Subtitle</label>
                        <textarea name="subtitle" id="podcastSubtitle" placeholder="Short description (optional)"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Thumbnail Image</label>
                        <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" onchange="handleImageUpload(event, 'thumbnailPreview')">
                        <small style="color: #718096; margin-top: 5px;">Leave empty to use default image. Image will be auto-resized to 80-100KB</small>
                        <img id="thumbnailPreview" class="thumbnail-preview" src="" alt="Preview">
                    </div>
                </div>
                
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="is_active" id="podcastActive" checked>
                    <label for="podcastActive" style="margin: 0; font-weight: 500;">Active (Show on website)</label>
                </div>
                
                <div class="form-actions" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <span id="btnText">Add Podcast</span>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Podcasts Table -->
        <div class="table-section">
            <h2><i class="fas fa-list"></i> All Podcasts (<?= $total_items; ?>)</h2>
            <?php if (empty($podcasts)): ?>
                <p style="text-align: center; padding: 40px 0; color: #718096;">
                    <i class="fas fa-podcast" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                    No podcasts found. Add your first podcast using the form above!
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($podcasts as $index => $podcast): ?>
                        <tr>
                            <td><strong><?= $offset + $index + 1; ?></strong></td>
                            <td>
                                <img src="img/<?= htmlspecialchars($podcast['thumbnail']); ?>" class="podcast-thumb" alt="">
                            </td>
                            <td><strong><?= htmlspecialchars($podcast['title']); ?></strong></td>
                            <td><?= htmlspecialchars(mb_substr($podcast['subtitle'] ?? '', 0, 50)); ?><?= mb_strlen($podcast['subtitle'] ?? '') > 50 ? '...' : ''; ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($podcast['youtube_link']); ?>" target="_blank" style="color: #667eea;">
                                    <i class="fas fa-external-link-alt"></i> View
                                </a>
                            </td>
                            <td>
                                <?php if ($podcast['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($podcast['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($can_edit): ?>
                                    <button class="btn btn-warning" onclick='editPodcast(<?= json_encode($podcast); ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($can_delete): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this podcast?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $podcast['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if (!$can_edit && !$can_delete): ?>
                                    <span style="color:#999;">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1"><i class="fas fa-angle-double-left"></i></a>
                        <a href="?page=<?= $page - 1 ?>"><i class="fas fa-angle-left"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-angle-double-left"></i></span>
                        <span class="disabled"><i class="fas fa-angle-left"></i></span>
                    <?php endif; ?>
                    
                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <?php if ($p == $page): ?>
                            <span class="active"><?= $p ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $p ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>"><i class="fas fa-angle-right"></i></a>
                        <a href="?page=<?= $total_pages ?>"><i class="fas fa-angle-double-right"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-angle-right"></i></span>
                        <span class="disabled"><i class="fas fa-angle-double-right"></i></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Image resizing function to compress images to 80-100KB
        async function resizeImage(file, maxSizeKB = 100, minSizeKB = 80) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        const maxDimension = 1920;
                        if (width > height && width > maxDimension) {
                            height = (height * maxDimension) / width;
                            width = maxDimension;
                        } else if (height > maxDimension) {
                            width = (width * maxDimension) / height;
                            height = maxDimension;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        let quality = 0.9;
                        let blob = null;
                        
                        function tryCompress(q) {
                            return new Promise((res) => {
                                canvas.toBlob((b) => {
                                    res(b);
                                }, 'image/jpeg', q);
                            });
                        }
                        
                        async function findOptimalQuality() {
                            let minQ = 0.1, maxQ = 0.95;
                            let bestBlob = null;
                            
                            for (let i = 0; i < 10; i++) {
                                quality = (minQ + maxQ) / 2;
                                blob = await tryCompress(quality);
                                const sizeKB = blob.size / 1024;
                                
                                if (sizeKB >= minSizeKB && sizeKB <= maxSizeKB) {
                                    bestBlob = blob;
                                    break;
                                } else if (sizeKB > maxSizeKB) {
                                    maxQ = quality;
                                } else {
                                    minQ = quality;
                                }
                                bestBlob = blob;
                            }
                            
                            return bestBlob || blob;
                        }
                        
                        findOptimalQuality().then(finalBlob => {
                            const resizedFile = new File([finalBlob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(resizedFile);
                        });
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Handle image upload with resize
        async function handleImageUpload(event, previewId) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.getElementById(previewId);
                preview.src = '';
                preview.classList.remove('show');
                
                const resizedFile = await resizeImage(file);
                const sizeKB = (resizedFile.size / 1024).toFixed(2);
                console.log('Resized image size:', sizeKB, 'KB');
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                event.target.files = dataTransfer.files;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(resizedFile);
            }
        }
        
        // Edit podcast - populate form with existing data
        function editPodcast(podcast) {
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            document.getElementById('formAction').value = 'edit';
            document.getElementById('formTitle').textContent = 'Edit Podcast';
            document.getElementById('btnText').textContent = 'Update Podcast';
            
            document.getElementById('podcastId').value = podcast.id;
            document.getElementById('podcastTitle').value = podcast.title;
            document.getElementById('podcastSubtitle').value = podcast.subtitle || '';
            document.getElementById('youtubeLink').value = podcast.youtube_link;
            document.getElementById('existingThumbnail').value = podcast.thumbnail;
            document.getElementById('podcastActive').checked = podcast.is_active == 1;
            
            const preview = document.getElementById('thumbnailPreview');
            preview.src = 'img/' + podcast.thumbnail;
            preview.classList.add('show');
        }
        
        // Reset form to add mode
        function resetForm() {
            document.getElementById('podcastForm').reset();
            document.getElementById('formAction').value = 'add';
            document.getElementById('formTitle').textContent = 'Add New Podcast';
            document.getElementById('btnText').textContent = 'Add Podcast';
            document.getElementById('podcastId').value = '';
            document.getElementById('existingThumbnail').value = '';
            document.getElementById('thumbnailPreview').classList.remove('show');
        }
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>
