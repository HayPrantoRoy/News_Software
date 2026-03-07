<?php
session_start();
header('Content-Type: application/json');
include '../connection.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ==================== ROLE OPERATIONS ====================
    case 'get_roles':
        $result = $conn->query("SELECT id, role_name, username, is_super_admin, is_active, created_at FROM roles ORDER BY id ASC");
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $roles]);
        break;

    case 'get_role':
        $id = intval($_GET['id'] ?? 0);
        $stmt = $conn->prepare("SELECT id, role_name, username, is_super_admin, is_active FROM roles WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Role not found']);
        }
        break;

    case 'add_role':
        $role_name = trim($_POST['role_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $is_super_admin = intval($_POST['is_super_admin'] ?? 0);

        if (empty($role_name) || empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            break;
        }

        // Check if username exists
        $check = $conn->prepare("SELECT id FROM roles WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO roles (role_name, username, password, is_super_admin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $role_name, $username, $password, $is_super_admin);
        
        if ($stmt->execute()) {
            $newRoleId = $conn->insert_id;
            
            // Assign default permissions (view only) for all menus
            $menuResult = $conn->query("SELECT id FROM menus WHERE is_active = 1");
            if ($menuResult->num_rows > 0) {
                $permStmt = $conn->prepare("INSERT INTO role_permissions (role_id, menu_id, can_view, can_edit, can_delete) VALUES (?, ?, 1, 0, 0)");
                while ($menu = $menuResult->fetch_assoc()) {
                    $permStmt->bind_param("ii", $newRoleId, $menu['id']);
                    $permStmt->execute();
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Role added successfully with default view permissions', 'id' => $newRoleId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add role: ' . $conn->error]);
        }
        break;

    case 'update_role':
        $id = intval($_POST['id'] ?? 0);
        $role_name = trim($_POST['role_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $is_super_admin = intval($_POST['is_super_admin'] ?? 0);
        $is_active = intval($_POST['is_active'] ?? 1);

        if (empty($role_name) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Role name and username are required']);
            break;
        }

        // Check if username exists for other roles
        $check = $conn->prepare("SELECT id FROM roles WHERE username = ? AND id != ?");
        $check->bind_param("si", $username, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            break;
        }

        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE roles SET role_name = ?, username = ?, password = ?, is_super_admin = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssiii", $role_name, $username, $password, $is_super_admin, $is_active, $id);
        } else {
            $stmt = $conn->prepare("UPDATE roles SET role_name = ?, username = ?, is_super_admin = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("ssiii", $role_name, $username, $is_super_admin, $is_active, $id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update role']);
        }
        break;

    case 'delete_role':
        $id = intval($_POST['id'] ?? 0);
        
        // Prevent deleting super admin
        $check = $conn->prepare("SELECT is_super_admin FROM roles WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        
        if ($result && $result['is_super_admin'] == 1) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete Super Admin role']);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Role deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete role']);
        }
        break;

    // ==================== MENU OPERATIONS ====================
    case 'get_menus':
        $result = $conn->query("SELECT * FROM menus WHERE is_active = 1 ORDER BY sort_order ASC");
        $menus = [];
        while ($row = $result->fetch_assoc()) {
            $menus[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $menus]);
        break;

    case 'add_menu':
        $menu_name = trim($_POST['menu_name'] ?? '');
        $page_link = trim($_POST['page_link'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-circle');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        if (empty($menu_name) || empty($page_link)) {
            echo json_encode(['success' => false, 'message' => 'Menu name and page link are required']);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO menus (menu_name, page_link, icon, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $menu_name, $page_link, $icon, $sort_order);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Menu added successfully', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add menu']);
        }
        break;

    case 'update_menu':
        $id = intval($_POST['id'] ?? 0);
        $menu_name = trim($_POST['menu_name'] ?? '');
        $page_link = trim($_POST['page_link'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-circle');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        $stmt = $conn->prepare("UPDATE menus SET menu_name = ?, page_link = ?, icon = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param("sssii", $menu_name, $page_link, $icon, $sort_order, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Menu updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update menu']);
        }
        break;

    case 'delete_menu':
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Menu deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete menu']);
        }
        break;

    // ==================== PERMISSION OPERATIONS ====================
    case 'get_permissions':
        $role_id = intval($_GET['role_id'] ?? 0);
        
        if ($role_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Role ID is required']);
            break;
        }

        $query = "SELECT m.id as menu_id, m.menu_name, m.page_link, m.icon,
                         COALESCE(rp.can_view, 0) as can_view,
                         COALESCE(rp.can_edit, 0) as can_edit,
                         COALESCE(rp.can_delete, 0) as can_delete
                  FROM menus m
                  LEFT JOIN role_permissions rp ON m.id = rp.menu_id AND rp.role_id = ?
                  WHERE m.is_active = 1
                  ORDER BY m.sort_order ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $permissions]);
        break;

    case 'save_permissions':
        $role_id = intval($_POST['role_id'] ?? 0);
        $permissions = json_decode($_POST['permissions'] ?? '[]', true);

        if ($role_id == 0) {
            echo json_encode(['success' => false, 'message' => 'Role ID is required']);
            break;
        }

        // Delete existing permissions for this role
        $deleteStmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $deleteStmt->bind_param("i", $role_id);
        $deleteStmt->execute();

        // Insert new permissions
        $insertStmt = $conn->prepare("INSERT INTO role_permissions (role_id, menu_id, can_view, can_edit, can_delete) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($permissions as $perm) {
            $menu_id = intval($perm['menu_id']);
            $can_view = intval($perm['can_view'] ?? 0);
            $can_edit = intval($perm['can_edit'] ?? 0);
            $can_delete = intval($perm['can_delete'] ?? 0);
            
            $insertStmt->bind_param("iiiii", $role_id, $menu_id, $can_view, $can_edit, $can_delete);
            $insertStmt->execute();
        }

        echo json_encode(['success' => true, 'message' => 'Permissions saved successfully']);
        break;

    // ==================== AUTH OPERATIONS ====================
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required']);
            break;
        }

        $stmt = $conn->prepare("SELECT id, role_name, username, password, is_super_admin, is_active FROM roles WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($user['is_active'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Account is disabled']);
                break;
            }

            if (password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_name'] = $user['role_name'];
                $_SESSION['is_super_admin'] = $user['is_super_admin'];
                
                echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => 'dashboard.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        break;

    case 'check_permission':
        $page = $_GET['page'] ?? '';
        $user_id = $_SESSION['user_id'] ?? 0;
        
        if ($user_id == 0) {
            echo json_encode(['success' => false, 'has_access' => false]);
            break;
        }

        // Super admin has all access
        if ($_SESSION['is_super_admin'] ?? false) {
            echo json_encode(['success' => true, 'has_access' => true, 'can_view' => true, 'can_edit' => true, 'can_delete' => true]);
            break;
        }

        $stmt = $conn->prepare("SELECT rp.can_view, rp.can_edit, rp.can_delete 
                                FROM role_permissions rp 
                                JOIN menus m ON rp.menu_id = m.id 
                                WHERE rp.role_id = ? AND m.page_link = ?");
        $stmt->bind_param("is", $user_id, $page);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($perm = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true, 
                'has_access' => $perm['can_view'] == 1,
                'can_view' => $perm['can_view'] == 1,
                'can_edit' => $perm['can_edit'] == 1,
                'can_delete' => $perm['can_delete'] == 1
            ]);
        } else {
            echo json_encode(['success' => true, 'has_access' => false, 'can_view' => false, 'can_edit' => false, 'can_delete' => false]);
        }
        break;

    case 'get_user_menus':
        $user_id = $_SESSION['user_id'] ?? 0;
        
        if ($user_id == 0) {
            echo json_encode(['success' => false, 'data' => []]);
            break;
        }

        // Super admin gets all menus
        if ($_SESSION['is_super_admin'] ?? false) {
            $result = $conn->query("SELECT id, menu_name, page_link, icon FROM menus WHERE is_active = 1 ORDER BY sort_order ASC");
            $menus = [];
            while ($row = $result->fetch_assoc()) {
                $row['can_view'] = 1;
                $row['can_edit'] = 1;
                $row['can_delete'] = 1;
                $menus[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $menus]);
            break;
        }

        $stmt = $conn->prepare("SELECT m.id, m.menu_name, m.page_link, m.icon, rp.can_view, rp.can_edit, rp.can_delete
                                FROM menus m
                                JOIN role_permissions rp ON m.id = rp.menu_id
                                WHERE rp.role_id = ? AND rp.can_view = 1 AND m.is_active = 1
                                ORDER BY m.sort_order ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $menus = [];
        while ($row = $result->fetch_assoc()) {
            $menus[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $menus]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
