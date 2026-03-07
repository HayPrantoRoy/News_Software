<?php
/**
 * Database Setup for Role & Permission System
 * Run this file once to create tables and seed initial data
 */

include '../connection.php';

$queries = [
    // Roles table
    "CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_super_admin TINYINT(1) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Menus table - stores all menu items
    "CREATE TABLE IF NOT EXISTS menus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_name VARCHAR(100) NOT NULL,
        page_link VARCHAR(255) NOT NULL,
        icon VARCHAR(100) DEFAULT 'fas fa-circle',
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Role Permissions table - maps roles to menu permissions
    "CREATE TABLE IF NOT EXISTS role_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_id INT NOT NULL,
        menu_id INT NOT NULL,
        can_view TINYINT(1) DEFAULT 0,
        can_edit TINYINT(1) DEFAULT 0,
        can_delete TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
        UNIQUE KEY unique_role_menu (role_id, menu_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$success = true;
$messages = [];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        $messages[] = "✓ Table created/verified successfully";
    } else {
        $success = false;
        $messages[] = "✗ Error: " . $conn->error;
    }
}

// Default menus configuration
$defaultMenus = [
    ['Dashboard', 'dashboard.php', 'fas fa-chart-line', 1],
    ['News', 'index.php', 'fas fa-newspaper', 2],
    ['Videos', 'manage_videos.php', 'fas fa-video', 3],
    ['Podcasts', 'manage_podcasts.php', 'fas fa-podcast', 4],
    ['Opinions', 'manage_opinions.php', 'fas fa-comment-dots', 5],
    ['Quizzes', 'manage_quizzes.php', 'fas fa-question-circle', 6],
    ['Category', 'category.php', 'fas fa-folder', 7],
    ['Reporter', 'reporter.php', 'fas fa-users', 8],
    ['News Links', 'news_links.php', 'fas fa-link', 9],
    ['Reporter Earning', 'reporter_earning.php', 'fas fa-dollar-sign', 10],
    ['Video Earning', 'video_earning.php', 'fas fa-video', 11],
    ['Total Earning', 'total_earning.php', 'fas fa-money-bill-wave', 12],
    ['Earning Report', 'earning_report.php', 'fas fa-chart-bar', 13],
    ['Payment', 'payment.php', 'fas fa-credit-card', 14],
    ['Settings', 'settings.php', 'fas fa-cog', 15],
    ['Roles', 'role.php', 'fas fa-user-shield', 16],
    ['Permissions', 'permission.php', 'fas fa-key', 17]
];

// Reset and insert menus with correct page_link values
// Disable foreign key checks temporarily
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE role_permissions");
$conn->query("TRUNCATE TABLE menus");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$messages[] = "✓ Menus and permissions tables reset";

$stmt = $conn->prepare("INSERT INTO menus (menu_name, page_link, icon, sort_order) VALUES (?, ?, ?, ?)");
foreach ($defaultMenus as $menu) {
    $stmt->bind_param("sssi", $menu[0], $menu[1], $menu[2], $menu[3]);
    $stmt->execute();
}
$messages[] = "✓ Default menus inserted with correct page links";

// Assign all permissions to existing super admin roles
$superAdmins = $conn->query("SELECT id FROM roles WHERE is_super_admin = 1");
$menus = $conn->query("SELECT id FROM menus");
$menuIds = [];
while ($m = $menus->fetch_assoc()) {
    $menuIds[] = $m['id'];
}

if ($superAdmins->num_rows > 0) {
    $permStmt = $conn->prepare("INSERT INTO role_permissions (role_id, menu_id, can_view, can_edit, can_delete) VALUES (?, ?, 1, 1, 1)");
    while ($admin = $superAdmins->fetch_assoc()) {
        foreach ($menuIds as $menuId) {
            $permStmt->bind_param("ii", $admin['id'], $menuId);
            $permStmt->execute();
        }
    }
    $messages[] = "✓ Super Admin permissions reassigned";
}

// Insert default Super Admin if roles table is empty
$checkRoles = $conn->query("SELECT COUNT(*) as cnt FROM roles");
$roleCount = $checkRoles->fetch_assoc()['cnt'];

if ($roleCount == 0) {
    $plainPassword = '#hindus@news';
    $stmt = $conn->prepare("INSERT INTO roles (role_name, username, password, is_super_admin) VALUES (?, ?, ?, ?)");
    $roleName = 'Super Admin';
    $username = 'hindusnews';
    $isSuperAdmin = 1;
    $stmt->bind_param("sssi", $roleName, $username, $plainPassword, $isSuperAdmin);
    $stmt->execute();
    
    // Give Super Admin all permissions
    $superAdminId = $conn->insert_id;
    $menus = $conn->query("SELECT id FROM menus");
    $permStmt = $conn->prepare("INSERT INTO role_permissions (role_id, menu_id, can_view, can_edit, can_delete) VALUES (?, ?, 1, 1, 1)");
    while ($menu = $menus->fetch_assoc()) {
        $permStmt->bind_param("ii", $superAdminId, $menu['id']);
        $permStmt->execute();
    }
    $messages[] = "✓ Default Super Admin created (username: hindusnews)";
    
    // Create default Editor role
    $editorPassword = 'editor@2306';
    $stmt = $conn->prepare("INSERT INTO roles (role_name, username, password, is_super_admin) VALUES (?, ?, ?, ?)");
    $editorRoleName = 'Editor';
    $editorUsername = 'editor';
    $isNotSuperAdmin = 0;
    $stmt->bind_param("sssi", $editorRoleName, $editorUsername, $editorPassword, $isNotSuperAdmin);
    $stmt->execute();
    
    // Give Editor access to Dashboard, News, and News Links only
    $editorId = $conn->insert_id;
    $editorMenus = ['dashboard.php', 'index.php', 'news_links.php'];
    $permStmt = $conn->prepare("INSERT INTO role_permissions (role_id, menu_id, can_view, can_edit, can_delete) VALUES (?, ?, ?, ?, ?)");
    $allMenus = $conn->query("SELECT id, page_link FROM menus");
    while ($menu = $allMenus->fetch_assoc()) {
        if (in_array($menu['page_link'], $editorMenus)) {
            // Full access for Dashboard, News, News Links
            $canView = 1; $canEdit = 1; $canDelete = 1;
        } else {
            // No access for other menus
            $canView = 0; $canEdit = 0; $canDelete = 0;
        }
        $permStmt->bind_param("iiiii", $editorId, $menu['id'], $canView, $canEdit, $canDelete);
        $permStmt->execute();
    }
    $messages[] = "✓ Default Editor created (username: editor) with Dashboard, News, News Links access";
}

// Output results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Role Permission Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .message { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Role & Permission Setup</h1>
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo strpos($msg, '✓') !== false ? 'success' : 'error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if ($success): ?>
            <p style="margin-top: 20px; color: #28a745;"><strong>Setup completed successfully!</strong></p>
            <a href="index.php" class="btn">Go to Login</a>
        <?php else: ?>
            <p style="margin-top: 20px; color: #dc3545;"><strong>Some errors occurred. Please check your database.</strong></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>
