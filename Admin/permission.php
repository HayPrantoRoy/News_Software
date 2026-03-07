<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif; background-color: #f5f5f5; color: #333; line-height: 1.6; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .section { background: white; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; overflow: hidden; }
        .section-header { background: #f8f9fa; border-bottom: 1px solid #ddd; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .section-title { font-size: 1.2rem; font-weight: 600; color: #495057; }
        .section-body { padding: 20px; }
        
        .btn { padding: 10px 20px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; background: #fff; color: #333; }
        .btn:hover { background: #f8f9fa; border-color: #adb5bd; }
        .btn-primary { background: #007bff; color: white; border-color: #007bff; }
        .btn-primary:hover { background: #0056b3; border-color: #0056b3; }
        .btn-success { background: #28a745; color: white; border-color: #28a745; }
        .btn-success:hover { background: #1e7e34; border-color: #1e7e34; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #495057; }
        select { width: 100%; max-width: 300px; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background: #fff; }
        select:focus { outline: none; border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; }
        tr:hover { background: #f8f9fa; }
        
        .permission-checkbox { text-align: center; }
        .permission-checkbox input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        
        .menu-info { display: flex; align-items: center; gap: 10px; }
        .menu-icon { width: 32px; height: 32px; background: #007bff; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .menu-name { font-weight: 500; }
        .menu-link { font-size: 12px; color: #6c757d; }
        
        .select-all-row { background: #e3f2fd !important; }
        .select-all-row td { font-weight: 600; color: #1976d2; }
        
        .alert { padding: 12px 15px; border-radius: 4px; margin-bottom: 15px; border: 1px solid; }
        .alert-success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border-color: #ffeeba; }
        
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; display: block; }
        
        .actions-bar { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        
        .super-admin-notice { background: #fff3cd; padding: 15px; border-radius: 4px; border: 1px solid #ffeeba; display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .super-admin-notice i { font-size: 20px; color: #856404; }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            select { max-width: 100%; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="container">
        <div id="alertContainer"></div>
        
        <!-- Role Selection -->
        <div class="section">
            <div class="section-header">
                <span class="section-title"><i class="fas fa-key"></i> Permission Management</span>
            </div>
            <div class="section-body">
                <div class="form-group">
                    <label><i class="fas fa-user-shield"></i> Select Role:</label>
                    <select id="roleSelect" onchange="loadPermissions()">
                        <option value="">-- Select a Role --</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Permissions Table -->
        <div id="permissionsContainer" style="display: none;">
            <div id="superAdminNotice" class="super-admin-notice" style="display: none;">
                <i class="fas fa-crown"></i>
                <span><strong>Super Admin</strong> has full access to all menus. Permissions cannot be modified.</span>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <span class="section-title"><i class="fas fa-list-check"></i> Menu Permissions</span>
                </div>
                <div class="table-container">
                    <table id="permissionsTable">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Menu</th>
                                <th class="permission-checkbox">View</th>
                                <th class="permission-checkbox">Edit</th>
                                <th class="permission-checkbox">Delete</th>
                            </tr>
                            <tr class="select-all-row">
                                <td><i class="fas fa-check-double"></i> Select All</td>
                                <td class="permission-checkbox"><input type="checkbox" id="selectAllView" onchange="toggleAll('view')"></td>
                                <td class="permission-checkbox"><input type="checkbox" id="selectAllEdit" onchange="toggleAll('edit')"></td>
                                <td class="permission-checkbox"><input type="checkbox" id="selectAllDelete" onchange="toggleAll('delete')"></td>
                            </tr>
                        </thead>
                        <tbody id="permissionsTableBody"></tbody>
                    </table>
                </div>
                
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="empty-state">
            <i class="fas fa-hand-pointer"></i>
            <h3>Select a Role</h3>
            <p>Choose a role from the dropdown to manage its permissions</p>
        </div>
    </div>
    
    <script>
        let currentRoleId = null;
        let isSuperAdmin = false;
        
        document.addEventListener('DOMContentLoaded', loadRoles);
        
        async function loadRoles() {
            const response = await fetch('rolepermission_api.php?action=get_roles');
            const data = await response.json();
            if (data.success) {
                const select = document.getElementById('roleSelect');
                select.innerHTML = '<option value="">-- Select a Role --</option>';
                data.data.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.role_name + (role.is_super_admin == 1 ? ' (Super Admin)' : '');
                    option.dataset.superAdmin = role.is_super_admin;
                    select.appendChild(option);
                });
            }
        }
        
        async function loadPermissions() {
            const select = document.getElementById('roleSelect');
            currentRoleId = select.value;
            
            if (!currentRoleId) {
                document.getElementById('permissionsContainer').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
                return;
            }
            
            const selectedOption = select.options[select.selectedIndex];
            isSuperAdmin = selectedOption.dataset.superAdmin == 1;
            
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('permissionsContainer').style.display = 'block';
            document.getElementById('superAdminNotice').style.display = isSuperAdmin ? 'flex' : 'none';
            
            const response = await fetch(`rolepermission_api.php?action=get_permissions&role_id=${currentRoleId}`);
            const data = await response.json();
            
            if (data.success) {
                renderPermissions(data.data);
            }
        }
        
        function renderPermissions(permissions) {
            const tbody = document.getElementById('permissionsTableBody');
            tbody.innerHTML = permissions.map(p => `
                <tr>
                    <td>
                        <div class="menu-info">
                            <div class="menu-icon"><i class="${p.icon || 'fas fa-circle'}"></i></div>
                            <div>
                                <div class="menu-name">${p.menu_name}</div>
                                <div class="menu-link">${p.page_link}</div>
                            </div>
                        </div>
                    </td>
                    <td class="permission-checkbox">
                        <input type="checkbox" class="perm-view" data-menu-id="${p.menu_id}" ${p.can_view == 1 || isSuperAdmin ? 'checked' : ''} ${isSuperAdmin ? 'disabled' : ''} onchange="savePermission(${p.menu_id}); updateSelectAll()">
                    </td>
                    <td class="permission-checkbox">
                        <input type="checkbox" class="perm-edit" data-menu-id="${p.menu_id}" ${p.can_edit == 1 || isSuperAdmin ? 'checked' : ''} ${isSuperAdmin ? 'disabled' : ''} onchange="savePermission(${p.menu_id}); updateSelectAll()">
                    </td>
                    <td class="permission-checkbox">
                        <input type="checkbox" class="perm-delete" data-menu-id="${p.menu_id}" ${p.can_delete == 1 || isSuperAdmin ? 'checked' : ''} ${isSuperAdmin ? 'disabled' : ''} onchange="savePermission(${p.menu_id}); updateSelectAll()">
                    </td>
                </tr>
            `).join('');
            updateSelectAll();
        }
        
        function toggleAll(type) {
            const selectAll = document.getElementById('selectAll' + type.charAt(0).toUpperCase() + type.slice(1));
            document.querySelectorAll('.perm-' + type).forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = selectAll.checked;
                    savePermission(cb.dataset.menuId);
                }
            });
        }
        
        function updateSelectAll() {
            ['view', 'edit', 'delete'].forEach(type => {
                const checkboxes = document.querySelectorAll('.perm-' + type + ':not(:disabled)');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                document.getElementById('selectAll' + type.charAt(0).toUpperCase() + type.slice(1)).checked = allChecked;
            });
        }
        
        async function savePermission(menuId) {
            if (isSuperAdmin) return;
            
            const viewCb = document.querySelector(`.perm-view[data-menu-id="${menuId}"]`);
            const editCb = document.querySelector(`.perm-edit[data-menu-id="${menuId}"]`);
            const deleteCb = document.querySelector(`.perm-delete[data-menu-id="${menuId}"]`);
            
            const permissions = [{
                menu_id: menuId,
                can_view: viewCb.checked ? 1 : 0,
                can_edit: editCb.checked ? 1 : 0,
                can_delete: deleteCb.checked ? 1 : 0
            }];
            
            // Also include all other permissions to maintain them
            document.querySelectorAll('.perm-view').forEach(cb => {
                const mid = cb.dataset.menuId;
                if (mid != menuId) {
                    permissions.push({
                        menu_id: mid,
                        can_view: cb.checked ? 1 : 0,
                        can_edit: document.querySelector(`.perm-edit[data-menu-id="${mid}"]`).checked ? 1 : 0,
                        can_delete: document.querySelector(`.perm-delete[data-menu-id="${mid}"]`).checked ? 1 : 0
                    });
                }
            });
            
            const formData = new FormData();
            formData.append('action', 'save_permissions');
            formData.append('role_id', currentRoleId);
            formData.append('permissions', JSON.stringify(permissions));
            
            try {
                const response = await fetch('rolepermission_api.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                // Show brief feedback
                const container = document.getElementById('alertContainer');
                container.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'error'}">${data.success ? 'Saved!' : data.message}</div>`;
                setTimeout(() => container.innerHTML = '', 1500);
            } catch (error) {
                console.error('Error saving permission:', error);
            }
        }
    </script>
</body>
</html>
