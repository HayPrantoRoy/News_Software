<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="modern_admin_styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .header-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
            background: #fff;
            color: #333;
        }

        .btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .btn-success:hover {
            background: #1e7e34;
            border-color: #1e7e34;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
            border-color: #ffc107;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-warning:hover {
            background: #e0a800;
            border-color: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger:hover {
            background: #c82333;
            border-color: #c82333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .section {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #495057;
        }

        .form-container {
            padding: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .filters {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .filter-group input,
        .filter-group select {
            padding: 6px 8px;
            font-size: 13px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .pagination-info {
            font-size: 13px;
            color: #6c757d;
        }

        .pagination-buttons {
            display: flex;
            gap: 5px;
        }

        .pagination-buttons button {
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }

        .pagination-buttons button:hover:not(:disabled) {
            background: #f8f9fa;
        }

        .pagination-buttons button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-buttons button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .hidden {
            display: none;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal.hidden {
            display: none;
        }

        .modal-content {
            background: white;
            border-radius: 4px;
            width: 90%;
            max-width: 600px;
            max-height: 90%;
            overflow-y: auto;
        }

        .modal-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            text-align: right;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .header-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .filters {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .filter-group {
                flex-direction: row;
                align-items: center;
                gap: 10px;
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-folder"></i>
            বিভাগ ব্যবস্থাপনা
        </h1>
    </div>

    <div class="container">
        <div id="success-message" class="alert alert-success hidden"></div>
        <div id="error-message" class="alert alert-error hidden"></div>

        <!-- Add Category Form Section -->
        <div class="form-section">
            <h2>
                <i class="fas fa-plus-circle"></i>
                নতুন বিভাগ তৈরি করুন
            </h2>
            <form id="categoryForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-heading"></i>
                            বিভাগের নাম
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="name" name="name" required placeholder="বিভাগের নাম লিখুন">
                    </div>

                    <div class="form-group">
                        <label for="slug">
                            <i class="fas fa-link"></i>
                            স্লাগ
                        </label>
                        <input type="text" id="slug" name="slug" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="is_active">
                            <i class="fas fa-toggle-on"></i>
                            অবস্থা
                        </label>
                        <select id="is_active" name="is_active">
                            <option value="1">সক্রিয়</option>
                            <option value="0">নিষ্ক্রিয়</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> বিভাগ যোগ করুন
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('categoryForm').reset()">
                        <i class="fas fa-times"></i> বাতিল
                    </button>
                </div>
            </form>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-bar-left">
                <button class="btn btn-success" onclick="loadCategories()">
                    <i class="fas fa-sync-alt"></i> রিফ্রেশ
                </button>
            </div>
            <div class="action-bar-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-filter" placeholder="বিভাগ খুঁজুন..." onkeyup="applyFilters()">
                </div>
                <select class="filter-select" id="status-filter" onchange="applyFilters()">
                    <option value="">সব অবস্থা</option>
                    <option value="1">সক্রিয়</option>
                    <option value="0">নিষ্ক্রিয়</option>
                </select>
                <button class="btn btn-info" onclick="clearFilters()">
                    <i class="fas fa-redo"></i> রিসেট
                </button>
            </div>
        </div>

        <!-- Categories Table Section -->
        <div class="table-section">
            <h2>
                <i class="fas fa-list"></i>
                সমস্ত বিভাগ (<span id="total-records">0</span>)
            </h2>

            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> আইডি</th>
                        <th><i class="fas fa-heading"></i> নাম</th>
                        <th><i class="fas fa-link"></i> স্লাগ</th>
                        <th><i class="fas fa-toggle-on"></i> অবস্থা</th>
                        <th><i class="fas fa-cogs"></i> অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="categories-table-body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #308e87;"></i>
                            <p style="margin-top: 10px;">বিভাগসমূহ লোড হচ্ছে...</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination">
                <div class="pagination-info">
                    মোট <span id="total-records">0</span> টি রেকর্ড, পৃষ্ঠা <span id="current-page">1</span> এর <span id="total-pages">1</span>
                </div>
                <div class="pagination-buttons">
                    <button onclick="changePage(1)" id="first-btn">প্রথম</button>
                    <button onclick="changePage(currentPage - 1)" id="prev-btn">পূর্ববর্তী</button>
                    <span id="page-numbers"></span>
                    <button onclick="changePage(currentPage + 1)" id="next-btn">পরবর্তী</button>
                    <button onclick="changePage(totalPages)" id="last-btn">শেষ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3>বিভাগ সম্পাদনা</h3>
                <button class="btn btn-danger" onclick="closeEditModal()" style="padding: 5px 10px;">×</button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" id="edit-category-id">
                    <div class="form-group">
                        <label for="edit-name">বিভাগের নাম</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-slug">স্লাগ</label>
                        <input type="text" id="edit-slug" name="slug" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-is_active">অবস্থা</label>
                        <select id="edit-is_active" name="is_active">
                            <option value="1">সক্রিয়</option>
                            <option value="0">নিষ্ক্রিয়</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times"></i> বাতিল
                </button>
                <button type="submit" form="editCategoryForm" class="btn btn-primary">
                    <i class="fas fa-save"></i> বিভাগ আপডেট করুন
                </button>
            </div>
        </div>
    </div>

    <script>
// Permission variables from PHP
const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;

// Global variables
let allCategories = [];
let filteredCategories = [];
let currentPage = 1;
let recordsPerPage = 10;
let totalPages = 1;

// Category API endpoints
const API_BASE = 'category_api.php';

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    setupEventListeners();
});

function setupEventListeners() {
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = generateSlug(this.value);
        document.getElementById('slug').value = slug;
    });

    // Auto-generate slug for edit form
    document.getElementById('edit-name').addEventListener('input', function() {
        const slug = generateSlug(this.value);
        document.getElementById('edit-slug').value = slug;
    });

    // Form submission
    document.getElementById('categoryForm').addEventListener('submit', handleFormSubmit);
    document.getElementById('editCategoryForm').addEventListener('submit', handleEditSubmit);

    // Search filter
    document.getElementById('search-filter').addEventListener('input', applyFilters);
}

// API Functions
async function loadCategories() {
    try {
        showLoader(true);
        const response = await fetch(`${API_BASE}?action=get_categories`);
        const result = await response.json();
        
        if (result.success) {
            allCategories = result.data;
            filteredCategories = [...allCategories];
            updatePagination();
            displayCategories();
        } else {
            showMessage('error', 'বিভাগসমূহ লোড করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'বিভাগসমূহ লোড করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

async function deleteCategory(id) {
    if (!confirm('আপনি কি নিশ্চিত যে এই বিভাগটি মুছে ফেলতে চান?')) return;
    
    try {
        showLoader(true);
        const formData = new FormData();
        formData.append('action', 'delete_category');
        formData.append('id', id);
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'বিভাগটি সফলভাবে মুছে ফেলা হয়েছে');
            loadCategories();
        } else {
            showMessage('error', 'বিভাগ মুছে ফেলতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'বিভাগ মুছে ফেলার সময় ত্রুটি');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

async function editCategory(id) {
    try {
        showLoader(true);
        const response = await fetch(`${API_BASE}?action=get_category&id=${id}`);
        const result = await response.json();
        
        if (result.success) {
            const category = result.data;
            // Populate edit form
            document.getElementById('edit-category-id').value = category.id;
            document.getElementById('edit-name').value = category.name;
            document.getElementById('edit-slug').value = category.slug;
            document.getElementById('edit-is_active').value = category.is_active;
            
            // Show modal
            document.getElementById('edit-modal').classList.remove('hidden');
        } else {
            showMessage('error', 'সম্পাদনার জন্য বিভাগ লোড করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সম্পাদনার জন্য বিভাগ লোড করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

// Event Handlers
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'create_category');
    formData.append('name', document.getElementById('name').value);
    formData.append('slug', document.getElementById('slug').value);
    formData.append('is_active', document.getElementById('is_active').value);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'যোগ হচ্ছে...';
    submitBtn.disabled = true;
    
    try {
        showLoader(true);
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'বিভাগ সফলভাবে যোগ করা হয়েছে!');
            this.reset();
            loadCategories();
        } else {
            showMessage('error', 'ত্রুটি: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'বিভাগ যোগ করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        showLoader(false);
    }
}

async function handleEditSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'update_category');
    formData.append('id', document.getElementById('edit-category-id').value);
    formData.append('name', document.getElementById('edit-name').value);
    formData.append('slug', document.getElementById('edit-slug').value);
    formData.append('is_active', document.getElementById('edit-is_active').value);
    
    try {
        showLoader(true);
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'বিভাগ সফলভাবে আপডেট হয়েছে!');
            closeEditModal();
            loadCategories();
        } else {
            showMessage('error', 'ত্রুটি: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'বিভাগ আপডেট করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

// Utility Functions
function generateSlug(text) {
    return text.toLowerCase()
              .replace(/[^\w\u0980-\u09FF\s-]/g, '') // Allow Bengali characters
              .replace(/\s+/g, '-')
              .trim();
}

function displayCategories() {
    const tbody = document.getElementById('categories-table-body');
    
    if (!filteredCategories || filteredCategories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #666;">কোনো বিভাগ পাওয়া যায়নি</td></tr>';
        return;
    }
    
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const currentRecords = filteredCategories.slice(startIndex, endIndex);
    
    tbody.innerHTML = currentRecords.map(category => {
        let actionsHtml = '<div class="actions">';
        if (canEdit) {
            actionsHtml += `<button class="btn btn-warning" onclick="editCategory(${category.id})"><i class="fas fa-edit"></i> সম্পাদনা</button>`;
        }
        if (canDelete) {
            actionsHtml += `<button class="btn btn-danger" onclick="deleteCategory(${category.id})"><i class="fas fa-trash"></i> মুছুন</button>`;
        }
        if (!canEdit && !canDelete) {
            actionsHtml += '<span style="color:#999;">-</span>';
        }
        actionsHtml += '</div>';
        
        return `
            <tr>
                <td><strong>#${category.id}</strong></td>
                <td>${category.name}</td>
                <td><code>${category.slug}</code></td>
                <td>
                    <span class="badge ${category.is_active == 1 ? 'badge-success' : 'badge-inactive'}">
                        <i class="fas ${category.is_active == 1 ? 'fa-check-circle' : 'fa-ban'}"></i>
                        ${category.is_active == 1 ? 'সক্রিয়' : 'নিষ্ক্রিয়'}
                    </span>
                </td>
                <td>${actionsHtml}</td>
            </tr>
        `;
    }).join('');
}

function updatePagination() {
    const totalRecords = filteredCategories.length;
    totalPages = Math.ceil(totalRecords / recordsPerPage);
    
    document.getElementById('total-records').textContent = totalRecords;
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('total-pages').textContent = totalPages;
    
    // Update pagination buttons
    document.getElementById('first-btn').disabled = currentPage === 1;
    document.getElementById('prev-btn').disabled = currentPage === 1;
    document.getElementById('next-btn').disabled = currentPage === totalPages;
    document.getElementById('last-btn').disabled = currentPage === totalPages;
    
    // Generate page numbers
    const pageNumbers = document.getElementById('page-numbers');
    pageNumbers.innerHTML = '';
    
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.onclick = () => changePage(i);
        if (i === currentPage) {
            button.classList.add('active');
        }
        pageNumbers.appendChild(button);
    }
}

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayCategories();
    updatePagination();
}

function applyFilters() {
    const searchTerm = document.getElementById('search-filter').value.toLowerCase();
    const statusFilter = document.getElementById('status-filter').value;
    
    filteredCategories = allCategories.filter(category => {
        const matchesSearch = !searchTerm || category.name.toLowerCase().includes(searchTerm);
        const matchesStatus = statusFilter === '' || category.is_active == statusFilter;
        
        return matchesSearch && matchesStatus;
    });
    
    currentPage = 1;
    updatePagination();
    displayCategories();
}

function clearFilters() {
    document.getElementById('search-filter').value = '';
    document.getElementById('status-filter').value = '';
    filteredCategories = [...allCategories];
    currentPage = 1;
    updatePagination();
    displayCategories();
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

function showMessage(type, message) {
    const messageDiv = document.getElementById(type + '-message');
    messageDiv.textContent = message;
    messageDiv.classList.remove('hidden');
    
    setTimeout(() => {
        messageDiv.classList.add('hidden');
    }, 5000);
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showLoader(show) {
    const loader = document.getElementById('loader');
    if (!loader && show) {
        // Create loader if it doesn't exist
        const loaderDiv = document.createElement('div');
        loaderDiv.id = 'loader';
        loaderDiv.style.position = 'fixed';
        loaderDiv.style.top = '0';
        loaderDiv.style.left = '0';
        loaderDiv.style.width = '100%';
        loaderDiv.style.height = '100%';
        loaderDiv.style.backgroundColor = 'rgba(255,255,255,0.7)';
        loaderDiv.style.display = 'flex';
        loaderDiv.style.justifyContent = 'center';
        loaderDiv.style.alignItems = 'center';
        loaderDiv.style.zIndex = '9999';
        loaderDiv.innerHTML = '<div style="font-size: 20px; font-weight: bold;">লোড হচ্ছে...</div>';
        document.body.appendChild(loaderDiv);
    } else if (loader) {
        loader.style.display = show ? 'flex' : 'none';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('edit-modal');
    if (e.target === modal) {
        closeEditModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('edit-modal');
        if (!modal.classList.contains('hidden')) {
            closeEditModal();
        }
    }
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>