<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/connection.php';

// Fetch basic_info for dynamic logo and portal name
$basic_info = [];
$basic_info_result = $conn->query("SELECT * FROM basic_info LIMIT 1");
if ($basic_info_result && $basic_info_result->num_rows > 0) {
    $basic_info = $basic_info_result->fetch_assoc();
}

$user_id = $_SESSION['user_id'] ?? 0;
$isSuperAdmin = $_SESSION['is_super_admin'] ?? false;
$currentPage = basename($_SERVER['PHP_SELF']);
$userName = $_SESSION['username'] ?? 'Admin';
$roleName = $_SESSION['role_name'] ?? 'Admin';

// Get user's accessible menus from database
$userMenus = [];
if ($user_id > 0) {
    if ($isSuperAdmin) {
        // Super admin gets all menus
        $menuResult = $conn->query("SELECT id, menu_name, page_link, icon FROM menus WHERE is_active = 1 ORDER BY sort_order ASC");
        while ($row = $menuResult->fetch_assoc()) {
            $row['can_view'] = 1;
            $row['can_edit'] = 1;
            $row['can_delete'] = 1;
            $userMenus[] = $row;
        }
    } else {
        // Regular users get menus based on permissions
        $stmt = $conn->prepare("SELECT m.id, m.menu_name, m.page_link, m.icon, rp.can_view, rp.can_edit, rp.can_delete
                                FROM menus m
                                JOIN role_permissions rp ON m.id = rp.menu_id
                                WHERE rp.role_id = ? AND rp.can_view = 1 AND m.is_active = 1
                                ORDER BY m.sort_order ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $userMenus[] = $row;
        }
    }
}

// Store permissions in session for easy access
$_SESSION['user_menus'] = $userMenus;

// Helper function to check if user can access a page
function canAccessPage($page, $userMenus) {
    foreach ($userMenus as $menu) {
        if ($menu['page_link'] === $page && $menu['can_view'] == 1) {
            return true;
        }
    }
    return false;
}

// Helper function to get page permissions
function getPagePermissions($page, $userMenus) {
    foreach ($userMenus as $menu) {
        if ($menu['page_link'] === $page) {
            return [
                'can_view' => $menu['can_view'] == 1,
                'can_edit' => $menu['can_edit'] == 1,
                'can_delete' => $menu['can_delete'] == 1
            ];
        }
    }
    return ['can_view' => false, 'can_edit' => false, 'can_delete' => false];
}
?>

<!-- Top Header -->
<header class="top-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <?php if (!empty($basic_info['image'])): ?>
        <img src="../<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" class="header-logo">
        <?php endif; ?>
    </div>
    <div class="header-right">
        <a href="../index.php?user_id=<?php echo $_SESSION['current_user_id'] ?? 0; ?>" target="_blank" class="website-btn" title="ওয়েবসাইটে যান">
            <i class="fas fa-globe"></i>
            <span>Website</span>
        </a>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($userName); ?><?php if ($isSuperAdmin): ?> <strong>(Super Admin)</strong><?php endif; ?></span>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</header>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-tachometer-alt"></i> Admin Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <?php if (!empty($userMenus)): ?>
            <?php foreach ($userMenus as $menu): ?>
                <a href="<?php echo htmlspecialchars($menu['page_link']); ?>" 
                   class="nav-item <?php echo ($currentPage == $menu['page_link']) ? 'active' : ''; ?>">
                    <i class="<?php echo htmlspecialchars($menu['icon']); ?>"></i>
                    <span><?php echo htmlspecialchars($menu['menu_name']); ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback static menu if no database menus -->
            <a href="dashboard.php" class="nav-item <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>

<!-- Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Content Wrapper -->
<div class="main-wrapper" id="mainWrapper">
<style>
/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f8f9fc;
    overflow-x: hidden;
}

/* Top Header */
.top-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 65px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    z-index: 1000;
    border-bottom: 1px solid #e8eaed;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.sidebar-toggle {
    background: transparent;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    color: #5f6368;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-toggle:hover {
    background: #f1f3f4;
    color: #308e87;
}

.header-logo {
    height: 42px;
    width: auto;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #5f6368;
    font-size: 14px;
    font-weight: 500;
    padding: 8px 16px;
    background: #f8f9fa;
    border-radius: 20px;
}

.user-info i {
    font-size: 20px;
    color: #308e87;
}

.logout-btn {
    background: #308e87;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(48, 142, 135, 0.2);
}

.logout-btn:hover {
    background: #267872;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(48, 142, 135, 0.3);
}

.website-btn {
    background: #4a90e2;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(74, 144, 226, 0.2);
}

.website-btn:hover {
    background: #357abd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(74, 144, 226, 0.3);
}

/* Sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 65px;
    width: 200px;
    height: calc(100vh - 65px);
    background: #ffffff;
    box-shadow: 1px 0 3px rgba(0, 0, 0, 0.05);
    overflow-y: auto;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 999;
    border-right: 1px solid #e8eaed;
}

.sidebar.collapsed {
    left: -200px;
}

.sidebar-header {
    padding: 20px 16px;
    background: #308e87;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h3 {
    color: white;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sidebar-header h3 i {
    font-size: 16px;
}

.sidebar-nav {
    padding: 12px 0;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #5f6368;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    border-left: 3px solid transparent;
    margin: 2px 0;
}

.nav-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
    color: #80868b;
    transition: color 0.2s;
    flex-shrink: 0;
}

.nav-item:hover {
    background: #f8f9fa;
    color: #308e87;
}

.nav-item:hover i {
    color: #308e87;
}

.nav-item.active {
    background: rgba(48, 142, 135, 0.08);
    color: #308e87;
    border-left-color: #308e87;
    font-weight: 600;
}

.nav-item.active i {
    color: #308e87;
}

/* Main Content Wrapper */
.main-wrapper {
    margin-left: 200px;
    margin-top: 65px;
    padding: 24px;
    min-height: calc(100vh - 65px);
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-wrapper.expanded {
    margin-left: 0;
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar {
        left: -200px;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar.active {
        left: 0;
    }
    
    .main-wrapper {
        margin-left: 0;
    }
    
    .header-logo {
        height: 38px;
    }
    
    .user-info span {
        display: none;
    }
}

@media (max-width: 576px) {
    .top-header {
        padding: 0 16px;
        height: 60px;
    }
    
    .header-logo {
        height: 34px;
    }
    
    .logout-btn span {
        display: none;
    }
    
    .logout-btn {
        padding: 10px 14px;
    }
    
    .main-wrapper {
        padding: 16px;
        margin-top: 60px;
    }
    
    .sidebar {
        width: 200px;
        top: 60px;
        height: calc(100vh - 60px);
    }
}

/* Scrollbar for Sidebar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #dadce0;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #bdc1c6;
}

/* Overlay for mobile */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 65px;
    left: 0;
    width: 100%;
    height: calc(100vh - 65px);
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
    opacity: 0;
    transition: opacity 0.3s;
}

.sidebar-overlay.active {
    display: block;
    opacity: 1;
}

@media (max-width: 992px) {
    .sidebar-overlay {
        top: 60px;
        height: calc(100vh - 60px);
    }
}
</style>

<script>
(function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('mainWrapper');
    const overlay = document.getElementById('sidebarOverlay');
    
    function toggleSidebar() {
        const isMobile = window.innerWidth <= 992;
        
        if (isMobile) {
            // Mobile behavior - toggle overlay and sidebar
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            // Desktop behavior - collapse/expand
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('expanded');
        }
    }
    
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    }
    
    if (sidebarToggle && sidebar && mainWrapper && overlay) {
        // Toggle button click
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
        
        // Overlay click - close sidebar
        overlay.addEventListener('click', closeSidebar);
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeSidebar();
                sidebar.classList.remove('collapsed');
                mainWrapper.classList.remove('expanded');
            }
        });
    }
})();
</script>
