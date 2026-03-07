<?php
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
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $basic_info['portal_name'] ?? 'সংবাদ প্রশাসন প্যানেল'; ?></title>
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

        /* Global font for entire website */
body, button, input, textarea, select, a, p, h1, h2, h3, h4, h5, h6 {
    font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif !important;
}

/* Base Button Style */
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

/* Hover Effect */
.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

/* Primary Button */
.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056d6);
    color: #fff;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056d6, #003c9e);
}

/* Success Button */
.btn-success {
    background: linear-gradient(135deg, #28a745, #1f7e35);
    color: #fff;
}

.btn-success:hover {
    background: linear-gradient(135deg, #1f7e35, #145e24);
}

/* Warning Button */
.btn-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    color: #212529;
    padding: 8px 18px;
    font-size: 14px;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ff9800, #e58300);
}

/* Danger Button */
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
            transition: all 0.3s;
        }

        .section.edit-mode {
            border: 2px solid red;
            box-shadow: 0 0 15px rgba(255, 7, 7, 0.3);
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
            font-size: 18px;
            color: #495057;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
            background: #fff;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .image-upload-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .image-upload-box {
            border: 2px dashed #ced4da;
            border-radius: 4px;
            padding: 20px 10px;
            text-align: center;
            background: #f8f9fa;
            position: relative;
            transition: border-color 0.2s;
        }

        .image-upload-box:hover {
            border-color: #adb5bd;
        }

        .image-upload-box input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            padding: 0;
            border: none;
        }

        .upload-text {
            color: #6c757d;
            font-size: 13px;
        }

        .image-preview {
    max-width: 100%;
    max-height: 80px;
    border-radius: 4px;
    margin: 10px auto;   /* center horizontally */
    display: none;
}

        .image-remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            width: 28px;
            height: 28px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s ease;
            line-height: 1;
            box-shadow: 0 2px 8px rgba(238, 90, 90, 0.4);
        }
        
        .image-remove-btn:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 12px rgba(211, 47, 47, 0.5);
        }
        
        .image-upload-box.has-image .image-remove-btn {
            display: flex;
        }

        /* Modern Filter Bar Styles */
        .modern-filters {
            background: #f7f9fb;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 18px 24px 10px 24px;
            margin-bottom: 24px;
            width: 100%;
        }
        .modern-filters .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 24px;
            align-items: flex-end;
        }
        .modern-filters .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 140px;
            flex: 1 1 180px;
        }
        .modern-filters .filter-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .modern-filters .filter-group input,
        .modern-filters .filter-group select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 15px;
            background: #fff;
            transition: border 0.2s;
        }
        .modern-filters .filter-group input:focus,
        .modern-filters .filter-group select:focus {
            border: 1.5px solid #007bff;
            outline: none;
        }
        .modern-filters .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-left: 8px;
        }
        .modern-filters .icon-btn {
            width: 38px;
            height: 38px;
            
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            
           
        }
        .modern-filters .icon-btn:hover {
            background: #d0e6ff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.10);
            transform: translateY(-2px) scale(1.08);
        }
        .modern-filters .icon-btn:active {
            background: #b3d4fc;
            transform: scale(0.97);
        }
        @media (max-width: 900px) {
            .modern-filters .filter-row {
                flex-direction: column;
                gap: 12px 0;
            }
            .modern-filters {
                padding: 12px 8px 6px 8px;
            }
        }

        @media (max-width: 600px) {
            .modern-filters {
                padding: 6px 2vw 4px 2vw;
            }
            .modern-filters .filter-row {
                flex-direction: column;
                gap: 8px 0;
                align-items: stretch;
            }
            .modern-filters .filter-group {
                min-width: 0;
                flex: 1 1 100%;
                width: 100%;
            }
            .modern-filters .filter-group label {
                font-size: 13px;
                margin-bottom: 3px;
            }
            .modern-filters .filter-group input,
            .modern-filters .filter-group select {
                font-size: 14px;
                padding: 8px 8px;
            }
            .modern-filters .filter-actions {
                justify-content: flex-end;
                margin-left: 0;
                margin-top: 4px;
                gap: 10px;
            }
            .modern-filters .icon-btn {
                width: 36px;
                height: 36px;
                min-width: 36px;
                min-height: 36px;
            }
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

        .image-size-info {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 1200px) {
            .filters {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .filter-group {
                flex-direction: row;
                align-items: center;
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .header-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 15px;
            }
            .image-upload-container {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        /* Custom Rich Text Editor Styles */
        .rte-wrapper {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            margin-bottom: 15px;
        }

        .rte-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }

        .rte-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 6px;
            border: 1px solid #dee2e6;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            color: #495057;
        }

        .rte-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
            color: #212529;
        }

        .rte-btn:active {
            background: #dee2e6;
            transform: scale(0.95);
        }

        .rte-btn svg {
            width: 16px;
            height: 16px;
            pointer-events: none;
        }

        .rte-separator {
            width: 1px;
            height: 24px;
            background: #dee2e6;
            margin: 0 4px;
        }

        .rte-content {
            min-height: 250px;
            max-height: 500px;
            overflow-y: auto;
            padding: 15px;
            font-family: 'Kalpurush', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            outline: none;
        }

        .rte-content:focus {
            background: #fff;
        }

        .rte-content:empty:before {
            content: attr(data-placeholder);
            color: #999;
        }

        .rte-content p {
            margin: 0 0 10px 0;
        }

        .rte-content ul, .rte-content ol {
            margin: 10px 0;
            padding-left: 30px;
        }

        .rte-content li {
            margin: 5px 0;
        }

        .rte-content a {
            color: #007bff;
            text-decoration: underline;
        }

        .rte-content pre {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .rte-content b, .rte-content strong {
            font-weight: bold;
        }

        .rte-content i, .rte-content em {
            font-style: italic;
        }

        .rte-content u {
            text-decoration: underline;
        }
    </style>
    <style>
        /* Toggle Switch CSS */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            vertical-align: middle;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #f8d7da;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: #fff;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        input:checked + .slider {
            background-color: #d4edda;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(20px);
            -ms-transform: translateX(20px);
            transform: translateX(20px);
        }
        .slider.round {
            border-radius: 24px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
        </style>
</head>
<body>
        <!-- News View Popup -->
        <div id="news-view-popup" class="news-view-popup hidden">
            <div class="news-view-content-full">
                <button class="news-view-close" onclick="closeNewsView()">&times;</button>
                <div id="news-view-body"></div>
            </div>
        </div>
        <style>
        .news-view-popup {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30,30,30,0.45);
            z-index: 10000;
            display: flex;
            align-items: stretch;
            justify-content: stretch;
            transition: background 0.3s;
        }
        .news-view-popup.hidden { display: none; }
        .news-view-content-full {
            background: #fff;
            border-radius: 0;
            width: 95vw;
            height: 95vh;
            max-width: 100vw;
            max-height: 100vh;
            overflow-y: auto;
            box-shadow: none;
            padding: 0 0 24px 0;
            position: relative;
            animation: popup-fade-in 0.4s cubic-bezier(.4,1.4,.6,1) 1;
            display: flex;
            flex-direction: column;
        }
        .news-view-close {
            position: absolute;
            top: 18px; right: 28px;
            background: none;
            border: none;
            font-size: 2.8em;
            color: #888;
            cursor: pointer;
            z-index: 2;
            transition: color 0.2s;
        }
        .news-view-close:hover { color: #333; }
        #news-view-body {
            padding: 48px 8vw 0 8vw;
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
            width: 100%;
            max-width: 100vw;
            box-sizing: border-box;
        }
        .news-view-headline {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
            text-align: center;
        }
        .news-view-meta {
            text-align: center;
            color: #888;
            font-size: 1.2em;
            margin-bottom: 24px;
        }
        .news-view-img {
            display: block;
            max-width: 100%;
            max-height: 400px;
            margin: 0 auto 24px auto;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            object-fit: cover;
        }
        .news-view-section {
            margin-bottom: 24px;
            font-size: 1.25em;
            color: #222;
            line-height: 1.8;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 22px 28px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .news-view-quote {
            border-left: 5px solid #007bff;
            background: #eaf4ff;
            font-style: italic;
            color: #333;
            margin: 24px 0 14px 0;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .news-view-author {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 14px;
            margin-left: 12px;
            font-size: 1.1em;
        }
        .news-view-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            margin-bottom: 24px;
        }
        .news-view-gallery img {
            max-width: 180px;
            max-height: 120px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
        }
        @media (max-width: 900px) {
            #news-view-body { padding: 32px 2vw 0 2vw; }
            .news-view-section { font-size: 1.08em; padding: 12px 6vw; }
        }
        @media (max-width: 600px) {
            .news-view-content-full { padding: 0 0 8px 0; }
            #news-view-body { padding: 18px 2vw 0 2vw; }
            .news-view-headline { font-size: 1.2rem; }
            .news-view-section { font-size: 1em; padding: 10px 2vw; }
            .news-view-img { max-height: 180px; }
            .news-view-gallery img { max-width: 90vw; max-height: 80px; }
        }
        
        </style>

    <div class="container">
        <!-- Popup Message Modal -->
        <div id="popup-message" class="popup-message hidden">
            <span id="popup-icon"></span>
            <span id="popup-text"></span>
            <button id="popup-close" onclick="hidePopupMessage()">&times;</button>
        </div>
    <style>
    .popup-message {
        position: fixed;
        top: 40px;
        left: 50%;
        transform: translateX(-50%) scale(1);
        min-width: 320px;
        max-width: 90vw;
        background: #fff;
        color: #333;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18), 0 1.5px 6px rgba(0,0,0,0.08);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 32px 18px 20px;
        font-size: 1.08rem;
        border-left: 6px solid #007bff;
        opacity: 1;
        transition: opacity 0.3s, transform 0.3s;
        animation: popup-fade-in 0.4s cubic-bezier(.4,1.4,.6,1) 1;
    }
    .popup-message.success {
        border-left-color: #28a745;
    }
    .popup-message.error {
        border-left-color: #dc3545;
    }
    .popup-message.hidden {
        opacity: 0;
        pointer-events: none;
        transform: translateX(-50%) scale(0.98);
    }
    #popup-icon {
        font-size: 1.5em;
        margin-right: 6px;
    }
    .popup-message.success #popup-icon::before {
        content: '\2714'; /* checkmark */
        color: #28a745;
    }
    .popup-message.error #popup-icon::before {
        content: '\26A0'; /* warning */
        color: #dc3545;
    }
    #popup-close {
        background: none;
        border: none;
        color: #888;
        font-size: 1.3em;
        cursor: pointer;
        margin-left: auto;
        padding: 0 0 0 10px;
        line-height: 1;
        transition: color 0.2s;
    }
    #popup-close:hover {
        color: #333;
    }
    @keyframes popup-fade-in {
        0% { opacity: 0; transform: translateX(-50%) scale(0.95); }
        100% { opacity: 1; transform: translateX(-50%) scale(1); }
    }
    @media (max-width: 600px) {
        .popup-message {
            min-width: 180px;
            font-size: 0.98rem;
            padding: 12px 10px 12px 12px;
        }
    }
    /* Modern News View Popup */
#news-view-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    backdrop-filter: blur(8px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

#news-view-popup:not(.hidden) {
    opacity: 1;
    visibility: visible;
}

.news-view-container {
    background: white;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    overflow: hidden;
}

#news-view-popup:not(.hidden) .news-view-container {
    transform: scale(1);
}

.news-view-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.news-view-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.news-view-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: black;
}

.news-view-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.news-view-body {
    padding: 30px;
    overflow-y: auto;
    flex: 1;
}

.news-view-body::-webkit-scrollbar {
    width: 8px;
}

.news-view-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.news-view-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.news-view-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.news-view-headline {
    font-size: 28px;
    font-weight: 700;
    color: #1a202c;
    line-height: 1.4;
    margin-bottom: 12px;
}

.news-view-meta {
    font-size: 14px;
    color: #718096;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
}



.news-view-img {
    width: 100%;
    height: auto;
    max-height: 450px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.news-view-section {
    font-size: 16px;
    line-height: 1.8;
    color: #2d3748;
    margin-bottom: 20px;
    text-align: justify;
}

.news-view-section p {
    margin-bottom: 12px;
}

.news-view-quote {
    border-left: 4px solid #667eea;
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 20px 24px;
    margin: 24px 0;
    border-radius: 8px;
    font-style: italic;
    font-size: 17px;
    color: #2d3748;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: relative;
}

.news-view-quote::before {
    content: '"';
    font-size: 60px;
    color: #667eea;
    opacity: 0.3;
    position: absolute;
    top: 10px;
    left: 10px;
    font-family: Georgia, serif;
    line-height: 1;
}

.news-view-author {
    font-weight: 600;
    color: #667eea;
    font-size: 15px;
    margin-top: -16px;
    margin-bottom: 24px;
    padding-left: 28px;
    position: relative;
}

.news-view-author::before {
    content: '—';
    position: absolute;
    left: 12px;
}

.news-view-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 24px;
}

.news-view-gallery img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.news-view-gallery img:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    #news-view-popup {
        padding: 10px;
    }
    
    .news-view-container {
        max-height: 95vh;
        border-radius: 12px;
    }
    
    .news-view-header {
        padding: 16px 20px;
    }
    
    .news-view-header h2 {
        font-size: 18px;
    }
    
    .news-view-body {
        padding: 20px;
    }
    
    .news-view-headline {
        font-size: 22px;
    }
    
    .news-view-section {
        font-size: 15px;
    }
    
    .news-view-gallery {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }
    
    .news-view-gallery img {
        height: 150px;
    }
}

@media (max-width: 480px) {
    .news-view-headline {
        font-size: 20px;
    }
    
    .news-view-section {
        font-size: 14px;
    }
    
    .news-view-quote {
        padding: 16px 18px;
        font-size: 15px;
    }
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
         @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                background: #ffffff;
                flex-direction: column;
                padding: 2rem;
                gap: 1.5rem;
                transition: left 0.3s;
                border-bottom: 1px solid #e8e8e8;
                align-items: flex-start;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }

            .nav-menu.active {
                left: 0;
            }

            .hamburger {
                display: flex;
            }

            .hamburger.active span:nth-child(1) {
                transform: rotate(45deg) translate(7px, 7px);
            }

            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }

            .hamburger.active span:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -7px);
            }

            .container {
                padding: 2rem 1rem;
            }

            .nav-container {
                padding: 0 1rem;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 0.8rem 0.5rem;
            }

            .headline-cell {
                max-width: 200px;
                font-size: 12px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .summary-value {
                font-size: 24px;
            }

            .earning-value {
                font-size: 28px;
            }
        }
    </style>
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
            <ul class="nav-menu" id="navMenu">
                <li><a href="home.php<?php echo isset($_GET['id']) ? '?id=' . htmlspecialchars($_GET['id']) : ''; ?>">Home</a></li>
                <li><a href="#">News</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
        <div id="news-view-popup" class="hidden">
    <div class="news-view-container">
        <div class="news-view-header">
            <h2>সংবাদ বিস্তারিত</h2>
            <button class="news-view-close" onclick="closeNewsView()">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="news-view-body" id="news-view-body">
            <!-- News content will be loaded here -->
        </div>
    </div>
</div>
        <!-- Add/Edit News Form Section -->
        <div class="section" id="form-section">
            
            <div class="form-container">
                <form id="newsForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category_id">বিভাগ</label>
                            <select id="category_id" name="category_id" required style="font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;">
                                <option value="" style="font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;">বিভাগ নির্বাচন করুন</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reporter_id">সাংবাদিক</label>
                            <select id="reporter_id" name="reporter_id" style="font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;">
                                <option value="" style="font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;">সাংবাদিক নির্বাচন করুন</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="headline">শিরোনাম</label>
                            <input type="text" id="headline" name="headline" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="short_description">সংক্ষিপ্ত বিবরণ</label>
                            <textarea id="short_description" name="short_description" ></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="slug">স্লাগ</label>
                            <input type="text" id="slug" name="slug" required maxlength="100" readonly>
                        </div>

                        <div class="form-group">
                            <label>ছবিসমূহ (স্বয়ংক্রিয়ভাবে ১০০KB এর নিচে আকার পরিবর্তন হবে)</label>
                            <div class="image-upload-container">
                                <div class="image-upload-box" id="box_image_url">
                                    <button type="button" class="image-remove-btn" onclick="clearImage('image_url')" title="ছবি মুছুন">×</button>
                                    <input type="file" id="image_url" name="image_url" accept="image/*">
                                    <img src="../image-upload.png" width="50">
                                    <div class="upload-text">মূল ছবি</div>
                                    <img class="image-preview" id="preview_image_url">
                                    <div class="image-size-info" id="size_image_url"></div>
                                </div>
                            </div>
                            <input type="text" id="image_url_title" name="image_url_title" placeholder="মূল ছবির ক্যাপশন/শিরোনাম" style="margin-top: 8px; width: 100%; padding: 10px 14px; background: rgba(0,0,0,0.03); border: none; border-left: 3px solid #f0c040; border-radius: 0 8px 8px 0; font-size: 13px; color: #444;">
                        </div>

                        <div class="form-group full-width">
                            <label for="news_1">সংবাদ বিষয়বস্তু ১</label>
                            <textarea id="news_1" name="news_1"></textarea>
                        </div>

                  <!--      <div class="form-group full-width">
                            <label for="news_2">সংবাদ বিষয়বস্তু ২</label>
                            <textarea id="news_2" name="news_2"></textarea>
                        </div> -->

                        <div class="form-group">
                            <label for="quote_1">উক্তি ১</label>
                            <textarea id="quote_1" name="quote_1" style="border-left: 4px solid #007bff; background: #f8f9fa; font-style: italic; padding: 12px 16px; border-radius: 6px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.03);"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="auture_1">লেখক ১</label>
                            <input type="text" id="auture_1" name="auture_1" style="border: none; border-bottom: 2px dashed #007bff; background: transparent; font-weight: bold; color: #007bff; margin-top: 6px; padding: 6px 0 4px 0; border-radius: 0;">
                        </div>

                        <div class="form-group">
                            <label>ছবিসমূহ (স্বয়ংক্রিয়ভাবে ১০০KB এর নিচে আকার পরিবর্তন হবে)</label>
                            <div class="image-upload-container">
                                <div class="image-upload-box" id="box_image_2">
                                    <button type="button" class="image-remove-btn" onclick="clearImage('image_2')" title="ছবি মুছুন">×</button>
                                    <input type="file" id="image_2" name="image_2" accept="image/*">
                                    <img src="../image-upload.png" width="50">
                                    <div class="upload-text">ছবি ২</div>
                                    <img class="image-preview" id="preview_image_2">
                                    <div class="image-size-info" id="size_image_2"></div>
                                </div>
                                <div class="image-upload-box" id="box_image_3">
                                    <button type="button" class="image-remove-btn" onclick="clearImage('image_3')" title="ছবি মুছুন">×</button>
                                    <input type="file" id="image_3" name="image_3" accept="image/*">
                                    <img src="../image-upload.png" width="50">
                                    <div class="upload-text">ছবি ৩</div>
                                    <img class="image-preview" id="preview_image_3">
                                    <div class="image-size-info" id="size_image_3"></div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" id="image_2_title" name="image_2_title" placeholder="ছবি ২ এর ক্যাপশন" style="flex: 1; padding: 10px 14px; background: rgba(0,0,0,0.03); border: none; border-left: 3px solid #f0c040; border-radius: 0 8px 8px 0; font-size: 13px; color: #444;">
                                <input type="text" id="image_3_title" name="image_3_title" placeholder="ছবি ৩ এর ক্যাপশন" style="flex: 1; padding: 10px 14px; background: rgba(0,0,0,0.03); border: none; border-left: 3px solid #f0c040; border-radius: 0 8px 8px 0; font-size: 13px; color: #444;">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="news_3">সংবাদ বিষয়বস্তু ৩</label>
                            <textarea id="news_3" name="news_3"></textarea>
                        </div>

                        <div class="form-group">
                                                    <label for="quote_2">উক্তি ২</label>
                                                    <textarea id="quote_2" name="quote_2" style="border-left: 4px solid #007bff; background: #f8f9fa; font-style: italic; padding: 12px 16px; border-radius: 6px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.03);"></textarea>
                        </div>

                        <div class="form-group">
                                                    <label for="auture_2">লেখক ২</label>
                                                    <input type="text" id="auture_2" name="auture_2" style="border: none; border-bottom: 2px dashed #007bff; background: transparent; font-weight: bold; color: #007bff; margin-top: 6px; padding: 6px 0 4px 0; border-radius: 0;">
                        </div>

                        <div class="form-group">
                            <label>ছবিসমূহ (স্বয়ংক্রিয়ভাবে ১০০KB এর নিচে আকার পরিবর্তন হবে)</label>
                            <div class="image-upload-container">
                                <div class="image-upload-box" id="box_image_4">
                                    <button type="button" class="image-remove-btn" onclick="clearImage('image_4')" title="ছবি মুছুন">×</button>
                                    <input type="file" id="image_4" name="image_4" accept="image/*">
                                    <img src="../image-upload.png" width="50">
                                    <div class="upload-text">ছবি ৪</div>
                                    <img class="image-preview" id="preview_image_4">
                                    <div class="image-size-info" id="size_image_4"></div>
                                </div>
                                <div class="image-upload-box" id="box_image_5">
                                    <button type="button" class="image-remove-btn" onclick="clearImage('image_5')" title="ছবি মুছুন">×</button>
                                    <input type="file" id="image_5" name="image_5" accept="image/*">
                                    <img src="../image-upload.png" width="50">
                                    <div class="upload-text">ছবি ৫</div>
                                    <img class="image-preview" id="preview_image_5">
                                    <div class="image-size-info" id="size_image_5"></div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" id="image_4_title" name="image_4_title" placeholder="ছবি ৪ এর ক্যাপশন" style="flex: 1; padding: 10px 14px; background: rgba(0,0,0,0.03); border: none; border-left: 3px solid #f0c040; border-radius: 0 8px 8px 0; font-size: 13px; color: #444;">
                                <input type="text" id="image_5_title" name="image_5_title" placeholder="ছবি ৫ এর ক্যাপশন" style="flex: 1; padding: 10px 14px; background: rgba(0,0,0,0.03); border: none; border-left: 3px solid #f0c040; border-radius: 0 8px 8px 0; font-size: 13px; color: #444;">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="news_4">সংবাদ বিষয়বস্তু ৪</label>
                            <textarea id="news_4" name="news_4"></textarea>
                        </div>
                    </div>

                    <!-- Hidden fields for image deletion tracking -->
                    <input type="hidden" id="delete_image_url" name="delete_image_url" value="0">
                    <input type="hidden" id="delete_image_2" name="delete_image_2" value="0">
                    <input type="hidden" id="delete_image_3" name="delete_image_3" value="0">
                    <input type="hidden" id="delete_image_4" name="delete_image_4" value="0">
                    <input type="hidden" id="delete_image_5" name="delete_image_5" value="0">

                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="btn btn-success">সংবাদ প্রকাশ করুন</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- News Table Section -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">সমস্ত সংবাদ নিবন্ধ</div>
                <button class="btn btn-primary" onclick="loadNews()" style="padding: 6px 10px; background: none; border: none; box-shadow: none;">
                    <img src="../Admin/icons/refresh-button.png" alt="রিফ্রেশ" title="রিফ্রেশ" style="width:28px;height:28px;vertical-align:middle;display:inline-block;">
                </button>
            </div>
            
            <div class="filters modern-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="search-filter">খুঁজুন</label>
                        <input type="text" id="search-filter" placeholder="শিরোনামে খুঁজুন...">
                    </div>
                    <div class="filter-group">
                        <label for="date-from-filter">তারিখ থেকে</label>
                        <input type="date" id="date-from-filter">
                    </div>
                    <div class="filter-group">
                        <label for="date-to-filter">তারিখ পর্যন্ত</label>
                        <input type="date" id="date-to-filter">
                    </div>
                    <div class="filter-group">
                        <label for="category-filter">বিভাগ</label>
                        <select id="category-filter">
                            <option value="">সব বিভাগ</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <img src="../Admin/icons/searchbackground.png" alt="ফিল্টার" class="icon-btn" id="filter-btn" title="ফিল্টার" onclick="applyFilters()">
                        <img src="../Admin/icons/rubber.png" alt="পরিষ্কার" class="icon-btn" id="clear-btn" title="পরিষ্কার" onclick="clearFilters()">
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>আইডি</th>
                            <th>শিরোনাম</th>
                            <th>বিভাগ</th>
                            <th>সাংবাদিক</th>
                            <th>তারিখ</th>
                            <th>কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody id="news-table-body">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                সংবাদ নিবন্ধ লোড হচ্ছে...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
        let allNews = [];
        let filteredNews = [];
        let categories = [];
        let reporters = [];
        let currentPage = 1;
        let recordsPerPage = 10;
        let totalPages = 1;
        let currentReporterId = null;  
        let isEditMode = false;

        // Clear image function
        function clearImage(fieldId) {
            const fileInput = document.getElementById(fieldId);
            const preview = document.getElementById('preview_' + fieldId);
            const sizeInfo = document.getElementById('size_' + fieldId);
            const imageBox = document.getElementById('box_' + fieldId);
            const titleInput = document.getElementById(fieldId + '_title');
            const deleteField = document.getElementById('delete_' + fieldId);
            
            if (fileInput) {
                fileInput.value = '';
            }
            if (preview) {
                preview.src = '';
                preview.style.display = 'none';
            }
            if (sizeInfo) {
                sizeInfo.textContent = '';
            }
            if (imageBox) {
                imageBox.classList.remove('has-image');
            }
            if (titleInput) {
                titleInput.value = '';
            }
            if (deleteField && isEditMode) {
                deleteField.value = '1';
            }
        }
        
            // Custom Rich Text Editor Implementation
            class RichTextEditor {
                constructor(textareaId) {
                    this.textarea = document.getElementById(textareaId);
                    this.editor = null;
                    if (!this.textarea) return;
                    
                    this.editorId = textareaId + '_editor';
                    this.history = [];
                    this.historyStep = -1;
                    this.init();
                }

                init() {
                    this.textarea.style.display = 'none';
                    
                    const editorWrapper = document.createElement('div');
                    editorWrapper.className = 'rte-wrapper';
                    editorWrapper.innerHTML = `
                        <div class="rte-toolbar">
                            <button type="button" class="rte-btn" data-command="undo" title="পূর্বাবস্থায় ফিরুন">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 7v6h6"/><path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13"/>
                                </svg>
                            </button>
                            <button type="button" class="rte-btn" data-command="redo" title="পুনরায় করুন">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 7v6h-6"/><path d="M3 17a9 9 0 019-9 9 9 0 016 2.3l3 2.7"/>
                                </svg>
                            </button>
                            <span class="rte-separator"></span>
                            <button type="button" class="rte-btn" data-command="bold" title="গাঢ় (Ctrl+B)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
                                </svg>
                            </button>
                            <button type="button" class="rte-btn" data-command="italic" title="তির্যক (Ctrl+I)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/>
                                </svg>
                            </button>
                            <button type="button" class="rte-btn" data-command="underline" title="নিম্নরেখা (Ctrl+U)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"/><line x1="4" y1="21" x2="20" y2="21"/>
                                </svg>
                            </button>
                            <span class="rte-separator"></span>
                            <button type="button" class="rte-btn" data-command="insertUnorderedList" title="বুলেট তালিকা">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                                    <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                                </svg>
                            </button>
                            <button type="button" class="rte-btn" data-command="insertOrderedList" title="সংখ্যাযুক্ত তালিকা">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/>
                                    <path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/>
                                </svg>
                            </button>
                            <span class="rte-separator"></span>
                            <button type="button" class="rte-btn" data-command="createLink" title="লিংক সন্নিবেশ করুন">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                            </button>
                            <button type="button" class="rte-btn" data-command="formatBlock" data-value="pre" title="কোড ব্লক">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                                </svg>
                            </button>
                            <span class="rte-separator"></span>
                            <button type="button" class="rte-btn" data-command="removeFormat" title="ফরম্যাট মুছুন">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7V4h16v3"/><path d="M5 20h6"/><path d="M13 4L8 20"/><line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                        <div class="rte-content" id="${this.editorId}" contenteditable="true"></div>
                    `;
                    
                    this.textarea.parentNode.insertBefore(editorWrapper, this.textarea);
                    this.editor = document.getElementById(this.editorId);
                    this.toolbar = editorWrapper.querySelector('.rte-toolbar');
                    
                    this.attachEvents();
                    this.loadContent();
                }

                attachEvents() {
                    this.toolbar.querySelectorAll('.rte-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            const command = btn.dataset.command;
                            const value = btn.dataset.value || null;
                            
                            if (command === 'createLink') {
                                this.insertLink();
                            } else if (command === 'undo') {
                                this.undo();
                            } else if (command === 'redo') {
                                this.redo();
                            } else {
                                document.execCommand(command, false, value);
                            }
                            
                            this.editor.focus();
                        });
                    });

                    this.editor.addEventListener('input', () => {
                        this.saveContent();
                        this.saveHistory();
                    });

                    this.editor.addEventListener('keydown', (e) => {
                        if (e.ctrlKey || e.metaKey) {
                            switch(e.key.toLowerCase()) {
                                case 'b':
                                    e.preventDefault();
                                    document.execCommand('bold');
                                    break;
                                case 'i':
                                    e.preventDefault();
                                    document.execCommand('italic');
                                    break;
                                case 'u':
                                    e.preventDefault();
                                    document.execCommand('underline');
                                    break;
                                case 'z':
                                    if (e.shiftKey) {
                                        e.preventDefault();
                                        this.redo();
                                    } else {
                                        e.preventDefault();
                                        this.undo();
                                    }
                                    break;
                                case 'y':
                                    e.preventDefault();
                                    this.redo();
                                    break;
                            }
                        }
                    });

                    this.editor.addEventListener('paste', (e) => {
                        e.preventDefault();
                        const text = e.clipboardData.getData('text/plain');
                        document.execCommand('insertText', false, text);
                    });
                }

                insertLink() {
                    const url = prompt('লিংক URL লিখুন:', 'https://');
                    if (url && url !== 'https://') {
                        document.execCommand('createLink', false, url);
                    }
                }

                saveContent() {
                    if (!this.editor) return;
                    this.textarea.value = this.editor.innerHTML;
                }

                loadContent() {
                    if (!this.editor) return;
                    this.editor.innerHTML = this.textarea.value || '';
                    this.saveHistory();
                }

                getContent() {
                    if (!this.editor) return '';
                    return this.editor.innerHTML;
                }

                setContent(html) {
                    if (!this.editor) return;
                    this.editor.innerHTML = html;
                    this.saveContent();
                    this.saveHistory();
                }

                getText() {
                    if (!this.editor) return '';
                    return this.editor.innerText.trim();
                }

                focus() {
                    if (!this.editor) return;
                    this.editor.focus();
                }

                saveHistory() {
                    if (!this.editor) return;
                    const content = this.editor.innerHTML;
                    if (this.history[this.historyStep] !== content) {
                        this.historyStep++;
                        this.history = this.history.slice(0, this.historyStep);
                        this.history.push(content);
                        if (this.history.length > 50) {
                            this.history.shift();
                            this.historyStep--;
                        }
                    }
                }

                undo() {
                    if (!this.editor) return;
                    if (this.historyStep > 0) {
                        this.historyStep--;
                        this.editor.innerHTML = this.history[this.historyStep];
                        this.saveContent();
                    }
                }

                redo() {
                    if (!this.editor) return;
                    if (this.historyStep < this.history.length - 1) {
                        this.historyStep++;
                        this.editor.innerHTML = this.history[this.historyStep];
                        this.saveContent();
                    }
                }
            }

            // Initialize editors
            const editors = {};
            document.addEventListener('DOMContentLoaded', function() {
                ['news_1', 'news_2', 'news_3', 'news_4'].forEach(id => {
                    editors[id] = new RichTextEditor(id);
                });
            });

        // currentEditId tracks the news ID being edited
        let currentEditId = null;

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadReporters();
            loadNews();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Auto-generate slug
            document.getElementById('headline').addEventListener('input', function() {
                const slug = generateSlug(this.value);
                document.getElementById('slug').value = slug;
            });

            // Form submission
            document.getElementById('newsForm').addEventListener('submit', handleFormSubmit);

            // Image previews with compression
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', handleImagePreview);
            });

            // Search filter
            document.getElementById('search-filter').addEventListener('input', applyFilters);
        }

        // API Functions
        async function loadCategories() {
            try {
                const response = await fetch('../Admin/api.php?action=get_categories');
                categories = await response.json();
                
                const selects = ['category_id', 'category-filter'];
                selects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    if (select) {
                        const firstOption = select.querySelector('option') ? select.querySelector('option').outerHTML : '<option value="">বিভাগ নির্বাচন করুন</option>';
                        select.innerHTML = firstOption;
                        
                        categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            select.appendChild(option);
                        });
                    }
                });
            } catch (error) {
                showMessage('error', 'বিভাগ লোড করতে ব্যর্থ');
                console.error('Error:', error);
            }
        }

        async function loadReporters() {
            try {
                const response = await fetch('../Admin/api.php?action=get_reporters');
                reporters = await response.json();
                
                const selects = ['reporter_id'];
                selects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    if (select) {
                        const firstOption = select.querySelector('option').outerHTML;
                        select.innerHTML = firstOption;
                        
                        reporters.forEach(reporter => {
                            const option = document.createElement('option');
                            option.value = reporter.id;
                            option.textContent = reporter.name;
                            select.appendChild(option);
                        });

                        // Get reporter ID from URL if it exists
                        const urlParams = new URLSearchParams(window.location.search);
                        const reporterId = urlParams.get('id');
                        
                        if (reporterId) {
                            // Find the reporter in the list
                            const reporter = reporters.find(r => r.id == reporterId);
                            if (reporter) {
                                // Set the selected reporter
                                select.value = reporterId;
                                // Make the dropdown read-only and show only the selected reporter
                                select.innerHTML = '';
                                const option = document.createElement('option');
                                option.value = reporterId;
                                option.textContent = reporter.name;
                                select.appendChild(option);
                                select.disabled = true;
                                // Add hidden field to maintain the reporter_id in form submission
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'reporter_id';
                                hiddenInput.value = reporterId;
                                select.parentNode.insertBefore(hiddenInput, select.nextSibling);
                            }
                        }
                    }
                });
            } catch (error) {
                showMessage('error', 'সাংবাদিক লোড করতে ব্যর্থ');
                console.error('Error:', error);
            }
        }

        async function loadNews() {
    try {
        console.log('loadNews called'); // Debug log
        
        // Get reporter ID from URL if it exists
        const urlParams = new URLSearchParams(window.location.search);
        currentReporterId = urlParams.get('id'); // Set the global currentReporterId
        console.log('Current Reporter ID from URL:', currentReporterId); // Debug log
        
        // Construct the API URL with reporter_id if available
        let apiUrl = '../Admin/api.php?action=get_news';
        if (currentReporterId) {
            apiUrl += `&reporter_id=${currentReporterId}`;
        }
        console.log('API URL:', apiUrl); // Debug log
        
        const response = await fetch(apiUrl);
        console.log('Response status:', response.status); // Debug log
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('News data received:', data); // Debug log
        
        allNews = Array.isArray(data) ? data : [];
        filteredNews = [...allNews];
        console.log('All news count:', allNews.length); // Debug log
        
        updatePagination();
        displayNews();
    } catch (error) {
        console.error('Error in loadNews:', error); // More detailed error logging
        showMessage('error', 'সংবাদ নিবন্ধ লোড করতে ব্যর্থ: ' + error.message);
    }
}

        async function deleteNews(id) {
            if (!confirm('আপনি কি নিশ্চিত যে এই নিবন্ধটি মুছে ফেলতে চান? এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না।')) return;
            
            try {
                const response = await fetch('../Admin/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete_news&id=${id}`
                });
                
                const result = await response.json();
                if (result.success) {
                    showMessage('success', 'নিবন্ধটি সফলভাবে মুছে ফেলা হয়েছে');
                    loadNews();
                } else {
                    showMessage('error', 'নিবন্ধ মুছে ফেলতে ব্যর্থ');
                }
            } catch (error) {
                showMessage('error', 'নিবন্ধ মুছে ফেলার সময় ত্রুটি');
                console.error('Error:', error);
            }
        }

        // Edit News - Populate form with data (NO MODAL)
        // Edit News - Populate form with data (NO MODAL)
        async function editNews(id) {
            try {
                // Wait for categories and reporters to be loaded
                if (!categories.length) await loadCategories();
                if (!reporters.length) await loadReporters();

                const response = await fetch(`../Admin/api.php?action=get_news&id=${id}`);
                
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const news = await response.json();
                console.log('editNews API response:', news); // Debug log

                // Check if we got valid data
                if (!news || !news.id) {
                    throw new Error('Invalid news data received');
                }

                isEditMode = true;
                currentEditId = news.id;
                document.getElementById('form-section').classList.add('edit-mode');
                
                // Helper to select correct option
                function selectDropdownValue(selectId, value) {
                    const select = document.getElementById(selectId);
                    if (select) {
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value == value) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
                
                selectDropdownValue('category_id', news.category_id || '');
                selectDropdownValue('reporter_id', news.reporter_id || '');
                
                // Safely set text input values with null checks
                const setInputValue = (id, value) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.value = value || '';
                    } else {
                        console.warn('Element not found:', id);
                    }
                };
                
                setInputValue('headline', news.headline);
                setInputValue('short_description', news.short_description);
                setInputValue('slug', news.slug);
                
                // Set custom editor content
                ['news_1','news_2','news_3','news_4'].forEach(function(id) {
                    if (editors[id]) {
                        editors[id].setContent(news[id] || '');
                    } else {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = news[id] || '';
                        }
                    }
                });
                
                setInputValue('quote_1', news.quote_1);
                setInputValue('quote_2', news.quote_2);
                setInputValue('auture_1', news.auture_1);
                setInputValue('auture_2', news.auture_2);
                
                // Load image titles
                setInputValue('image_url_title', news.image_url_title);
                setInputValue('image_2_title', news.image_2_title);
                setInputValue('image_3_title', news.image_3_title);
                setInputValue('image_4_title', news.image_4_title);
                setInputValue('image_5_title', news.image_5_title);
                
                // Fixed image loading with multiple path attempts
                const imageFields = ['image_url', 'image_2', 'image_3', 'image_4', 'image_5'];
                imageFields.forEach(field => {
                    const preview = document.getElementById('preview_' + field);
                    const sizeInfo = document.getElementById('size_' + field);
                    const imageValue = news[field];
                    
                    if (preview && imageValue && imageValue.trim() !== '') {
                        // Try multiple possible paths
                        const possiblePaths = [
                            '../img/' + imageValue,           // Relative to admin folder
                            'img/' + imageValue,              // Direct img folder
                            '../Admin/img/' + imageValue,     // From root
                            'Admin/img/' + imageValue,        // Alternative
                            imageValue                        // Use as-is if full path
                        ];
                        
                        console.log('Loading image:', field, 'value:', imageValue); // Debug log
                        
                        // Test image loading with error handling
                        const testImg = new Image();
                        let pathIndex = 0;
                        
                        const tryNextPath = () => {
                            if (pathIndex < possiblePaths.length) {
                                testImg.src = possiblePaths[pathIndex];
                                console.log('Trying path:', possiblePaths[pathIndex]); // Debug
                            } else {
                                console.error('All image paths failed for:', field);
                                preview.src = '';
                                preview.style.display = 'none';
                                if (sizeInfo) sizeInfo.textContent = 'ছবি লোড ব্যর্থ';
                            }
                        };
                        
                        testImg.onload = function() {
                            console.log('Image loaded successfully:', possiblePaths[pathIndex]); // Debug
                            preview.src = possiblePaths[pathIndex];
                            preview.style.display = 'block';
                            if (sizeInfo) sizeInfo.textContent = 'বর্তমান ছবি লোড হয়েছে';
                            // Add has-image class to show remove button
                            const imageBox = document.getElementById('box_' + field);
                            if (imageBox) imageBox.classList.add('has-image');
                        };
                        
                        testImg.onerror = function() {
                            console.warn('Failed to load:', possiblePaths[pathIndex]); // Debug
                            pathIndex++;
                            tryNextPath();
                        };
                        
                        tryNextPath();
                    } else {
                        if (preview) {
                            preview.src = '';
                            preview.style.display = 'none';
                        }
                        if (sizeInfo) sizeInfo.textContent = '';
                    }
                });
                
                const sectionTitle = document.querySelector('#form-section .section-header .section-title');
                if (sectionTitle) {
                    sectionTitle.textContent = 'সংবাদ নিবন্ধ সম্পাদনা করুন (ID: ' + news.id + ')';
                }
                
                const submitBtn = document.querySelector('#newsForm button[type="submit"]');
                if (submitBtn) {
                    submitBtn.textContent = 'নিবন্ধ আপডেট করুন';
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-warning');
                }
                
                let cancelBtn = document.getElementById('cancel-edit-btn');
                if (!cancelBtn) {
                    cancelBtn = document.createElement('button');
                    cancelBtn.id = 'cancel-edit-btn';
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'btn btn-danger';
                    cancelBtn.textContent = 'সম্পাদনা বাতিল করুন';
                    cancelBtn.onclick = cancelEdit;
                    const buttonContainer = document.querySelector('#newsForm > div[style*="text-align: center"]');
                    if (buttonContainer && submitBtn) {
                        buttonContainer.insertBefore(cancelBtn, submitBtn);
                        buttonContainer.insertBefore(document.createTextNode(' '), submitBtn);
                    }
                }
                
                const formSection = document.getElementById('form-section');
                if (formSection) {
                    formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                showMessage('success', 'নিবন্ধ সম্পাদনার জন্য লোড হয়েছে');
                
            } catch (error) {
                showMessage('error', 'সম্পাদনার জন্য নিবন্ধ লোড করতে ব্যর্থ: ' + error.message);
                console.error('Error in editNews:', error);
            }
        }

        // Function to cancel edit mode
        function cancelEdit() {
            isEditMode = false;
            currentEditId = null;
            
            // Safely remove edit-mode class
            const formSection = document.getElementById('form-section');
            if (formSection) {
                formSection.classList.remove('edit-mode');
                
                // Restore original form title if it exists
                const sectionTitle = formSection.querySelector('.section-header .section-title');
                if (sectionTitle) {
                    sectionTitle.textContent = 'নতুন নিবন্ধ তৈরি করুন';
                }
            }
            
            // Reset form if it exists
            const newsForm = document.getElementById('newsForm');
            if (newsForm) {
                newsForm.reset();
                
                // Clear custom editors
                ['news_1', 'news_2', 'news_3', 'news_4'].forEach(id => {
                    if (editors[id]) {
                        editors[id].setContent('');
                    }
                });
                
                // Restore submit button if it exists
                const submitBtn = newsForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.textContent = 'সংবাদ প্রকাশ করুন';
        }
    }
    
    // Clear image previews if they exist
    document.querySelectorAll('.image-preview').forEach(img => {
        if (img) {
            img.style.display = 'none';
            img.src = '';
        }
    });
    
    document.querySelectorAll('.image-size-info').forEach(info => {
        if (info) info.textContent = '';
    });
    
    // Remove has-image class from all image boxes
    document.querySelectorAll('.image-upload-box').forEach(box => {
        if (box) box.classList.remove('has-image');
    });
    
    // Reset image deletion flags
    ['delete_image_url', 'delete_image_2', 'delete_image_3', 'delete_image_4', 'delete_image_5'].forEach(id => {
        const field = document.getElementById(id);
        if (field) field.value = '0';
    });
    
    // Remove cancel button if it exists
    const cancelBtn = document.getElementById('cancel-edit-btn');
    if (cancelBtn) {
        cancelBtn.remove();
    }
    
    // Show success message
    showMessage('success', 'সম্পাদনা বাতিল হয়েছে');
}

// Modified handleFormSubmit to handle both create and update
async function handleFormSubmit(e) {
    e.preventDefault();

            // Editors automatically sync with textareas, but ensure it's done
            ['news_1', 'news_2', 'news_3', 'news_4'].forEach(function(id) {
                if (editors[id]) {
                    editors[id].saveContent();
                }
            });

            // Custom validation for news_1 (required)
            const news1Content = editors['news_1'] ? editors['news_1'].getText() : '';
            if (!news1Content) {
                showMessage('error', 'সংবাদ বিষয়বস্তু ১ অবশ্যই পূরণ করতে হবে');
                if (editors['news_1']) editors['news_1'].focus();
                return;
            }

            const formData = new FormData(this);

            // Get reporter ID from URL if it exists and not in edit mode
            if (!isEditMode) {
                const urlParams = new URLSearchParams(window.location.search);
                const reporterId = urlParams.get('id');
                if (reporterId) {
                    formData.set('reporter_id', reporterId);
                }
            }

            // Determine action based on edit mode
            if (isEditMode && currentEditId) {
                formData.append('action', 'update_news');
                formData.append('id', currentEditId);
            } else {
                formData.append('action', 'create_news');
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = isEditMode ? 'আপডেট হচ্ছে...' : 'প্রকাশ হচ্ছে...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('../Admin/api.php', {
                    method: 'POST',
                    body: formData
                });

                let result;
                try {
                    const responseText = await response.text();
                    result = JSON.parse(responseText);
                    
                    if (result && result.success) {
                        showMessage('success', isEditMode ? 'নিবন্ধ সফলভাবে আপডেট হয়েছে!' : 'নিবন্ধ সফলভাবে প্রকাশিত হয়েছে!');
                        // Reset form and exit edit mode
                        cancelEdit();
                        loadNews();
                    } else {
                        const errorMessage = result && result.message 
                            ? result.message 
                            : 'অজানা ত্রুটি হয়েছে। দয়া করে আবার চেষ্টা করুন।';
                        showMessage('error', 'ত্রুটি: ' + errorMessage);
                        console.error('API Error:', result);
                    }
                } catch (parseError) {
                    console.error('Error parsing API response:', parseError);
                    showMessage('error', 'সার্ভার থেকে সঠিক উত্তর পাওয়া যায়নি। দয়া করে আবার চেষ্টা করুন।');
                }
            } catch (error) {
                showMessage('error', isEditMode ? 'নিবন্ধ আপডেট করতে ব্যর্থ' : 'নিবন্ধ প্রকাশ করতে ব্যর্থ');
                console.error('Error:', error);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        async function handleImagePreview(e) {
            const file = e.target.files[0];
            if (file) {
                const previewId = 'preview_' + this.id;
                const sizeInfoId = 'size_' + this.id;
                const boxId = 'box_' + this.id;
                const preview = document.getElementById(previewId);
                const sizeInfo = document.getElementById(sizeInfoId);
                const imageBox = document.getElementById(boxId);
                
                if (preview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                    
                    // Add has-image class to show remove button
                    if (imageBox) {
                        imageBox.classList.add('has-image');
                    }
                    
                    // Show original file size
                    sizeInfo.textContent = `মূল আকার: ${formatFileSize(file.size)}`;
                    
                    try {
                        // Compress image to 100KB
                        const compressedFile = await compressImageTo100KB(file);
                        
                        // Replace the original file with compressed version
                        const dt = new DataTransfer();
                        dt.items.add(compressedFile);
                        e.target.files = dt.files;
                        
                        // Update size info
                        sizeInfo.textContent = `মূল: ${formatFileSize(file.size)} → সংকুচিত: ${formatFileSize(compressedFile.size)}`;
                    } catch (err) {
                        console.error('Image compression error:', err);
                        sizeInfo.textContent = `মূল আকার: ${formatFileSize(file.size)} (সংকোচন ব্যর্থ)`;
                    }
                }
            }
        }

        // Image compression to exactly 100KB or less
        function compressImageTo100KB(file) {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                
                img.onload = function() {
                    const targetSize = 100 * 1024; // 100KB
                    
                    // Calculate initial dimensions
                    let { width, height } = img;
                    const maxDimension = 1200;
                    
                    if (width > height && width > maxDimension) {
                        height = (height * maxDimension) / width;
                        width = maxDimension;
                    } else if (height > maxDimension) {
                        width = (width * maxDimension) / height;
                        height = maxDimension;
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Start with high quality and reduce until under 100KB
                    let quality = 0.9;
                    let scale = 1.0;
                    
                    const tryCompress = () => {
                        // Adjust canvas size if quality is too low
                        if (quality < 0.3 && scale > 0.5) {
                            scale -= 0.1;
                            quality = 0.8;
                            
                            canvas.width = width * scale;
                            canvas.height = height * scale;
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        }
                        
                        canvas.toBlob((blob) => {
                            if (blob.size <= targetSize || (quality <= 0.1 && scale <= 0.5)) {
                                const compressedFile = new File([blob], file.name, {
                                    type: file.type,
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                quality -= 0.1;
                                tryCompress();
                            }
                        }, file.type, quality);
                    };
                    
                    tryCompress();
                };
                
                img.src = URL.createObjectURL(file);
            });
        }

        // Utility Functions
        function generateSlug(text) {
            if (!text) return '';
            
            // Remove all punctuation and special characters except spaces
            // This handles: . , ! ? ; : ' " - — – ( ) [ ] { } / \ | @ # $ % ^ & * + = < > ` ~ etc.
            let slug = text
                .trim()
                .toLowerCase()
                // Remove common punctuation marks
                .replace(/[।!@#$%^&*()_+=\[\]{};:'",.<>?\/\\|`~।॥]/g, ' ')
                // Replace multiple spaces with single space
                .replace(/\s+/g, ' ')
                .trim();
            
            // Take first 10 words
            const words = slug.split(/\s+/).slice(0, 10);
            
            // Join with underscore
            slug = words.join('_');
            
            // Remove any remaining special characters that might have slipped through
            slug = slug.replace(/[^\u0980-\u09FFa-z0-9_\-]/gi, '');
            
            // Remove multiple underscores/hyphens
            slug = slug.replace(/[_\-]+/g, '_');
            
            // Remove leading/trailing underscores or hyphens
            slug = slug.replace(/^[_\-]+|[_\-]+$/g, '');
            
            return slug || 'news_' + Date.now();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // ============================================
// FIND THIS SECTION IN YOUR CODE:
// ============================================
function dateWithAgo(dateString) {
    const now = new Date();
    const past = new Date(dateString);

    // ---- Time Ago ----
    const diffSeconds = Math.floor((now - past) / 1000);

    let ago = '';
    if (diffSeconds < 60) {
        ago = diffSeconds + ' সেকেন্ড আগে';
    } else if (diffSeconds < 3600) {
        ago = Math.floor(diffSeconds / 60) + ' মিনিট আগে';
    } else if (diffSeconds < 86400) {
        ago = Math.floor(diffSeconds / 3600) + ' ঘণ্টা আগে';
    } else if (diffSeconds < 2592000) {
        ago = Math.floor(diffSeconds / 86400) + ' দিন আগে';
    } else if (diffSeconds < 31536000) {
        ago = Math.floor(diffSeconds / 2592000) + ' মাস আগে';
    } else {
        ago = Math.floor(diffSeconds / 31536000) + ' বছর আগে';
    }

    // ---- Date Format ----
    const date = past.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    return `${date} · ${ago}`;
}
function displayNews() {
    const tbody = document.getElementById('news-table-body');

    if (!filteredNews || filteredNews.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center; padding:40px; color:#666;">
                    কোনো নিবন্ধ পাওয়া যায়নি
                </td>
            </tr>
        `;
        return;
    }

    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const currentRecords = filteredNews.slice(startIndex, endIndex);

    tbody.innerHTML = currentRecords.map(article => {
        const category = categories.find(c => c.id == article.category_id);
        const reporter = reporters.find(r => r.id == article.reporter_id);
        const dateAgo = dateWithAgo(article.created_at);

        // Only filter by reporter if currentReporterId is set
        if (currentReporterId && (!reporter || reporter.id != currentReporterId)) return '';
        
        return `
            <tr>
                <td><strong>#${article.id}</strong></td>

                <td style="max-width:300px;">
                    <div style="font-weight:600; color:#2c3e50; margin-bottom:4px;">
                        ${article.headline}
                    </div>
                    <div style="font-size:12px; color:#666;">
                        ${article.short_description || ''}
                    </div>
                </td>

                <td>
                    <span style="background:#e9ecef; padding:4px 8px; border-radius:12px; font-size:12px;">
                        ${category ? category.name : 'N/A'}
                    </span>
                </td>

                <td>
                    ${reporter ? reporter.name : 'নিজস্ব প্রতিবেদক'}
                </td>

                <td>
                    <div style="font-size:13px; color:#666;">
                        ${dateAgo}
                    </div>
                </td>

                <td class="actions">
                    <button class="btn" onclick="viewNews(${article.id})">
                        <img src="../Admin/icons/view.png" width="20">
                    </button>
                    ${article.is_active == 0 ? 
                        `<button class="btn" onclick="editNews(${article.id})">
                            <img src="../Admin/icons/edit.png" width="20">
                        </button>` : 
                        `<span style="color:#28a745; font-weight:600; padding:8px 12px;">Published</span>`
                    }
                </td>
            </tr>
        `;
    }).join('');
}

// ✅ displayNews() function ENDS HERE

// ============================================
// ADD THESE FUNCTIONS RIGHT AFTER displayNews()
// ============================================

// News View Popup Functions - GLOBAL SCOPE (CORRECTED IMAGE PATHS)
async function viewNews(id) {
    try {
        const response = await fetch(`../Admin/api.php?action=get_news&id=${id}`);
        if (!response.ok) throw new Error('Failed to fetch news');
        
        const news = await response.json();
        
        if (!news || !news.id) {
            throw new Error('Invalid news data');
        }
        
        // Compose popup HTML
        let html = '<div class="news-content-wrapper">';
        
        // Headline
        if (news.headline) {
            html += `<div class='news-view-headline'>${news.headline}</div>`;
        }
        
        // Meta info (date, category, reporter)
        let metaItems = [];
        if (news.created_at) {
            const date = new Date(news.created_at).toLocaleDateString('bn-BD', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            metaItems.push(`<span class="news-view-meta-item">তারিখ: ${date}</span>`);
        }
        
        const category = categories.find(c => c.id == news.category_id);
        if (category) {
            metaItems.push(`<span class="news-view-meta-item">ক্যাটাগরি: ${category.name}</span>`);
        }
        
        const reporter = reporters.find(r => r.id == news.reporter_id);
        if (reporter) {
            metaItems.push(`<span class="news-view-meta-item">রিপোর্টার: ${reporter.name}</span>`);
        }
        
        if (metaItems.length > 0) {
            html += `<div class='news-view-meta'>${metaItems.join('')}</div>`;
        }
        
        // Short description
        if (news.short_description) {
            html += `<div class='news-view-short-desc'>${news.short_description}</div>`;
        }
        
        // Main image - CORRECTED PATH
        if (news.image_url) {
            html += `<img class='news-view-img' src='../Admin/img/${news.image_url}' alt='Main Image' onerror="this.style.display='none'">`;
        }
        
        // News content 1
        if (news.news_1) {
            html += `<div class='news-view-section'>${news.news_1}</div>`;
        }
        
        // Quote 1 and Author 1
        if (news.quote_1) {
            html += `<div class='news-view-quote'>${news.quote_1}</div>`;
            if (news.auture_1) {
                html += `<div class='news-view-author'>${news.auture_1}</div>`;
            }
        }
        
        // Images 2 and 3 - CORRECTED PATH
        const images23 = [news.image_2, news.image_3].filter(Boolean);
        if (images23.length > 0) {
            html += `<div class='news-view-gallery'>`;
            images23.forEach(img => {
                html += `<img src='img/${img}' alt='Gallery' onerror="this.style.display='none'">`;
            });
            html += `</div>`;
        }
        
        // News content 2
        if (news.news_2) {
            if (news.news_1) html += `<div class='news-view-divider'></div>`;
            html += `<div class='news-view-section'>${news.news_2}</div>`;
        }
        
        // News content 3
        if (news.news_3) {
            if (news.news_1 || news.news_2) html += `<div class='news-view-divider'></div>`;
            html += `<div class='news-view-section'>${news.news_3}</div>`;
        }
        
        // Quote 2 and Author 2
        if (news.quote_2) {
            html += `<div class='news-view-quote'>${news.quote_2}</div>`;
            if (news.auture_2) {
                html += `<div class='news-view-author'>${news.auture_2}</div>`;
            }
        }
        
        // Images 4 and 5 - CORRECTED PATH
        const images45 = [news.image_4, news.image_5].filter(Boolean);
        if (images45.length > 0) {
            html += `<div class='news-view-gallery'>`;
            images45.forEach(img => {
                html += `<img src='img/${img}' alt='Gallery' onerror="this.style.display='none'">`;
            });
            html += `</div>`;
        }
        
        // News content 4
        if (news.news_4) {
            if (news.news_1 || news.news_2 || news.news_3) html += `<div class='news-view-divider'></div>`;
            html += `<div class='news-view-section'>${news.news_4}</div>`;
        }
        
        html += '</div>'; // Close news-content-wrapper
        
        // If no content at all
        if (!news.headline && !news.news_1 && !news.news_2 && !news.news_3 && !news.news_4) {
            html = '<div class="news-content-wrapper"><p style="text-align: center; color: #999; padding: 40px;">কোনো বিষয়বস্তু নেই</p></div>';
        }
        
        // Insert HTML into popup
        document.getElementById('news-view-body').innerHTML = html;
        
        // Show popup with smooth animation
        const popup = document.getElementById('news-view-popup');
        popup.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
    } catch (error) {
        console.error('Error viewing news:', error);
        showMessage('error', 'নিউজ লোড করতে ব্যর্থ হয়েছে');
    }
}

function closeNewsView() {
    const popup = document.getElementById('news-view-popup');
    popup.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close popup when clicking outside the container
document.addEventListener('click', function(e) {
    const popup = document.getElementById('news-view-popup');
    if (e.target === popup) {
        closeNewsView();
    }
});

// Close popup on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const popup = document.getElementById('news-view-popup');
        if (!popup.classList.contains('hidden')) {
            closeNewsView();
        }
    }
});

function closeNewsView() {
    const popup = document.getElementById('news-view-popup');
    popup.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close popup when clicking outside the container
document.addEventListener('click', function(e) {
    const popup = document.getElementById('news-view-popup');
    if (e.target === popup) {
        closeNewsView();
    }
});


        

        function updatePagination() {
            const totalRecords = filteredNews.length;
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
            displayNews();
            updatePagination();
        }

        function applyFilters() {
            const searchTerm = document.getElementById('search-filter').value.toLowerCase();
            const dateFrom = document.getElementById('date-from-filter').value;
            const dateTo = document.getElementById('date-to-filter').value;
            const categoryFilter = document.getElementById('category-filter').value;
            
            filteredNews = allNews.filter(article => {
                const matchesSearch = !searchTerm || article.headline.toLowerCase().includes(searchTerm);
                const matchesCategory = !categoryFilter || article.category_id == categoryFilter;
                
                const articleDate = new Date(article.created_at).toISOString().split('T')[0];
                const matchesDateFrom = !dateFrom || articleDate >= dateFrom;
                const matchesDateTo = !dateTo || articleDate <= dateTo;
                
                return matchesSearch && matchesCategory && matchesDateFrom && matchesDateTo;
            });
            
            currentPage = 1;
            updatePagination();
            displayNews();
        }

        function clearFilters() {
            document.getElementById('search-filter').value = '';
            document.getElementById('date-from-filter').value = '';
            document.getElementById('date-to-filter').value = '';
            document.getElementById('category-filter').value = '';
            filteredNews = [...allNews];
            currentPage = 1;
            updatePagination();
            displayNews();
        }

        // Modern Popup Message
        let popupTimeout = null;
        function showMessage(type, message) {
            const popup = document.getElementById('popup-message');
            const popupText = document.getElementById('popup-text');
            popupText.textContent = message;
            popup.classList.remove('hidden', 'success', 'error');
            popup.classList.add(type === 'success' ? 'success' : 'error');
            // Show popup
            popup.style.display = 'flex';
            // Auto-hide after 5 seconds
            if (popupTimeout) clearTimeout(popupTimeout);
            popupTimeout = setTimeout(hidePopupMessage, 5000);
        }

        function hidePopupMessage() {
            const popup = document.getElementById('popup-message');
            popup.classList.add('hidden');
            // Optionally hide after animation
            setTimeout(() => { popup.style.display = 'none'; }, 350);
        }

        // ESC key to cancel edit
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isEditMode) {
                if (confirm('আপনি কি সম্পাদনা বাতিল করতে চান?')) {
                    cancelEdit();
                }
            }
        });
            // Toggle status AJAX
            async function toggleStatus(id, checkbox) {
                const is_active = checkbox.checked ? 1 : 0;
                checkbox.disabled = true;
                try {
                    const response = await fetch('../Admin/api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=toggle_status&id=${id}&is_active=${is_active}`
                    });
                    const result = await response.json();
                    if (!result.success) {
                        showMessage('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ: ' + (result.message || '')); 
                        checkbox.checked = !checkbox.checked; // revert
                    } else {
                        showMessage('success', is_active ? 'নিউজ প্রকাশিত হয়েছে' : 'নিউজ অপ্রকাশিত হয়েছে');
                    }
                } catch (error) {
                    showMessage('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ');
                    checkbox.checked = !checkbox.checked; // revert
                } finally {
                    checkbox.disabled = false;
                }
            }
    </script>
</body>
</html>