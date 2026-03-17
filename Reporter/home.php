<?php
require_once __DIR__ . '/reporter_connection.php';

// Redirect if no valid connection or reporter
if (!$conn || $user_id <= 0) {
    header('Location: index.php');
    exit;
}
if ($reporter_id <= 0) {
    header('Location: index.php?user_id=' . $user_id);
    exit;
}

// Fetch basic_info for dynamic logo and portal name
$basic_info = [];
$basic_info_result = $conn->query("SELECT * FROM basic_info LIMIT 1");
if ($basic_info_result && $basic_info_result->num_rows > 0) {
    $basic_info = $basic_info_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporter Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #2c3e50;
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e8e8e8;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .logo {
            height: 40px;
        }

        .nav-menu {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: #2c3e50;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            transition: width 0.3s;
        }

        .nav-menu a:hover {
            color: #e74c3c;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 2px;
            background: #2c3e50;
            transition: 0.3s;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 6rem 2rem;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f0f0f0;
            border-top-color: #e74c3c;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Profile Layout */
        .profile-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            display: none;
        }

        /* Left Side */
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .profile-photo-wrapper {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-photo {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid red;
            box-shadow: 0 8px 30px rgba(231, 76, 60, 0.3);
        }

        .profile-name {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #2c3e50;
        }

        .profile-role {
            text-align: center;
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .total-earning-box {
            background: red;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
        }

        .earning-label {
            font-size: 12px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            font-weight: 600;
            opacity: 0.9;
        }

        .earning-value {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
        }

        /* Pagination (red theme) */
        .pagination {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
            margin-top: 12px;
        }
        .page-btn {
            border: 1px solid #e74c3c;
            color: #e74c3c;
            background: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .page-btn:hover { background: #fff5f5; }
        .page-btn.active { background: #e74c3c; color: #fff; }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Headline link + responsive truncation */
        .headline-link { color: #2c3e50; text-decoration: none; cursor: pointer; }
        .full-headline { display: inline; }
        .short-headline { display: none; }
        @media (max-width: 768px) {
            .full-headline { display: none; }
            .short-headline { display: inline; }
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .modal-box {
            background: #fff;
            width: min(800px, 92%);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: #fff;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title { font-size: 18px; font-weight: 700; }
        .modal-close { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; }
        .modal-body { padding: 16px 18px; color: #2c3e50; }

        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e8e8e8;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .info-value {
            color: #2c3e50;
            font-size: 14px;
            word-break: break-word;
            font-weight: 500;
        }

        /* Right Side */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .earnings-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e8e8e8;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .payment-history-btn {
            background: red;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(255,0,0,0.3);
            transition: all 0.2s ease;
        }

        .payment-history-btn:hover {
            background: #cc0000;
            transform: translateY(-1px);
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .earnings-table {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: red;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            font-weight: 600;
        }

        td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid #e8e8e8;
            font-size: 14px;
        }

        tbody tr {
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: #fff5f5;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .earnings-tfoot tr {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            font-weight: bold;
        }

        .earnings-tfoot td {
            border-top: 3px solid #e74c3c;
        }

        .tfoot-label {
            font-size: 16px;
            color: #2c3e50;
        }

        .tfoot-total {
            font-size: 18px;
            color: #e74c3c;
        }

        .headline-cell {
            color: #2c3e50;
            max-width: 400px;
            font-weight: 500;
        }

        .amount-cell {
            color: #e74c3c;
            font-weight: 700;
        }

        .empty-cell {
            color: #bdc3c7;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-box {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #ffdddd;
        }

        .summary-label {
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .id-card-section {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .id-card-wrapper {
            margin-top: 1.5rem;
            text-align: center;
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            border: 2px solid #e8e8e8;
        }

        .id-card-photo {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Error */
        .error-message {
            background: #ffffff;
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            border: 2px solid #e74c3c;
            display: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .error-message h2 {
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .error-message p {
            color: #7f8c8d;
        }

        /* Calculation Section */
        .calc-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 2rem;
            border-radius: 12px;
            margin-top: 1.5rem;
            border: 2px solid #e8e8e8;
        }

        .calc-title-wrapper {
            text-align: center;
            margin-bottom: 1rem;
        }

        .calc-title {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .calc-flex {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .calc-box {
            text-align: center;
        }

        .calc-box-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 0.3rem;
        }

        .calc-box-value {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            background: #fff;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            border: 2px solid #e8e8e8;
        }

        .calc-box-value.calc-share {
            color: #e74c3c;
            border-color: #e74c3c;
        }

        .calc-operator {
            font-size: 32px;
            font-weight: 700;
        }

        .calc-operator.calc-multiply {
            color: #e74c3c;
        }

        .calc-operator.calc-equals {
            color: #27ae60;
        }

        .calc-result {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            padding: 0.8rem 1.8rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        /* Responsive - Tablet */
        @media screen and (max-width: 1024px) {
            .profile-layout {
                grid-template-columns: 1fr !important;
                display: grid !important;
            }
            
            .profile-sidebar {
                order: -1;
            }
            
            .profile-card {
                padding: 1.5rem !important;
            }
            
            .earnings-card {
                padding: 1.5rem !important;
            }
        }

        /* Responsive - Mobile */
        @media screen and (max-width: 768px) {
            body {
                font-size: 14px !important;
            }
            
            .navbar {
                position: sticky !important;
                top: 0 !important;
            }
            
            .nav-container {
                padding: 0 1rem !important;
                height: 56px !important;
            }
            
            .logo {
                height: 28px !important;
            }

            .nav-menu {
                position: fixed !important;
                top: 56px !important;
                left: -100% !important;
                width: 100% !important;
                background: #ffffff !important;
                flex-direction: column !important;
                padding: 1.5rem !important;
                gap: 1rem !important;
                transition: left 0.3s ease !important;
                border-bottom: 1px solid #e8e8e8 !important;
                align-items: flex-start !important;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
                z-index: 999 !important;
            }

            .nav-menu.active {
                left: 0 !important;
            }

            .hamburger {
                display: flex !important;
            }

            .hamburger.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }

            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }

            .hamburger.active span:nth-child(3) {
                transform: rotate(-45deg) translate(5px, -5px);
            }

            .container {
                padding: 1rem 0.5rem !important;
                max-width: 100% !important;
            }
            
            /* Profile Layout Mobile */
            .profile-layout {
                display: block !important;
                grid-template-columns: 1fr !important;
            }
            
            .profile-sidebar {
                margin-bottom: 1rem !important;
            }
            
            .main-content {
                width: 100% !important;
            }

            /* Profile Card Mobile */
            .profile-card {
                padding: 1rem !important;
                border-radius: 12px !important;
                margin-bottom: 1rem !important;
            }
            
            .profile-photo-wrapper {
                margin-bottom: 1rem !important;
            }
            
            .profile-photo {
                width: 90px !important;
                height: 90px !important;
                border-width: 3px !important;
            }
            
            .profile-name {
                font-size: 18px !important;
            }
            
            .profile-role {
                font-size: 12px !important;
            }
            
            .total-earning-box {
                padding: 1rem !important;
                margin-bottom: 1rem !important;
                border-radius: 10px !important;
            }
            
            .earning-label {
                font-size: 10px !important;
            }
            
            .earning-value {
                font-size: 22px !important;
            }
            
            .info-item {
                padding: 0.6rem 0 !important;
            }
            
            .info-label {
                font-size: 10px;
            }
            
            .info-value {
                font-size: 13px;
            }

            /* Earnings Card Mobile */
            .earnings-card {
                padding: 0.75rem !important;
                border-radius: 12px !important;
                margin-bottom: 1rem !important;
            }
            
            .card-header {
                flex-direction: column !important;
                gap: 0.75rem !important;
                align-items: stretch !important;
                margin-bottom: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            .card-title {
                font-size: 16px !important;
                text-align: center !important;
            }
            
            .payment-history-btn,
            #paymentHistoryBtn {
                width: 100% !important;
                justify-content: center !important;
                padding: 12px 16px !important;
                font-size: 13px !important;
                border-radius: 8px !important;
            }

            /* Table Mobile - Horizontal Scroll */
            .earnings-table {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                margin: 0 -0.75rem !important;
                padding: 0 !important;
                width: calc(100% + 1.5rem) !important;
            }
            
            .earnings-table table {
                min-width: 500px !important;
                width: 100% !important;
            }

            table {
                font-size: 11px !important;
                border-collapse: collapse !important;
            }

            thead {
                background: red !important;
            }

            th {
                padding: 0.6rem 0.4rem !important;
                font-size: 9px !important;
                white-space: nowrap !important;
                letter-spacing: 0 !important;
            }
            
            td {
                padding: 0.6rem 0.4rem !important;
                font-size: 11px !important;
            }

            .headline-cell {
                max-width: 120px !important;
                font-size: 10px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            .amount-cell {
                font-size: 11px !important;
            }

            .earnings-tfoot td,
            tfoot td {
                font-size: 10px !important;
                padding: 0.5rem 0.4rem !important;
            }
            
            .tfoot-label,
            tfoot td:first-child {
                font-size: 11px !important;
            }
            
            .tfoot-total,
            tfoot td:last-child {
                font-size: 12px !important;
            }

            /* Pagination Mobile */
            .pagination {
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 4px !important;
                margin-top: 0.75rem !important;
                padding: 0 0.5rem !important;
            }
            
            .page-btn {
                padding: 6px 10px !important;
                font-size: 11px !important;
                min-width: 32px !important;
            }

            /* Summary Grid Mobile */
            .summary-grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }

            .summary-box {
                padding: 0.75rem !important;
            }

            .summary-value {
                font-size: 20px !important;
            }

            /* Calculation Design Mobile - Horizontal Row */
            .calc-container {
                padding: 0.5rem !important;
                margin-top: 0.75rem !important;
                border-radius: 8px !important;
            }

            .calc-title-wrapper {
                margin-bottom: 0.5rem !important;
            }

            .calc-title {
                font-size: 9px !important;
            }

            .calc-flex {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 0.3rem !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .calc-box {
                text-align: center !important;
                flex-shrink: 1 !important;
            }

            .calc-box-label {
                font-size: 7px !important;
                margin-bottom: 0.15rem !important;
                white-space: nowrap !important;
            }

            .calc-box-value {
                font-size: 12px !important;
                padding: 0.3rem 0.4rem !important;
                border-radius: 4px !important;
                border-width: 1px !important;
            }

            .calc-operator {
                font-size: 14px !important;
                margin: 0 !important;
                flex-shrink: 0 !important;
            }

            .calc-result {
                font-size: 13px !important;
                padding: 0.3rem 0.5rem !important;
                border-radius: 4px !important;
            }

            /* Modal Mobile */
            .modal-overlay {
                padding: 10px !important;
            }
            
            .modal-box {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                max-height: 90vh !important;
                border-radius: 12px !important;
            }
            
            .modal-header {
                padding: 12px 14px;
            }
            
            .modal-title {
                font-size: 15px;
            }
            
            .modal-body {
                padding: 12px 14px;
                max-height: 60vh;
                overflow-y: auto;
            }

            /* ID Card Section Mobile */
            .id-card-section {
                padding: 1rem;
            }
            
            .id-card-wrapper {
                padding: 1rem;
                margin-top: 1rem;
            }
        }

        /* Responsive - Small Mobile */
        @media screen and (max-width: 480px) {
            .container {
                padding: 0.5rem !important;
            }
            
            .profile-layout {
                display: block !important;
                gap: 0.75rem !important;
            }
            
            .profile-sidebar {
                gap: 0.75rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            .main-content {
                gap: 0.75rem !important;
            }
            
            .profile-card, .earnings-card {
                padding: 0.75rem !important;
                border-radius: 10px !important;
            }
            
            .profile-photo {
                width: 70px !important;
                height: 70px !important;
                border-width: 2px !important;
            }
            
            .profile-name {
                font-size: 15px !important;
            }
            
            .profile-role {
                font-size: 11px !important;
            }
            
            .total-earning-box {
                padding: 0.75rem !important;
                border-radius: 8px !important;
            }
            
            .earning-label {
                font-size: 9px !important;
            }
            
            .earning-value {
                font-size: 20px !important;
            }
            
            .card-title {
                font-size: 14px !important;
            }
            
            .earnings-table {
                margin: 0 -0.75rem !important;
                width: calc(100% + 1.5rem) !important;
            }
            
            .earnings-table table {
                min-width: 450px !important;
            }
            
            th {
                font-size: 8px !important;
                padding: 0.5rem 0.3rem !important;
            }
            
            td {
                font-size: 10px !important;
                padding: 0.5rem 0.3rem !important;
            }
            
            .headline-cell {
                max-width: 100px !important;
                font-size: 9px !important;
            }
            
            .page-btn {
                padding: 5px 8px !important;
                font-size: 10px !important;
                min-width: 28px !important;
            }
            
            .calc-container {
                padding: 0.4rem !important;
            }
            
            .calc-box-label {
                font-size: 6px !important;
            }
            
            .calc-box-value {
                font-size: 10px !important;
                padding: 0.25rem 0.35rem !important;
            }
            
            .calc-result {
                font-size: 11px !important;
                padding: 0.25rem 0.4rem !important;
            }
            
            .calc-operator {
                font-size: 12px !important;
            }
            
            /* Payment History Modal Small */
            #paymentHistoryOverlay .modal-box {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
                height: 100vh !important;
                max-height: 100vh !important;
            }
        }

        /* Extra Small Mobile (320px) */
        @media screen and (max-width: 360px) {
            .nav-container {
                padding: 0 0.5rem !important;
            }
            
            .logo {
                height: 24px !important;
            }
            
            .container {
                padding: 0.4rem !important;
            }
            
            .profile-photo {
                width: 60px !important;
                height: 60px !important;
            }
            
            .profile-name {
                font-size: 14px !important;
            }
            
            .earning-value {
                font-size: 18px !important;
            }
            
            .earnings-table table {
                min-width: 400px !important;
            }
            
            th {
                font-size: 7px !important;
                padding: 0.4rem 0.2rem !important;
            }
            
            td {
                font-size: 9px !important;
                padding: 0.4rem 0.2rem !important;
            }
            
            .headline-cell {
                max-width: 80px !important;
            }
        }

        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            .page-btn {
                min-height: 40px !important;
                min-width: 40px !important;
            }
            
            #paymentHistoryBtn {
                min-height: 44px !important;
            }
            
            .nav-menu a {
                padding: 12px 0 !important;
                display: block !important;
                width: 100% !important;
            }
            
            .hamburger {
                padding: 10px !important;
            }
            
            tbody tr:active {
                background: #fff5f5 !important;
            }
            
            .headline-link {
                padding: 4px 0 !important;
                display: block !important;
            }
        }

        /* Landscape mobile */
        @media screen and (max-width: 768px) and (orientation: landscape) {
            .profile-layout {
                display: grid !important;
                grid-template-columns: 220px 1fr !important;
            }
            
            .profile-photo {
                width: 60px !important;
                height: 60px !important;
            }
            
            .modal-box {
                max-height: 80vh !important;
            }
            
            .profile-card {
                padding: 0.75rem !important;
            }
        }

        /* Print styles */
        @media print {
            .navbar, .hamburger, #paymentHistoryBtn, .pagination {
                display: none !important;
            }
            
            .profile-layout {
                display: block;
            }
            
            .earnings-table {
                overflow: visible;
            }
            
            table {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <?php if (!empty($basic_info['image'])): ?>
            <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'News Portal'); ?>" class="logo">
            <?php endif; ?>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>

    <!-- Modal for headline details -->
    <div id="modalOverlay" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Details</div>
                <button type="button" class="modal-close" id="modalClose">×</button>
            </div>
            <div class="modal-body" id="modalBody">Loading...</div>
        </div>
    </div>
             <ul class="nav-menu" id="navMenu">
               <li><a href="<?= reporter_url('home.php') ?>">Home</a></li>
                <li><a href="<?= reporter_url('news.php') ?>">News</a></li>
                <li><a href="logout.php?user_id=<?= $user_id ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Container -->
    <div class="container">
        <!-- Loading -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="color: #7f8c8d;">Loading profile...</p>
        </div>

        <!-- Error -->
        <div class="error-message" id="errorMessage">
            <h2>âš ï¸ Unable to Load Profile</h2>
            <p id="errorText">Something went wrong</p>
        </div>

        <!-- Profile Layout -->
        <div class="profile-layout" id="profileLayout">
            <!-- Left Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-photo-wrapper">
                        <img id="profilePhoto" class="profile-photo" src="" alt="Reporter">
                    </div>

                    <h1 class="profile-name" id="reporterName"></h1>
                    <p class="profile-role">News Reporter</p>

                    <div class="total-earning-box">
                        <div class="earning-label">My Earning</div>
                        <div class="earning-value" id="sidebarTotal">$0.00</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value" id="reporterEmail"></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value" id="reporterPhone"></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">ID Card</div>
                        <div class="info-value" id="reporterIdCard"></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value" id="reporterAddress"></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Member Since</div>
                        <div class="info-value" id="reporterJoined"></div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Earnings Table -->
                <div class="earnings-card">
                    <div class="card-header">
                        <h2 class="card-title">Earnings Report</h2>
                        <button id="paymentHistoryBtn" class="payment-history-btn">
                            <i class="fas fa-receipt"></i> Payment History
                        </button>
                    </div>

                    <div class="earnings-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>News Headline</th>
                                    <th>Web Income</th>
                                    <th>FB Income</th>
                                    <th>YouTube Income</th>
                                    <th>Total Income</th>
                                </tr>
                            </thead>
                            <tbody id="earningsTbody">
  <!-- Dynamic rows loaded via JS -->
                              </tbody>
                            <tfoot class="earnings-tfoot">
                                <tr>
                                    <td class="tfoot-label">TOTAL EARNINGS</td>
                                    <td id="totalWebIncome">$0.00</td>
                                    <td id="totalFbIncome">$0.00</td>
                                    <td id="totalYtIncome">$0.00</td>
                                    <td id="totalAllIncome" class="tfoot-total">$0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="earningsPagination" class="pagination"></div>

                    <!-- Mathematical Calculation Design 
                    <div class="calc-container">
                        <div class="calc-title-wrapper">
                            <div class="calc-title">My Earning Calculation</div>
                        </div>
                        <div class="calc-flex">
                            <div class="calc-box">
                                <div class="calc-box-label">Total Income</div>
                                <div id="calcTotalIncome" class="calc-box-value">$0.00</div>
                            </div>
                            <div class="calc-operator calc-multiply">×</div>
                            <div class="calc-box">
                                <div class="calc-box-label">My Share</div>
                                <div class="calc-box-value calc-share">50%</div>
                            </div>
                            <div class="calc-operator calc-equals">=</div>
                            <div class="calc-box">
                                <div class="calc-box-label">My Total Earning</div>
                                <div id="calcMyEarning" class="calc-result">$0.00</div>
                            </div>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Modal -->
    <div class="modal-overlay" id="paymentHistoryOverlay" aria-hidden="true">
        <div class="modal-box" style="max-width: 380px; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); background: #fff;">
            <!-- Receipt Header - White with Logo -->
            <div style="background: #fff; padding: 20px; text-align: center; position: relative; border-bottom: 1px solid #eee;">
                <?php if (!empty($basic_info['image'])): ?>
                <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'Logo'); ?>" style="height: 40px; margin-bottom: 8px;">
                <?php endif; ?>
                <div style="font-size: 16px; font-weight: 600; color: red; letter-spacing: 0.5px;">Payment History</div>
                <button class="modal-close" id="paymentHistoryClose" style="position: absolute; top: 12px; right: 12px; font-size: 22px; color: #999; background: none; border: none; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            <!-- Receipt Body -->
            <div style="max-height: 420px; overflow-y: auto; background: #fafafa;">
                <div id="paymentHistoryContent" style="padding: 16px;">
                    <p style="text-align: center; padding: 40px; color: #666;">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });

        // Load profile
        $(document).ready(function() {
            const reporterId = <?= json_encode($reporter_id) ?>;

            if (!reporterId) {
                showError('No reporter ID provided');
                return;
            }

            loadEarnings(reporterId);

            $.ajax({
                url: '../Admin/reporter_registration.php',
                method: 'GET',
                data: {
                    action: 'get_reporter',
                    id: reporterId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        displayReporter(response.data);
                    } else {
                        showError(response.message || 'Reporter not found');
                    }
                },
                error: function() {
                    showError('Failed to load data');
                }
            });

            function displayReporter(data) {
                $('#loading').hide();
                $('#profileLayout').css('display', 'grid');

                $('#reporterName').text(data.name || 'N/A');
                $('#reporterEmail').text(data.email || 'N/A');
                $('#reporterPhone').text(data.mobile || 'N/A');
                $('#reporterIdCard').text(data.id_card || 'N/A');
                $('#reporterAddress').text(data.address || 'N/A');
                
                if (data.created_at) {
                    const date = new Date(data.created_at);
                    $('#reporterJoined').text(date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                } else {
                    $('#reporterJoined').text('N/A');
                }

                if (data.image) {
                    $('#profilePhoto').attr('src', '../Admin/' + data.image).on('error', function() {
                        $(this).attr('src', 'https://via.placeholder.com/160x160/e74c3c/ffffff?text=No+Photo');
                    });
                } else {
                    $('#profilePhoto').attr('src', 'https://via.placeholder.com/160x160/e74c3c/ffffff?text=No+Photo');
                }

                if (data.id_card_photo) {
                    $('#idCardPhoto').attr('src', '../Admin/' + data.id_card_photo).on('error', function() {
                        $(this).attr('src', 'https://via.placeholder.com/800x400/f8f9fa/7f8c8d?text=ID+Card+Not+Available');
                    });
                } else {
                    $('#idCardPhoto').attr('src', 'https://via.placeholder.com/800x400/f8f9fa/7f8c8d?text=ID+Card+Not+Available');
                }
            }

            function showError(message) {
                $('#loading').hide();
                $('#errorText').text(message);
                $('#errorMessage').css('display', 'block');
            }
        });
    </script>
    <script>
     function formatUSD(amount) {
  const n = Number(amount) || 0;
  return '$' + n.toFixed(2);
}

function escapeHtml(s) {
  return String(s || '').replace(/[&<>"']/g, m => (
    { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]
  ));
}

// removed duplicate recursive loadEarnings
// Re-entry guard
window._earningsLoading = false;
// client-side pagination state
window._earningsData = [];
window._pageSize = 10;
window._currentPage = 1;

function truncateWords(text, count) {
  const parts = String(text || '').trim().split(/\s+/);
  if (parts.length <= count) return text || '';
  return parts.slice(0, count).join(' ') + '…';
}

function renderPagination(totalItems) {
  const totalPages = Math.ceil(totalItems / window._pageSize);
  const $p = $('#earningsPagination');
  $p.empty();
  if (totalPages <= 1) {
    // Hide pagination when there is only one page (<= page size)
    $p.hide();
    return;
  }
  $p.show();
  const prev = $('<button class="page-btn">Prev</button>').prop('disabled', window._currentPage === 1).on('click', function(){
    if (window._currentPage > 1) { window._currentPage--; renderEarningsPage(window._currentPage); }
  });
  $p.append(prev);
  for (let i=1; i<= totalPages; i++) {
    const btn = $('<button class="page-btn"></button>').text(i);
    if (i === window._currentPage) btn.addClass('active');
    btn.on('click', function(){ window._currentPage = i; renderEarningsPage(i); });
    $p.append(btn);
  }
  const next = $('<button class="page-btn">Next</button>').prop('disabled', window._currentPage === totalPages).on('click', function(){
    if (window._currentPage < totalPages) { window._currentPage++; renderEarningsPage(window._currentPage); }
  });
  $p.append(next);
}

function renderEarningsPage(page) {
  const start = (page - 1) * window._pageSize;
  const slice = window._earningsData.slice(start, start + window._pageSize);
  const $tbody = $('#earningsTbody');
  $tbody.empty();
  slice.forEach(item => {
    const headline = escapeHtml(item.headline);
    const short = escapeHtml(truncateWords(item.headline, 4));
    const webEarning = Number(item.webEarning) || 0;
    const fbEarning = Number(item.fbEarning) || 0;
    const ytEarning = Number(item.ytEarning) || 0;
    const totalItemEarning = webEarning + fbEarning + ytEarning;
    const tr = `
      <tr>
        <td class="headline-cell">
          <a href="#" class="headline-link" data-id="${item.id}" data-headline="${headline}">
            <span class="full-headline">${headline}</span>
            <span class="short-headline">${short}</span>
          </a>
        </td>
        <td class="amount-cell">${formatUSD(webEarning)}</td>
        <td class="amount-cell">${formatUSD(fbEarning)}</td>
        <td class="amount-cell">${formatUSD(ytEarning)}</td>
        <td class="amount-cell">${formatUSD(totalItemEarning)}</td>
      </tr>`;
    $tbody.append(tr);
  });
  renderPagination(window._earningsData.length);
}

function loadEarnings(reporterId) {
  if (window._earningsLoading) return;
  window._earningsLoading = true;

  // Load both news earnings and video earnings
  Promise.all([
    $.ajax({
      url: '../Admin/api.php',
      method: 'GET',
      data: { action: 'get_reporter_earnings', reporter_id: reporterId },
      dataType: 'json'
    }),
    $.ajax({
      url: '../Admin/api.php',
      method: 'GET',
      data: { action: 'get_reporter_video_earnings', reporter_id: reporterId },
      dataType: 'json'
    })
  ]).then(function([newsRes, videoRes]) {
    console.log('news earnings response', newsRes);
    console.log('video earnings response', videoRes);

    // Process news earnings
    const newsRows = (newsRes && newsRes.success && Array.isArray(newsRes.data)) ? newsRes.data : [];
    let newsFbTotal = 0;
    let newsWebTotal = 0;
    newsRows.forEach(it => {
      newsFbTotal += Number(it.earning) || 0;
      newsWebTotal += Number(it.web_earning) || 0;
    });

    // Process video earnings
    const videoRows = (videoRes && videoRes.success && Array.isArray(videoRes.data)) ? videoRes.data : [];
    let videoFbTotal = 0;
    let videoYtTotal = 0;
    videoRows.forEach(it => {
      videoFbTotal += Number(it.earning) || 0;
      videoYtTotal += Number(it.youtube_earning) || 0;
    });

    // Combine data for display
    window._earningsData = newsRows.map(item => ({
      id: item.id,
      headline: item.headline,
      webEarning: Number(item.web_earning) || 0,
      fbEarning: Number(item.earning) || 0,
      ytEarning: 0,
      created_at: item.created_at,
      type: 'news'
    }));

    // Add video earnings as separate rows
    videoRows.forEach(video => {
      window._earningsData.push({
        id: 'v' + video.id,
        headline: video.video_headline,
        webEarning: 0,
        fbEarning: Number(video.earning) || 0,
        ytEarning: Number(video.youtube_earning) || 0,
        created_at: video.created_at,
        type: 'video'
      });
    });

    if (!window._earningsData.length) {
      $('#sidebarTotal').text('$0.00');
      $('#totalWebIncome').text('$0.00');
      $('#totalFbIncome').text('$0.00');
      $('#totalYtIncome').text('$0.00');
      $('#totalAllIncome').text('$0.00');
      $('#calcTotalIncome').text('$0.00');
      $('#calcMyEarning').text('$0.00');
      $('#earningsTbody').empty().append('<tr><td colspan="5" class="empty-cell">No earnings yet</td></tr>');
      window._earningsLoading = false;
      return;
    }

    const totalWebIncome = newsWebTotal;
    const totalFbIncome = newsFbTotal + videoFbTotal;
    const totalYtIncome = videoYtTotal;
    const totalIncome = totalWebIncome + totalFbIncome + totalYtIncome;
    const earning50 = totalIncome;
    
    // Update table footer
    $('#totalWebIncome').text(formatUSD(totalWebIncome));
    $('#totalFbIncome').text(formatUSD(totalFbIncome));
    $('#totalYtIncome').text(formatUSD(totalYtIncome));
    $('#totalAllIncome').text(formatUSD(totalIncome));
    
    // Update calculation display
    $('#calcTotalIncome').text(formatUSD(totalIncome));
    $('#calcMyEarning').text(formatUSD(earning50));
    
    // Update sidebar
    $('#sidebarTotal').text(formatUSD(earning50));
    
    window._currentPage = 1;
    renderEarningsPage(1);
    window._earningsLoading = false;
  }).catch(function(err) {
    console.error('earnings failed', err);
    $('#earningsTbody').empty().append('<tr><td colspan="5" class="empty-cell">Failed to load earnings</td></tr>');
    $('#sidebarTotal').text('$0.00');
    $('#totalWebIncome').text('$0.00');
    $('#totalFbIncome').text('$0.00');
    $('#totalYtIncome').text('$0.00');
    $('#totalAllIncome').text('$0.00');
    $('#calcTotalIncome').text('$0.00');
    $('#calcMyEarning').text('$0.00');
    window._earningsLoading = false;
  });
}

// Modal handlers: open on headline click, fetch details
$(document).on('click', '.headline-link', function(e){
  e.preventDefault();
  const id = $(this).data('id');
  const title = $(this).data('headline');
  $('#modalTitle').text(title);
  $('#modalBody').text('Loading...');
  $('#modalOverlay').css('display','flex').attr('aria-hidden','false');
  $.ajax({
    url: '../Admin/api.php',
    method: 'GET',
    data: { action: 'get_news', id: id },
    dataType: 'json'
  }).done(function(res){
    if (res && res.news_1) {
      // Render HTML directly to keep original design
      $('#modalBody').html(res.news_1);
    } else if (res && res.short_description) {
      $('#modalBody').html(res.short_description);
    } else {
      $('#modalBody').text('No details available.');
    }
  }).fail(function(){
    $('#modalBody').text('Failed to load details');
  });
});

$('#modalClose, #modalOverlay').on('click', function(e){
  if (e.target.id === 'modalOverlay' || e.target.id === 'modalClose') {
    $('#modalOverlay').hide().attr('aria-hidden','true');
  }
});

// Payment History Modal
$('#paymentHistoryBtn').on('click', function() {
  const reporterId = <?= json_encode($reporter_id) ?>;
  
  if (!reporterId) {
    alert('Reporter ID not found');
    return;
  }
  
  $('#paymentHistoryOverlay').css('display', 'flex').attr('aria-hidden', 'false');
  $('#paymentHistoryContent').html('<p style="text-align: center; padding: 40px; color: #666;">Loading...</p>');
  
  $.ajax({
    url: '../Admin/api.php',
    method: 'GET',
    data: { action: 'get_reporter_payment_history', reporter_id: reporterId },
    dataType: 'json'
  }).done(function(res) {
    if (res && res.success && res.data && res.data.length > 0) {
      // Group by payment date
      const grouped = {};
      res.data.forEach(function(item) {
        const dateKey = item.paid_at ? item.paid_at.split(' ')[0] : 'unknown';
        if (!grouped[dateKey]) {
          grouped[dateKey] = { newsCount: 0, videoCount: 0, total: 0 };
        }
        if (item.type === 'video') {
          grouped[dateKey].videoCount++;
        } else {
          grouped[dateKey].newsCount++;
        }
        grouped[dateKey].total += Number(item.earning) || 0;
      });
      
      // Sort dates descending
      const sortedDates = Object.keys(grouped).sort((a, b) => new Date(b) - new Date(a));
      
      let grandTotal = 0;
      let html = '';
      
      sortedDates.forEach(function(dateKey, index) {
        const data = grouped[dateKey];
        grandTotal += data.total;
        const myEarning = data.total * 0.5;
        const formattedDate = new Date(dateKey).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        
        // Build item count
        let items = [];
        if (data.newsCount > 0) items.push(data.newsCount + ' News');
        if (data.videoCount > 0) items.push(data.videoCount + ' Video');
        
        html += '<div style="background: #fff; border-radius: 8px; padding: 14px 16px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">';
        html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
        html += '<span style="font-size: 13px; font-weight: 600; color: #333;">Paid: ' + formattedDate + '</span>';
        html += '<span style="font-size: 11px; color: #888;">' + items.join(', ') + '</span>';
        html += '</div>';
        html += '<div style="display: flex; justify-content: space-between; font-size: 12px; color: #666; padding-top: 8px; border-top: 1px dashed #eee;">';
        html += '<span>Total Income</span><span style="font-family: monospace;">$' + data.total.toFixed(2) + '</span>';
        html += '</div>';
        html += '<div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; color: red; margin-top: 6px;">';
        html += '<span>My Earning </span><span style="font-family: monospace;">$' + myEarning.toFixed(2) + '</span>';
        html += '</div>';
        html += '</div>';
      });
      
      // Grand total section
      const grandMyEarning = grandTotal * 0.5;
      html += '<div style="background: red; border-radius: 8px; padding: 16px; margin-top: 6px; color: #fff;">';
      html += '<div style="display: flex; justify-content: space-between; font-size: 12px; opacity: 0.9; margin-bottom: 6px;">';
      html += '<span>Total Income</span><span style="font-family: monospace;">$' + grandTotal.toFixed(2) + '</span>';
      html += '</div>';
      html += '<div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 700;">';
      html += '<span>My Earning (50%)</span><span style="font-family: monospace;">$' + grandMyEarning.toFixed(2) + '</span>';
      html += '</div>';
      html += '<div style="text-align: center; margin-top: 12px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 11px; opacity: 0.8;">';
      html += sortedDates.length + ' payment(s) completed';
      html += '</div>';
      html += '</div>';
      
      $('#paymentHistoryContent').html(html);
    } else {
      $('#paymentHistoryContent').html('<div style="text-align: center; padding: 50px 20px; color: #999;">No payment history found</div>');
    }
  }).fail(function() {
    $('#paymentHistoryContent').html('<div style="text-align: center; padding: 40px; color: #8B0000;">Failed to load</div>');
  });
});

$('#paymentHistoryClose, #paymentHistoryOverlay').on('click', function(e) {
  if (e.target.id === 'paymentHistoryOverlay' || e.target.id === 'paymentHistoryClose') {
    $('#paymentHistoryOverlay').hide().attr('aria-hidden', 'true');
  }
});
    </script>
</body>
</html>
