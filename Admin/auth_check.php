<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Get user info from session
$user_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? '';
$role_name = $_SESSION['role_name'] ?? '';
$is_super_admin = $_SESSION['is_super_admin'] ?? false;

// For backward compatibility
$userRole = $is_super_admin ? 'admin' : 'editor';
$isEditor = !$is_super_admin;

// Get current page permissions
$currentPage = basename($_SERVER['PHP_SELF']);
$can_view = false;
$can_edit = false;
$can_delete = false;

if ($is_super_admin) {
    // Super admin has all permissions
    $can_view = true;
    $can_edit = true;
    $can_delete = true;
} else {
    // Check permissions from session
    $userMenus = $_SESSION['user_menus'] ?? [];
    foreach ($userMenus as $menu) {
        if ($menu['page_link'] === $currentPage) {
            $can_view = (bool)($menu['can_view'] ?? false);
            $can_edit = (bool)($menu['can_edit'] ?? false);
            $can_delete = (bool)($menu['can_delete'] ?? false);
            break;
        }
    }
}
?>
