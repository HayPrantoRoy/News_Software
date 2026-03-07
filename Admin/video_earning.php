<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Earnings - Hindus News</title>
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


        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .hidden {
            display: none;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .form-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .form-section h2 i {
            color: #308e87;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-field.full-width {
            grid-column: 1 / -1;
        }

        .form-field label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-field label i {
            color: #308e87;
            font-size: 16px;
        }

        .form-field input,
        .form-field textarea,
        .form-field select {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'SolaimanLipi', Arial, sans-serif;
            transition: all 0.3s ease;
        }

        .form-field input:focus,
        .form-field textarea:focus,
        .form-field select:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
        }

        .form-field textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-family: 'SolaimanLipi', Arial, sans-serif;
        }

        .btn i {
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #308e87, #2a7a73);
            color: white;
            box-shadow: 0 2px 8px rgba(48, 142, 135, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(48, 142, 135, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1f7e35);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #212529;
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #b82030);
            color: white;
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .action-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-bar-left,
        .action-bar-right {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            min-width: 200px;
            font-family: 'SolaimanLipi', Arial, sans-serif;
        }

        .filter-select:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #308e87, #2a7a73);
            color: white;
        }

        thead th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff !important;
            cursor: default;
        }

        tbody td {
            padding: 16px;
            border-bottom: 1px solid #e9ecef;
            font-size: 15px;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #6c757d;
            font-style: italic;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .pagination-info span {
            color: #308e87;
            font-weight: 700;
        }

        .pagination-buttons {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .pagination-buttons button {
            padding: 8px 14px;
            font-size: 13px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-buttons button:hover:not(:disabled) {
            background: #f8f9fa;
            border-color: #308e87;
            color: #308e87;
        }

        .pagination-buttons button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-buttons button.active {
            background: linear-gradient(135deg, #308e87, #2a7a73);
            color: white;
            border-color: #308e87;
        }

        @media (max-width: 768px) {
            .pagination {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .pagination-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }

            .pagination-buttons button {
                padding: 6px 12px;
                font-size: 12px;
            }
            .page-header {
                padding: 20px;
                margin: -20px -20px 20px -20px;
            }

            .page-header h1 {
                font-size: 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .action-bar-left,
            .action-bar-right {
                width: 100%;
                flex-direction: column;
            }

            .filter-select {
                width: 100%;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <div class="page-header">
        <h1>
            <i class="fas fa-video"></i>
            ভিডিও আয় ব্যবস্থাপনা
        </h1>
    </div>

    <div class="container">
        <div id="success-message" class="alert alert-success hidden"></div>
        <div id="error-message" class="alert alert-error hidden"></div>

        <!-- Add/Edit Video Earning Form -->
        <div class="form-section">
            <h2>
                <i class="fas fa-plus-circle" id="form-icon"></i>
                <span id="form-title">নতুন ভিডিও আয় যোগ করুন</span>
                <span class="badge badge-info" id="form-mode" style="display: none;">সম্পাদনা মোড</span>
            </h2>
            
            <input type="hidden" id="edit_id" value="">
            
            <div class="form-grid">
                <div class="form-field">
                    <label>
                        <i class="fas fa-user"></i>
                        সাংবাদিক নির্বাচন করুন:
                        <span style="color: red;">*</span>
                    </label>
                    <select id="add_reporter_id" required>
                        <option value="">-- সাংবাদিক নির্বাচন করুন --</option>
                    </select>
                </div>
                
                <div class="form-field">
                    <label>
                        <i class="fas fa-dollar-sign"></i>
                        আয় (ডলার):
                    </label>
                    <input type="number" id="add_earning" step="0.01" min="0" value="0" placeholder="0.00">
                </div>
                
                <div class="form-field full-width">
                    <label>
                        <i class="fas fa-heading"></i>
                        ভিডিও শিরোনাম:
                        <span style="color: red;">*</span>
                    </label>
                    <textarea id="add_video_headline" rows="3" placeholder="ভিডিও শিরোনাম লিখুন..." required></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="resetAddForm()">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button class="btn btn-primary" onclick="saveVideoEarning()" id="save-button">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-bar-left">
                <button class="btn btn-success" onclick="loadVideoEarnings()">
                    <i class="fas fa-sync-alt"></i> রিফ্রেশ
                </button>
            </div>
            <div class="action-bar-right">
                <select class="filter-select" id="filter-reporter-select">
                    <option value="">-- সকল সাংবাদিক --</option>
                </select>
                <input type="date" class="filter-select" id="from-date" placeholder="শুরুর তারিখ">
                <input type="date" class="filter-select" id="to-date" placeholder="শেষ তারিখ">
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i> ফিল্টার
                </button>
                <button class="btn btn-info" onclick="clearFilters()">
                    <i class="fas fa-redo"></i> পরিষ্কার
                </button>
            </div>
        </div>
        
        <!-- Video Earnings Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>আইডি</th>
                        <th>সাংবাদিক</th>
                        <th>ভিডিও শিরোনাম</th>
                        <th>আয় ($)</th>
                        <th>তারিখ</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="earnings-table-body">
                    <tr>
                        <td colspan="6" class="no-data">লোড হচ্ছে...</td>
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

<script>
// Permission variables from PHP
const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;

let allEarnings = [];
let filteredEarnings = [];
let allReporters = [];

// Pagination variables
let currentPage = 1;
let totalPages = 1;
let recordsPerPage = 10;

// API endpoints
const API_BASE = 'api.php';

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadReporters();
    loadVideoEarnings();
});

// Load reporters for dropdowns
async function loadReporters() {
    try {
        const response = await fetch(`${API_BASE}?action=get_reporters`);
        const reporters = await response.json();
        
        // Populate add form dropdown
        const addSelect = document.getElementById('add_reporter_id');
        addSelect.innerHTML = '<option value="">-- সাংবাদিক নির্বাচন করুন --</option>';
        
        // Populate filter dropdown
        const filterSelect = document.getElementById('filter-reporter-select');
        filterSelect.innerHTML = '<option value="">-- সকল সাংবাদিক --</option>';
        
        reporters.forEach(reporter => {
            const option1 = document.createElement('option');
            option1.value = reporter.id;
            option1.textContent = `${reporter.name} (${reporter.email})`;
            addSelect.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = reporter.id;
            option2.textContent = `${reporter.name}`;
            filterSelect.appendChild(option2);
        });
        
        allReporters = reporters;
    } catch (error) {
        showMessage('error', 'সাংবাদিকদের লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Load video earnings
async function loadVideoEarnings() {
    try {
        const response = await fetch(`${API_BASE}?action=get_video_earnings`);
        const result = await response.json();
        
        if (result.success) {
            allEarnings = result.data;
            filteredEarnings = allEarnings;
            currentPage = 1;
            updatePagination();
            displayCurrentPage();
        } else {
            showMessage('error', 'ভিডিও আয় লোড করতে ব্যর্থ');
        }
    } catch (error) {
        showMessage('error', 'ভিডিও আয় লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Apply filters
function applyFilters() {
    const reporterId = document.getElementById('filter-reporter-select').value;
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;
    
    filteredEarnings = allEarnings.filter(earning => {
        let matches = true;
        
        if (reporterId && earning.reporter_id != reporterId) {
            matches = false;
        }
        
        if (fromDate && earning.created_at < fromDate) {
            matches = false;
        }
        
        if (toDate && earning.created_at > toDate) {
            matches = false;
        }
        
        return matches;
    });
    
    currentPage = 1;
    updatePagination();
    displayCurrentPage();
}

// Clear filters
function clearFilters() {
    document.getElementById('filter-reporter-select').value = '';
    document.getElementById('from-date').value = '';
    document.getElementById('to-date').value = '';
    filteredEarnings = allEarnings;
    currentPage = 1;
    updatePagination();
    displayCurrentPage();
}

// Update pagination
function updatePagination() {
    const totalRecords = filteredEarnings.length;
    totalPages = Math.ceil(totalRecords / recordsPerPage) || 1;
    
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    
    document.getElementById('total-records').textContent = totalRecords;
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('total-pages').textContent = totalPages;
    
    // Update pagination buttons
    document.getElementById('first-btn').disabled = currentPage === 1;
    document.getElementById('prev-btn').disabled = currentPage === 1;
    document.getElementById('next-btn').disabled = currentPage === totalPages;
    document.getElementById('last-btn').disabled = currentPage === totalPages;
    
    // Generate page numbers
    const pageNumbersContainer = document.getElementById('page-numbers');
    pageNumbersContainer.innerHTML = '';
    
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.onclick = () => changePage(i);
        if (i === currentPage) {
            btn.classList.add('active');
        }
        pageNumbersContainer.appendChild(btn);
    }
}

// Change page
function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayCurrentPage();
    updatePagination();
}

// Display current page
function displayCurrentPage() {
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageData = filteredEarnings.slice(startIndex, endIndex);
    
    renderTable(pageData);
}

// Render table
function renderTable(data = filteredEarnings) {
    const tbody = document.getElementById('earnings-table-body');
    
    if (filteredEarnings.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-data">কোনো ডেটা পাওয়া যায়নি</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(earning => {
        let actionsHtml = '';
        if (canEdit) {
            actionsHtml += `<button class="btn btn-warning" onclick="editVideoEarning(${earning.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>`;
        }
        if (canDelete) {
            actionsHtml += `<button class="btn btn-danger" onclick="deleteVideoEarning(${earning.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>`;
        }
        if (!canEdit && !canDelete) {
            actionsHtml = '<span style="color:#999;">-</span>';
        }
        return `
        <tr>
            <td>${earning.id}</td>
            <td>${earning.reporter_name}</td>
            <td>${earning.video_headline}</td>
            <td>$${parseFloat(earning.earning).toFixed(2)}</td>
            <td>${formatDate(earning.created_at)}</td>
            <td>${actionsHtml}</td>
        </tr>
    `;
    }).join('');
}

// Save video earning (Add or Update)
async function saveVideoEarning() {
    const editId = document.getElementById('edit_id').value;
    const reporterId = document.getElementById('add_reporter_id').value;
    const videoHeadline = document.getElementById('add_video_headline').value.trim();
    const earning = document.getElementById('add_earning').value;
    
    if (!reporterId) {
        showMessage('error', 'অনুগ্রহ করে একজন সাংবাদিক নির্বাচন করুন');
        return;
    }
    
    if (!videoHeadline) {
        showMessage('error', 'অনুগ্রহ করে ভিডিও শিরোনাম লিখুন');
        return;
    }
    
    try {
        const formData = new FormData();
        
        if (editId) {
            // Update existing entry
            formData.append('action', 'update_video_earning');
            formData.append('id', editId);
            formData.append('earning', earning || 0);
        } else {
            // Add new entry
            formData.append('action', 'add_video_earning');
            formData.append('reporter_id', reporterId);
            formData.append('video_headline', videoHeadline);
            formData.append('earning', earning || 0);
        }
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            if (editId) {
                showMessage('success', 'ভিডিও আয় সফলভাবে আপডেট করা হয়েছে');
            } else {
                showMessage('success', 'ভিডিও আয় সফলভাবে যোগ করা হয়েছে');
            }
            resetAddForm();
            loadVideoEarnings();
        } else {
            showMessage('error', editId ? 'আপডেট করতে ব্যর্থ: ' : 'যোগ করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', editId ? 'আপডেট করতে ব্যর্থ' : 'যোগ করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Edit video earning - Load data into form
async function editVideoEarning(id) {
    const earning = filteredEarnings.find(e => parseInt(e.id) === parseInt(id));
    if (!earning) {
        console.error('Earning not found for id:', id);
        return;
    }
    
    // Populate form with existing data
    document.getElementById('edit_id').value = earning.id;
    document.getElementById('add_reporter_id').value = earning.reporter_id;
    document.getElementById('add_video_headline').value = earning.video_headline;
    document.getElementById('add_earning').value = earning.earning;
    
    // Update form UI to show edit mode
    document.getElementById('form-title').textContent = 'ভিডিও আয় সম্পাদনা করুন';
    document.getElementById('form-icon').className = 'fas fa-edit';
    document.getElementById('form-mode').style.display = 'inline-block';
    document.getElementById('save-button').innerHTML = '<i class="fas fa-save"></i> আপডেট করুন';
    
    // Disable reporter and headline fields in edit mode
    document.getElementById('add_reporter_id').disabled = true;
    document.getElementById('add_video_headline').disabled = true;
    
    // Scroll to form
    const formSection = document.querySelector('.form-section');
    if (formSection) {
        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Focus on earning input
    document.getElementById('add_earning').focus();
    document.getElementById('add_earning').select();
}

// Delete video earning
async function deleteVideoEarning(id) {
    if (!confirm('আপনি কি নিশ্চিত যে এই ভিডিও আয় মুছে ফেলতে চান?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_video_earning');
        formData.append('id', id);
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', 'ভিডিও আয় সফলভাবে মুছে ফেলা হয়েছে');
            loadVideoEarnings();
        } else {
            showMessage('error', 'মুছে ফেলতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'মুছে ফেলতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Reset add form
function resetAddForm() {
    // Clear form fields
    document.getElementById('edit_id').value = '';
    document.getElementById('add_reporter_id').value = '';
    document.getElementById('add_video_headline').value = '';
    document.getElementById('add_earning').value = '0';
    
    // Reset form UI to add mode
    document.getElementById('form-title').textContent = 'নতুন ভিডিও আয় যোগ করুন';
    document.getElementById('form-icon').className = 'fas fa-plus-circle';
    document.getElementById('form-mode').style.display = 'none';
    document.getElementById('save-button').innerHTML = '<i class="fas fa-save"></i> Save';
    
    // Re-enable all fields
    document.getElementById('add_reporter_id').disabled = false;
    document.getElementById('add_video_headline').disabled = false;
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Show message
function showMessage(type, message) {
    const successDiv = document.getElementById('success-message');
    const errorDiv = document.getElementById('error-message');
    
    successDiv.classList.add('hidden');
    errorDiv.classList.add('hidden');
    
    if (type === 'success') {
        successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        successDiv.classList.remove('hidden');
        setTimeout(() => successDiv.classList.add('hidden'), 5000);
    } else {
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        errorDiv.classList.remove('hidden');
        setTimeout(() => errorDiv.classList.add('hidden'), 5000);
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>
