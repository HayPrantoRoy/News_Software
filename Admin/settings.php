<?php
ob_start();
include 'auth_check.php';
include 'connection.php';

// Check if user has permission to view this page
if (!$can_view) {
    header('Location: dashboard.php');
    exit;
}

// Create settings table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `serial_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_end_clean(); // Clear any output buffer
    header('Content-Type: application/json');
    
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Update order
    if ($action === 'update_order') {
        $ordersJson = isset($_POST['orders']) ? $_POST['orders'] : '';
        $orders = json_decode($ordersJson, true);
        
        if (!$orders || !is_array($orders)) {
            die(json_encode(['success' => false, 'error' => 'Invalid orders data', 'received' => $ordersJson]));
        }
        
        $success = true;
        $updated = 0;
        $errors = [];
        
        foreach ($orders as $item) {
            $categoryId = (int)$item['category_id'];
            $serialOrder = (int)$item['serial_order'];
            $isActive = isset($item['is_active']) ? (int)$item['is_active'] : 1;
            
            // Check if record exists
            $checkResult = $conn->query("SELECT id FROM settings WHERE category_id = $categoryId");
            
            if ($checkResult && $checkResult->num_rows > 0) {
                // Update existing record
                $sql = "UPDATE settings SET serial_order = $serialOrder, is_active = $isActive WHERE category_id = $categoryId";
            } else {
                // Insert new record
                $sql = "INSERT INTO settings (category_id, serial_order, is_active) VALUES ($categoryId, $serialOrder, $isActive)";
            }
            
            if ($conn->query($sql)) {
                $updated++;
            } else {
                $success = false;
                $errors[] = $conn->error;
            }
        }
        
        die(json_encode(['success' => $success, 'updated' => $updated, 'total' => count($orders), 'errors' => $errors]));
    }
    
    // Toggle active status
    if ($action === 'toggle_active') {
        $categoryId = (int)$_POST['category_id'];
        $isActive = (int)$_POST['is_active'];
        
        $checkResult = $conn->query("SELECT id, serial_order FROM settings WHERE category_id = $categoryId");
        
        if ($checkResult && $checkResult->num_rows > 0) {
            $sql = "UPDATE settings SET is_active = $isActive WHERE category_id = $categoryId";
        } else {
            $sql = "INSERT INTO settings (category_id, serial_order, is_active) VALUES ($categoryId, 999, $isActive)";
        }
        
        $result = $conn->query($sql);
        die(json_encode(['success' => $result ? true : false, 'error' => $result ? null : $conn->error]));
    }
    
    // Unknown action
    die(json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]));
}

// Fetch all categories with their settings
$sql = "SELECT c.id, c.name, c.slug, c.is_active as cat_active,
        COALESCE(s.serial_order, 999) as serial_order,
        COALESCE(s.is_active, 1) as display_active
        FROM category c
        LEFT JOIN settings s ON c.id = s.category_id
        WHERE c.is_active = 1
        ORDER BY COALESCE(s.serial_order, 999) ASC, c.id ASC";
$result = $conn->query($sql);
$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .settings-container {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .page-header h1 {
            font-size: 24px;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-header h1 i {
            color: #6366f1;
        }
        
        .save-btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .save-btn:hover {
            background: #4f46e5;
            transform: translateY(-1px);
        }
        
        .save-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        .category-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .category-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            transition: all 0.2s;
        }
        
        .category-item:last-child {
            border-bottom: none;
        }
        
        .category-item:hover {
            background: #f9fafb;
        }
        
        .category-item.dragging {
            opacity: 0.5;
            background: #e5e7eb;
        }
        
        .drag-handle {
            cursor: grab;
            padding: 10px;
            color: #9ca3af;
            font-size: 18px;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .category-info {
            flex: 1;
            margin-left: 15px;
        }
        
        .category-name {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .category-slug {
            font-size: 12px;
            color: #6b7280;
        }
        
        .serial-number {
            width: 60px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 15px;
            cursor: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
            position: relative;
            z-index: 10;
        }
        
        .serial-number:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .serial-number:hover {
            border-color: #6366f1;
        }
        
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: 0.3s;
            border-radius: 26px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background-color: #10b981;
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-active {
            background: #d1fae5;
            color: #059669;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .info-box i {
            color: #3b82f6;
            font-size: 18px;
            margin-top: 2px;
        }
        
        .info-box p {
            color: #1e40af;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="settings-container">
        <div class="alert alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span>Settings saved successfully!</span>
        </div>
        
        <div class="alert alert-error" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span>Error saving settings. Please try again.</span>
        </div>
        
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Category Display Settings</h1>
            <button class="save-btn" id="saveBtn" onclick="saveOrder()">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Drag and drop categories to change their display order on the homepage, or enter a serial number. 
               Lower numbers appear first. Toggle the switch to show/hide categories from the homepage.</p>
        </div>
        
        <div class="category-list" id="categoryList">
            <?php foreach ($categories as $index => $cat): ?>
            <div class="category-item" draggable="true" data-id="<?= $cat['id'] ?>">
                <div class="drag-handle">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <input type="number" class="serial-number" 
                       value="<?= $cat['serial_order'] ?>" 
                       data-category-id="<?= $cat['id'] ?>"
                       min="1" max="999"
                       onclick="event.stopPropagation()"
                       ondragstart="event.preventDefault(); event.stopPropagation(); return false;">
                <div class="category-info">
                    <div class="category-name">
                        <?= htmlspecialchars($cat['name']) ?>
                        <span class="status-badge <?= $cat['display_active'] ? 'status-active' : 'status-inactive' ?>">
                            <?= $cat['display_active'] ? 'Visible' : 'Hidden' ?>
                        </span>
                    </div>
                    <div class="category-slug">/category/<?= htmlspecialchars($cat['slug']) ?></div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" 
                           <?= $cat['display_active'] ? 'checked' : '' ?>
                           data-category-id="<?= $cat['id'] ?>"
                           onchange="toggleActive(this)">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        // Drag and drop functionality
        const categoryList = document.getElementById('categoryList');
        let draggedItem = null;
        
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                draggedItem = this;
                this.classList.add('dragging');
            });
            
            item.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
                updateSerialNumbers();
            });
            
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = getDragAfterElement(categoryList, e.clientY);
                if (afterElement == null) {
                    categoryList.appendChild(draggedItem);
                } else {
                    categoryList.insertBefore(draggedItem, afterElement);
                }
            });
        });
        
        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.category-item:not(.dragging)')];
            
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
        
        function updateSerialNumbers() {
            document.querySelectorAll('.category-item').forEach((item, index) => {
                item.querySelector('.serial-number').value = index + 1;
            });
        }
        
        // Save order
        function saveOrder() {
            const btn = document.getElementById('saveBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            const orders = [];
            document.querySelectorAll('.category-item').forEach(item => {
                const categoryId = item.dataset.id;
                const serialOrder = item.querySelector('.serial-number').value;
                const isActive = item.querySelector('.toggle-switch input').checked ? 1 : 0;
                
                orders.push({
                    category_id: categoryId,
                    serial_order: serialOrder,
                    is_active: isActive
                });
            });
            
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=update_order&orders=' + encodeURIComponent(JSON.stringify(orders))
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                
                if (data.success) {
                    showAlert('success');
                    // Reload page to reflect changes
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error');
                    console.error('Error:', data.error || data.errors);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                showAlert('error');
            });
        }
        
        // Toggle active status
        function toggleActive(checkbox) {
            const categoryId = checkbox.dataset.categoryId;
            const isActive = checkbox.checked ? 1 : 0;
            const badge = checkbox.closest('.category-item').querySelector('.status-badge');
            
            badge.className = 'status-badge ' + (isActive ? 'status-active' : 'status-inactive');
            badge.textContent = isActive ? 'Visible' : 'Hidden';
            
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=toggle_active&category_id=' + categoryId + '&is_active=' + isActive
            });
        }
        
        // Show alert
        function showAlert(type) {
            const alert = document.getElementById(type + 'Alert');
            alert.style.display = 'flex';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }
        
        // Serial number change handler
        document.querySelectorAll('.serial-number').forEach(input => {
            // Prevent drag when clicking on input
            input.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                this.closest('.category-item').setAttribute('draggable', 'false');
            });
            
            input.addEventListener('mouseup', function(e) {
                this.closest('.category-item').setAttribute('draggable', 'true');
            });
            
            input.addEventListener('focus', function(e) {
                this.closest('.category-item').setAttribute('draggable', 'false');
            });
            
            input.addEventListener('blur', function(e) {
                this.closest('.category-item').setAttribute('draggable', 'true');
            });
            
            input.addEventListener('change', function() {
                // Sort items by serial number
                const items = [...document.querySelectorAll('.category-item')];
                items.sort((a, b) => {
                    const aVal = parseInt(a.querySelector('.serial-number').value) || 999;
                    const bVal = parseInt(b.querySelector('.serial-number').value) || 999;
                    return aVal - bVal;
                });
                
                items.forEach(item => categoryList.appendChild(item));
            });
        });
    </script>
</body>
</html>
