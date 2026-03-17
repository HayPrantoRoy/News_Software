<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="modern_admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        html {
            overflow-x: hidden;
        }
        
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            padding: 20px;
        }
        
        @media (min-width: 768px) {
            .container {
                padding: 30px;
            }
        }
        
        .dashboard-welcome {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            color: #1e293b;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .dashboard-welcome h1 {
            font-size: 28px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1e293b;
        }
        .dashboard-welcome h1 i {
            color: #308e87;
        }
        
        .dashboard-welcome p {
            color: #64748b;
            font-size: 15px;
            font-weight: 400;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .stat-icon.primary {
            color: #308e87;
        }
        
        .stat-icon.success {
            color: #64748b;
        }
        
        .stat-icon.warning {
            color: #94a3b8;
        }
        
        .stat-icon.danger {
            color: #cbd5e0;
        }
        
        .stat-icon.info {
            color: #308e87;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-content h3 {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 4px;
            font-weight: 700;
        }
        
        .stat-content p {
            color: #718096;
            font-size: 14px;
            margin: 0;
        }
        
        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-top: 8px;
        }
        
        .stat-trend.up {
            color: #10b981;
        }
        
        .stat-trend.down {
            color: #ef4444;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 2px solid #e2e8f0;
        }
        
        .chart-card h2 {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        
        .chart-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-filters {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-dropdown {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            color: #4a5568;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .filter-dropdown:hover {
            border-color: #308e87;
        }
        
        .filter-dropdown:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
        }
        
        .date-input {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            color: #4a5568;
        }
        
        .date-input:focus {
            outline: none;
            border-color: #308e87;
            box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .chart-container {
                height: 250px;
            }
        }
        
        .chart-data-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        
        .chart-data-table thead {
            background: linear-gradient(135deg, #308e87 0%, #2a7a73 100%);
            border-bottom: 3px solid #308e87;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(48, 142, 135, 0.1);
        }
        
        .chart-data-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 700;
            color: #ffffff !important;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: transparent;
            cursor: default;
        }
        
        .chart-data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 15px;
        }
        
        .chart-data-table tbody tr:hover {
            background: #f0fdf9;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .chart-data-table .number-cell {
            font-weight: 700;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 15px;
            background: #fafbfc;
        }
        .table-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-wrapper::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .chart-data-table tfoot {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            font-weight: 600;
        }
        
        .chart-data-table tfoot td {
            padding: 12px;
            color: #1e293b;
        }
        
        .split-table-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .split-table {
            margin-top: 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        
        .split-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        #reporters-data-table {
            font-size: 14px;
        }
        
        #reporters-data-table th {
            font-size: 12px;
        }
        
        #reporters-data-table .number-cell {
            font-size: 15px;
        }
        
        .table-footer-row {
            margin-top: 15px;
            padding: 16px;
            background: linear-gradient(135deg, #308e87 0%, #2a7a73 100%);
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 8px rgba(48, 142, 135, 0.2);
        }
        
        .table-footer-row strong {
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
        }
        
        .table-footer-row .number-cell {
            font-size: 20px;
            color: #ffffff;
            font-weight: 800;
            margin-left: 8px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quick-action-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #2d3748;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        
        .quick-action-btn:hover {
            border-color: #308e87;
            background: #f0fdf9;
            transform: translateY(-2px);
        }
        
        .quick-action-btn i {
            font-size: 32px;
            color: #308e87;
        }
        
        .quick-action-btn span {
            font-size: 14px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-filters {
                flex-wrap: wrap;
            }
            
            .filter-dropdown, .date-input {
                width: 100%;
                margin-bottom: 8px;
            }
            
            #reporters-custom-limit {
                width: 100% !important;
            }
            
            .chart-data-table {
                font-size: 13px;
                overflow-x: auto;
                display: block;
            }
            
            .chart-data-table th,
            .chart-data-table td {
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .chart-data-table .number-cell {
                font-size: 14px;
            }
            
            .chart-data-table .date-cell {
                font-size: 12px;
            }
            
            .dashboard-welcome {
                padding: 20px;
            }
            
            .dashboard-welcome h1 {
                font-size: 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dashboard-welcome p {
                font-size: 13px;
            }
            
            .stat-card {
                flex-direction: row;
                padding: 18px;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 22px;
            }
            
            .stat-content h3 {
                font-size: 22px;
            }
            
            .stat-content p {
                font-size: 12px;
            }
            
            .split-table-container {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .chart-card {
                padding: 18px;
            }
            
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
        
        @media (max-width: 1200px) {
            .chart-filters {
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .split-table-container {
                gap: 15px;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .chart-card {
                padding: 20px;
            }
        }
        
        @media (max-width: 600px) {
            .split-table-container {
                gap: 10px;
            }
            
            .table-wrapper {
                padding: 10px;
            }
            
            .table-footer-row {
                font-size: 14px;
                padding: 12px;
            }
            
            .table-footer-row .number-cell {
                font-size: 18px;
            }
            
            .dashboard-welcome h1 {
                font-size: 18px;
            }
            
            .stat-content h3 {
                font-size: 20px;
            }
            
            .chart-filters {
                gap: 6px;
                width: 100%;
            }
            
            .filter-dropdown,
            .date-input {
                font-size: 14px;
                padding: 10px;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .chart-header span {
                font-size: 16px;
            }
            
            h2 {
                font-size: 18px;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 10px;
            }
            
            .table-section {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table-section table {
                min-width: 600px;
            }
            
            .chart-card h2 {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .container {
                padding: 10px;
            }
            
            .quick-action-btn {
                flex-direction: row;
                padding: 15px;
                gap: 10px;
            }
            
            .quick-action-btn i {
                font-size: 24px;
            }
        }
        
        @media (min-width: 601px) and (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <div class="container">
        <!-- Welcome Section -->
        <div class="dashboard-welcome">
            <h1>
                <i class="fas fa-chart-line"></i>
                স্বাগতম, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!
            </h1>
            <p>আজ: <?php echo date('l, F j, Y'); ?> | সময়: <span id="current-time"></span></p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <h3 id="total-news">0</h3>
                    <p>মোট সংবাদ</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span id="news-trend">0</span> এই মাসে
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 id="total-reporters">0</h3>
                    <p>মোট সাংবাদিক</p>
                    <div class="stat-trend">
                        <i class="fas fa-user-check"></i>
                        <span id="active-reporters">0</span> সক্রিয়
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <h3 id="total-views">0</h3>
                    <p>মোট ভিউ</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span id="views-today">0</span> আজ
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 id="unpublished-news">0</h3>
                    <p>অপ্রকাশিত সংবাদ</p>
                    <div class="stat-trend">
                        <i class="fas fa-hourglass-half"></i>
                        পর্যালোচনার অপেক্ষায়
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="index.php" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>নতুন সংবাদ যোগ করুন</span>
            </a>
            <a href="manage_videos.php" class="quick-action-btn">
                <i class="fas fa-video"></i>
                <span>ভিডিও ব্যবস্থাপনা</span>
            </a>
            <a href="manage_opinions.php" class="quick-action-btn">
                <i class="fas fa-comment"></i>
                <span>মতামত ব্যবস্থাপনা</span>
            </a>
            <a href="category.php" class="quick-action-btn">
                <i class="fas fa-folder"></i>
                <span>বিভাগ ব্যবস্থাপনা</span>
            </a>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <!-- News Publication Trend -->
            <div class="chart-card">
                <h2>
                    <div class="chart-header">
                        <i class="fas fa-chart-line"></i>
                        <span>সংবাদ প্রকাশনা</span>
                    </div>
                    <div class="chart-filters">
                        <select class="filter-dropdown" id="news-period-filter" onchange="updateNewsChart()">
                            <option value="1">২৪ ঘণ্টা</option>
                            <option value="7">৭ দিন</option>
                            <option value="10" selected>১০ দিন</option>
                            <option value="30">৩০ দিন</option>
                        </select>
                        <input type="date" class="date-input" id="news-from-date" onchange="updateNewsChart()" placeholder="শুরুর তারিখ">
                        <input type="date" class="date-input" id="news-to-date" onchange="updateNewsChart()" placeholder="শেষ তারিখ">
                    </div>
                </h2>
                <div class="chart-container">
                    <canvas id="newsPublicationChart"></canvas>
                </div><br>
                <div class="table-wrapper">
                    <div class="split-table-container">
                        <table class="chart-data-table split-table">
                            <thead>
                                <tr>
                                    <th>তারিখ</th>
                                    <th>প্রকাশিত সংবাদ</th>
                                </tr>
                            </thead>
                            <tbody id="news-table-body-left">
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 20px;">
                                        <i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="chart-data-table split-table">
                            <thead>
                                <tr>
                                    <th>তারিখ</th>
                                    <th>প্রকাশিত সংবাদ</th>
                                </tr>
                            </thead>
                            <tbody id="news-table-body-right">
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 20px;">
                                        <i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer-row">
                        <strong>মোট প্রকাশিত সংবাদ:</strong> <span id="news-total" class="number-cell">0</span>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="chart-card">
                <h2>
                    <div class="chart-header">
                        <i class="fas fa-chart-pie"></i>
                        <span>বিভাগ অনুসারে বিতরণ</span>
                    </div>
                </h2>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Views Chart -->
        <div class="chart-card" style="margin-bottom: 30px;">
            <h2>
                <div class="chart-header">
                    <i class="fas fa-eye"></i>
                    <span>ভিউ ট্রেন্ড</span>
                </div>
                <div class="chart-filters">
                    <select class="filter-dropdown" id="views-period-filter" onchange="updateViewsChart()">
                        <option value="1">২৪ ঘণ্টা</option>
                        <option value="7">৭ দিন</option>
                        <option value="10" selected>১০ দিন</option>
                        <option value="30">৩০ দিন</option>
                    </select>
                    <input type="date" class="date-input" id="views-from-date" onchange="updateViewsChart()" placeholder="শুরুর তারিখ">
                    <input type="date" class="date-input" id="views-to-date" onchange="updateViewsChart()" placeholder="শেষ তারিখ">
                </div>
            </h2>
            <div class="chart-container">
                <canvas id="viewsChart"></canvas>
            </div><br>
            <div class="table-wrapper">
                <div class="split-table-container">
                    <table class="chart-data-table split-table">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>মোট ভিউ</th>
                            </tr>
                        </thead>
                        <tbody id="views-table-body-left">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px;">
                                    <i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="chart-data-table split-table">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>মোট ভিউ</th>
                            </tr>
                        </thead>
                        <tbody id="views-table-body-right">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px;">
                                    <i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer-row">
                    <strong>মোট ভিউ:</strong> <span id="views-total" class="number-cell">0</span>
                </div>
            </div>
        </div>

        <!-- Top Reporters by Views Chart -->
        <div class="chart-card" style="margin-bottom: 30px;">
            <h2>
                <div class="chart-header">
                    <i class="fas fa-user-tie"></i>
                    <span>শীর্ষ সাংবাদিক (ভিউ অনুসারে)</span>
                </div>
                <div class="chart-filters">
                    <select class="filter-dropdown" id="reporters-limit" onchange="updateTopReportersChart()">
                        <option value="10" selected>শীর্ষ ১০</option>
                        <option value="20">শীর্ষ ২০</option>
                        <option value="30">শীর্ষ ৩০</option>
                        <option value="40">শীর্ষ ৪০</option>
                        <option value="50">শীর্ষ ৫০</option>
                        <option value="75">শীর্ষ ৭৫</option>
                        <option value="100">শীর্ষ ১০০</option>
                        <option value="all">সব সাংবাদিক</option>
                        <option value="custom">কাস্টম...</option>
                    </select>
                    <input type="number" class="date-input" id="reporters-custom-limit" min="1" max="500" placeholder="সংখ্যা লিখুন" style="display: none; width: 120px;" onchange="updateTopReportersChart()">
                    <select class="filter-dropdown" id="top-reporters-period" onchange="updateTopReportersChart()">
                        <option value="1">২৪ ঘণ্টা</option>
                        <option value="7">৭ দিন</option>
                        <option value="30" selected>৩০ দিন</option>
                    </select>
                    <input type="date" class="date-input" id="reporters-from-date" onchange="updateTopReportersChart()" placeholder="শুরুর তারিখ">
                    <input type="date" class="date-input" id="reporters-to-date" onchange="updateTopReportersChart()" placeholder="শেষ তারিখ">
                </div>
            </h2>
            <div class="chart-container" style="height: auto; min-height: 300px;">
                <canvas id="topReportersChart"></canvas>
            </div>
            <div class="table-wrapper">
                <table class="chart-data-table" id="reporters-data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>সাংবাদিক</th>
                            <th>মোট ভিউ</th>
                            <th>সংবাদ সংখ্যা</th>
                        </tr>
                    </thead>
                    <tbody id="reporters-table-body">
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">
                                <i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="reporters-table-footer" style="display: none;">
                        <tr>
                            <td colspan="2">মোট</td>
                            <td class="number-cell" id="reporters-views-total">0</td>
                            <td class="number-cell" id="reporters-news-total">0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Reporter Performance Table -->
        <div class="table-section">
            <h2>
                <div class="chart-header">
                    <i class="fas fa-trophy"></i>
                    <span>সাংবাদিকদের কর্মক্ষমতা</span>
                </div>
                <div class="chart-filters">
                    <select class="filter-dropdown" id="performance-period-filter" onchange="updateReporterPerformance()">
                        <option value="1">২৪ ঘণ্টা</option>
                        <option value="7">৭ দিন</option>
                        <option value="30" selected>৩০ দিন</option>
                    </select>
                    <input type="date" class="date-input" id="performance-from-date" onchange="updateReporterPerformance()" placeholder="শুরুর তারিখ">
                    <input type="date" class="date-input" id="performance-to-date" onchange="updateReporterPerformance()" placeholder="শেষ তারিখ">
                </div>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> #</th>
                        <th><i class="fas fa-user"></i> সাংবাদিক</th>
                        <th><i class="fas fa-newspaper"></i> প্রকাশিত সংবাদ</th>
                        <th><i class="fas fa-eye"></i> মোট ভিউ</th>
                        <th><i class="fas fa-dollar-sign"></i> আয়</th>
                        <th><i class="fas fa-chart-line"></i> পারফরম্যান্স</th>
                    </tr>
                </thead>
                <tbody id="reporter-performance-tbody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #308e87;"></i>
                            <p style="margin-top: 10px;">তথ্য লোড হচ্ছে...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('bn-BD', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Global chart instances
        let newsChart = null;
        let categoryChart = null;
        let viewsChart = null;
        let topReportersChart = null;

        // Load dashboard data
        async function loadDashboardData() {
            try {
                const response = await fetch('api/dashboard_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    // Update stats
                    document.getElementById('total-news').textContent = data.total_news || 0;
                    document.getElementById('total-reporters').textContent = data.total_reporters || 0;
                    document.getElementById('total-views').textContent = (data.total_views || 0).toLocaleString();
                    document.getElementById('unpublished-news').textContent = data.unpublished_news || 0;
                    document.getElementById('news-trend').textContent = data.news_this_month || 0;
                    document.getElementById('active-reporters').textContent = data.active_reporters || 0;
                    document.getElementById('views-today').textContent = (data.views_today || 0).toLocaleString();
                    
                    // Load charts
                    loadNewsPublicationChart(data.weekly_news || []);
                    loadCategoryChart(data.category_distribution || []);
                    loadViewsChart(data.views_trend || []);
                    loadTopReportersChart(data.top_reporters || []);
                    
                    // Load reporter performance
                    loadReporterPerformance(data.reporter_performance || []);
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        // News Publication Chart
        function loadNewsPublicationChart(data) {
            const ctx = document.getElementById('newsPublicationChart');
            
            if (newsChart) {
                newsChart.destroy();
            }
            
            newsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'প্রকাশিত সংবাদ',
                        data: data.map(d => d.count),
                        borderColor: '#308e87',
                        backgroundColor: 'rgba(48, 142, 135, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#308e87',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: '#e2e8f0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Populate split table
            const tbodyLeft = document.getElementById('news-table-body-left');
            const tbodyRight = document.getElementById('news-table-body-right');
            
            if (data.length === 0) {
                tbodyLeft.innerHTML = '<tr><td colspan="2" style="text-align: center; padding: 20px; color: #94a3b8;">কোনো ডেটা পাওয়া যায়নি</td></tr>';
                tbodyRight.innerHTML = '';
                document.getElementById('news-total').textContent = '0';
            } else {
                // Split data into two halves
                const midPoint = Math.ceil(data.length / 2);
                const leftData = data.slice(0, midPoint);
                const rightData = data.slice(midPoint);
                
                tbodyLeft.innerHTML = leftData.map(d => `
                    <tr>
                        <td class="date-cell">${d.date}</td>
                        <td class="number-cell">${d.count}</td>
                    </tr>
                `).join('');
                
                if (rightData.length > 0) {
                    tbodyRight.innerHTML = rightData.map(d => `
                        <tr>
                            <td class="date-cell">${d.date}</td>
                            <td class="number-cell">${d.count}</td>
                        </tr>
                    `).join('');
                } else {
                    tbodyRight.innerHTML = '';
                }
                
                // Calculate total
                const total = data.reduce((sum, d) => sum + d.count, 0);
                document.getElementById('news-total').textContent = total;
            }
        }

        // Category Distribution Chart
        function loadCategoryChart(data) {
            const ctx = document.getElementById('categoryChart');
            
            if (categoryChart) {
                categoryChart.destroy();
            }
            
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.category),
                    datasets: [{
                        data: data.map(d => d.count),
                        backgroundColor: [
                            '#308e87',
                            '#64748b',
                            '#94a3b8',
                            '#cbd5e0',
                            '#e2e8f0',
                            '#475569',
                            '#1e293b'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                color: '#4a5568'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            padding: 12
                        }
                    }
                }
            });
        }

        // Views Trend Chart
        function loadViewsChart(data) {
            const ctx = document.getElementById('viewsChart');
            
            if (viewsChart) {
                viewsChart.destroy();
            }
            
            viewsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'মোট ভিউ',
                        data: data.map(d => d.views),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.parsed.y.toLocaleString() + ' ভিউ';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(1) + 'K';
                                    }
                                    return value;
                                }
                            },
                            grid: {
                                color: '#e2e8f0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Helper function to format views
            function formatViews(views) {
                if (views >= 1000000) {
                    return (views / 1000000).toFixed(1) + 'M';
                } else if (views >= 1000) {
                    return (views / 1000).toFixed(1) + 'K';
                }
                return views.toString();
            }
            
            // Populate split table
            const tbodyLeft = document.getElementById('views-table-body-left');
            const tbodyRight = document.getElementById('views-table-body-right');
            
            if (data.length === 0) {
                tbodyLeft.innerHTML = '<tr><td colspan="2" style="text-align: center; padding: 20px; color: #94a3b8;">কোনো ডেটা পাওয়া যায়নি</td></tr>';
                tbodyRight.innerHTML = '';
                document.getElementById('views-total').textContent = '0';
            } else {
                // Split data into two halves
                const midPoint = Math.ceil(data.length / 2);
                const leftData = data.slice(0, midPoint);
                const rightData = data.slice(midPoint);
                
                tbodyLeft.innerHTML = leftData.map(d => `
                    <tr>
                        <td class="date-cell">${d.date}</td>
                        <td class="number-cell">${formatViews(d.views)}</td>
                    </tr>
                `).join('');
                
                if (rightData.length > 0) {
                    tbodyRight.innerHTML = rightData.map(d => `
                        <tr>
                            <td class="date-cell">${d.date}</td>
                            <td class="number-cell">${formatViews(d.views)}</td>
                        </tr>
                    `).join('');
                } else {
                    tbodyRight.innerHTML = '';
                }
                
                // Calculate total
                const total = data.reduce((sum, d) => sum + d.views, 0);
                document.getElementById('views-total').textContent = formatViews(total);
            }
        }

        // Top Reporters by Views Chart
        function loadTopReportersChart(data) {
            const ctx = document.getElementById('topReportersChart');
            
            if (topReportersChart) {
                topReportersChart.destroy();
            }
            
            // Calculate bar thickness based on number of items
            let barThickness = 35;
            if (data.length > 50) {
                barThickness = 20;
            } else if (data.length > 30) {
                barThickness = 25;
            } else if (data.length > 15) {
                barThickness = 30;
            }
            
            topReportersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.name),
                    datasets: [{
                        label: 'মোট ভিউ',
                        data: data.map(d => d.total_views),
                        backgroundColor: '#308e87',
                        borderRadius: 6,
                        barThickness: barThickness
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const views = context.parsed.x.toLocaleString();
                                    const newsCount = data[context.dataIndex].news_count;
                                    return [
                                        ' ' + views + ' ভিউ',
                                        ' ' + newsCount + ' সংবাদ'
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: '#e2e8f0'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(1) + 'K';
                                    }
                                    return value.toLocaleString();
                                },
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: data.length > 30 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });
            
            // Populate table
            const tbody = document.getElementById('reporters-table-body');
            const footer = document.getElementById('reporters-table-footer');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #94a3b8;">কোনো ডেটা পাওয়া যায়নি</td></tr>';
                footer.style.display = 'none';
            } else {
                tbody.innerHTML = data.map((d, index) => `
                    <tr>
                        <td class="number-cell">${index + 1}</td>
                        <td>${d.name}</td>
                        <td class="number-cell">${d.total_views.toLocaleString()}</td>
                        <td class="number-cell">${d.news_count}</td>
                    </tr>
                `).join('');
                
                // Calculate totals
                const totalViews = data.reduce((sum, d) => sum + parseInt(d.total_views), 0);
                const totalNews = data.reduce((sum, d) => sum + parseInt(d.news_count), 0);
                document.getElementById('reporters-views-total').textContent = totalViews.toLocaleString();
                document.getElementById('reporters-news-total').textContent = totalNews;
                footer.style.display = '';
            }
        }

        // Load Reporter Performance
        function loadReporterPerformance(data) {
            const tbody = document.getElementById('reporter-performance-tbody');
            
            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #718096;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e0;"></i>
                            <p style="margin-top: 15px;">কোনো তথ্য পাওয়া যায়নি</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = data.map((reporter, index) => {
                const performance = reporter.published_news > 10 ? 'উচ্চ' : reporter.published_news > 5 ? 'মাঝারি' : 'কম';
                const performanceClass = reporter.published_news > 10 ? 'badge-success' : reporter.published_news > 5 ? 'badge-info' : 'badge-warning';
                
                return `
                    <tr>
                        <td><strong>#${index + 1}</strong></td>
                        <td>
                            <i class="fas fa-user-circle" style="color: #308e87; margin-right: 8px;"></i>
                            ${reporter.name}
                        </td>
                        <td><strong>${reporter.published_news}</strong></td>
                        <td>${(reporter.total_views || 0).toLocaleString('bn-BD')}</td>
                        <td>$${Number(reporter.earnings || 0).toFixed(2)}</td>
                        <td>
                            <span class="badge ${performanceClass}">
                                <i class="fas fa-chart-line"></i> ${performance}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Update News Chart based on filters
        async function updateNewsChart() {
            const period = document.getElementById('news-period-filter').value;
            const fromDate = document.getElementById('news-from-date').value;
            const toDate = document.getElementById('news-to-date').value;
            
            let url = 'api/dashboard_stats.php?chart=news';
            
            if (fromDate && toDate) {
                url += `&from=${fromDate}&to=${toDate}`;
                // Clear dropdown when using date range
                document.getElementById('news-period-filter').value = '';
            } else if (period) {
                url += `&period=${period}`;
                // Clear date inputs when using dropdown
                document.getElementById('news-from-date').value = '';
                document.getElementById('news-to-date').value = '';
            }
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    loadNewsPublicationChart(data.weekly_news || []);
                }
            } catch (error) {
                console.error('Error updating news chart:', error);
            }
        }

        // Update Views Chart based on filters
        async function updateViewsChart() {
            const period = document.getElementById('views-period-filter').value;
            const fromDate = document.getElementById('views-from-date').value;
            const toDate = document.getElementById('views-to-date').value;
            
            let url = 'api/dashboard_stats.php?chart=views';
            
            if (fromDate && toDate) {
                url += `&from=${fromDate}&to=${toDate}`;
                // Clear dropdown when using date range
                document.getElementById('views-period-filter').value = '';
            } else if (period) {
                url += `&period=${period}`;
                // Clear date inputs when using dropdown
                document.getElementById('views-from-date').value = '';
                document.getElementById('views-to-date').value = '';
            }
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    loadViewsChart(data.views_trend || []);
                }
            } catch (error) {
                console.error('Error updating views chart:', error);
            }
        }

        // Update Top Reporters Chart
        async function updateTopReportersChart() {
            const limitSelect = document.getElementById('reporters-limit');
            const customInput = document.getElementById('reporters-custom-limit');
            const period = document.getElementById('top-reporters-period').value;
            const fromDate = document.getElementById('reporters-from-date').value;
            const toDate = document.getElementById('reporters-to-date').value;
            
            // Handle limit dropdown change
            if (limitSelect.value === 'custom') {
                customInput.style.display = 'inline-block';
                if (!customInput.value) return; // Wait for custom value
            } else {
                customInput.style.display = 'none';
            }
            
            // Get the limit value
            let limit = 10; // default
            if (limitSelect.value === 'custom') {
                limit = parseInt(customInput.value) || 10;
            } else if (limitSelect.value === 'all') {
                limit = 1000; // Large number to get all
            } else {
                limit = parseInt(limitSelect.value);
            }
            
            let url = `api/dashboard_stats.php?chart=reporters&limit=${limit}`;
            
            if (fromDate && toDate) {
                url += `&from=${fromDate}&to=${toDate}`;
                // Clear dropdown when using date range
                document.getElementById('top-reporters-period').value = '';
            } else if (period) {
                url += `&period=${period}`;
                // Clear date inputs when using dropdown
                document.getElementById('reporters-from-date').value = '';
                document.getElementById('reporters-to-date').value = '';
            }
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    // Adjust container height based on number of reporters
                    const container = document.querySelector('#topReportersChart').parentElement;
                    const itemHeight = 40; // Height per bar
                    const minHeight = 300;
                    const calculatedHeight = Math.max(minHeight, (data.top_reporters.length * itemHeight) + 60);
                    container.style.height = calculatedHeight + 'px';
                    
                    loadTopReportersChart(data.top_reporters || []);
                }
            } catch (error) {
                console.error('Error updating top reporters chart:', error);
            }
        }

        // Update Reporter Performance Table
        async function updateReporterPerformance() {
            const period = document.getElementById('performance-period-filter').value;
            const fromDate = document.getElementById('performance-from-date').value;
            const toDate = document.getElementById('performance-to-date').value;
            
            let url = 'api/dashboard_stats.php?table=performance';
            
            if (fromDate && toDate) {
                url += `&from=${fromDate}&to=${toDate}`;
                // Clear dropdown when using date range
                document.getElementById('performance-period-filter').value = '';
            } else if (period) {
                url += `&period=${period}`;
                // Clear date inputs when using dropdown
                document.getElementById('performance-from-date').value = '';
                document.getElementById('performance-to-date').value = '';
            }
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    loadReporterPerformance(data.reporter_performance || []);
                }
            } catch (error) {
                console.error('Error updating reporter performance:', error);
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', loadDashboardData);
    </script>
</body>
</html>
