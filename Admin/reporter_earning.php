<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporter Earnings - Hindus News</title>
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
            padding: 10px 22px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.25s ease;
            background: #ffffff;
            color: #333;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056d6);
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056d6, #003c9e);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1f7e35);
            color: #fff;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #1f7e35, #145e24);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #212529;
            padding: 8px 18px;
            font-size: 14px;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #ff9800, #e58300);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #b82030);
            color: #fff;
            padding: 8px 18px;
            font-size: 14px;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b82030, #8a1a25);
        }

        .container {
            max-width: 1400px;
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

        .modern-filters {
            background: #f7f9fb;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 18px 24px 10px 24px;
            margin-bottom: 24px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 24px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 140px;
            flex: 1 1 180px;
        }

        .filter-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 15px;
            background: #fff;
            transition: border 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border: 1.5px solid #007bff;
            outline: none;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-left: 8px;
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

        .edit-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .edit-card h3 {
            color: #333;
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .edit-form-field {
            display: flex;
            flex-direction: column;
        }

        .edit-form-field.full-width {
            grid-column: 1 / -1;
        }

        .edit-form-field label {
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .edit-form-field input,
        .edit-form-field textarea {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-form-field input:focus,
        .edit-form-field textarea:focus {
            outline: none;
            border-color: #999;
        }

        .edit-form-field input:disabled,
        .edit-form-field textarea:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
            color: #666;
        }

        .edit-form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .info-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #f5f5f5;
            border-radius: 3px;
            font-size: 11px;
            color: #666;
            font-weight: normal;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .edit-card h3 {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .edit-form-grid {
                grid-template-columns: 1fr;
            }
            
            .info-badge {
                margin-left: 0;
            }
        }

        .summary-box {
            background: linear-gradient(135deg, #e7f3ff, #d4edff);
            padding: 20px;
            border-radius: 12px;
            margin-top: 15px;
            border: 1px solid #b3d9ff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.1);
        }

        .summary-box h3 {
            margin-bottom: 12px;
            color: #004085;
            font-size: 18px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
        }

        .summary-item strong {
            color: #004085;
            font-size: 18px;
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

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        @media (max-width: 768px) {
            .header-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .filter-row {
                flex-direction: column;
                gap: 12px;
            }
            
            .filter-group {
                flex: 1 1 100%;
                width: 100%;
                min-width: 0;
            }
            
            .filter-actions {
                margin-left: 0;
                justify-content: flex-end;
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
            <i class="fas fa-dollar-sign"></i>
            সাংবাদিক আয় ব্যবস্থাপনা
        </h1>
    </div>

    <div class="container">
        <div id="success-message" class="alert alert-success hidden"></div>
        <div id="error-message" class="alert alert-error hidden"></div>

        <!-- Edit Form Card -->
        <div class="form-section">
            <h2>
                <i class="fas fa-edit"></i>
                আয় সম্পাদনা করুন
                <span class="badge badge-info" id="edit-status">কোনো সংবাদ নির্বাচিত নয়</span>
            </h2>
            
            <div class="edit-form-grid">
                <div class="edit-form-field">
                    <label>
                        <i class="fas fa-hashtag"></i>
                        সংবাদ আইডI:
                    </label>
                    <input type="text" id="edit_news_id" disabled>
                </div>
                
                <div class="edit-form-field">
                    <label>
                        <i class="fas fa-calendar"></i>
                        তারিখ:
                    </label>
                    <input type="text" id="edit_news_date" disabled>
                </div>
                
                <div class="edit-form-field full-width">
                    <label>
                        <i class="fas fa-heading"></i>
                        শিরোনাম:
                    </label>
                    <textarea id="edit_news_headline" rows="2" disabled style="resize: vertical;"></textarea>
                </div>
                
                <div class="edit-form-field">
                    <label>
                        <i class="fas fa-dollar-sign"></i>
                        আয় (ডলার):
                    </label>
                    <input type="number" id="edit_news_earning" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="resetEditForm()">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button class="btn btn-primary" id="save-btn" onclick="saveEarningData()" disabled>
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-bar-left">
                <button class="btn btn-success" onclick="loadEarnings()">
                    <i class="fas fa-sync-alt"></i> রিফ্রেশ
                </button>
            </div>
            <div class="action-bar-right">
                <select class="filter-select" id="reporter-select">
                    <option value="">-- সাংবাদিক নির্বাচন করুন --</option>
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
        
        <!-- Stats Cards -->
        <div id="summary-container" class="stats-grid hidden">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <h3 id="total-news">0</h3>
                    <p>মোট সংবাদ</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 id="total-earning">০ $</h3>
                    <p>মোট আয়</p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <h2>
                <i class="fas fa-list"></i>
                আয় বিবরণ
            </h2>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> আইডI</th>
                        <th><i class="fas fa-heading"></i> শিরোনাম</th>
                        <th><i class="fas fa-calendar"></i> তারিখ ও সময়</th>
                        <th><i class="fas fa-dollar-sign"></i> আয়</th>
                        <th><i class="fas fa-cog"></i> কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody id="earnings-table-body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <i class="fas fa-user-tie" style="font-size: 48px; color: #cbd5e0;"></i>
                            <p style="margin-top: 15px; color: #718096;">অনুগ্রহ করে একজন সাংবাদিক নির্বাচন করুন</p>
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
    
    <?php include 'footer.php'; ?>


    <script>
// Permission variables from PHP
const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;

// Global variables
let allReporters = [];
let currentReporterId = null;
let earningsData = [];
let filteredEarningsData = [];
let currentPage = 1;
let recordsPerPage = 10;
let totalPages = 1;

// API endpoints
const API_BASE = 'api.php';

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadReporters();
    setupEventListeners();
});

function setupEventListeners() {
    // No form listeners needed for inline editing
}

// Load reporters for dropdown
async function loadReporters() {
    try {
        const response = await fetch(`${API_BASE}?action=get_reporters`);
        const reporters = await response.json();
        
        const select = document.getElementById('reporter-select');
        select.innerHTML = '<option value="">-- সাংবাদিক নির্বাচন করুন --</option>';
        
        reporters.forEach(reporter => {
            const option = document.createElement('option');
            option.value = reporter.id;
            option.textContent = `${reporter.name} (${reporter.email})`;
            select.appendChild(option);
        });
        
        allReporters = reporters;
    } catch (error) {
        showMessage('error', 'সাংবাদিকদের লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Apply filters
function applyFilters() {
    const reporterId = document.getElementById('reporter-select').value;
    
    if (!reporterId) {
        showMessage('error', 'অনুগ্রহ করে একজন সাংবাদিক নির্বাচন করুন');
        return;
    }
    
    currentPage = 1;
    loadEarnings();
}

// Clear filters
function clearFilters() {
    document.getElementById('reporter-select').value = '';
    document.getElementById('from-date').value = '';
    document.getElementById('to-date').value = '';
    document.getElementById('earnings-table-body').innerHTML = `
        <tr>
            <td colspan="5" class="no-data">অনুগ্রহ করে একজন সাংবাদিক নির্বাচন করুন</td>
        </tr>
    `;
    document.getElementById('summary-container').classList.add('hidden');
    currentPage = 1;
}

// Load earnings for selected reporter
async function loadEarnings() {
    const reporterId = document.getElementById('reporter-select').value;
    
    if (!reporterId) {
        document.getElementById('earnings-table-body').innerHTML = `
            <tr>
                <td colspan="5" class="no-data">অনুগ্রহ করে একজন সাংবাদিক নির্বাচন করুন</td>
            </tr>
        `;
        document.getElementById('summary-container').classList.add('hidden');
        return;
    }
    
    currentReporterId = reporterId;
    
    try {
        const response = await fetch(`${API_BASE}?action=get_reporter_earnings&reporter_id=${reporterId}`);
        const result = await response.json();
        
        if (result.success) {
            earningsData = result.data;
            applyDateFilter();
        } else {
            showMessage('error', 'আয় লোড করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'আয় লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Apply date filter
function applyDateFilter() {
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;
    
    filteredEarningsData = earningsData.filter(item => {
        if (!item.created_at) return true;
        
        const itemDate = item.created_at.split(' ')[0]; // Get date part only
        
        if (fromDate && itemDate < fromDate) return false;
        if (toDate && itemDate > toDate) return false;
        
        return true;
    });
    
    updatePagination();
    displayCurrentPage();
}

// Update pagination
function updatePagination() {
    const totalRecords = filteredEarningsData.length;
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
    const pageData = filteredEarningsData.slice(startIndex, endIndex);
    
    // Calculate total
    const total = filteredEarningsData.reduce((sum, item) => sum + parseFloat(item.earning || 0), 0);
    
    displayEarnings(pageData, total);
}

// Display earnings in table
function displayEarnings(data, total) {
    const tbody = document.getElementById('earnings-table-body');
    
    if (filteredEarningsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="no-data">কোনো সংবাদ পাওয়া যায়নি</td>
            </tr>
        `;
        document.getElementById('summary-container').classList.add('hidden');
        return;
    }
    
    tbody.innerHTML = '';
    data.forEach(item => {
        const row = document.createElement('tr');
        let actionHtml = '';
        if (canEdit) {
            actionHtml = `<button class="btn" onclick="loadNewsDataToEdit(${item.id})">
                    <img src="icons/edit.png" width="20">
                </button>`;
        } else {
            actionHtml = '<span style="color:#999;">-</span>';
        }
        row.innerHTML = `
            <td>${item.id}</td>
            <td style="max-width: 400px;">${item.headline}</td>
            <td>${item.created_at || 'N/A'}</td>
            <td>${parseFloat(item.earning || 0).toFixed(2)}</td>
            <td>${actionHtml}</td>
        `;
        tbody.appendChild(row);
    });
    
    // Update summary
    document.getElementById('total-news').textContent = filteredEarningsData.length;
    document.getElementById('total-earning').textContent = parseFloat(total || 0).toFixed(2) + ' ডলার';
    document.getElementById('summary-container').classList.remove('hidden');
}

// Load news data into edit form via API
function loadNewsDataToEdit(newsId) {
    // Show loading state
    document.getElementById('edit-status').textContent = 'লোড হচ্ছে...';
    document.getElementById('save-btn').disabled = true;
    
    // Make API call to get news details
    fetch(`${API_BASE}?action=get_news_detail&news_id=${newsId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.news) {
                const news = data.news;
                
                // Populate form fields
                document.getElementById('edit_news_id').value = news.id;
                document.getElementById('edit_news_date').value = news.created_at || 'N/A';
                document.getElementById('edit_news_headline').value = news.headline || '';
                document.getElementById('edit_news_earning').value = parseFloat(news.earning || 0).toFixed(2);
                
                // Update status
                document.getElementById('edit-status').textContent = '✓ সংবাদ লোড হয়েছে';
                document.getElementById('edit-status').style.background = '#d4edda';
                document.getElementById('edit-status').style.color = '#155724';
                
                // Enable save button
                document.getElementById('save-btn').disabled = false;
                
                // Focus on earning input
                document.getElementById('edit_news_earning').focus();
                document.getElementById('edit_news_earning').select();
                
                // Scroll to form
                const editSection = document.querySelector('.form-section');
                if (editSection) {
                    editSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                showMessage('error', 'সংবাদ লোড করতে ব্যর্থ');
                resetEditForm();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', 'সংবাদ লোড করতে ত্রুটি');
            resetEditForm();
        });
}

// Reset edit form
function resetEditForm() {
    document.getElementById('edit_news_id').value = '';
    document.getElementById('edit_news_date').value = '';
    document.getElementById('edit_news_headline').value = '';
    document.getElementById('edit_news_earning').value = '';
    document.getElementById('edit-status').textContent = 'কোনো সংবাদ নির্বাচিত নয়';
    document.getElementById('edit-status').style.background = '#f5f5f5';
    document.getElementById('edit-status').style.color = '#666';
    document.getElementById('save-btn').disabled = true;
}

// Save earning data
function saveEarningData() {
    const newsId = document.getElementById('edit_news_id').value;
    const earning = document.getElementById('edit_news_earning').value;
    
    if (!newsId) {
        showMessage('error', 'অনুগ্রহ করে একটি সংবাদ নির্বাচন করুন');
        return;
    }
    
    if (!earning || earning < 0) {
        showMessage('error', 'অনুগ্রহ করে একটি বৈধ আয়ের পরিমাণ লিখুন');
        return;
    }
    
    const saveBtn = document.getElementById('save-btn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'সংরক্ষণ হচ্ছে...';
    saveBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'update_earning');
    formData.append('news_id', newsId);
    formData.append('earning', earning);
    
    fetch(API_BASE, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showMessage('success', 'আয় সফলভাবে আপডেট করা হয়েছে!');
            resetEditForm();
            loadEarnings();
        } else {
            showMessage('error', 'ত্রুটি: ' + result.message);
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'আয় আপডেট করতে ব্যর্থ');
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// Show message
function showMessage(type, message) {
    const successDiv = document.getElementById('success-message');
    const errorDiv = document.getElementById('error-message');
    
    successDiv.classList.add('hidden');
    errorDiv.classList.add('hidden');
    
    if (type === 'success') {
        successDiv.textContent = message;
        successDiv.classList.remove('hidden');
        setTimeout(() => successDiv.classList.add('hidden'), 5000);
    } else {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        setTimeout(() => errorDiv.classList.add('hidden'), 5000);
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
    </script>
</body>
</html>
