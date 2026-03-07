<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif; background-color: #f5f5f5; color: #333; line-height: 1.6; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .section { background: white; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; overflow: hidden; }
        .section-header { background: #f8f9fa; border-bottom: 1px solid #ddd; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .section-title { font-size: 1.2rem; font-weight: 600; color: #495057; }
        
        .btn { padding: 10px 20px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; background: #fff; color: #333; }
        .btn:hover { background: #f8f9fa; border-color: #adb5bd; }
        .btn-primary { background: #007bff; color: white; border-color: #007bff; }
        .btn-primary:hover { background: #0056b3; border-color: #0056b3; }
        .btn-success { background: #28a745; color: white; border-color: #28a745; }
        .btn-success:hover { background: #1e7e34; border-color: #1e7e34; }
        .btn-warning { background: #ffc107; color: #212529; border-color: #ffc107; padding: 6px 12px; font-size: 12px; }
        .btn-warning:hover { background: #e0a800; border-color: #e0a800; }
        .btn-danger { background: #dc3545; color: white; border-color: #dc3545; padding: 6px 12px; font-size: 12px; }
        .btn-danger:hover { background: #c82333; border-color: #c82333; }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; }
        tr:hover { background: #f8f9fa; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-primary { background: #cce5ff; color: #004085; }
        
        .actions { display: flex; gap: 5px; }
        
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; }
        .modal.hidden { display: none; }
        .modal-content { background: white; border-radius: 4px; width: 90%; max-width: 500px; max-height: 90%; overflow-y: auto; }
        .modal-header { background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 15px 20px; border-top: 1px solid #ddd; text-align: right; display: flex; gap: 10px; justify-content: flex-end; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #495057; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background: #fff; }
        .form-control:focus { outline: none; border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
        
        .form-check { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .form-check input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        
        .alert { padding: 12px 15px; border-radius: 4px; margin-bottom: 15px; border: 1px solid; }
        .alert-success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; display: block; }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .section-header { flex-direction: column; gap: 10px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="container">
        <div id="alertContainer"></div>
        
        <div class="section">
            <div class="section-header">
                <span class="section-title"><i class="fas fa-user-shield"></i> Role Management</span>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add New Role
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rolesTableBody">
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div class="modal hidden" id="roleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Role</h3>
                <button class="btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId" name="id">
                    
                    <div class="form-group">
                        <label for="roleName">Role Name *</label>
                        <input type="text" class="form-control" id="roleName" name="role_name" required placeholder="e.g., Editor, Manager">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Login username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span id="passwordHint">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        <small style="color: #999;">Leave blank to keep existing password (when editing)</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="isSuperAdmin" name="is_super_admin" value="1">
                            <label for="isSuperAdmin">Super Admin (Full Access)</label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="statusGroup" style="display: none;">
                        <div class="form-check">
                            <input type="checkbox" id="isActive" name="is_active" value="1" checked>
                            <label for="isActive">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal()">Cancel</button>
                <button class="btn btn-success" onclick="saveRole()">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal hidden" id="deleteModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Confirm Delete</h3>
                <button class="btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this role? This action cannot be undone.</p>
                <input type="hidden" id="deleteRoleId">
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Permission variables from PHP
        const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
        const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;
        
        let isEditMode = false;
        
        // Load roles on page load
        document.addEventListener('DOMContentLoaded', loadRoles);
        
        async function loadRoles() {
            try {
                const response = await fetch('rolepermission_api.php?action=get_roles');
                const data = await response.json();
                
                if (data.success) {
                    renderRoles(data.data);
                } else {
                    showAlert('danger', 'Failed to load roles');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Error loading roles');
            }
        }
        
        function renderRoles(roles) {
            const tbody = document.getElementById('rolesTableBody');
            
            if (roles.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p>No roles found</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = roles.map((role, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${escapeHtml(role.role_name)}</strong></td>
                    <td>${escapeHtml(role.username)}</td>
                    <td>
                        ${role.is_super_admin == 1 
                            ? '<span class="badge badge-primary"><i class="fas fa-crown"></i> Super Admin</span>' 
                            : '<span class="badge badge-success">Standard</span>'}
                    </td>
                    <td>
                        ${role.is_active == 1 
                            ? '<span class="badge badge-success">Active</span>' 
                            : '<span class="badge badge-danger">Inactive</span>'}
                    </td>
                    <td>${formatDate(role.created_at)}</td>
                    <td>
                        <div class="actions">
                            ${canEdit ? `<button class="btn btn-warning btn-sm" onclick="editRole(${role.id})" title="Edit"><i class="fas fa-edit"></i></button>` : ''}
                            ${canDelete && role.is_super_admin != 1 ? `<button class="btn btn-danger btn-sm" onclick="deleteRole(${role.id})" title="Delete"><i class="fas fa-trash"></i></button>` : ''}
                            ${!canEdit && !canDelete ? '<span style="color:#999;">-</span>' : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        function openModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Add New Role';
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = '';
            document.getElementById('passwordHint').textContent = '*';
            document.getElementById('password').required = true;
            document.getElementById('statusGroup').style.display = 'none';
            document.getElementById('roleModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }
        
        async function editRole(id) {
            try {
                const response = await fetch(`rolepermission_api.php?action=get_role&id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    isEditMode = true;
                    const role = data.data;
                    
                    document.getElementById('modalTitle').textContent = 'Edit Role';
                    document.getElementById('roleId').value = role.id;
                    document.getElementById('roleName').value = role.role_name;
                    document.getElementById('username').value = role.username;
                    document.getElementById('password').value = '';
                    document.getElementById('password').required = false;
                    document.getElementById('passwordHint').textContent = '(optional)';
                    document.getElementById('isSuperAdmin').checked = role.is_super_admin == 1;
                    document.getElementById('isActive').checked = role.is_active == 1;
                    document.getElementById('statusGroup').style.display = 'block';
                    
                    document.getElementById('roleModal').classList.remove('hidden');
                } else {
                    showAlert('error', 'Failed to load role data');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error loading role');
            }
        }
        
        async function saveRole() {
            const form = document.getElementById('roleForm');
            const formData = new FormData(form);
            
            const action = isEditMode ? 'update_role' : 'add_role';
            formData.append('action', action);
            
            // Handle checkboxes
            formData.set('is_super_admin', document.getElementById('isSuperAdmin').checked ? 1 : 0);
            formData.set('is_active', document.getElementById('isActive').checked ? 1 : 0);
            
            try {
                const response = await fetch('rolepermission_api.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    closeModal();
                    loadRoles();
                } else {
                    showAlert('danger', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Error saving role');
            }
        }
        
        function deleteRole(id) {
            document.getElementById('deleteRoleId').value = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        
        async function confirmDelete() {
            const id = document.getElementById('deleteRoleId').value;
            const formData = new FormData();
            formData.append('action', 'delete_role');
            formData.append('id', id);
            
            try {
                const response = await fetch('rolepermission_api.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    closeDeleteModal();
                    loadRoles();
                } else {
                    showAlert('danger', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Error deleting role');
            }
        }
        
        function showAlert(type, message) {
            const container = document.getElementById('alertContainer');
            const alertClass = type === 'danger' ? 'error' : type;
            container.innerHTML = `
                <div class="alert alert-${alertClass}">
                    ${message}
                </div>
            `;
            setTimeout(() => container.innerHTML = '', 5000);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }
    </script>
</body>
</html>
