<?php
include 'auth_check.php';
include 'connection.php';

// Check if user has permission to view this page
if (!$can_view) {
    header("Location: dashboard.php");
    exit();
}

// Handle Update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $id = $_POST['id'] ?? 1;
        $news_portal_name = $_POST['news_portal_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $editor_in_chief = $_POST['editor_in_chief'] ?? '';
        $media_info = $_POST['media_info'] ?? '';
        $privacy_policy = $_POST['privacy_policy'] ?? '';
        $about_us = $_POST['about_us'] ?? '';
        $comment_policy = $_POST['comment_policy'] ?? '';
        $advertisement_policy = $_POST['advertisement_policy'] ?? '';
        $terms = $_POST['terms'] ?? '';
        $advertisement_list = $_POST['advertisement_list'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $youtube = $_POST['youtube'] ?? '';
        $whatsapp = $_POST['whatsapp'] ?? '';
        $twitter = $_POST['twitter'] ?? '';
        $tiktok = $_POST['tiktok'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $mobile_number = $_POST['mobile_number'] ?? '';
        $email = $_POST['email'] ?? '';
        
        // Handle logo upload
        $image = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = 'img/' . $newname;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $upload_path;
                }
            }
        }
        
        // Check if record exists
        $check = $conn->query("SELECT id FROM basic_info LIMIT 1");
        if ($check->num_rows > 0) {
            // Update existing record
            $stmt = $conn->prepare("UPDATE basic_info SET 
                news_portal_name=?, image=?, description=?, editor_in_chief=?, media_info=?,
                privacy_policy=?, about_us=?, comment_policy=?, advertisement_policy=?, terms=?,
                advertisement_list=?, facebook=?, youtube=?, whatsapp=?, twitter=?, tiktok=?,
                instagram=?, mobile_number=?, email=? WHERE id=?");
            $stmt->bind_param("sssssssssssssssssssi", 
                $news_portal_name, $image, $description, $editor_in_chief, $media_info,
                $privacy_policy, $about_us, $comment_policy, $advertisement_policy, $terms,
                $advertisement_list, $facebook, $youtube, $whatsapp, $twitter, $tiktok,
                $instagram, $mobile_number, $email, $id);
        } else {
            // Insert first record
            $stmt = $conn->prepare("INSERT INTO basic_info 
                (news_portal_name, image, description, editor_in_chief, media_info,
                privacy_policy, about_us, comment_policy, advertisement_policy, terms,
                advertisement_list, facebook, youtube, whatsapp, twitter, tiktok,
                instagram, mobile_number, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssssssss", 
                $news_portal_name, $image, $description, $editor_in_chief, $media_info,
                $privacy_policy, $about_us, $comment_policy, $advertisement_policy, $terms,
                $advertisement_list, $facebook, $youtube, $whatsapp, $twitter, $tiktok,
                $instagram, $mobile_number, $email);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Portal information updated successfully!";
        } else {
            $_SESSION['error'] = "Update failed: " . $conn->error;
        }
        $stmt->close();
    }
    
    header("Location: news_portal_info.php");
    exit();
}

// Fetch existing data
$info = null;
$result = $conn->query("SELECT * FROM basic_info LIMIT 1");
if ($result && $result->num_rows > 0) {
    $info = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Portal Information</title>
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
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-grid-full {
            grid-template-columns: 1fr;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
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
            min-height: 120px;
        }
        .form-group textarea.large {
            min-height: 200px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
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
        
        /* Image Preview */
        .image-preview {
            max-width: 200px;
            max-height: 120px;
            margin-top: 10px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 5px;
            background: #f7fafc;
        }
        .current-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            padding: 10px;
            background: #f7fafc;
            border-radius: 8px;
        }
        .current-logo img {
            max-width: 100px;
            max-height: 60px;
            border-radius: 6px;
        }
        .current-logo span {
            color: #4a5568;
            font-size: 13px;
        }
        
        /* Section Divider */
        .section-divider {
            margin: 30px 0;
            border: none;
            border-top: 2px dashed #e2e8f0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .page-header { padding: 20px; }
            .page-header h1 { font-size: 22px; }
            .form-section { padding: 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> News Portal Information</h1>
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

    <!-- Main Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $info['id'] ?? 1 ?>">

        <!-- Basic Information Section -->
        <div class="form-section">
            <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="news_portal_name">News Portal Name</label>
                    <input type="text" id="news_portal_name" name="news_portal_name" 
                           value="<?= htmlspecialchars($info['news_portal_name'] ?? '') ?>" 
                           placeholder="Enter portal name">
                </div>
                <div class="form-group">
                    <label for="editor_in_chief">Editor in Chief</label>
                    <input type="text" id="editor_in_chief" name="editor_in_chief" 
                           value="<?= htmlspecialchars($info['editor_in_chief'] ?? '') ?>" 
                           placeholder="Enter editor name">
                </div>
                <div class="form-group">
                    <label for="image">Logo Image</label>
                    <?php if (!empty($info['image'])): 
                        $logoPath = $info['image'];
                        $displayPath = $logoPath;
                        // Try multiple possible locations
                        if (file_exists($logoPath)) {
                            $displayPath = $logoPath;
                        } elseif (file_exists('../' . $logoPath)) {
                            $displayPath = '../' . $logoPath;
                        } elseif (file_exists('img/' . basename($logoPath))) {
                            $displayPath = 'img/' . basename($logoPath);
                        } elseif (file_exists('../img/' . basename($logoPath))) {
                            $displayPath = '../img/' . basename($logoPath);
                        }
                    ?>
                        <div class="current-logo" style="margin-bottom: 10px;">
                            <img src="<?= htmlspecialchars($displayPath) ?>" alt="Current Logo" style="max-width: 200px; max-height: 80px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span style="display:none; color:#e53e3e; font-size:13px;">Logo not found (<?= htmlspecialchars($logoPath) ?>)</span>
                            <p style="color: #718096; font-size: 13px; margin-top: 5px;">Current logo: <?= htmlspecialchars(basename($logoPath)) ?></p>
                        </div>
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($logoPath) ?>">
                    <?php else: ?>
                        <input type="hidden" name="existing_image" value="">
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="media_info">Media Info</label>
                    <input type="text" id="media_info" name="media_info" 
                           value="<?= htmlspecialchars($info['media_info'] ?? '') ?>" 
                           placeholder="e.g., Media Limited">
                </div>
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Enter portal description"><?= htmlspecialchars($info['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="form-section">
            <h2><i class="fas fa-address-book"></i> Contact Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile_number" name="mobile_number" 
                           value="<?= htmlspecialchars($info['mobile_number'] ?? '') ?>" 
                           placeholder="+880 1XXX-XXXXXX">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($info['email'] ?? '') ?>" 
                           placeholder="news@example.com">
                </div>
            </div>
        </div>

        <!-- Social Media Section -->
        <div class="form-section">
            <h2><i class="fas fa-share-alt"></i> Social Media Links</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="facebook"><i class="fab fa-facebook" style="color:#1877f2"></i> Facebook</label>
                    <input type="url" id="facebook" name="facebook" 
                           value="<?= htmlspecialchars($info['facebook'] ?? '') ?>" 
                           placeholder="https://facebook.com/...">
                </div>
                <div class="form-group">
                    <label for="youtube"><i class="fab fa-youtube" style="color:#ff0000"></i> YouTube</label>
                    <input type="url" id="youtube" name="youtube" 
                           value="<?= htmlspecialchars($info['youtube'] ?? '') ?>" 
                           placeholder="https://youtube.com/...">
                </div>
                <div class="form-group">
                    <label for="whatsapp"><i class="fab fa-whatsapp" style="color:#25D366"></i> WhatsApp</label>
                    <input type="url" id="whatsapp" name="whatsapp" 
                           value="<?= htmlspecialchars($info['whatsapp'] ?? '') ?>" 
                           placeholder="https://wa.me/...">
                </div>
                <div class="form-group">
                    <label for="twitter"><i class="fab fa-twitter" style="color:#1DA1F2"></i> Twitter/X</label>
                    <input type="url" id="twitter" name="twitter" 
                           value="<?= htmlspecialchars($info['twitter'] ?? '') ?>" 
                           placeholder="https://twitter.com/...">
                </div>
                <div class="form-group">
                    <label for="tiktok"><i class="fab fa-tiktok" style="color:#000"></i> TikTok</label>
                    <input type="url" id="tiktok" name="tiktok" 
                           value="<?= htmlspecialchars($info['tiktok'] ?? '') ?>" 
                           placeholder="https://tiktok.com/...">
                </div>
                <div class="form-group">
                    <label for="instagram"><i class="fab fa-instagram" style="color:#E4405F"></i> Instagram</label>
                    <input type="url" id="instagram" name="instagram" 
                           value="<?= htmlspecialchars($info['instagram'] ?? '') ?>" 
                           placeholder="https://instagram.com/...">
                </div>
            </div>
        </div>

        <!-- Policies Section -->
        <div class="form-section">
            <h2><i class="fas fa-file-contract"></i> Policies & Information</h2>
            <div class="form-grid form-grid-full">
                <div class="form-group">
                    <label for="about_us">About Us</label>
                    <textarea id="about_us" name="about_us" class="large" 
                              placeholder="About your news portal..."><?= htmlspecialchars($info['about_us'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="privacy_policy">Privacy Policy</label>
                    <textarea id="privacy_policy" name="privacy_policy" class="large" 
                              placeholder="Privacy policy content..."><?= htmlspecialchars($info['privacy_policy'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="terms">Terms & Conditions</label>
                    <textarea id="terms" name="terms" class="large" 
                              placeholder="Terms and conditions..."><?= htmlspecialchars($info['terms'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="comment_policy">Comment Policy</label>
                    <textarea id="comment_policy" name="comment_policy" class="large" 
                              placeholder="Comment policy content..."><?= htmlspecialchars($info['comment_policy'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="advertisement_policy">Advertisement Policy</label>
                    <textarea id="advertisement_policy" name="advertisement_policy" class="large" 
                              placeholder="Advertisement policy content..."><?= htmlspecialchars($info['advertisement_policy'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="advertisement_list">Advertisement List / Pricing</label>
                    <textarea id="advertisement_list" name="advertisement_list" class="large" 
                              placeholder="Advertisement pricing and packages..."><?= htmlspecialchars($info['advertisement_list'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-section">
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </form>

    <script>
        // Image preview on file select
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.current-logo img');
                    if (!preview) {
                        const container = document.createElement('div');
                        container.className = 'current-logo';
                        container.innerHTML = '<img src="" alt="Preview"><span>New logo preview</span>';
                        document.getElementById('image').parentNode.insertBefore(container, document.getElementById('image'));
                        preview = container.querySelector('img');
                    }
                    preview.src = e.target.result;
                    preview.parentNode.querySelector('span').textContent = 'New logo preview';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>