<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Earnings</title>
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

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
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

        tfoot {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            font-weight: bold;
        }

        tfoot td {
            padding: 20px 16px;
            font-size: 16px;
            color: #2c3e50;
            border-top: 3px solid #308e87;
        }

        .total-value {
            font-size: 18px;
            color: #308e87;
            font-weight: 700;
        }

        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #6c757d;
            font-style: italic;
        }

        .editable-cell {
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .editable-cell:hover {
            background: #fff3cd;
        }

        .editable-cell::after {
            content: '\f304';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #6c757d;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .editable-cell:hover::after {
            opacity: 1;
        }

        .editing-cell {
            background: #fff3cd !important;
            padding: 8px !important;
        }

        .edit-input {
            width: 100%;
            padding: 8px;
            border: 2px solid #308e87;
            border-radius: 4px;
            font-size: 15px;
            font-family: inherit;
        }

        .edit-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.2);
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.news {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .stat-icon.video {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #27ae60, #229954);
        }

        .stat-content h3 {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .stat-content p {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
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
                min-width: 800px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <div class="page-header">
        <h1>
            <i class="fas fa-money-bill-wave"></i>
            মোট আয় ব্যবস্থাপনা
        </h1>
    </div>

    <div class="container">
        <div id="success-message" class="alert alert-success hidden"></div>
        <div id="error-message" class="alert alert-error hidden"></div>

        <!-- Statistics Cards -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-card">
                <div class="stat-icon news">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <h3>মোট আয়</h3>
                    <p id="totalEarningStat">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon news">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="stat-content">
                    <h3>ওয়েবসাইট মোট আয়</h3>
                    <p id="totalWebEarningStat">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon video">
                    <i class="fas fa-youtube"></i>
                </div>
                <div class="stat-content">
                    <h3>ইউটিউব মোট আয়</h3>
                    <p id="totalYoutubeEarningStat">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>সর্বমোট আয়</h3>
                    <p id="grandTotal">$0.00</p>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-bar-left">
                <button class="btn btn-success" onclick="loadAllEarnings()">
                    <i class="fas fa-sync-alt"></i> রিফ্রেশ
                </button>
            </div>
            <div class="action-bar-right">
                <select class="filter-select" id="filter-reporter-select">
                    <option value="">-- সকল সাংবাদিক --</option>
                </select>
                <select class="filter-select" id="filter-type-select">
                    <option value="">-- সকল ধরন --</option>
                    <option value="news">নিউজ</option>
                    <option value="video">ভিডিও</option>
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
        
        <!-- Earnings Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>আইডি</th>
                        <th>সাংবাদিক</th>
                        <th>শিরোনাম</th>
                        <th>ধরন</th>
                        <th>নিউজ আয় ($)</th>
                        <th>ওয়েব আয় ($)</th>
                        <th>ইউটিউব আয় ($)</th>
                        <th>মোট আয় ($)</th>
                        <th>তারিখ</th>
                    </tr>
                </thead>
                <tbody id="earnings-table-body">
                    <tr>
                        <td colspan="9" class="no-data">লোড হচ্ছে...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">সর্বমোট আয়:</td>
                        <td class="total-value" id="totalEarning">$0.00</td>
                        <td class="total-value" id="totalWebEarning">$0.00</td>
                        <td class="total-value" id="totalYoutubeEarning">$0.00</td>
                        <td class="total-value" id="tableTotal">$0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
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

let currentPage = 1;
let totalPages = 1;
let recordsPerPage = 10;

const API_BASE = 'api.php';

document.addEventListener('DOMContentLoaded', function() {
    loadReporters();
    loadAllEarnings();
});

async function loadReporters() {
    try {
        const response = await fetch(`${API_BASE}?action=get_reporters`);
        const reporters = await response.json();
        
        const filterSelect = document.getElementById('filter-reporter-select');
        filterSelect.innerHTML = '<option value="">-- সকল সাংবাদিক --</option>';
        
        reporters.forEach(reporter => {
            const option = document.createElement('option');
            option.value = reporter.id;
            option.textContent = reporter.name;
            filterSelect.appendChild(option);
        });
        
        allReporters = reporters;
    } catch (error) {
        showMessage('error', 'সাংবাদিকদের লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

async function loadAllEarnings() {
    try {
        const response = await fetch(`${API_BASE}?action=get_all_earnings`);
        const result = await response.json();
        
        if (result.success) {
            allEarnings = result.data || [];
            filteredEarnings = allEarnings;
            currentPage = 1;
            updateStatistics();
            updatePagination();
            displayCurrentPage();
        } else {
            showMessage('error', 'আয় লোড করতে ব্যর্থ');
        }
    } catch (error) {
        showMessage('error', 'আয় লোড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

function updateStatistics() {
    let earningTotal = 0;
    let webTotal = 0;
    let youtubeTotal = 0;
    
    allEarnings.forEach(earning => {
        const earningAmount = parseFloat(earning.earning);
        const webAmount = parseFloat(earning.web_earning);
        const youtubeAmount = parseFloat(earning.youtube_earning);
        earningTotal += isNaN(earningAmount) ? 0 : earningAmount;
        webTotal += isNaN(webAmount) ? 0 : webAmount;
        youtubeTotal += isNaN(youtubeAmount) ? 0 : youtubeAmount;
    });
    
    const grandTotalValue = earningTotal + webTotal + youtubeTotal;
    
    document.getElementById('totalEarningStat').textContent = '$' + earningTotal.toFixed(2);
    document.getElementById('totalWebEarningStat').textContent = '$' + webTotal.toFixed(2);
    document.getElementById('totalYoutubeEarningStat').textContent = '$' + youtubeTotal.toFixed(2);
    document.getElementById('grandTotal').textContent = '$' + grandTotalValue.toFixed(2);
}

function applyFilters() {
    const reporterId = document.getElementById('filter-reporter-select').value;
    const type = document.getElementById('filter-type-select').value;
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;
    
    filteredEarnings = allEarnings.filter(earning => {
        if (reporterId && earning.reporter_id != reporterId) {
            return false;
        }
        
        if (type && earning.type !== type) {
            return false;
        }
        
        if (fromDate || toDate) {
            if (!earning.created_at) {
                return false;
            }
            
            let earningDate = String(earning.created_at).substring(0, 10);
            
            if (fromDate && earningDate < fromDate) {
                return false;
            }
            
            if (toDate && earningDate > toDate) {
                return false;
            }
        }
        
        return true;
    });
    
    currentPage = 1;
    updatePagination();
    displayCurrentPage();
}

function clearFilters() {
    document.getElementById('filter-reporter-select').value = '';
    document.getElementById('filter-type-select').value = '';
    document.getElementById('from-date').value = '';
    document.getElementById('to-date').value = '';
    filteredEarnings = allEarnings;
    currentPage = 1;
    updatePagination();
    displayCurrentPage();
}

function updatePagination() {
    const totalRecords = filteredEarnings.length;
    totalPages = Math.ceil(totalRecords / recordsPerPage) || 1;
    
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    
    document.getElementById('total-records').textContent = totalRecords;
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('total-pages').textContent = totalPages;
    
    document.getElementById('first-btn').disabled = currentPage === 1;
    document.getElementById('prev-btn').disabled = currentPage === 1;
    document.getElementById('next-btn').disabled = currentPage === totalPages;
    document.getElementById('last-btn').disabled = currentPage === totalPages;
    
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

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayCurrentPage();
    updatePagination();
}

function displayCurrentPage() {
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageData = filteredEarnings.slice(startIndex, endIndex);
    
    renderTable(pageData);
    updateTableTotal();
}

function renderTable(data) {
    const tbody = document.getElementById('earnings-table-body');
    
    if (filteredEarnings.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="no-data">কোনো ডেটা পাওয়া যায়নি</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map((earning) => {
        const earningAmount = parseFloat(earning.earning) || 0;
        const webEarning = parseFloat(earning.web_earning) || 0;
        const youtubeEarning = parseFloat(earning.youtube_earning) || 0;
        const totalEarning = earningAmount + webEarning + youtubeEarning;
        
        // Only add editable-cell class if user has edit permission
        const editableClass = canEdit ? 'editable-cell' : '';
        
        return `
        <tr data-row-id="${earning.id}-${earning.type}">
            <td>${earning.id}</td>
            <td>${escapeHtml(earning.reporter_name)}</td>
            <td style="max-width: 400px;">${escapeHtml(earning.headline)}</td>
            <td>
                <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; ${earning.type === 'news' ? 'background: #d1ecf1; color: #0c5460;' : 'background: #f8d7da; color: #721c24;'}">
                    ${earning.type === 'news' ? 'নিউজ' : 'ভিডিও'}
                </span>
            </td>
            <td class="${editableClass}" data-id="${earning.id}" data-type="${earning.type}" data-field="earning" data-current="${earningAmount}">
                $${earningAmount.toFixed(2)}
            </td>
            <td ${earning.type === 'news' && canEdit ? 'class="editable-cell"' : ''} data-id="${earning.id}" data-type="${earning.type}" data-field="web_earning" data-current="${webEarning}">
                ${earning.type === 'news' ? '$' + webEarning.toFixed(2) : '-'}
            </td>
            <td ${earning.type === 'video' && canEdit ? 'class="editable-cell"' : ''} data-id="${earning.id}" data-type="${earning.type}" data-field="youtube_earning" data-current="${youtubeEarning}">
                ${earning.type === 'video' ? '$' + youtubeEarning.toFixed(2) : '-'}
            </td>
            <td style="font-weight: 700; color: #308e87;">
                $${totalEarning.toFixed(2)}
            </td>
            <td>${formatDate(earning.created_at)}</td>
        </tr>
    `;
    }).join('');
    
    attachEditableListeners();
}

function attachEditableListeners() {
    const editableCells = document.querySelectorAll('.editable-cell');
    editableCells.forEach(cell => {
        cell.addEventListener('click', function() {
            if (this.querySelector('.edit-input')) return;
            
            const currentValue = parseFloat(this.dataset.current);
            const validCurrentValue = isNaN(currentValue) ? 0 : currentValue;
            const id = this.dataset.id;
            const type = this.dataset.type;
            const field = this.dataset.field; // earning, web_earning or youtube_earning
            const row = this.closest('tr');
            
            this.classList.add('editing-cell');
            const originalContent = this.innerHTML;
            
            // Show input with save and cancel buttons inline
            this.innerHTML = `
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="number" class="edit-input" value="${validCurrentValue.toFixed(2)}" step="0.01" min="0" style="width: 100px;">
                    <button class="btn btn-success" style="padding: 6px 12px; font-size: 13px;" onclick="event.stopPropagation(); saveEarning('${id}', '${type}', '${field}', this)">
                        <i class="fas fa-save"></i> সেভ
                    </button>
                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;" onclick="event.stopPropagation(); cancelEdit('${id}', '${type}', '${field}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            const input = this.querySelector('.edit-input');
            input.focus();
            input.select();
            
            // Store original content in cell data attribute
            cell.dataset.originalContent = originalContent;
            
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const saveBtn = cell.querySelector('.btn-success');
                    if (saveBtn) saveBtn.click();
                } else if (e.key === 'Escape') {
                    const cancelBtn = cell.querySelector('.btn-secondary');
                    if (cancelBtn) cancelBtn.click();
                }
            });
        });
    });
}

async function saveEarning(id, type, field, button) {
    const cell = button.closest('.editable-cell');
    const input = cell.querySelector('.edit-input');
    const originalContent = cell.dataset.originalContent;
    
    const newValue = parseFloat(input.value);
    const validNewValue = isNaN(newValue) ? 0 : newValue;
    const currentValue = parseFloat(cell.dataset.current);
    const validCurrentValue = isNaN(currentValue) ? 0 : currentValue;
    
    if (validNewValue === validCurrentValue) {
        cell.classList.remove('editing-cell');
        cell.innerHTML = originalContent;
        return;
    }
    
    // Disable button during save
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const formData = new FormData();
        formData.append('action', type === 'news' ? 'update_news_earning_field' : 'update_video_earning_field');
        formData.append(type === 'news' ? 'news_id' : 'id', id);
        formData.append('field', field); // web_earning or youtube_earning
        formData.append('value', validNewValue);
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            cell.dataset.current = validNewValue;
            cell.classList.remove('editing-cell');
            cell.innerHTML = '$' + validNewValue.toFixed(2);
            
            const earning = allEarnings.find(e => e.id == id && e.type === type);
            if (earning) {
                earning[field] = validNewValue;
            }
            
            // Update total column in the row
            const row = cell.closest('tr');
            const earningCell = row.querySelector('[data-field="earning"]');
            const webCell = row.querySelector('[data-field="web_earning"]');
            const youtubeCell = row.querySelector('[data-field="youtube_earning"]');
            const earningVal = parseFloat(earningCell.dataset.current) || 0;
            const webVal = parseFloat(webCell.dataset.current) || 0;
            const youtubeVal = parseFloat(youtubeCell.dataset.current) || 0;
            const totalCell = row.children[7]; // Total column
            totalCell.textContent = '$' + (earningVal + webVal + youtubeVal).toFixed(2);
            
            updateStatistics();
            updateTableTotal();
        } else {
            throw new Error(result.message || 'Update failed');
        }
    } catch (error) {
        showMessage('error', 'আয় আপডেট করতে ব্যর্থ');
        cell.classList.remove('editing-cell');
        cell.innerHTML = originalContent;
        console.error('Error:', error);
    }
}

function cancelEdit(id, type, field) {
    const row = document.querySelector(`tr[data-row-id="${id}-${type}"]`);
    if (!row) return;
    
    const cell = row.querySelector(`.editable-cell[data-field="${field}"]`);
    if (!cell) return;
    
    const originalContent = cell.dataset.originalContent;
    
    cell.classList.remove('editing-cell');
    cell.innerHTML = originalContent;
}

function updateTableTotal() {
    let earningTotal = 0;
    let webTotal = 0;
    let youtubeTotal = 0;
    
    filteredEarnings.forEach(earning => {
        const earningAmount = parseFloat(earning.earning);
        const webAmount = parseFloat(earning.web_earning);
        const youtubeAmount = parseFloat(earning.youtube_earning);
        earningTotal += isNaN(earningAmount) ? 0 : earningAmount;
        webTotal += isNaN(webAmount) ? 0 : webAmount;
        youtubeTotal += isNaN(youtubeAmount) ? 0 : youtubeAmount;
    });
    
    document.getElementById('totalEarning').textContent = '$' + earningTotal.toFixed(2);
    document.getElementById('totalWebEarning').textContent = '$' + webTotal.toFixed(2);
    document.getElementById('totalYoutubeEarning').textContent = '$' + youtubeTotal.toFixed(2);
    document.getElementById('tableTotal').textContent = '$' + (earningTotal + webTotal + youtubeTotal).toFixed(2);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

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
