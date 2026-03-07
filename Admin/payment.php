<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Hindus News</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="modern_admin_styles.css">
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .container { max-width: 1400px; margin: 0 auto; padding: 0; }
        
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
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .hidden { display: none; }
        
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 25px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .tab-btn {
            flex: 1;
            padding: 16px 24px;
            border: none;
            background: white;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
        }
        .tab-btn:hover { background: #f8f9fa; color: #308e87; }
        .tab-btn.active {
            background: linear-gradient(135deg, #308e87, #2a7a73);
            color: white;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
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
        .action-bar-left, .action-bar-right {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-select, .filter-input {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            min-width: 180px;
            font-family: inherit;
        }
        .filter-select:focus, .filter-input:focus {
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
            font-family: inherit;
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
        }
        .btn-success:hover { transform: translateY(-2px); }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        .btn-danger:hover { transform: translateY(-2px); }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }
        .stat-icon.pending { background: linear-gradient(135deg, #ffc107, #e0a800); }
        .stat-icon.paid { background: linear-gradient(135deg, #28a745, #1f7e35); }
        .stat-icon.total { background: linear-gradient(135deg, #308e87, #2a7a73); }
        .stat-icon.selected { background: linear-gradient(135deg, #17a2b8, #138496); }
        .stat-info h4 { font-size: 13px; color: #6c757d; margin-bottom: 4px; }
        .stat-info p { font-size: 22px; font-weight: 700; color: #2c3e50; }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: linear-gradient(135deg, #308e87, #2a7a73); color: white; }
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
            padding: 14px 16px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        tbody tr:hover { background: #f8f9fa; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr.selected { background: #e8f5e9; }
        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #6c757d;
            font-style: italic;
        }
        
        .checkbox-cell {
            width: 50px;
            text-align: center;
        }
        .payment-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #308e87;
        }
        
        .type-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-badge.news { background: #d1ecf1; color: #0c5460; }
        .type-badge.video { background: #f8d7da; color: #721c24; }
        
        .earning-amount {
            font-weight: 700;
            color: #308e87;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-top: 1px solid #e9ecef;
        }
        .pagination-info { font-size: 14px; color: #6c757d; }
        .pagination-info span { color: #308e87; font-weight: 700; }
        .pagination-buttons { display: flex; gap: 6px; }
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
        .pagination-buttons button:disabled { opacity: 0.5; cursor: not-allowed; }
        .pagination-buttons button.active {
            background: linear-gradient(135deg, #308e87, #2a7a73);
            color: white;
            border-color: #308e87;
        }
        
        tfoot { background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%); font-weight: bold; }
        tfoot td { padding: 16px; font-size: 15px; border-top: 3px solid #308e87; }
        
        .select-all-row {
            background: #f8f9fa;
        }
        .select-all-row td {
            padding: 12px 16px;
            border-bottom: 2px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .action-bar { flex-direction: column; align-items: stretch; }
            .action-bar-left, .action-bar-right { flex-direction: column; width: 100%; }
            .filter-select, .filter-input { width: 100%; min-width: unset; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .table-container { overflow-x: auto; }
            table { min-width: 900px; }
        }
    </style>
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container">
    <div id="success-message" class="alert alert-success hidden"></div>
    <div id="error-message" class="alert alert-error hidden"></div>
    
    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('pending')">
            <i class="fas fa-clock"></i> পেমেন্ট বাকি
        </button>
        <button class="tab-btn" onclick="switchTab('history')">
            <i class="fas fa-history"></i> পেমেন্ট হিস্টোরি
        </button>
    </div>
    
    <!-- Pending Payments Tab -->
    <div id="pending-tab" class="tab-content active">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h4>মোট বাকি</h4>
                    <p id="pendingTotal">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon selected"><i class="fas fa-check-square"></i></div>
                <div class="stat-info">
                    <h4>সিলেক্টেড</h4>
                    <p id="selectedTotal">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h4>রিপোর্টার সংখ্যা</h4>
                    <p id="reporterCount">0</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="action-bar">
            <div class="action-bar-left">
                <select id="reporterFilter" class="filter-select" onchange="filterData()">
                    <option value="">সকল রিপোর্টার</option>
                </select>
                <input type="date" id="dateFrom" class="filter-input" onchange="filterData()" placeholder="থেকে">
                <input type="date" id="dateTo" class="filter-input" onchange="filterData()" placeholder="পর্যন্ত">
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> রিসেট
                </button>
            </div>
            <div class="action-bar-right">
                <?php if ($can_edit): ?>
                <button id="paySelectedBtn" class="btn btn-success" onclick="markSelectedAsPaid()" disabled>
                    <i class="fas fa-check"></i> পেমেন্ট সম্পন্ন (<span id="selectedCount">0</span>)
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <?php if ($can_edit): ?>
                            <input type="checkbox" class="payment-checkbox" id="selectAll" onchange="toggleSelectAll()">
                            <?php else: ?>
                            <span style="color:#999;">-</span>
                            <?php endif; ?>
                        </th>
                        <th>আইডি</th>
                        <th>রিপোর্টার</th>
                        <th>শিরোনাম</th>
                        <th>টাইপ</th>
                        <th>আয়</th>
                        <th>তারিখ</th>
                    </tr>
                </thead>
                <tbody id="pending-table-body">
                    <tr><td colspan="7" class="no-data">লোড হচ্ছে...</td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;">সিলেক্টেড মোট:</td>
                        <td colspan="2" class="earning-amount" id="footerSelectedTotal">$0.00</td>
                    </tr>
                </tfoot>
            </table>
            <div class="pagination">
                <div class="pagination-info">
                    দেখাচ্ছে <span id="showingFrom">0</span> - <span id="showingTo">0</span> / মোট <span id="totalRecords">0</span>
                </div>
                <div class="pagination-buttons">
                    <button onclick="changePage(1)" id="firstPageBtn"><i class="fas fa-angle-double-left"></i></button>
                    <button onclick="changePage(currentPage - 1)" id="prevPageBtn"><i class="fas fa-angle-left"></i></button>
                    <span id="pageNumbers"></span>
                    <button onclick="changePage(currentPage + 1)" id="nextPageBtn"><i class="fas fa-angle-right"></i></button>
                    <button onclick="changePage(totalPages)" id="lastPageBtn"><i class="fas fa-angle-double-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment History Tab -->
    <div id="history-tab" class="tab-content">
        <!-- History Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon paid"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h4>মোট পেমেন্ট</h4>
                    <p id="historyTotal">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-receipt"></i></div>
                <div class="stat-info">
                    <h4>পেমেন্ট সংখ্যা</h4>
                    <p id="historyCount">0</p>
                </div>
            </div>
        </div>
        
        <!-- History Filters -->
        <div class="action-bar">
            <div class="action-bar-left">
                <select id="historyReporterFilter" class="filter-select" onchange="filterHistory()">
                    <option value="">সকল রিপোর্টার</option>
                </select>
                <input type="date" id="historyDateFrom" class="filter-input" onchange="filterHistory()">
                <input type="date" id="historyDateTo" class="filter-input" onchange="filterHistory()">
                <button class="btn btn-secondary" onclick="resetHistoryFilters()">
                    <i class="fas fa-undo"></i> রিসেট
                </button>
            </div>
        </div>
        
        <!-- History Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>আইডি</th>
                        <th>রিপোর্টার</th>
                        <th>শিরোনাম</th>
                        <th>টাইপ</th>
                        <th>আয়</th>
                        <th>পেমেন্ট তারিখ</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    <tr><td colspan="7" class="no-data">লোড হচ্ছে...</td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">মোট পেমেন্ট:</td>
                        <td colspan="3" class="earning-amount" id="footerHistoryTotal">$0.00</td>
                    </tr>
                </tfoot>
            </table>
            <div class="pagination">
                <div class="pagination-info">
                    দেখাচ্ছে <span id="historyShowingFrom">0</span> - <span id="historyShowingTo">0</span> / মোট <span id="historyTotalRecords">0</span>
                </div>
                <div class="pagination-buttons">
                    <button onclick="changeHistoryPage(1)" id="historyFirstPageBtn"><i class="fas fa-angle-double-left"></i></button>
                    <button onclick="changeHistoryPage(historyCurrentPage - 1)" id="historyPrevPageBtn"><i class="fas fa-angle-left"></i></button>
                    <span id="historyPageNumbers"></span>
                    <button onclick="changeHistoryPage(historyCurrentPage + 1)" id="historyNextPageBtn"><i class="fas fa-angle-right"></i></button>
                    <button onclick="changeHistoryPage(historyTotalPages)" id="historyLastPageBtn"><i class="fas fa-angle-double-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Permission variables from PHP
const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;

const API_BASE = 'api/payment.php';

// ============================================
// EmailJS Configuration
// ============================================
const EMAILJS_PUBLIC_KEY = 'pZWlSpIcQrm7v5uWl';
const EMAILJS_SERVICE_ID = 'service_p9oocfe';
const EMAILJS_TEMPLATE_ID = 'template_zctatnb';
const ADMIN_EMAIL = 'admin@hindusnews.com';        // Change to your recipient email

// Initialize EmailJS
(function() {
    emailjs.init(EMAILJS_PUBLIC_KEY);
})();

let allPendingData = [];
let filteredPendingData = [];
let selectedItems = new Set();
let reporters = [];

let allHistoryData = [];
let filteredHistoryData = [];

let currentPage = 1;
let totalPages = 1;
const recordsPerPage = 20;

let historyCurrentPage = 1;
let historyTotalPages = 1;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadReporters();
    loadPendingData();
    loadHistoryData();
});

// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    if (tab === 'pending') {
        document.querySelector('.tab-btn:first-child').classList.add('active');
        document.getElementById('pending-tab').classList.add('active');
    } else {
        document.querySelector('.tab-btn:last-child').classList.add('active');
        document.getElementById('history-tab').classList.add('active');
    }
}

// Load reporters for filter
async function loadReporters() {
    try {
        const response = await fetch(API_BASE + '?action=get_reporters');
        const result = await response.json();
        if (result.success) {
            reporters = result.data;
            populateReporterFilters();
        }
    } catch (error) {
        console.error('Error loading reporters:', error);
    }
}

function populateReporterFilters() {
    const pendingFilter = document.getElementById('reporterFilter');
    const historyFilter = document.getElementById('historyReporterFilter');
    
    const options = reporters.map(r => `<option value="${r.id}" data-email="${escapeHtml(r.email || '')}">${escapeHtml(r.name)} ${r.email ? '(' + escapeHtml(r.email) + ')' : ''}</option>`).join('');
    
    pendingFilter.innerHTML = '<option value="">সকল রিপোর্টার</option>' + options;
    historyFilter.innerHTML = '<option value="">সকল রিপোর্টার</option>' + options;
}

// Load pending payments
async function loadPendingData() {
    try {
        const response = await fetch(API_BASE + '?action=get_pending');
        const result = await response.json();
        if (result.success) {
            allPendingData = result.data;
            filterData();
            updatePendingStats();
        }
    } catch (error) {
        console.error('Error loading pending data:', error);
        showMessage('error', 'ডেটা লোড করতে ব্যর্থ');
    }
}

// Load payment history
async function loadHistoryData() {
    try {
        const response = await fetch(API_BASE + '?action=get_history');
        const result = await response.json();
        if (result.success) {
            allHistoryData = result.data;
            filterHistory();
            updateHistoryStats();
        }
    } catch (error) {
        console.error('Error loading history:', error);
    }
}

// Filter pending data
function filterData() {
    const reporterId = document.getElementById('reporterFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    filteredPendingData = allPendingData.filter(item => {
        if (reporterId && item.reporter_id != reporterId) return false;
        if (dateFrom && item.created_at < dateFrom) return false;
        if (dateTo && item.created_at > dateTo + ' 23:59:59') return false;
        return true;
    });
    
    currentPage = 1;
    selectedItems.clear();
    updateSelectedCount();
    displayPendingPage();
}

function resetFilters() {
    document.getElementById('reporterFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    filterData();
}

// Filter history data
function filterHistory() {
    const reporterId = document.getElementById('historyReporterFilter').value;
    const dateFrom = document.getElementById('historyDateFrom').value;
    const dateTo = document.getElementById('historyDateTo').value;
    
    filteredHistoryData = allHistoryData.filter(item => {
        if (reporterId && item.reporter_id != reporterId) return false;
        if (dateFrom && item.paid_at < dateFrom) return false;
        if (dateTo && item.paid_at > dateTo + ' 23:59:59') return false;
        return true;
    });
    
    historyCurrentPage = 1;
    displayHistoryPage();
    updateHistoryStats();
}

function resetHistoryFilters() {
    document.getElementById('historyReporterFilter').value = '';
    document.getElementById('historyDateFrom').value = '';
    document.getElementById('historyDateTo').value = '';
    filterHistory();
}

// Display pending page
function displayPendingPage() {
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageData = filteredPendingData.slice(startIndex, endIndex);
    
    totalPages = Math.ceil(filteredPendingData.length / recordsPerPage) || 1;
    
    renderPendingTable(pageData);
    updatePendingPagination();
}

function renderPendingTable(data) {
    const tbody = document.getElementById('pending-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data">কোনো বাকি পেমেন্ট নেই</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(item => {
        const key = `${item.type}-${item.id}`;
        const isSelected = selectedItems.has(key);
        const totalEarning = (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
        
        // Only show checkbox if user has edit permission
        const checkboxHtml = canEdit 
            ? `<input type="checkbox" class="payment-checkbox" ${isSelected ? 'checked' : ''} onchange="toggleItem('${key}', ${totalEarning})">`
            : '<span style="color:#999;">-</span>';
        
        return `
        <tr class="${isSelected ? 'selected' : ''}" data-key="${key}">
            <td class="checkbox-cell">${checkboxHtml}</td>
            <td>${item.id}</td>
            <td>${escapeHtml(item.reporter_name)}</td>
            <td style="max-width: 300px;">${escapeHtml(item.headline)}</td>
            <td><span class="type-badge ${item.type}">${item.type === 'news' ? 'নিউজ' : 'ভিডিও'}</span></td>
            <td class="earning-amount">$${totalEarning.toFixed(2)}</td>
            <td>${formatDate(item.created_at)}</td>
        </tr>
        `;
    }).join('');
}

// Display history page
function displayHistoryPage() {
    const startIndex = (historyCurrentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageData = filteredHistoryData.slice(startIndex, endIndex);
    
    historyTotalPages = Math.ceil(filteredHistoryData.length / recordsPerPage) || 1;
    
    renderHistoryTable(pageData);
    updateHistoryPagination();
}

function renderHistoryTable(data) {
    const tbody = document.getElementById('history-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data">কোনো পেমেন্ট হিস্টোরি নেই</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(item => {
        const totalEarning = (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
        
        let actionHtml = '';
        if (canEdit) {
            actionHtml = `<button class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" 
                    onclick="markAsUnpaid('${item.type}', ${item.id})">
                    <i class="fas fa-undo"></i> আনপেইড
                </button>`;
        } else {
            actionHtml = '<span style="color:#999;">-</span>';
        }
        
        return `
        <tr>
            <td>${item.id}</td>
            <td>${escapeHtml(item.reporter_name)}</td>
            <td style="max-width: 300px;">${escapeHtml(item.headline)}</td>
            <td><span class="type-badge ${item.type}">${item.type === 'news' ? 'নিউজ' : 'ভিডিও'}</span></td>
            <td class="earning-amount">$${totalEarning.toFixed(2)}</td>
            <td>${formatDateTime(item.paid_at)}</td>
            <td>${actionHtml}</td>
        </tr>
        `;
    }).join('');
}

// Selection handling
function toggleItem(key, amount) {
    if (selectedItems.has(key)) {
        selectedItems.delete(key);
    } else {
        selectedItems.add(key);
    }
    
    const row = document.querySelector(`tr[data-key="${key}"]`);
    if (row) {
        row.classList.toggle('selected', selectedItems.has(key));
    }
    
    updateSelectedCount();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('#pending-table-body .payment-checkbox');
    
    if (selectAll.checked) {
        filteredPendingData.forEach(item => {
            const key = `${item.type}-${item.id}`;
            selectedItems.add(key);
        });
    } else {
        selectedItems.clear();
    }
    
    displayPendingPage();
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = selectedItems.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('paySelectedBtn').disabled = count === 0;
    
    // Calculate selected total
    let selectedTotal = 0;
    selectedItems.forEach(key => {
        const [type, id] = key.split('-');
        const item = allPendingData.find(i => i.type === type && i.id == id);
        if (item) {
            selectedTotal += (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
        }
    });
    
    document.getElementById('selectedTotal').textContent = '$' + selectedTotal.toFixed(2);
    document.getElementById('footerSelectedTotal').textContent = '$' + selectedTotal.toFixed(2);
}

// Mark as paid
async function markSelectedAsPaid() {
    if (selectedItems.size === 0) return;
    
    if (!confirm(`আপনি কি নিশ্চিত ${selectedItems.size}টি আইটেম পেমেন্ট সম্পন্ন হিসেবে চিহ্নিত করতে চান?`)) {
        return;
    }
    
    const btn = document.getElementById('paySelectedBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> প্রসেসিং...';
    
    try {
        const items = Array.from(selectedItems).map(key => {
            const [type, id] = key.split('-');
            return { type, id: parseInt(id) };
        });
        
        // Collect selected items data for email receipt
        const selectedItemsData = [];
        let totalAmount = 0;
        selectedItems.forEach(key => {
            const [type, id] = key.split('-');
            const item = allPendingData.find(i => i.type === type && i.id == id);
            if (item) {
                const itemTotal = (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
                totalAmount += itemTotal;
                selectedItemsData.push(item);
            }
        });
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'mark_paid', items })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Send payment receipt email via EmailJS
            btn.innerHTML = '<i class="fas fa-envelope fa-spin"></i> ইমেইল পাঠানো হচ্ছে...';
            
            const emailResult = await sendPaymentReceipt(selectedItemsData, totalAmount);
            
            if (emailResult.success) {
                const emailMsg = emailResult.emailsSent === emailResult.totalEmails 
                    ? `রিসিপ্ট ${emailResult.emailsSent}টি ইমেইলে পাঠানো হয়েছে`
                    : `রিসিপ্ট ${emailResult.emailsSent}/${emailResult.totalEmails}টি ইমেইলে পাঠানো হয়েছে`;
                showMessage('success', `${result.updated} টি আইটেম পেমেন্ট সম্পন্ন হয়েছে। ${emailMsg} (${emailResult.receiptNumber})`);
                
                if (emailResult.failedEmails.length > 0) {
                    console.warn('Failed to send to:', emailResult.failedEmails);
                }
            } else {
                showMessage('success', `${result.updated} টি আইটেম পেমেন্ট সম্পন্ন হয়েছে। তবে ইমেইল পাঠাতে ব্যর্থ হয়েছে।`);
                console.warn('Email sending failed:', emailResult.failedEmails);
            }
            
            selectedItems.clear();
            loadPendingData();
            loadHistoryData();
        } else {
            throw new Error(result.message || 'Failed to update');
        }
    } catch (error) {
        showMessage('error', 'পেমেন্ট আপডেট করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> পেমেন্ট সম্পন্ন (<span id="selectedCount">0</span>)';
        updateSelectedCount();
    }
}

// Mark as unpaid
async function markAsUnpaid(type, id) {
    if (!confirm('আপনি কি নিশ্চিত এই পেমেন্ট আনপেইড করতে চান?')) {
        return;
    }
    
    try {
        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'mark_unpaid', type, id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', 'পেমেন্ট আনপেইড করা হয়েছে');
            loadPendingData();
            loadHistoryData();
        } else {
            throw new Error(result.message || 'Failed');
        }
    } catch (error) {
        showMessage('error', 'আনপেইড করতে ব্যর্থ');
        console.error('Error:', error);
    }
}

// Stats
function updatePendingStats() {
    let pendingTotal = 0;
    const reporterSet = new Set();
    
    allPendingData.forEach(item => {
        pendingTotal += (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
        reporterSet.add(item.reporter_id);
    });
    
    document.getElementById('pendingTotal').textContent = '$' + pendingTotal.toFixed(2);
    document.getElementById('reporterCount').textContent = reporterSet.size;
}

function updateHistoryStats() {
    let historyTotal = 0;
    
    filteredHistoryData.forEach(item => {
        historyTotal += (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0);
    });
    
    document.getElementById('historyTotal').textContent = '$' + historyTotal.toFixed(2);
    document.getElementById('historyCount').textContent = filteredHistoryData.length;
    document.getElementById('footerHistoryTotal').textContent = '$' + historyTotal.toFixed(2);
}

// Pagination
function updatePendingPagination() {
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = Math.min(startIndex + recordsPerPage, filteredPendingData.length);
    
    document.getElementById('showingFrom').textContent = filteredPendingData.length > 0 ? startIndex + 1 : 0;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('totalRecords').textContent = filteredPendingData.length;
    
    document.getElementById('firstPageBtn').disabled = currentPage === 1;
    document.getElementById('prevPageBtn').disabled = currentPage === 1;
    document.getElementById('nextPageBtn').disabled = currentPage === totalPages;
    document.getElementById('lastPageBtn').disabled = currentPage === totalPages;
    
    const pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.onclick = () => changePage(i);
        if (i === currentPage) btn.classList.add('active');
        pageNumbers.appendChild(btn);
    }
}

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayPendingPage();
}

function updateHistoryPagination() {
    const startIndex = (historyCurrentPage - 1) * recordsPerPage;
    const endIndex = Math.min(startIndex + recordsPerPage, filteredHistoryData.length);
    
    document.getElementById('historyShowingFrom').textContent = filteredHistoryData.length > 0 ? startIndex + 1 : 0;
    document.getElementById('historyShowingTo').textContent = endIndex;
    document.getElementById('historyTotalRecords').textContent = filteredHistoryData.length;
    
    document.getElementById('historyFirstPageBtn').disabled = historyCurrentPage === 1;
    document.getElementById('historyPrevPageBtn').disabled = historyCurrentPage === 1;
    document.getElementById('historyNextPageBtn').disabled = historyCurrentPage === historyTotalPages;
    document.getElementById('historyLastPageBtn').disabled = historyCurrentPage === historyTotalPages;
    
    const pageNumbers = document.getElementById('historyPageNumbers');
    pageNumbers.innerHTML = '';
    const startPage = Math.max(1, historyCurrentPage - 2);
    const endPage = Math.min(historyTotalPages, historyCurrentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.onclick = () => changeHistoryPage(i);
        if (i === historyCurrentPage) btn.classList.add('active');
        pageNumbers.appendChild(btn);
    }
}

function changeHistoryPage(page) {
    if (page < 1 || page > historyTotalPages) return;
    historyCurrentPage = page;
    displayHistoryPage();
}

// Utilities
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('bn-BD');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('bn-BD') + ' ' + date.toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    if (!text) return '';
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

// ============================================
// EmailJS Payment Receipt Functions
// ============================================

function generateReceiptNumber() {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    return `HN-${year}${month}${day}-${random}`;
}

function generatePaymentReceiptHTML(paymentData) {
    const { receiptNumber, paymentDate, items, totalAmount, reporterSummary } = paymentData;
    
    // Calculate 50% admin income
    const adminIncome = totalAmount * 0.5;
    
    const itemRows = items.map((item, index) => `
        <tr style="background: ${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
            <td style="padding: 12px 16px; font-size: 13px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">${index + 1}</td>
            <td style="padding: 12px 16px; font-size: 13px; color: #374151; font-weight: 500; border-bottom: 1px solid #e5e7eb;">${escapeHtml(item.reporter_name)}</td>
            <td style="padding: 12px 16px; font-size: 13px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">${escapeHtml(item.headline.substring(0, 35))}${item.headline.length > 35 ? '...' : ''}</td>
            <td style="padding: 12px 16px; text-align: center; border-bottom: 1px solid #e5e7eb;">
                <span style="padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 500; background: ${item.type === 'news' ? '#f3f4f6' : '#f3f4f6'}; color: #374151;">
                    ${item.type === 'news' ? 'News' : 'Video'}
                </span>
            </td>
            <td style="padding: 12px 16px; font-size: 13px; color: #374151; font-weight: 600; text-align: right; border-bottom: 1px solid #e5e7eb;">$${item.amount.toFixed(2)}</td>
        </tr>
    `).join('');

    const reporterRows = Object.entries(reporterSummary).map(([name, amount], index) => `
        <tr style="background: ${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
            <td style="padding: 10px 16px; font-size: 13px; color: #374151; font-weight: 500; border-bottom: 1px solid #e5e7eb;">${escapeHtml(name)}</td>
            <td style="padding: 10px 16px; font-size: 13px; color: #374151; font-weight: 600; text-align: right; border-bottom: 1px solid #e5e7eb;">$${amount.toFixed(2)}</td>
        </tr>
    `).join('');

    return `
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="margin: 0; padding: 0; background-color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background: #ffffff;">
        
        <!-- Header -->
        <tr>
            <td style="background: #f9fafb; padding: 25px 0; text-align: center; border-bottom: 1px solid #e5e7eb;">
                <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Hindus News" style="height: 40px; margin-bottom: 8px;">
                <p style="margin: 0; color: #9ca3af; font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">Payment Receipt</p>
            </td>
        </tr>
        
        <!-- Receipt Info -->
        <tr>
            <td style="padding: 20px 15px; border-bottom: 1px solid #e5e7eb;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%;">
                            <p style="margin: 0 0 4px; color: #9ca3af; font-size: 11px; text-transform: uppercase;">Receipt No.</p>
                            <p style="margin: 0; color: #111827; font-size: 14px; font-weight: 600;">${receiptNumber}</p>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <p style="margin: 0 0 4px; color: #9ca3af; font-size: 11px; text-transform: uppercase;">Date</p>
                            <p style="margin: 0; color: #111827; font-size: 14px; font-weight: 600;">${paymentDate}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Payment Details Header -->
        <tr>
            <td style="padding: 15px 15px 10px; background: #ffffff;">
                <p style="margin: 0; color: #111827; font-size: 13px; font-weight: 600;">
                    <i class="fas fa-list-ul" style="color: #6b7280; margin-right: 6px;"></i>Payment Details
                </p>
            </td>
        </tr>
        
        <!-- Payment Details Table -->
        <tr>
            <td style="padding: 0;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 10px 15px; text-align: left; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">#</th>
                            <th style="padding: 10px 15px; text-align: left; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Reporter</th>
                            <th style="padding: 10px 15px; text-align: left; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Title</th>
                            <th style="padding: 10px 15px; text-align: center; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Type</th>
                            <th style="padding: 10px 15px; text-align: right; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemRows}
                    </tbody>
                </table>
            </td>
        </tr>
        
        <!-- Reporter Summary Header -->
        <tr>
            <td style="padding: 20px 15px 10px; background: #ffffff;">
                <p style="margin: 0; color: #111827; font-size: 13px; font-weight: 600;">
                    <i class="fas fa-users" style="color: #6b7280; margin-right: 6px;"></i>Reporter Summary
                </p>
            </td>
        </tr>
        
        <!-- Reporter Summary Table -->
        <tr>
            <td style="padding: 0;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 10px 15px; text-align: left; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Reporter Name</th>
                            <th style="padding: 10px 15px; text-align: right; color: #6b7280; font-size: 11px; font-weight: 600; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${reporterRows}
                    </tbody>
                </table>
            </td>
        </tr>
        
        <!-- Financial Summary -->
        <tr>
            <td style="padding: 0; border-top: 1px solid #e5e7eb;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px 15px; background: #ffffff; border-bottom: 1px solid #e5e7eb;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td>
                                        <i class="fas fa-money-bill-wave" style="color: #6b7280; margin-right: 6px;"></i>
                                        <span style="color: #374151; font-size: 13px;">Reporter Payout</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span style="color: #374151; font-size: 14px; font-weight: 600;">$${totalAmount.toFixed(2)}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; background: #f9fafb;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td>
                                        <i class="fas fa-chart-line" style="color: #374151; margin-right: 6px;"></i>
                                        <span style="color: #111827; font-size: 14px; font-weight: 600;">My Earning (50%)</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span style="color: #111827; font-size: 18px; font-weight: 700;">$${adminIncome.toFixed(2)}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Stats -->
        <tr>
            <td style="padding: 0; border-top: 1px solid #e5e7eb;">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 33.33%; text-align: center; padding: 15px 10px; background: #f9fafb; border-right: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #111827; font-size: 18px; font-weight: 700;">${items.length}</p>
                            <p style="margin: 4px 0 0; color: #6b7280; font-size: 10px; text-transform: uppercase;">Items</p>
                        </td>
                        <td style="width: 33.33%; text-align: center; padding: 15px 10px; background: #f9fafb; border-right: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #111827; font-size: 18px; font-weight: 700;">${Object.keys(reporterSummary).length}</p>
                            <p style="margin: 4px 0 0; color: #6b7280; font-size: 10px; text-transform: uppercase;">Reporters</p>
                        </td>
                        <td style="width: 33.33%; text-align: center; padding: 15px 10px; background: #f9fafb;">
                            <p style="margin: 0; color: #111827; font-size: 18px; font-weight: 700;">${items.filter(i => i.type === 'news').length}/${items.filter(i => i.type === 'video').length}</p>
                            <p style="margin: 4px 0 0; color: #6b7280; font-size: 10px; text-transform: uppercase;">News/Video</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background: #f9fafb; padding: 20px 15px; text-align: center; border-top: 1px solid #e5e7eb;">
                <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Hindus News" style="height: 22px; margin-bottom: 10px; opacity: 0.6;">
                <p style="margin: 0 0 4px; color: #9ca3af; font-size: 10px;">This receipt was generated automatically</p>
                <p style="margin: 0; color: #6b7280; font-size: 11px; font-weight: 500;">Hindus News Media &copy; ${new Date().getFullYear()}</p>
                <p style="margin: 6px 0 0;">
                    <a href="https://www.hindus.news" style="color: #6b7280; text-decoration: none; font-size: 10px;">www.hindus.news</a>
                </p>
            </td>
        </tr>
        
    </table>
</body>
</html>
    `;
}

async function sendPaymentReceipt(selectedItemsData, totalAmount) {
    const receiptNumber = generateReceiptNumber();
    const paymentDate = new Date().toLocaleDateString('bn-BD', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Get current month in Bengali
    const bengaliMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
    const currentMonth = bengaliMonths[new Date().getMonth()];

    // Prepare items data
    const items = selectedItemsData.map(item => ({
        reporter_name: item.reporter_name,
        reporter_email: item.reporter_email,
        headline: item.headline,
        type: item.type,
        amount: (parseFloat(item.earning) || 0) + (parseFloat(item.web_earning) || 0) + (parseFloat(item.youtube_earning) || 0)
    }));

    // Calculate reporter summary with emails - track name with email
    const reporterSummary = {};
    const reporterEmailMap = {}; // email -> name mapping
    items.forEach(item => {
        if (!reporterSummary[item.reporter_name]) {
            reporterSummary[item.reporter_name] = 0;
        }
        reporterSummary[item.reporter_name] += item.amount;
        
        // Map email to reporter name
        if (item.reporter_email) {
            reporterEmailMap[item.reporter_email] = item.reporter_name;
        }
    });

    // Generate receipt HTML
    const receiptHTML = generatePaymentReceiptHTML({
        receiptNumber,
        paymentDate,
        items,
        totalAmount,
        reporterSummary
    });

    // Get all unique reporter emails to send receipt
    const emailList = Object.keys(reporterEmailMap);
    
    if (emailList.length === 0) {
        console.warn('No reporter emails found, using admin email');
        emailList.push(ADMIN_EMAIL);
        reporterEmailMap[ADMIN_EMAIL] = 'Admin';
    }

    // Send email to each reporter
    const sendResults = [];
    for (const email of emailList) {
        const reporterName = reporterEmailMap[email] || 'Reporter';
        const templateParams = {
            to_email: email,
            subject: `প্রিয় ${reporterName}, আপনার ${currentMonth} HINDUS NEWS ইনকাম $${(totalAmount * 0.5).toFixed(2)}`,
            receipt_number: receiptNumber,
            payment_date: paymentDate,
            total_amount: `$${totalAmount.toFixed(2)}`,
            total_items: items.length,
            total_reporters: Object.keys(reporterSummary).length,
            message_html: receiptHTML
        };

        try {
            const response = await emailjs.send(
                EMAILJS_SERVICE_ID,
                EMAILJS_TEMPLATE_ID,
                templateParams
            );
            console.log(`Payment receipt sent to ${email}:`, response);
            sendResults.push({ email, success: true });
        } catch (error) {
            console.error(`Failed to send receipt to ${email}:`, error);
            sendResults.push({ email, success: false, error: error.text || error.message });
        }
    }

    const successCount = sendResults.filter(r => r.success).length;
    const failedEmails = sendResults.filter(r => !r.success).map(r => r.email);
    
    return { 
        success: successCount > 0, 
        receiptNumber,
        emailsSent: successCount,
        totalEmails: emailList.length,
        failedEmails
    };
}
</script>

</body>
</html>
