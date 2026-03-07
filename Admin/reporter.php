<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporter Management - Hindus News</title>
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
            max-width: 800px;
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

        .image-preview {
            width: 100px;
            height: 100px;
            border: 1px dashed #ddd;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
        }

        .file-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

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

    <div class="container">
        <div id="success-message" class="alert alert-success hidden"></div>
        <div id="error-message" class="alert alert-error hidden"></div>

        <!-- Add Reporter Form Section -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">সাংবাদিক রেজিস্ট্রেশন</div>
            </div>
            <div class="form-container">
                <form id="reporterForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">নাম</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">ইমেইল</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">ফোন নম্বর</label>
                            <input type="text" id="phone_number" name="phone_number" required>
                        </div>

                        <div class="form-group">
                            <label for="id_card">আইডি কার্ড নম্বর</label>
                            <input type="text" id="id_card" name="id_card" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="address">ঠিকানা</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="photo">ফটো</label>
                            <input type="file" id="photo" name="photo" accept="image/*" required>
                            <div class="file-info" id="photo-info">সর্বোচ্চ 80KB</div>
                            <div class="image-preview hidden" id="photo-preview">
                                <span>প্রিভিউ</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="id_card_photo">আইডি কার্ড ফটো</label>
                            <input type="file" id="id_card_photo" name="id_card_photo" accept="image/*" required>
                            <div class="file-info" id="id-card-photo-info">সর্বোচ্চ 80KB</div>
                            <div class="image-preview hidden" id="id-card-photo-preview">
                                <span>প্রিভিউ</span>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="btn btn-success">সাবমিট করুন</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reporters Table Section -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">সমস্ত সাংবাদিক</div>
                <button class="btn btn-primary" onclick="loadReporters()">রিফ্রেশ</button>
            </div>
            
            <div class="filters">
                <div class="filter-group">
                    <label>খুঁজুন</label>
                    <input type="text" id="search-filter" placeholder="নাম বা ইমেইলে খুঁজুন...">
                </div>
                <button class="btn btn-primary" onclick="applyFilters()">ফিল্টার</button>
                <button class="btn btn-warning" onclick="clearFilters()">পরিষ্কার</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>আইডি</th>
                            <th>নাম</th>
                            <th>ইমেইল</th>
                            <th>ফোন</th>
                            <th>আইডি কার্ড</th>
                            <th>ঠিকানা</th>
                            <th>ফটো</th>
                            <th>নিবন্ধনের তারিখ</th>
                            <th>স্ট্যাটাস</th>
                            <th>কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody id="reporters-table-body">
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: #666;">
                                সাংবাদিকদের তথ্য লোড হচ্ছে...
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

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3>সাংবাদিক সম্পাদনা</h3>
                <button class="btn btn-danger" onclick="closeEditModal()" style="padding: 5px 10px;">×</button>
            </div>
            <div class="modal-body">
                <form id="editReporterForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit-reporter-id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit-name">নাম</label>
                            <input type="text" id="edit-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-email">ইমেইল</label>
                            <input type="email" id="edit-email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-phone_number">ফোন নম্বর</label>
                            <input type="text" id="edit-phone_number" name="phone_number" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-id_card">আইডি কার্ড নম্বর</label>
                            <input type="text" id="edit-id_card" name="id_card" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="edit-address">ঠিকানা</label>
                            <textarea id="edit-address" name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit-photo">ফটো (শুধুমাত্র পরিবর্তন করতে চাইলে নির্বাচন করুন)</label>
                            <input type="file" id="edit-photo" name="photo" accept="image/*">
                            <div class="file-info">সর্বোচ্চ 80KB</div>
                            <div class="image-preview hidden" id="edit-photo-preview">
                                <span>প্রিভিউ</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit-id_card_photo">আইডি কার্ড ফটো (শুধুমাত্র পরিবর্তন করতে চাইলে নির্বাচন করুন)</label>
                            <input type="file" id="edit-id_card_photo" name="id_card_photo" accept="image/*">
                            <div class="file-info">সর্বোচ্চ 80KB</div>
                            <div class="image-preview hidden" id="edit-id-card-photo-preview">
                                <span>প্রিভিউ</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="closeEditModal()">বাতিল</button>
                <button type="submit" form="editReporterForm" class="btn btn-success">সাংবাদিক আপডেট করুন</button>
            </div>
        </div>
    </div>

    <script>
// Permission variables from PHP
const canEdit = <?php echo $can_edit ? 'true' : 'false'; ?>;
const canDelete = <?php echo $can_delete ? 'true' : 'false'; ?>;

// Global variables
let allReporters = [];
let filteredReporters = [];
let currentPage = 1;
let recordsPerPage = 10;
let totalPages = 1;

// Reporter API endpoints
const API_BASE = 'reporter_api.php';

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadReporters();
    setupEventListeners();
});

function setupEventListeners() {
    // Image preview and compression
    document.getElementById('photo').addEventListener('change', function(e) {
        handleImageUpload(e, 'photo-preview', 'photo-info');
    });
    
    document.getElementById('id_card_photo').addEventListener('change', function(e) {
        handleImageUpload(e, 'id-card-photo-preview', 'id-card-photo-info');
    });
    
    document.getElementById('edit-photo').addEventListener('change', function(e) {
        handleImageUpload(e, 'edit-photo-preview', null);
    });
    
    document.getElementById('edit-id_card_photo').addEventListener('change', function(e) {
        handleImageUpload(e, 'edit-id-card-photo-preview', null);
    });

    // Form submission
    document.getElementById('reporterForm').addEventListener('submit', handleFormSubmit);
    document.getElementById('editReporterForm').addEventListener('submit', handleEditSubmit);

    // Search filter
    document.getElementById('search-filter').addEventListener('input', applyFilters);
}

// Image compression function
async function compressImage(file, maxSizeKB = 80) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const maxDimension = 800; // Maximum dimension

                // Calculate new dimensions
                if (width > height) {
                    if (width > maxDimension) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    }
                } else {
                    if (height > maxDimension) {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Try different quality levels to meet size requirement
                let quality = 0.9;
                let compressedDataUrl;

                const attemptCompression = () => {
                    compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
                    // Calculate file size (approx)
                    const base64 = compressedDataUrl.split(',')[1];
                    const binaryLength = window.atob(base64).length;
                    const sizeKB = binaryLength / 1024;

                    if (sizeKB > maxSizeKB && quality > 0.1) {
                        quality -= 0.1;
                        attemptCompression();
                    } else {
                        // Convert data URL to blob
                        fetch(compressedDataUrl)
                            .then(res => res.blob())
                            .then(blob => {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            })
                            .catch(reject);
                    }
                };

                attemptCompression();
            };
            img.onerror = reject;
        };
        reader.onerror = reject;
    });
}

// Handle image upload with preview
async function handleImageUpload(event, previewId, infoId) {
    const file = event.target.files[0];
    if (!file) return;

    const preview = document.getElementById(previewId);
    const info = infoId ? document.getElementById(infoId) : null;

    // Show original file size
    if (info) {
        info.textContent = `ফাইলের আকার: ${(file.size / 1024).toFixed(2)}KB (সর্বোচ্চ 80KB)`;
    }

    // Check if image needs compression
    if (file.size > 80 * 1024) {
        try {
            const compressedFile = await compressImage(file, 80);
            if (info) {
                info.textContent = `ফাইলের আকার: ${(compressedFile.size / 1024).toFixed(2)}KB (কম্প্রেস করা হয়েছে)`;
            }
            // Replace the file in the input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            event.target.files = dataTransfer.files;
        } catch (error) {
            console.error('Image compression failed:', error);
            if (info) {
                info.textContent = 'কম্প্রেশন ব্যর্থ হয়েছে';
            }
        }
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(event.target.files[0]);
}

// API Functions
async function loadReporters() {
    try {
        showLoader(true);
        const response = await fetch(`${API_BASE}?action=get_reporters`);
        const result = await response.json();
        
        if (result.success) {
            allReporters = result.data;
            filteredReporters = [...allReporters];
            updatePagination();
            displayReporters();
        } else {
            showMessage('error', 'সাংবাদিকদের লোড করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সাংবাদিকদের লোড করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

async function deleteReporter(id) {
    if (!confirm('আপনি কি নিশ্চিত যে এই সাংবাদিককে মুছে ফেলতে চান?')) return;
    
    try {
        showLoader(true);
        const formData = new FormData();
        formData.append('action', 'delete_reporter');
        formData.append('id', id);
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'সাংবাদিক সফলভাবে মুছে ফেলা হয়েছে');
            loadReporters();
        } else {
            showMessage('error', 'সাংবাদিক মুছে ফেলতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সাংবাদিক মুছে ফেলার সময় ত্রুটি');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

async function editReporter(id) {
    try {
        showLoader(true);
        const response = await fetch(`${API_BASE}?action=get_reporter&id=${id}`);
        const result = await response.json();
        
        if (result.success) {
            const reporter = result.data;
            // Populate edit form
            document.getElementById('edit-reporter-id').value = reporter.id;
            document.getElementById('edit-name').value = reporter.name;
            document.getElementById('edit-email').value = reporter.email;
            document.getElementById('edit-phone_number').value = reporter.phone_number;
            document.getElementById('edit-id_card').value = reporter.id_card;
            document.getElementById('edit-address').value = reporter.address;
            
            // Show existing images
            const photoPreview = document.getElementById('edit-photo-preview');
            const idCardPreview = document.getElementById('edit-id-card-photo-preview');
            
            if (reporter.photo) {
                photoPreview.innerHTML = `<img src="${reporter.photo}" alt="Current Photo">`;
                photoPreview.classList.remove('hidden');
            }
            
            if (reporter.id_card_photo) {
                idCardPreview.innerHTML = `<img src="${reporter.id_card_photo}" alt="Current ID Card">`;
                idCardPreview.classList.remove('hidden');
            }
            
            // Show modal
            document.getElementById('edit-modal').classList.remove('hidden');
        } else {
            showMessage('error', 'সম্পাদনার জন্য সাংবাদিক লোড করতে ব্যর্থ: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সম্পাদনার জন্য সাংবাদিক লোড করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

// Event Handlers
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_reporter');
    
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
            showMessage('success', 'সাংবাদিক সফলভাবে যোগ করা হয়েছে!');
            this.reset();
            // Clear previews
            document.getElementById('photo-preview').classList.add('hidden');
            document.getElementById('id-card-photo-preview').classList.add('hidden');
            document.getElementById('photo-preview').innerHTML = '<span>প্রিভিউ</span>';
            document.getElementById('id-card-photo-preview').innerHTML = '<span>প্রিভিউ</span>';
            // Reset file info
            document.getElementById('photo-info').textContent = 'সর্বোচ্চ 80KB';
            document.getElementById('id-card-photo-info').textContent = 'সর্বোচ্চ 80KB';
            
            loadReporters();
        } else {
            showMessage('error', 'ত্রুটি: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সাংবাদিক যোগ করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        showLoader(false);
    }
}

async function handleEditSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update_reporter');
    formData.append('id', document.getElementById('edit-reporter-id').value);
    
    try {
        showLoader(true);
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'সাংবাদিক সফলভাবে আপডেট হয়েছে!');
            closeEditModal();
            loadReporters();
        } else {
            showMessage('error', 'ত্রুটি: ' + result.message);
        }
    } catch (error) {
        showMessage('error', 'সাংবাদিক আপডেট করতে ব্যর্থ');
        console.error('Error:', error);
    } finally {
        showLoader(false);
    }
}

// Utility Functions
function displayReporters() {
    const tbody = document.getElementById('reporters-table-body');
    
    if (!filteredReporters || filteredReporters.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 40px; color: #666;">কোনো সাংবাদিক পাওয়া যায়নি</td></tr>';
        return;
    }
    
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const currentRecords = filteredReporters.slice(startIndex, endIndex);
    
    tbody.innerHTML = currentRecords.map(reporter => {
        const isActive = reporter.is_active == 1;
        return `
            <tr>
                <td><strong>#${reporter.id}</strong></td>
                <td>${reporter.name}</td>
                <td>${reporter.email}</td>
                <td>${reporter.phone_number}</td>
                <td>${reporter.id_card}</td>
                <td>${reporter.address.substring(0, 30)}${reporter.address.length > 30 ? '...' : ''}</td>
                <td>${reporter.photo ? '<img src="' + reporter.photo + '" width="50" height="50" style="object-fit: cover; border-radius: 4px;">' : 'N/A'}</td>
                <td>${new Date(reporter.created_at).toLocaleDateString('bn-BD')}</td>
                <td>
                    <label class="switch">
                        <input type="checkbox"
                            onchange="toggleReporterStatus(${reporter.id}, this)"
                            ${isActive ? 'checked' : ''}>
                        <span class="slider round"></span>
                    </label>
                </td>
                <td class="actions">
                    ${canEdit ? `<button class="btn btn-warning" onclick="editReporter(${reporter.id})">এডিট</button>` : ''}
                    ${canDelete ? `<button class="btn btn-danger" onclick="deleteReporter(${reporter.id})">মুছুন</button>` : ''}
                    ${!canEdit && !canDelete ? '<span style="color:#999;">-</span>' : ''}
                </td>
            </tr>
        `;
    }).join('');
}
function updatePagination() {
    const totalRecords = filteredReporters.length;
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
    displayReporters();
    updatePagination();
}

function applyFilters() {
    const searchTerm = document.getElementById('search-filter').value.toLowerCase();
    
    filteredReporters = allReporters.filter(reporter => {
        const matchesSearch = !searchTerm || 
            reporter.name.toLowerCase().includes(searchTerm) ||
            reporter.email.toLowerCase().includes(searchTerm);
        
        return matchesSearch;
    });
    
    currentPage = 1;
    updatePagination();
    displayReporters();
}

function clearFilters() {
    document.getElementById('search-filter').value = '';
    filteredReporters = [...allReporters];
    currentPage = 1;
    updatePagination();
    displayReporters();
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

// Toggle reporter status
async function toggleReporterStatus(id, checkbox) {
    const is_active = checkbox.checked ? 1 : 0;
    checkbox.disabled = true;
    try {
        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=toggle_reporter_status&id=${id}&is_active=${is_active}`
        });
        const result = await response.json();
        if (!result.success) {
            showMessage('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ: ' + (result.message || '')); 
            checkbox.checked = !checkbox.checked;
        } else {
            showMessage('success', is_active ? 'সাংবাদিক সক্রিয় করা হয়েছে' : 'সাংবাদিক নিষ্ক্রিয় করা হয়েছে');
        }
    } catch (error) {
        showMessage('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ');
        checkbox.checked = !checkbox.checked;
        console.error('Error:', error);
    } finally {
        checkbox.disabled = false;
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
</body>
</html>