<?php include 'auth_check.php';
include_once __DIR__ . '/../connection.php';

// Fetch basic_info for dynamic logo and portal name
$basic_info = [];
$basic_info_result = $conn->query("SELECT * FROM basic_info LIMIT 1");
if ($basic_info_result && $basic_info_result->num_rows > 0) {
    $basic_info = $basic_info_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আয় রিপোর্ট - Admin Panel</title>
    <link rel="stylesheet" href="modern_admin_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #fafafa;
            direction: ltr;
            color: #1a1a1a;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fffe 100%);
            border-left: none;
            padding: 35px 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #308e87 0%, #308e87 50%, #f3f4f6 50%, #f3f4f6 100%);
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(48, 142, 135, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .page-header h1 i {
            color: #308e87;
            margin-right: 15px;
            background: rgba(48, 142, 135, 0.1);
            padding: 12px;
            border-radius: 12px;
            font-size: 28px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 15px;
            margin: 0;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }
        
        .page-title {
            background: linear-gradient(135deg, #ffffff 0%, #f8fffe 100%);
            padding: 35px 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .page-title::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #308e87 0%, #308e87 50%, #f3f4f6 50%, #f3f4f6 100%);
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(48, 142, 135, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .page-title h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .page-title h1 i {
            color: #308e87;
            margin-right: 15px;
            background: rgba(48, 142, 135, 0.1);
            padding: 12px;
            border-radius: 12px;
            font-size: 28px;
        }
        
        .page-title p {
            color: #6b7280;
            font-size: 15px;
            margin: 0;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }

        .report-container {
            max-width: 100%;
        }

        .filters-section {
            background: #ffffff;
            padding: 30px;
            margin-bottom: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        
        .filters-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc2626 0%, #dc2626 40%, #f3f4f6 40%, #f3f4f6 100%);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #1f2937;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .filter-group label::before {
            content: '';
            width: 3px;
            height: 14px;
            background: #dc2626;
            border-radius: 2px;
        }

        .filter-group select,
        .filter-group input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #ffffff;
            color: #1f2937;
            font-weight: 500;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
            transform: translateY(-1px);
        }
        
        .filter-group select:hover,
        .filter-group input:hover {
            border-color: #d1d5db;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 28px;
            padding-top: 28px;
            border-top: 2px solid #f3f4f6;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn:hover i {
            transform: scale(1.1);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #308e87 0%, #2a7a73 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(48, 142, 135, 0.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2a7a73 0%, #236860 100%);
            box-shadow: 0 6px 25px rgba(48, 142, 135, 0.45);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.45);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.35);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            box-shadow: 0 6px 25px rgba(239, 68, 68, 0.45);
        }

        .btn-warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);
            border-radius: 10px;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card h3 {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card p {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #6c757d;
            background: #f3f4f6;
            margin-bottom: 12px;
        }

        .report-table-container {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        .report-table-container h2 {
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .report-table-container h2 i {
            margin-right: 10px;
            color: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f9fafb;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
            color: #1a1a1a;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        tfoot tr {
            background: #f9fafb;
            font-weight: 600;
            border-top: 2px solid #e5e7eb;
        }

        tfoot td {
            padding: 14px 16px;
            color: #2c3e50;
        }

        .no-data {
            text-align: center;
            padding: 60px;
            color: #9ca3af;
            font-size: 15px;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #6c757d;
            font-size: 15px;
        }

        @media print {
            .filters-section,
            .action-buttons,
            aside,
            nav,
            .sidebar,
            .navbar,
            .navigation,
            .page-header,
            .page-title,
            .stats-grid,
            .report-table-container,
            #export-loader {
                display: none !important;
            }
            
            #receipt-template {
                display: block !important;
            }
            
            body {
                margin: 0;
                padding: 20px;
                counter-reset: page;
            }
            
            .receipt-table tfoot {
                display: none !important;
            }
            
            .receipt-table tbody tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            @page {
                margin-bottom: 15mm;
            }
            
            #receipt-template::after {
                content: "Page " counter(page);
                counter-increment: page;
                position: fixed;
                bottom: 0px;
                right: 15px;
                font-size: 9px;
                color: #6b7280;
                font-weight: 500;
            }
            
            .logo-hindus {
                background: none !important;
                -webkit-background-clip: unset !important;
                -webkit-text-fill-color: #1f2937 !important;
                background-clip: unset !important;
                color: #1f2937 !important;
            }
        }

        /* Receipt Template Styles */
        #receipt-template {
            display: none;
            background: white;
            padding: 50px;
            max-width: 900px;
            margin: 0 auto;
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            padding: 50px 40px 40px 40px;
            margin-bottom: 30px;
            position: relative;
            background: #ffffff;
            border-bottom: none;
        }
        
        .receipt-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background: #fef2f2;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }
        
        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 150px;
            height: 6px;
            background: #dc2626;
            transform: skewX(-45deg);
            transform-origin: bottom right;
        }

        .receipt-logo {
            font-size: 56px;
            font-weight: 900;
            margin-bottom: 18px;
            letter-spacing: 2px;
            line-height: 60px;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .logo-hindus {
            color: #1f2937;
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: 900;
        }

        .logo-news {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 6px 20px 8px 20px;
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: 900;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .logo-news::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .receipt-company {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #000;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .receipt-subtitle {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            position: relative;
            z-index: 1;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
            padding: 0;
            background: transparent;
            font-size: 13px;
        }
        
        .receipt-info > div {
            padding: 15px 20px;
            background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .receipt-info > div:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .receipt-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 35px;
            padding: 20px 40px 25px 40px;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            color: #1f2937;
            border: none;
            border-bottom: 2px solid #e5e7eb;
            border-radius: 12px 12px 0 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .receipt-totals {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
            padding: 0;
        }

        .total-item {
            text-align: center;
            padding: 20px 15px;
            background: #ffffff;
            border: 2px solid #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        
        

        .total-item span {
            display: block;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .total-item strong {
            display: block;
            font-size: 26px;
            color: #1f2937;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        
        
        
        .total-item.grand-total::before {
            background: #dc2626;
            height: 5px;
        }
        
        
        .total-item.grand-total span {
            color: #1f2937;
            font-weight: 700;
        }

        .total-item.grand-total strong {
            color: #1f2937;
            font-weight: 900;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .receipt-table th {
            background: linear-gradient(1i5deg, #0fn66e 0%, #1eb8a6 a00%)gradient(135deg, #0f766e 0%, #14b8a6 100%);
            color: white;
            border: 1px solid #0ff66e;
            padding: 14px 12px;
            text-align: left;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1;
        }

        .receipt-table td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            color: #2d3748;
            background: white;
            page-break-inside: avoid;
            word-wrap: break-word;
            font-size: 13px;
        }

        .receipt-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .receipt-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .receipt-table tfoot {
            page-break-inside: avoid;
            page-break-before: avoid;
            break-inside: avoid;
        }

        .receipt-table tfoot td {
            background: 345
            color: white;
            font-weight: bold;
            font-size: 14px;
            padding: 14px 10px;
            border: 1px solid #344151;
        }

        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
        
        .page-number {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 9px;
            color: #9ca3af;
            font-weight: 500;
        }

        .receipt-footer p {
            margin: 8px 0;
        }

        /* Loading Spinner */
        #export-loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 999999;
            justify-content: center;
            align-items: center;
        }

        #export-loader.active {
            display: flex;
        }

        .loader-content {
            text-align: center;
            color: white;
        }

        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #308e87;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>

    <!-- Loading Spinner -->
    <div id="export-loader">
        <div class="loader-content">
            <div class="spinner"></div>
            <div class="loader-text">রিপোর্ট তৈরি হচ্ছে...</div>
        </div>
    </div>

    <!-- Hidden Receipt Template -->
    <div id="receipt-template">
        <div class="receipt-header">
            <div class="receipt-logo">
                <?php if (!empty($basic_info['image'])): ?>
                <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" style="max-height: 50px; width: auto;">
                <?php else: ?>
                <span><?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?></span>
                <?php endif; ?>
            </div>
            <div class="receipt-subtitle">Earning Report • আয় রিপোর্ট</div>
        </div>

        <div class="receipt-info">
            <div>
                <strong>তারিখ:</strong> <span id="receipt-date"></span>
            </div>
            <div style="text-align: right;">
                <strong>রিপোর্ট নং:</strong> <span id="receipt-number"></span>
            </div>
        </div>

        <div class="receipt-title" id="receipt-report-title">আয় রিপোর্ট</div>

        <div class="receipt-totals" id="receipt-totals">
            <div class="total-item">
                <span>মোট আয়:</span>
                <strong id="receipt-total-earning">$0.00</strong>
            </div>
            <div class="total-item">
                <span>ওয়েব আয়:</span>
                <strong id="receipt-total-web">$0.00</strong>
            </div>
            <div class="total-item">
                <span>ইউটিউব আয়:</span>
                <strong id="receipt-total-youtube">$0.00</strong>
            </div>
            <div class="total-item grand-total">
                <span>সর্বমোট:</span>
                <strong id="receipt-grand-total">$0.00</strong>
            </div>
        </div>

        <table class="receipt-table" id="receipt-data-table">
            <thead>
                <tr>
                    <th>ক্রমিক</th>
                    <th>তারিখ</th>
                    <th>সাংবাদিক</th>
                    <th>শিরোনাম</th>
                    <th>ধরন</th>
                    <th>আয় ($)</th>
                    <th>ওয়েব ($)</th>
                    <th>ইউটিউব ($)</th>
                    <th>মোট ($)</th>
                </tr>
            </thead>
            <tbody id="receipt-tbody">
            </tbody>
            <tfoot id="receipt-tfoot">
            </tfoot>
        </table>

        <div class="receipt-footer">
            <p><strong><?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?></strong> - Professional Earning Report System</p>
            <p>Generated: <span id="receipt-generated-time"></span></p>
        </div>
    </div>

    <div class="page-title">
        <h1>
            <i class="fas fa-chart-bar"></i>
            আয় রিপোর্ট
        </h1>
        <p>মাসিক এবং কাস্টম রিপোর্ট দেখুন এবং এক্সপোর্ট করুন</p>
    </div>
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="report-type">রিপোর্ট টাইপ</label>
                        <select id="report-type">
                            <option value="monthly">মাসিক রিপোর্ট</option>
                            <option value="daterange">তারিখ রেঞ্জ</option>
                            <option value="reporter">সাংবাদিক ভিত্তিক</option>
                        </select>
                    </div>

                    <div class="filter-group" id="month-selector">
                        <label for="select-month">মাস নির্বাচন করুন</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <button type="button" class="btn btn-info" onclick="previousMonth()" style="padding: 10px 14px;">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <input type="month" id="select-month" style="flex: 1;">
                            <button type="button" class="btn btn-info" onclick="nextMonth()" style="padding: 10px 14px;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="filter-group" id="from-date-group" style="display: none;">
                        <label for="from-date">শুরুর তারিখ</label>
                        <input type="date" id="from-date">
                    </div>

                    <div class="filter-group" id="to-date-group" style="display: none;">
                        <label for="to-date">শেষ তারিখ</label>
                        <input type="date" id="to-date">
                    </div>

                    <div class="filter-group">
                        <label for="filter-reporter">সাংবাদিক ফিল্টার</label>
                        <select id="filter-reporter">
                            <option value="">সকল সাংবাদিক</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-type">কন্টেন্ট টাইপ</label>
                        <select id="filter-type">
                            <option value="">সকল</option>
                            <option value="news">নিউজ</option>
                            <option value="video">ভিডিও</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="conversion-rate">মুদ্রা রূপান্তর হার (৳)</label>
                        <input type="number" id="conversion-rate" placeholder="ডলার থেকে টাকা (যেমন: ৮৫)" step="0.01" min="0">
                        <small style="display: block; margin-top: 5px; color: #6b7280;">খালি রাখলে ডলারে দেখাবে</small>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="generateReport()">
                        <i class="fas fa-chart-line"></i> রিপোর্ট দেখুন
                    </button>
                    <button class="btn btn-warning" onclick="showReportersTotalEarning()">
                        <i class="fas fa-users"></i> Reporters Total Earning
                    </button>
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-spreadsheet"></i> Excel এক্সপোর্ট
                    </button>
                    <button class="btn btn-danger" onclick="openReceiptPreview()">
                        <i class="fas fa-file-pdf"></i> রিসিট তৈরি করুন
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid" id="stats-section">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>মোট আয়</h3>
                    <p id="total-earning-stat">$0.00</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3>নিউজ আয়</h3>
                    <p id="news-earning-stat">$0.00</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>ওয়েবসাইট আয়</h3>
                    <p id="web-earning-stat">$0.00</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-youtube"></i>
                    </div>
                    <h3>ইউটিউব আয়</h3>
                    <p id="youtube-earning-stat">$0.00</p>
                </div>
            </div>

            <!-- Reporters Total Earning Table -->
            <div class="report-table-container" id="reporters-total-container" style="display: none;">
                <h2 style="margin-bottom: 20px; color: #333;">
                    <i class="fas fa-users"></i>
                    <span>Reporters Total Earning Report</span>
                </h2>
                <table id="reporters-total-table">
                    <thead>
                        <tr>
                            <th>ক্রমিক</th>
                            <th>সাংবাদিক নাম</th>
                            <th>মোট নিউজ</th>
                            <th>মোট ভিডিও</th>
                            <th>মোট আয় ($)</th>
                            <th>ওয়েবসাইট আয় ($)</th>
                            <th>ইউটিউব আয় ($)</th>
                            <th>সর্বমোট ($)</th>
                        </tr>
                    </thead>
                    <tbody id="reporters-total-tbody">
                        <tr>
                            <td colspan="8" class="loading">লোড হচ্ছে...</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: 700; background: #f8f9fa;">
                            <td colspan="2" style="text-align: right;">সর্বমোট:</td>
                            <td id="reporters-footer-news">0</td>
                            <td id="reporters-footer-videos">0</td>
                            <td id="reporters-footer-earning">$0.00</td>
                            <td id="reporters-footer-web">$0.00</td>
                            <td id="reporters-footer-youtube">$0.00</td>
                            <td id="reporters-footer-total">$0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Report Table -->
            <div class="report-table-container" id="report-container">
                <h2 style="margin-bottom: 20px; color: #333;">
                    <i class="fas fa-table"></i>
                    <span id="report-title">বর্তমান মাসের রিপোর্ট</span>
                </h2>
                <table id="report-table">
                    <thead>
                        <tr>
                            <th>ক্রমিক</th>
                            <th>তারিখ</th>
                            <th>সাংবাদিক</th>
                            <th>শিরোনাম</th>
                            <th>ধরন</th>
                            <th>আয় ($)</th>
                            <th>ওয়েবসাইট আয় ($)</th>
                            <th>ইউটিউব আয় ($)</th>
                            <th>মোট ($)</th>
                        </tr>
                    </thead>
                    <tbody id="report-tbody">
                        <tr>
                            <td colspan="9" class="loading">লোড হচ্ছে...</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: 700; background: #f8f9fa;">
                            <td colspan="5" style="text-align: right;">সর্বমোট:</td>
                            <td id="footer-earning">$0.00</td>
                            <td id="footer-web">$0.00</td>
                            <td id="footer-youtube">$0.00</td>
                            <td id="footer-total">$0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

    <script>
        const API_BASE = 'api.php';
        let reportData = [];
        let reporters = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadReporters();
            setCurrentMonth();
            generateReport();
            
            // Report type change handler
            document.getElementById('report-type').addEventListener('change', handleReportTypeChange);
        });

        function setCurrentMonth() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            document.getElementById('select-month').value = `${year}-${month}`;
        }

        function previousMonth() {
            const monthInput = document.getElementById('select-month');
            const [year, month] = monthInput.value.split('-').map(Number);
            const date = new Date(year, month - 1, 1);
            date.setMonth(date.getMonth() - 1);
            const newYear = date.getFullYear();
            const newMonth = String(date.getMonth() + 1).padStart(2, '0');
            monthInput.value = `${newYear}-${newMonth}`;
            generateReport();
        }

        function nextMonth() {
            const monthInput = document.getElementById('select-month');
            const [year, month] = monthInput.value.split('-').map(Number);
            const date = new Date(year, month - 1, 1);
            date.setMonth(date.getMonth() + 1);
            const newYear = date.getFullYear();
            const newMonth = String(date.getMonth() + 1).padStart(2, '0');
            monthInput.value = `${newYear}-${newMonth}`;
            generateReport();
        }

        function handleReportTypeChange() {
            const reportType = document.getElementById('report-type').value;
            const monthSelector = document.getElementById('month-selector');
            const fromDateGroup = document.getElementById('from-date-group');
            const toDateGroup = document.getElementById('to-date-group');

            if (reportType === 'monthly') {
                monthSelector.style.display = 'flex';
                fromDateGroup.style.display = 'none';
                toDateGroup.style.display = 'none';
            } else if (reportType === 'daterange' || reportType === 'reporter') {
                monthSelector.style.display = 'none';
                fromDateGroup.style.display = 'flex';
                toDateGroup.style.display = 'flex';
            }
        }

        async function loadReporters() {
            console.log('Loading reporters...');
            try {
                const response = await fetch(`${API_BASE}?action=get_reporters`);
                const reportersData = await response.json();
                console.log('Reporters API response:', reportersData);
                
                if (reportersData && Array.isArray(reportersData)) {
                    reporters = reportersData;
                    console.log('Loaded reporters:', reporters.length);
                    
                    const select = document.getElementById('filter-reporter');
                    select.innerHTML = '<option value="">সকল সাংবাদিক</option>';
                    
                    reporters.forEach(reporter => {
                        const option = document.createElement('option');
                        option.value = reporter.id;
                        option.textContent = reporter.name;
                        select.appendChild(option);
                    });
                    console.log('Reporter dropdown populated with', reporters.length, 'reporters');
                } else {
                    console.error('Invalid reporters data:', reportersData);
                }
            } catch (error) {
                console.error('Error loading reporters:', error);
            }
        }

        async function generateReport() {
            console.log('Starting report generation...');
            
            // Show regular report, hide reporters total
            document.getElementById('report-container').style.display = 'block';
            document.getElementById('reporters-total-container').style.display = 'none';
            
            const reportType = document.getElementById('report-type').value;
            const reporterId = document.getElementById('filter-reporter').value;
            const contentType = document.getElementById('filter-type').value;
            
            let fromDate = '';
            let toDate = '';
            
            if (reportType === 'monthly') {
                const monthValue = document.getElementById('select-month').value;
                if (monthValue) {
                    const [year, month] = monthValue.split('-');
                    fromDate = `${year}-${month}-01`;
                    const lastDay = new Date(year, month, 0).getDate();
                    toDate = `${year}-${month}-${lastDay}`;
                    document.getElementById('report-title').textContent = `${getMonthName(month)} ${year} এর রিপোর্ট`;
                }
            } else {
                fromDate = document.getElementById('from-date').value;
                toDate = document.getElementById('to-date').value;
                if (fromDate && toDate) {
                    let title = `${formatDate(fromDate)} থেকে ${formatDate(toDate)} এর রিপোর্ট`;
                    
                    // Add reporter name to title if reporter filter is selected
                    if (reporterId && reporters.length > 0) {
                        const selectedReporter = reporters.find(r => r.id == reporterId);
                        if (selectedReporter) {
                            title = `${selectedReporter.name} - ${title}`;
                        }
                    }
                    
                    document.getElementById('report-title').textContent = title;
                }
            }

            console.log('Date range:', fromDate, 'to', toDate);

            try {
                const formData = new FormData();
                formData.append('action', 'get_all_earnings');
                
                const response = await fetch(API_BASE, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (result.success) {
                    reportData = result.data;
                    console.log('Total records:', reportData.length);
                    
                    // Apply filters
                    let filteredData = reportData;
                    
                    // Date filter
                    if (fromDate && toDate) {
                        filteredData = filteredData.filter(item => {
                            const itemDate = item.created_at.split(' ')[0];
                            return itemDate >= fromDate && itemDate <= toDate;
                        });
                    }
                    
                    // Reporter filter
                    if (reporterId) {
                        filteredData = filteredData.filter(item => item.reporter_id == reporterId);
                    }
                    
                    // Content type filter
                    if (contentType) {
                        filteredData = filteredData.filter(item => item.type === contentType);
                    }
                    
                    renderReport(filteredData);
                    updateStatistics(filteredData);
                }
            } catch (error) {
                console.error('Error generating report:', error);
                document.getElementById('report-tbody').innerHTML = 
                    '<tr><td colspan="9" class="no-data">রিপোর্ট লোড করতে ব্যর্থ</td></tr>';
            }
        }

        function renderReport(data) {
            const tbody = document.getElementById('report-tbody');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="no-data">কোনো ডেটা পাওয়া যায়নি</td></tr>';
                return;
            }
            
            // Get conversion rate
            const conversionRate = parseFloat(document.getElementById('conversion-rate').value) || 0;
            const currencySymbol = conversionRate > 0 ? '৳' : '$';
            const multiplier = conversionRate > 0 ? conversionRate : 1;
            
            let totalEarning = 0;
            let totalWeb = 0;
            let totalYoutube = 0;
            
            tbody.innerHTML = data.map((item, index) => {
                const earning = (parseFloat(item.earning) || 0) * multiplier;
                const webEarning = (parseFloat(item.web_earning) || 0) * multiplier;
                const youtubeEarning = (parseFloat(item.youtube_earning) || 0) * multiplier;
                const total = earning + webEarning + youtubeEarning;
                
                totalEarning += earning;
                totalWeb += webEarning;
                totalYoutube += youtubeEarning;
                
                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${formatDate(item.created_at.split(' ')[0])}</td>
                        <td>${escapeHtml(item.reporter_name)}</td>
                        <td style="max-width: 300px;">${escapeHtml(item.headline)}</td>
                        <td>
                            <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; ${item.type === 'news' ? 'background: #d1ecf1; color: #0c5460;' : 'background: #f8d7da; color: #721c24;'}">
                                ${item.type === 'news' ? 'নিউজ' : 'ভিডিও'}
                            </span>
                        </td>
                        <td>${currencySymbol}${earning.toFixed(2)}</td>
                        <td>${item.type === 'news' ? currencySymbol + webEarning.toFixed(2) : '-'}</td>
                        <td>${item.type === 'video' ? currencySymbol + youtubeEarning.toFixed(2) : '-'}</td>
                        <td style="font-weight: 700;">${currencySymbol}${total.toFixed(2)}</td>
                    </tr>
                `;
            }).join('');
            
            // Update footer
            document.getElementById('footer-earning').textContent = currencySymbol + totalEarning.toFixed(2);
            document.getElementById('footer-web').textContent = '$' + totalWeb.toFixed(2);
            document.getElementById('footer-youtube').textContent = '$' + totalYoutube.toFixed(2);
            document.getElementById('footer-total').textContent = '$' + (totalEarning + totalWeb + totalYoutube).toFixed(2);
        }

        function updateStatistics(data) {
            // Get conversion rate
            const conversionRate = parseFloat(document.getElementById('conversion-rate').value) || 0;
            const currencySymbol = conversionRate > 0 ? '৳' : '$';
            const multiplier = conversionRate > 0 ? conversionRate : 1;
            
            let totalEarning = 0;
            let newsEarning = 0;
            let webEarning = 0;
            let youtubeEarning = 0;
            
            data.forEach(item => {
                const earning = (parseFloat(item.earning) || 0) * multiplier;
                const web = (parseFloat(item.web_earning) || 0) * multiplier;
                const youtube = (parseFloat(item.youtube_earning) || 0) * multiplier;
                
                totalEarning += earning;
                webEarning += web;
                youtubeEarning += youtube;
                
                if (item.type === 'news') {
                    newsEarning += earning + web;
                }
            });
            
            document.getElementById('total-earning-stat').textContent = currencySymbol + (totalEarning + webEarning + youtubeEarning).toFixed(2);
            document.getElementById('news-earning-stat').textContent = currencySymbol + newsEarning.toFixed(2);
            document.getElementById('web-earning-stat').textContent = currencySymbol + webEarning.toFixed(2);
            document.getElementById('youtube-earning-stat').textContent = currencySymbol + youtubeEarning.toFixed(2);
        }

        function generateReceiptContent() {
            // Get current filtered data from the table
            const tbody = document.getElementById('report-tbody');
            const rows = tbody.querySelectorAll('tr');
            
            if (rows.length === 0 || rows[0].querySelector('.no-data, .loading')) {
                alert('কোনো ডেটা নেই!');
                return false;
            }

            // Set receipt header info
            const today = new Date();
            document.getElementById('receipt-date').textContent = formatDate(today.toISOString().split('T')[0]);
            document.getElementById('receipt-number').textContent = 'ER-' + Date.now().toString().slice(-8);
            document.getElementById('receipt-generated-time').textContent = today.toLocaleString('bn-BD');
            
            // Set report title
            const reportTitle = document.getElementById('report-title').textContent;
            document.getElementById('receipt-report-title').textContent = reportTitle;
            
            // Get currency symbol based on conversion rate
            const conversionRate = parseFloat(document.getElementById('conversion-rate').value) || 0;
            const currencySymbol = conversionRate > 0 ? '৳' : '$';
            
            // Restore original receipt table header for regular report with correct currency
            const receiptTable = document.getElementById('receipt-data-table');
            const thead = receiptTable.querySelector('thead tr');
            thead.innerHTML = `
                <th>ক্রমিক</th>
                <th>তারিখ</th>
                <th>সাংবাদিক</th>
                <th>শিরোনাম</th>
                <th>ধরন</th>
                <th>আয় (${currencySymbol})</th>
                <th>ওয়েব (${currencySymbol})</th>
                <th>ইউটিউব (${currencySymbol})</th>
                <th>মোট (${currencySymbol})</th>
            `;
            
            // Get footer totals
            const footerEarning = document.getElementById('footer-earning').textContent;
            const footerWeb = document.getElementById('footer-web').textContent;
            const footerYoutube = document.getElementById('footer-youtube').textContent;
            const footerTotal = document.getElementById('footer-total').textContent;
            
            // Generate receipt tbody
            const receiptTbody = document.getElementById('receipt-tbody');
            receiptTbody.innerHTML = Array.from(rows).map(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 9) return '';
                
                return `
                    <tr>
                        <td>${cells[0].textContent}</td>
                        <td>${cells[1].textContent}</td>
                        <td>${cells[2].textContent}</td>
                        <td style="max-width: 250px; word-wrap: break-word;">${cells[3].textContent}</td>
                        <td>${cells[4].textContent.trim()}</td>
                        <td align="right">${cells[5].textContent}</td>
                        <td align="right">${cells[6].textContent}</td>
                        <td align="right">${cells[7].textContent}</td>
                        <td align="right"><strong>${cells[8].textContent}</strong></td>
                    </tr>
                `;
            }).join('');
            
            // Populate receipt totals at top
            document.getElementById('receipt-total-earning').textContent = footerEarning;
            document.getElementById('receipt-total-web').textContent = footerWeb;
            document.getElementById('receipt-total-youtube').textContent = footerYoutube;
            document.getElementById('receipt-grand-total').textContent = footerTotal;
            
            // Add tfoot totals under table
            const receiptTfoot = document.getElementById('receipt-tfoot');
            receiptTfoot.innerHTML = `
                <tr>
                    <td colspan="5" align="right"><strong>সর্বমোট / Grand Total:</strong></td>
                    <td align="right"><strong>${footerEarning}</strong></td>
                    <td align="right"><strong>${footerWeb}</strong></td>
                    <td align="right"><strong>${footerYoutube}</strong></td>
                    <td align="right"><strong>${footerTotal}</strong></td>
                </tr>
            `;
            
            return true;
        }

        function showLoader() {
            document.getElementById('export-loader').classList.add('active');
        }

        function hideLoader() {
            document.getElementById('export-loader').classList.remove('active');
        }

        function generateReportersTotalReceiptContent() {
            // Get reporters total data from the table
            const tbody = document.getElementById('reporters-total-tbody');
            const rows = tbody.querySelectorAll('tr');
            
            if (rows.length === 0 || rows[0].querySelector('.no-data, .loading')) {
                alert('কোনো ডেটা নেই!');
                return false;
            }

            // Set receipt header info
            const today = new Date();
            document.getElementById('receipt-date').textContent = formatDate(today.toISOString().split('T')[0]);
            document.getElementById('receipt-number').textContent = 'RTE-' + Date.now().toString().slice(-8);
            document.getElementById('receipt-generated-time').textContent = today.toLocaleString('bn-BD');
            
            // Set report title
            document.getElementById('receipt-report-title').textContent = 'Reporters Total Earning Report';
            
            // Get currency symbol based on conversion rate
            const conversionRate = parseFloat(document.getElementById('conversion-rate').value) || 0;
            const currencySymbol = conversionRate > 0 ? '৳' : '$';
            
            // Update receipt table header for reporters total structure with correct currency
            const receiptTable = document.getElementById('receipt-data-table');
            const thead = receiptTable.querySelector('thead tr');
            thead.innerHTML = `
                <th>ক্রমিক</th>
                <th colspan="2">সাংবাদিক নাম</th>
                <th>মোট নিউজ</th>
                <th>মোট ভিডিও</th>
                <th>মোট আয় (${currencySymbol})</th>
                <th>ওয়েব (${currencySymbol})</th>
                <th>ইউটিউব (${currencySymbol})</th>
                <th>সর্বমোট (${currencySymbol})</th>
            `;
            
            // Get footer totals
            const footerNews = document.getElementById('reporters-footer-news').textContent;
            const footerVideos = document.getElementById('reporters-footer-videos').textContent;
            const footerEarning = document.getElementById('reporters-footer-earning').textContent;
            const footerWeb = document.getElementById('reporters-footer-web').textContent;
            const footerYoutube = document.getElementById('reporters-footer-youtube').textContent;
            const footerTotal = document.getElementById('reporters-footer-total').textContent;
            
            // Generate receipt tbody - match reporters total table structure
            const receiptTbody = document.getElementById('receipt-tbody');
            receiptTbody.innerHTML = Array.from(rows).map(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 8) return '';
                
                return `
                    <tr>
                        <td>${cells[0].textContent}</td>
                        <td colspan="2">${cells[1].textContent}</td>
                        <td>${cells[2].textContent}</td>
                        <td>${cells[3].textContent}</td>
                        <td align="right">${cells[4].textContent}</td>
                        <td align="right">${cells[5].textContent}</td>
                        <td align="right">${cells[6].textContent}</td>
                        <td align="right"><strong>${cells[7].textContent}</strong></td>
                    </tr>
                `;
            }).join('');
            
            // Populate receipt totals at top
            document.getElementById('receipt-total-earning').textContent = footerEarning;
            document.getElementById('receipt-total-web').textContent = footerWeb;
            document.getElementById('receipt-total-youtube').textContent = footerYoutube;
            document.getElementById('receipt-grand-total').textContent = footerTotal;
            
            // Add tfoot totals under table
            const receiptTfoot = document.getElementById('receipt-tfoot');
            receiptTfoot.innerHTML = `
                <tr>
                    <td colspan="5" align="right"><strong>সর্বমোট / Grand Total:</strong></td>
                    <td align="right"><strong>${footerEarning}</strong></td>
                    <td align="right"><strong>${footerWeb}</strong></td>
                    <td align="right"><strong>${footerYoutube}</strong></td>
                    <td align="right"><strong>${footerTotal}</strong></td>
                </tr>
            `;
            
            return true;
        }

        function openReceiptPreview() {
            // Check which report is currently visible
            const regularReportVisible = document.getElementById('report-container').style.display !== 'none';
            const reportersTotalVisible = document.getElementById('reporters-total-container').style.display !== 'none';
            
            let success = false;
            if (reportersTotalVisible) {
                success = generateReportersTotalReceiptContent();
            } else if (regularReportVisible) {
                success = generateReceiptContent();
            } else {
                alert('কোনো রিপোর্ট দেখানো হচ্ছে না!');
                return;
            }
            
            if (!success) return;
            
            showLoader();
            
            setTimeout(() => {
                const receiptHTML = document.getElementById('receipt-template').innerHTML;
                const previewWindow = window.open('', '_blank', 'width=1000,height=900');
                
                previewWindow.document.write(`<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Earning Report Receipt - Preview</title>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"><\/script>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body {
                                font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
                                padding: 0;
                                background: #f5f5f5;
                            }
                            
                            .action-bar {
                                background: linear-gradient(135deg, #308e87 0%, #2a7a73 100%);
                                padding: 25px 30px;
                                text-align: center;
                                position: sticky;
                                top: 0;
                                z-index: 1000;
                                box-shadow: 0 4px 20px rgba(48, 142, 135, 0.3);
                            }
                            
                            .action-bar h2 {
                                color: white;
                                margin-bottom: 20px;
                                font-size: 22px;
                                font-weight: 700;
                                letter-spacing: 0.5px;
                                text-transform: uppercase;
                            }
                            
                            .export-buttons {
                                display: flex;
                                gap: 15px;
                                justify-content: center;
                                flex-wrap: wrap;
                            }
                            
                            .btn {
                                padding: 14px 28px;
                                border: none;
                                cursor: pointer;
                                font-size: 14px;
                                font-weight: 700;
                                transition: all 0.3s;
                                display: inline-flex;
                                align-items: center;
                                gap: 10px;
                                border-radius: 12px;
                                text-transform: uppercase;
                                letter-spacing: 0.8px;
                                position: relative;
                                overflow: hidden;
                            }
                            
                            .btn::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: -100%;
                                width: 100%;
                                height: 100%;
                                background: rgba(255, 255, 255, 0.2);
                                transition: left 0.4s;
                            }
                            
                            .btn:hover::before {
                                left: 100%;
                            }
                            
                            .btn:hover {
                                transform: translateY(-3px);
                                box-shadow: 0 6px 20px rgba(0,0,0,0.25);
                            }
                            
                            .btn-pdf {
                                background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
                                color: white;
                                box-shadow: 0 3px 10px rgba(220, 38, 38, 0.4);
                            }
                            
                            .btn-pdf:hover {
                                box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
                            }
                            
                            .btn-print {
                                background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
                                color: white;
                                box-shadow: 0 3px 10px rgba(2, 132, 199, 0.4);
                            }
                            
                            .btn-print:hover {
                                box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5);
                            }
                            
                            .btn-image {
                                background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
                                color: white;
                                box-shadow: 0 3px 10px rgba(217, 119, 6, 0.4);
                            }
                            
                            .btn-image:hover {
                                box-shadow: 0 6px 20px rgba(217, 119, 6, 0.5);
                            }
                            
                            .receipt-container {
                                max-width: 900px;
                                margin: 30px auto;
                                background: white;
                                padding: 40px;
                                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                            }
                            
                            .receipt-header {
                                text-align: center;
                                padding: 50px 40px 40px 40px;
                                margin-bottom: 30px;
                                position: relative;
                                background: #ffffff;
                                border-bottom: none;
                            }
                            .receipt-header::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 200px;
                                height: 200px;
                                background: #fef2f2;
                                clip-path: polygon(0 0, 100% 0, 0 100%);
                            }
                            .receipt-header::after {
                                content: '';
                                position: absolute;
                                bottom: -2px;
                                right: 0;
                                width: 150px;
                                height: 6px;
                                background: #dc2626;
                                transform: skewX(-45deg);
                                transform-origin: bottom right;
                            }
                            .receipt-logo {
                                font-size: 56px;
                                font-weight: 900;
                                margin-bottom: 18px;
                                letter-spacing: 2px;
                                line-height: 60px;
                                position: relative;
                                z-index: 1;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 12px;
                            }
                            .logo-hindus {
                                color: #1f2937;
                                font-family: 'Arial Black', Arial, sans-serif;
                                font-weight: 900;
                            }
                            .logo-news {
                                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                color: white;
                                padding: 6px 20px 8px 20px;
                                font-family: 'Arial Black', Arial, sans-serif;
                                font-weight: 900;
                                border-radius: 8px;
                                box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
                                position: relative;
                                overflow: hidden;
                            }
                            .logo-news::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: -100%;
                                width: 100%;
                                height: 100%;
                                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                                animation: logoShine 3s infinite;
                            }
                            @keyframes logoShine {
                                0% { left: -100%; }
                                50% { left: 100%; }
                                100% { left: 100%; }
                            }
                            .receipt-subtitle {
                                font-size: 13px;
                                color: #6b7280;
                                font-weight: 500;
                                text-transform: uppercase;
                                letter-spacing: 2.5px;
                                position: relative;
                                z-index: 1;
                            }
                            .receipt-info {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 25px;
                                margin-bottom: 30px;
                                padding: 0;
                                background: transparent;
                                font-size: 13px;
                            }
                            .receipt-info > div {
                                padding: 15px 20px;
                                background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
                                border: 1px solid #e5e7eb;
                                border-radius: 8px;
                            }
                            .receipt-title {
                                text-align: center;
                                font-size: 22px;
                                font-weight: 700;
                                margin-bottom: 35px;
                                padding: 20px 40px 25px 40px;
                                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
                                color: #1f2937;
                                border: none;
                                border-bottom: 2px solid #e5e7eb;
                                border-radius: 12px 12px 0 0;
                                text-transform: uppercase;
                                letter-spacing: 2px;
                                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
                            }
                            
                            .receipt-totals {
                                display: grid;
                                grid-template-columns: repeat(4, 1fr);
                                gap: 18px;
                                margin-bottom: 30px;
                                padding: 0;
                            }
                            .total-item {
                                text-align: center;
                                padding: 20px 15px;
                                background: #ffffff;
                                border: 2px solid #f3f4f6;
                                position: relative;
                                overflow: hidden;
                            }
                            
                            .total-item span {
                                display: block;
                                font-size: 11px;
                                color: #6b7280;
                                margin-bottom: 10px;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                                position: relative;
                                z-index: 1;
                            }
                            .total-item strong {
                                display: block;
                                font-size: 26px;
                                color: #1f2937;
                                font-weight: 700;
                                position: relative;
                                z-index: 1;
                            }
                            
                            .total-item.grand-total::before {
                                background: #dc2626;
                                height: 5px;
                            }
                            
                            .total-item.grand-total span {
                                color: #1f2937;
                                font-weight: 700;
                            }
                            .total-item.grand-total strong {
                                color: #1f2937;
                                font-weight: 900;
                            }
                            .receipt-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 20px;
                                font-size: 13px;
                                border-radius: 8px;
                                overflow: hidden;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                            }
                            .receipt-table th {
                                background: #374151;
                                color: white;
                                border: 1px solid #374151;
                                padding: 12px 10px;
                                text-align: left;
                                font-weight: 700;
                                font-size: 13px;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                            }
                            .receipt-table td {
                                border: 1px solid #dee2e6;
                                padding: 10px 8px;
                                color: #2d3748;
                                background: white;
                                word-wrap: break-word;
                                font-size: 13px;
                            }
                            .receipt-table tbody tr {
                                page-break-inside: avoid;
                            }
                            .receipt-table tbody tr:nth-child(even) {
                                background: #f9f9f9;
                            }
                            .receipt-table tfoot td {
                                background: #374151;
                                color: white;
                                font-weight: bold;
                                font-size: 14px;
                                padding: 14px 10px;
                                border: 1px solid #374151;
                            }
                            .receipt-footer {
                                margin-top: 40px;
                                padding-top: 20px;
                                border-top: 2px solid #e5e7eb;
                                text-align: center;
                                font-size: 11px;
                                color: #9ca3af;
                            }
                            .page-number {
                                position: fixed;
                                bottom: 10px;
                                right: 20px;
                                font-size: 10px;
                                color: #6b7280;
                                font-weight: 600;
                            }
                            
                            @media print {
                                .action-bar { display: none !important; }
                                .receipt-container {
                                    margin: 0;
                                    padding: 20px;
                                    box-shadow: none;
                                }
                                body { 
                                    background: white;
                                    counter-reset: page;
                                }
                                .receipt-table tfoot { 
                                    display: none !important;
                                }
                                .receipt-table tbody tr {
                                    page-break-inside: avoid !important;
                                    break-inside: avoid !important;
                                }
                                * {
                                    -webkit-print-color-adjust: exact !important;
                                    print-color-adjust: exact !important;
                                    color-adjust: exact !important;
                                }
                                @page {
                                    margin-bottom: 15mm;
                                }
                                .receipt-container::after {
                                    content: "Page " counter(page);
                                    counter-increment: page;
                                    position: fixed;
                                    bottom: 3px;
                                    right: 15px;
                                    font-size: 9px;
                                    color: #6b7280;
                                    font-weight: 500;
                                }
                                .logo-hindus {
                                    background: none !important;
                                    -webkit-background-clip: unset !important;
                                    -webkit-text-fill-color: #1f2937 !important;
                                    background-clip: unset !important;
                                    color: #1f2937 !important;
                                }
                            }
                            
                            .loader {
                                display: none;
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(0,0,0,0.8);
                                z-index: 9999;
                                justify-content: center;
                                align-items: center;
                            }
                            .loader.active { display: flex; }
                            .loader-content {
                                text-align: center;
                                color: white;
                            }
                            .spinner {
                                border: 5px solid #f3f3f3;
                                border-top: 5px solid #308e87;
                                border-radius: 50%;
                                width: 60px;
                                height: 60px;
                                animation: spin 1s linear infinite;
                                margin: 0 auto 20px;
                            }
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                            .loader-text {
                                font-size: 18px;
                                font-weight: 600;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="loader" id="loader">
                            <div class="loader-content">
                                <div class="spinner"></div>
                                <div class="loader-text">প্রস্তুত করা হচ্ছে...</div>
                            </div>
                        </div>
                        
                        <div class="action-bar">
                            <h2>রিপোর্ট এক্সপোর্ট করুন</h2>
                            <div class="export-buttons">
                                <button class="btn btn-pdf" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf"></i> PDF ডাউনলোড
                                </button>
                                <button class="btn btn-print" onclick="printReceipt()">
                                    <i class="fas fa-print"></i> প্রিন্ট করুন
                                </button>
                                <button class="btn btn-image" onclick="exportImage()">
                                    <i class="fas fa-image"></i> Image ডাউনলোড
                                </button>
                            </div>
                        </div>
                        
                        <div class="receipt-container" id="receipt-content">
                        </div>
                        
                        <script>
                            function showLoader() {
                                document.getElementById('loader').classList.add('active');
                            }
                            
                            function hideLoader() {
                                document.getElementById('loader').classList.remove('active');
                            }
                            
                            function exportPDF() {
                                showLoader();
                                const receiptElement = document.getElementById('receipt-content');
                                
                                setTimeout(() => {
                                    html2canvas(receiptElement, {
                                        scale: 2,
                                        backgroundColor: '#ffffff',
                                        logging: false,
                                        useCORS: true,
                                        allowTaint: true
                                    }).then(canvas => {
                                        const { jsPDF } = window.jspdf;
                                        const pdf = new jsPDF('p', 'mm', 'a4');
                                        
                                        const pageWidth = 210;
                                        const pageHeight = 297;
                                        const margin = 5;
                                        const contentWidth = pageWidth - (2 * margin);
                                        
                                        const imgWidth = contentWidth;
                                        const pageHeightInPixels = (pageHeight - 2 * margin) * (canvas.width / contentWidth);
                                        
                                        const imgData = canvas.toDataURL('image/png', 1.0);
                                        let yPosition = 0;
                                        let pageCount = 0;
                                        
                                        while (yPosition < canvas.height) {
                                            if (pageCount > 0) {
                                                pdf.addPage();
                                            }
                                            
                                            const sourceY = yPosition;
                                            const sourceHeight = Math.min(pageHeightInPixels, canvas.height - yPosition);
                                            
                                            const pageCanvas = document.createElement('canvas');
                                            pageCanvas.width = canvas.width;
                                            pageCanvas.height = sourceHeight;
                                            const ctx = pageCanvas.getContext('2d');
                                            
                                            ctx.drawImage(canvas, 0, sourceY, canvas.width, sourceHeight, 0, 0, canvas.width, sourceHeight);
                                            
                                            const pageImgData = pageCanvas.toDataURL('image/png', 1.0);
                                            const sliceHeight = (sourceHeight * contentWidth) / canvas.width;
                                            
                                            pdf.addImage(pageImgData, 'PNG', margin, margin, contentWidth, sliceHeight);
                                            
                                            // Add page number
                                            pdf.setFontSize(8);
                                            pdf.setTextColor(156, 163, 175);
                                            const totalPages = Math.ceil(canvas.height / pageHeightInPixels);
                                            pdf.text('Page ' + (pageCount + 1) + ' of ' + totalPages, pageWidth - 25, pageHeight - 5);
                                            
                                            yPosition += pageHeightInPixels;
                                            pageCount++;
                                        }
                                        
                                        pdf.save('Earning_Report_Receipt.pdf');
                                        hideLoader();
                                    }).catch(error => {
                                        console.error('PDF error:', error);
                                        hideLoader();
                                        alert('PDF তৈরিতে সমস্যা হয়েছে');
                                    });
                                }, 300);
                            }
                            
                            function printReceipt() {
                                window.print();
                            }
                            
                            function exportImage() {
                                showLoader();
                                const receiptElement = document.getElementById('receipt-content');
                                
                                setTimeout(() => {
                                    html2canvas(receiptElement, {
                                        scale: 2,
                                        backgroundColor: '#ffffff',
                                        logging: false,
                                        useCORS: true
                                    }).then(canvas => {
                                        const link = document.createElement('a');
                                        link.download = 'Earning_Report_Receipt.png';
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                        hideLoader();
                                    }).catch(error => {
                                        console.error('Image error:', error);
                                        hideLoader();
                                        alert('ছবি তৈরিতে সমস্যা হয়েছে');
                                    });
                                }, 300);
                            }
                        <\/script>
                    </body>
                    </html>
                `);
                
                previewWindow.document.close();
                
                // Inject receipt HTML after document is ready
                setTimeout(() => {
                    const contentElement = previewWindow.document.getElementById('receipt-content');
                    if (contentElement) {
                        contentElement.innerHTML = receiptHTML;
                    } else {
                        console.error('Receipt content element not found in preview window');
                    }
                }, 300);
                
                hideLoader();
            }, 500);
        }

        function exportToExcel() {
            const table = document.getElementById('report-table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Report"});
            const reportTitle = document.getElementById('report-title').textContent;
            XLSX.writeFile(wb, `${reportTitle}.xlsx`);
        }

        async function showReportersTotalEarning() {
            // Hide regular report, show reporters total
            document.getElementById('report-container').style.display = 'none';
            document.getElementById('reporters-total-container').style.display = 'block';
            
            const tbody = document.getElementById('reporters-total-tbody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading">লোড হচ্ছে...</td></tr>';
            
            // Get date filters from the form
            const reportType = document.getElementById('report-type').value;
            let fromDate = '';
            let toDate = '';
            
            if (reportType === 'monthly') {
                const monthValue = document.getElementById('select-month').value;
                if (monthValue) {
                    const [year, month] = monthValue.split('-');
                    fromDate = `${year}-${month}-01`;
                    const lastDay = new Date(year, month, 0).getDate();
                    toDate = `${year}-${month}-${String(lastDay).padStart(2, '0')}`;
                }
            } else if (reportType === 'custom') {
                fromDate = document.getElementById('from-date').value;
                toDate = document.getElementById('to-date').value;
            }
            
            try {
                // Fetch data from backend with date filters
                const response = await fetch('get_reporter_earnings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        from_date: fromDate,
                        to_date: toDate
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.reporters && data.reporters.length > 0) {
                    // Get conversion rate
                    const conversionRate = parseFloat(document.getElementById('conversion-rate').value) || 0;
                    const currencySymbol = conversionRate > 0 ? '৳' : '$';
                    const multiplier = conversionRate > 0 ? conversionRate : 1;
                    
                    let html = '';
                    let totalNews = 0;
                    let totalVideos = 0;
                    let totalEarning = 0;
                    let totalWeb = 0;
                    let totalYoutube = 0;
                    let grandTotal = 0;
                    
                    data.reporters.forEach((reporter, index) => {
                        const earning = parseFloat(reporter.total_earning) * multiplier;
                        const webEarning = parseFloat(reporter.web_earning) * multiplier;
                        const youtubeEarning = parseFloat(reporter.youtube_earning) * multiplier;
                        const reporterTotal = earning + webEarning + youtubeEarning;
                        
                        totalNews += parseInt(reporter.news_count);
                        totalVideos += parseInt(reporter.video_count);
                        totalEarning += earning;
                        totalWeb += webEarning;
                        totalYoutube += youtubeEarning;
                        grandTotal += reporterTotal;
                        
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${escapeHtml(reporter.reporter_name)}</td>
                                <td>${reporter.news_count}</td>
                                <td>${reporter.video_count}</td>
                                <td>${currencySymbol}${earning.toFixed(2)}</td>
                                <td>${currencySymbol}${webEarning.toFixed(2)}</td>
                                <td>${currencySymbol}${youtubeEarning.toFixed(2)}</td>
                                <td><strong>${currencySymbol}${reporterTotal.toFixed(2)}</strong></td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                    
                    // Update footer
                    document.getElementById('reporters-footer-news').textContent = totalNews;
                    document.getElementById('reporters-footer-videos').textContent = totalVideos;
                    document.getElementById('reporters-footer-earning').textContent = `${currencySymbol}${totalEarning.toFixed(2)}`;
                    document.getElementById('reporters-footer-web').textContent = `${currencySymbol}${totalWeb.toFixed(2)}`;
                    document.getElementById('reporters-footer-youtube').textContent = `${currencySymbol}${totalYoutube.toFixed(2)}`;
                    document.getElementById('reporters-footer-total').textContent = `${currencySymbol}${grandTotal.toFixed(2)}`;
                    
                    // Update stats-grid with reporters total data
                    document.getElementById('total-earning-stat').textContent = currencySymbol + grandTotal.toFixed(2);
                    document.getElementById('news-earning-stat').textContent = totalNews + ' News';
                    document.getElementById('web-earning-stat').textContent = currencySymbol + totalWeb.toFixed(2);
                    document.getElementById('youtube-earning-stat').textContent = currencySymbol + totalYoutube.toFixed(2);
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="no-data">কোনো ডেটা পাওয়া যায়নি</td></tr>';
                    
                    // Reset stats
                    document.getElementById('total-earning-stat').textContent = '$0.00';
                    document.getElementById('news-earning-stat').textContent = '0 News';
                    document.getElementById('web-earning-stat').textContent = '$0.00';
                    document.getElementById('youtube-earning-stat').textContent = '$0.00';
                }
            } catch (error) {
                console.error('Error fetching reporter totals:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="no-data">ডেটা লোড করতে সমস্যা হয়েছে</td></tr>';
            }
        }

        function exportToPDF() {
            if (!generateReceiptContent()) return;
            
            showLoader();
            
            const receiptElement = document.getElementById('receipt-template');
            receiptElement.style.display = 'block';
            
            setTimeout(() => {
                html2canvas(receiptElement, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true,
                    allowTaint: true
                }).then(canvas => {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    
                    const pageWidth = 210; // A4 width in mm
                    const pageHeight = 297; // A4 height in mm
                    const margin = 10; // Margin
                    const contentWidth = pageWidth - (2 * margin);
                    const contentHeight = pageHeight - (2 * margin);
                    
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    
                    // Calculate dimensions to fit entire content on one page
                    const canvasAspectRatio = canvas.width / canvas.height;
                    const contentAspectRatio = contentWidth / contentHeight;
                    
                    let imgWidth, imgHeight;
                    
                    if (canvasAspectRatio > contentAspectRatio) {
                        // Canvas is wider, fit to width
                        imgWidth = contentWidth;
                        imgHeight = contentWidth / canvasAspectRatio;
                    } else {
                        // Canvas is taller, fit to height
                        imgHeight = contentHeight;
                        imgWidth = contentHeight * canvasAspectRatio;
                    }
                    
                    // Center the content on the page
                    const xPosition = margin + (contentWidth - imgWidth) / 2;
                    const yPosition = margin + (contentHeight - imgHeight) / 2;
                    
                    // Add entire content as single image on one page
                    pdf.addImage(imgData, 'PNG', xPosition, yPosition, imgWidth, imgHeight, '', 'FAST');
                    
                    const reportTitle = document.getElementById('report-title').textContent;
                    pdf.save(`${reportTitle}_Receipt.pdf`);
                    
                    receiptElement.style.display = 'none';
                    hideLoader();
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    receiptElement.style.display = 'none';
                    hideLoader();
                    alert('PDF তৈরিতে সমস্যা হয়েছে');
                });
            }, 300);
        }

        function exportToImage() {
            if (!generateReceiptContent()) return;
            
            showLoader();
            
            const receiptElement = document.getElementById('receipt-template');
            receiptElement.style.display = 'block';
            
            setTimeout(() => {
                html2canvas(receiptElement, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false
                }).then(canvas => {
                    const link = document.createElement('a');
                    const reportTitle = document.getElementById('report-title').textContent;
                    link.download = `${reportTitle}_Receipt.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    
                    receiptElement.style.display = 'none';
                    hideLoader();
                }).catch(error => {
                    console.error('Image generation error:', error);
                    receiptElement.style.display = 'none';
                    hideLoader();
                    alert('ছবি তৈরিতে সমস্যা হয়েছে');
                });
            }, 100);
        }

        function printReceipt() {
            if (!generateReceiptContent()) return;
            
            showLoader();
            
            setTimeout(() => {
                // Create new window with receipt only
                const receiptHTML = document.getElementById('receipt-template').innerHTML;
                const printWindow = window.open('', '_blank', 'width=900,height=800');
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Earning Report Receipt</title>
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body {
                                font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
                                color: #333;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 2px;
                            }
                            .receipt-info {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 15px;
                                margin-bottom: 20px;
                                padding: 15px 20px;
                                background: #f5f5f5;
                                border: 1px solid #ddd;
                            }
                            .receipt-title {
                                text-align: center;
                                font-size: 20px;
                                font-weight: 700;
                                margin-bottom: 20px;
                                padding: 15px 20px;
                                background: #f0f0f0;
                                color: #000;
                                border: 2px solid #333;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                            }
                            .receipt-totals {
                                display: grid;
                                grid-template-columns: repeat(4, 1fr);
                                gap: 15px;
                                margin-bottom: 20px;
                                padding: 20px;
                                background: #f5f5f5;
                                border: 2px solid #333;
                            }
                            .total-item {
                                text-align: center;
                                padding: 10px;
                                border: 1px solid #ccc;
                            }
                            .total-item span {
                                display: block;
                                font-size: 11px;
                                color: #333;
                                margin-bottom: 8px;
                                font-weight: 600;
                                text-transform: uppercase;
                            }
                            .total-item strong {
                                display: block;
                                font-size: 20px;
                                color: #000;
                                font-weight: 700;
                            }
                            .total-item.grand-total {
                                background: #333;
                                color: white;
                                padding: 15px;
                            }
                            .total-item.grand-total span,
                            .total-item.grand-total strong {
                                color: #fff;
                            }
                            .receipt-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 20px;
                                font-size: 10px;
                            }
                            .receipt-table th {
                                background: #333;
                                color: white;
                                border: 1px solid #000;
                                padding: 8px 6px;
                                text-align: left;
                                font-weight: 600;
                                font-size: 11px;
                            }
                            .receipt-table td {
                                border: 1px solid #999;
                                padding: 6px 4px;
                                color: #000;
                                background: white;
                                page-break-inside: avoid;
                                word-wrap: break-word;
                            }
                            .receipt-table tbody tr {
                                page-break-inside: avoid;
                            }
                            .receipt-table tbody tr:nth-child(even) {
                                background: #f9f9f9;
                            }
                            .receipt-table tfoot td {
                                background: #333;
                                color: white;
                                font-weight: bold;
                                font-size: 11px;
                                padding: 10px 6px;
                                border: 1px solid #000;
                            }
                            .receipt-footer {
                                margin-top: 40px;
                                padding-top: 25px;
                                border-top: 2px solid #ddd;
                                text-align: center;
                                font-size: 12px;
                                color: #888;
                            }
                            @media print {
                                body { padding: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        ${receiptHTML}
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
                
                // Wait for content to load then print
                setTimeout(() => {
                    printWindow.print();
                    hideLoader();
                }, 500);
            }, 500);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function getMonthName(month) {
            const months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 
                           'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
            return months[parseInt(month) - 1];
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
