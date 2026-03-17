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
        $display_order = $_POST['display_order'] ?? 0;
        $status = isset($_POST['status']) ? 1 : 0;
        $link = $_POST['link'] ?? '';
        
        // Handle image upload
        $image = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Create opinions directory if it doesn't exist
                $opinions_dir = 'img/opinions/';
                if (!file_exists($opinions_dir)) {
                    mkdir($opinions_dir, 0777, true);
                }
                
                $newname = 'opinion_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = $opinions_dir . $newname;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'opinions/' . $newname;
                    
                    // Delete old image if editing
                    if ($action === 'edit' && !empty($_POST['existing_image'])) {
                        $old_image = 'img/' . $_POST['existing_image'];
                        if (file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }
                }
            }
        }
        
        if ($action === 'add') {
            if (empty($image)) {
                $_SESSION['error'] = "Please upload an image!";
            } else {
                $stmt = $conn->prepare("INSERT INTO opinions (image, link, display_order, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssii", $image, $link, $display_order, $status);
                $message = "Opinion added successfully!";
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = $message;
                } else {
                    $_SESSION['error'] = "Operation failed: " . $conn->error;
                }
                $stmt->close();
            }
        } else {
            // If no new image uploaded, keep existing
            if (empty($image)) {
                $image = $_POST['existing_image'];
            }
            
            $stmt = $conn->prepare("UPDATE opinions SET image=?, link=?, display_order=?, status=? WHERE id=?");
            $stmt->bind_param("ssiii", $image, $link, $display_order, $status, $id);
            $message = "Opinion updated successfully!";
            
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
        
        // Get image path before deleting
        $stmt = $conn->prepare("SELECT image FROM opinions WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = 'img/' . $row['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM opinions WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Opinion deleted successfully!";
        }
        $stmt->close();
    }
    
    header("Location: manage_opinions.php");
    exit();
}

// Pagination settings
$items_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as total FROM opinions");
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch opinions for current page
$opinions = [];
$result = $conn->query("SELECT * FROM opinions ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $opinions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Opinions </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #308e87 0%, #267872 100%);
            color: white;
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(48, 142, 135, 0.3);
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
        .form-group input[type="number"],
        .form-group input[type="url"],
        .form-group input[type="file"] {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        .form-group input:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
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
        .btn-success {
            background: #48bb78;
            color: white;
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
        .opinion-thumb {
            width: 120px;
            height: auto;
            max-height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
            border: 2px solid #e2e8f0;
        }
        .image-preview.show {
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
        
        .info-box {
            background: #ebf4ff;
            border-left: 4px solid #4299e1;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 0;
            color: #2c5282;
            font-size: 14px;
            line-height: 1.6;
        }
        .info-box strong {
            color: #1a365d;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .page-header { padding: 20px; }
            .page-header h1 { font-size: 22px; }
            .form-section, .table-section { padding: 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .table-section { overflow-x: scroll; }
            table { min-width: 700px; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-comment-dots"></i> Manage Opinions</h1>
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
        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> <strong>Note:</strong> Upload photocard images for opinion section. Images will be displayed in order based on "Display Order" field. Lower numbers appear first.</p>
            <p style="margin-top: 10px;"><i class="fas fa-comment-dots"></i> <strong>Total Opinions:</strong> <strong><?= count($opinions); ?></strong> | Last 10 uploaded opinions will be shown in the comments section.</p>
        </div>

        <!-- Add/Edit Form -->
        <div class="form-section">
            <h2><i class="fas fa-plus-circle"></i> <span id="formTitle">Add New Opinion</span></h2>
            <form method="POST" enctype="multipart/form-data" id="opinionForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="opinionId">
                <input type="hidden" name="existing_image" id="existingImage">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Opinion Photocard Image <span style="color: #f56565;">*</span></label>
                        <input type="file" name="image" id="imageInput" accept="image/*" onchange="handleImageUpload(event, 'imagePreview')" required>
                        <small style="color: #718096; margin-top: 5px;">Recommended: 400px width, JPG/PNG format. Image will be auto-resized to 80-100KB</small>
                        <img id="imagePreview" class="image-preview" src="" alt="Preview">
                    </div>
                    
                    <div class="form-group">
                        <label>Link URL (Optional)</label>
                        <input type="url" name="link" id="opinionLink" placeholder="https://example.com">
                        <small style="color: #718096; margin-top: 5px;">Add a URL if this opinion should link to something</small>
                    </div>
                </div>
                
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="status" id="opinionStatus" checked>
                    <label for="opinionStatus" style="margin: 0; font-weight: 500;">Active (Show on website)</label>
                </div>
                
                <div class="form-actions" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <span id="btnText">Add Opinion</span>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Opinions Table -->
        <div class="table-section">
            <h2><i class="fas fa-list"></i> All Opinions (<?= count($opinions); ?>)</h2>
            <?php if (empty($opinions)): ?>
                <p style="text-align: center; padding: 40px 0; color: #718096;">
                    <i class="fas fa-comment-dots" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                    No opinions found. Add your first opinion using the form above!
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Preview</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opinions as $index => $opinion): ?>
                        <tr>
                            <td><strong><?= $index + 1; ?></strong></td>
                            <td>
                                <img src="img/<?= htmlspecialchars($opinion['image']); ?>" class="opinion-thumb" alt="Opinion">
                            </td>
                            <td>
                                <?php if (!empty($opinion['link'])): ?>
                                    <a href="<?= htmlspecialchars($opinion['link']); ?>" target="_blank" style="color: #667eea;">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                <?php else: ?>
                                    <span style="color: #a0aec0;">No link</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($opinion['status']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($opinion['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($can_edit): ?>
                                    <button class="btn btn-warning" onclick='editOpinion(<?= json_encode($opinion); ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($can_delete): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this opinion?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $opinion['id']; ?>">
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
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
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
                        
                        // Initial resize to reasonable dimensions
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
                        
                        // Binary search for optimal quality
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
                // Show loading
                const preview = document.getElementById(previewId);
                preview.src = '';
                preview.classList.remove('show');
                
                // Resize image
                const resizedFile = await resizeImage(file);
                const sizeKB = (resizedFile.size / 1024).toFixed(2);
                console.log('Resized image size:', sizeKB, 'KB');
                
                // Create new FileList with resized file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                event.target.files = dataTransfer.files;
                
                // Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(resizedFile);
            }
        }
        
        // Edit opinion - populate form with existing data
        function editOpinion(opinion) {
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Change form mode to edit
            document.getElementById('formAction').value = 'edit';
            document.getElementById('formTitle').textContent = 'Edit Opinion';
            document.getElementById('btnText').textContent = 'Update Opinion';
            
            // Populate form fields
            document.getElementById('opinionId').value = opinion.id;
            document.getElementById('existingImage').value = opinion.image;
            document.getElementById('opinionLink').value = opinion.link || '';
            document.getElementById('opinionStatus').checked = opinion.status == 1;
            
            // Show current image
            const preview = document.getElementById('imagePreview');
            preview.src = 'img/' + opinion.image;
            preview.classList.add('show');
            
            // Make image input optional for edit
            document.getElementById('imageInput').removeAttribute('required');
        }
        
        // Reset form to add mode
        function resetForm() {
            document.getElementById('opinionForm').reset();
            document.getElementById('formAction').value = 'add';
            document.getElementById('formTitle').textContent = 'Add New Opinion';
            document.getElementById('btnText').textContent = 'Add Opinion';
            document.getElementById('opinionId').value = '';
            document.getElementById('existingImage').value = '';
            document.getElementById('opinionLink').value = '';
            document.getElementById('imagePreview').classList.remove('show');
            document.getElementById('imageInput').setAttribute('required', 'required');
        }
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>
