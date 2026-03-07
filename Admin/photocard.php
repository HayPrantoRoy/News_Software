<?php
include 'auth_check.php';
include '../connection.php';

if (!$can_view) {
    header("Location: dashboard.php");
    exit();
}

// Fetch basic_info for logo
$basic_info = null;
$result = $conn->query("SELECT * FROM basic_info LIMIT 1");
if ($result && $result->num_rows > 0) {
    $basic_info = $result->fetch_assoc();
}

// Fetch latest news
$latest_news = null;
$result = $conn->query("SELECT n.*, c.name as category_name FROM news n LEFT JOIN category c ON n.category_id = c.id WHERE n.is_active = 1 ORDER BY n.created_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $latest_news = $result->fetch_assoc();
}

function formatBanglaDate($date) {
    $banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $banglaMonths = [1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল', 5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট', 9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'];
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = (int)date('n', $timestamp);
    $year = date('Y', $timestamp);
    $banglaDay = str_replace(range(0, 9), $banglaDigits, $day);
    $banglaYear = str_replace(range(0, 9), $banglaDigits, $year);
    return $banglaDay . ' ' . $banglaMonths[$month] . ', ' . $banglaYear;
}

$portalName = $basic_info['news_portal_name'] ?? 'News Portal';
$logoUrl = $basic_info['image'] ?? '';
$defaultDate = formatBanglaDate(date('Y-m-d'));

// Convert logo to base64 for html2canvas compatibility
$logoBase64 = '';
if ($logoUrl && file_exists('../' . $logoUrl)) {
    $logoPath = '../' . $logoUrl;
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
} elseif ($logoUrl && file_exists($logoUrl)) {
    $logoPath = $logoUrl;
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Card Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');
        @import url('https://fonts.maateen.me/solaiman-lipi/font.css');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'SolaimanLipi', sans-serif !important; }
        
        .page-header { background: #308e87; color: white; padding: 24px 30px; border-radius: 12px; margin-bottom: 24px; }
        .page-header h1 { font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; margin: 0; }
        
        .section { background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .section h2 { font-size: 18px; margin-bottom: 20px; color: #2d3748; padding-bottom: 12px; border-bottom: 2px solid #308e87; display: flex; align-items: center; gap: 10px; }
        
        .photocards-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; }
        .photocard-wrapper { display: flex; flex-direction: column; align-items: center; }
        .photocard-label { font-weight: 600; color: #4a5568; margin-bottom: 10px; font-size: 14px; }
        
        /* Design 1 - Clean White & Blue */
        .card-design-1 { width: 300px; height: 360px; background: #ffffff; position: relative; overflow: hidden; font-family: 'SolaimanLipi', sans-serif !important; border: 1px solid #2563eb;  }
        
        .card-design-1 .logo { width: 40px; height: 40px; border-radius: 8px; object-fit: contain; background: white; padding: 4px; }
        .card-design-1 .date-badge { color: white; font-size: 12px; font-weight: 500; }
        .card-design-1 .news-image { width: 100%; height: 240px; object-fit: cover; }
        .card-design-1 .category-badge { position: absolute; top: 230px; left: 15px; background: #2563eb; color: white; padding: 4px 8px; border-radius: 2px; font-size: 14px; }
        .card-design-1 .title { position: absolute; bottom: 50px; left: 15px; right: 15px; color: #1e293b; font-size: 17px;  font-family: 'Hind Siliguri', sans-serif; font-weight: 600; line-height: 1.5; }
        .card-design-1 .title .highlight { color: #2563eb; }
        .card-design-1 .footer { position: absolute; bottom: 0; left: 0; right: 0; background: #f1f5f9; padding: 5px 15px; display: flex; justify-content: space-between; color: #475569; font-size: 11px; font-weight: 500; }
        
        /* Design 2 - Red Gradient (Matching Reference) */
        .card-design-2 { width: 300px; height: 360px; position: relative; overflow: hidden; background:  linear-gradient(360deg, hsla(0, 100%, 50%, 1) 0%, hsla(33, 33%, 95%, 1) 100%); font-family: 'SolaimanLipi', sans-serif !important; border-radius: 0; }
        .card-design-2 .header { position: absolute; top: 0; left: 0; right: 0; padding: 12px 15px; display: flex; justify-content: space-between; align-items: flex-start; z-index: 20; }
        .card-design-2 .logo-overlay {  padding: 6px 10px; border-radius: 6px; z-index: 20; }
        .card-design-2 .logo-overlay img { height: 30px; width: auto; display: block; margin-top: -8px;}
        .card-design-2 .date-badge { color: black; font-size: 13px; font-weight: 500; padding-top: 8px; }
        .card-design-2 .image-wrapper { position: absolute; top: 45px; left: 20px; right: 20px; height: 220px; overflow: hidden; border-radius: 8px; }
        .card-design-2 .news-image { width: 100%; height: 160px; object-fit: cover; cursor: pointer; border: 2px solid white; margin-top: 10px; border-radius: 8px; }
        .card-design-2 .title-area { position: absolute; bottom: 50px; left: 0; right: 0; padding: 0 20px; text-align: center; }
        .card-design-2 .title { color: white; font-size: 20px;  font-family: 'Hind Siliguri', sans-serif; line-height: 1.4; text-shadow: 1px 1px 3px rgba(0,0,0,0.3); font-weight: 500; cursor: pointer; }
        .card-design-2 .title .highlight { color: #FFD700; }
        .card-design-2 .bottom-bar { position: absolute; bottom: 0; left: 0; right: 0; background: #8B0000; padding: 12px 20px; text-align: center; color: white; font-size: 14px; font-weight: 600; }
        
        /* Design 3 - Minimal Red Accent */
        .card-design-3 { width: 360px; height: 400px; position: relative; overflow: hidden; background: #fafafa; font-family: 'SolaimanLipi', sans-serif !important; border: 1px solid #2563eb;}
        .card-design-3 .logo-top { position: absolute; top: 15px; left: 15px; z-index: 10; display: flex; align-items: center; gap: 8px; }
        .card-design-3 .logo-top img { height: 32px; width: auto; }
        .card-design-3 .logo-top span { color: #1e293b; font-size: 14px; }
        .card-design-3 .news-image { width: 100%; height: 220px; object-fit: cover; margin-top: 60px; }
        .card-design-3 .content { padding: 15px 20px; }
        .card-design-3 .title { color: #1e293b; font-size: 17px;  font-family: 'Hind Siliguri', sans-serif; font-weight: 600; line-height: 1.5; margin-bottom: 8px; }
        .card-design-3 .title .highlight { color: #dc2626; }
        .card-design-3 .meta { display: flex; justify-content: space-between; color: #64748b; font-size: 12px; margin-top: 18px; }
        
        /* Design 4 - Premium Minimal */
        .card-design-4 { width: 360px; height: 450px; position: relative; overflow: hidden; background: #ffffff; font-family: 'SolaimanLipi', sans-serif !important; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .card-design-4 .image-container { width: 100%; height: 260px; overflow: hidden; position: relative; }
        .card-design-4 .news-image { width: 100%; height: 100%; object-fit: cover; transition: all 0.3s; }
        .card-design-4 .logo-badge { position: absolute; bottom: -20px; left: 20px; z-index: 10; background: white; padding: 10px 16px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 8px; }
        .card-design-4 .logo-badge img { height: 28px; width: auto; }
        .card-design-4 .logo-badge span { font-size: 13px; color: #1e293b; }
        .card-design-4 .content { padding: 35px 20px 15px; }
        .card-design-4 .title { color: #1e293b; font-size: 18px;  font-family: 'Hind Siliguri', sans-serif; font-weight: 600; line-height: 1.5; margin-bottom: 12px; }
        .card-design-4 .title .highlight { color: #0891b2; }
        .card-design-4 .date-badge { color: #64748b; font-size: 12px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .card-design-4 .accent-line { position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0891b2, #06b6d4); }
        
        .download-btn { margin-top: 12px; padding: 10px 18px; background: #308e87; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .download-btn:hover { background: #267872; transform: translateY(-2px); }
        
        .custom-section { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; margin-bottom: 6px; color: #4a5568; font-size: 13px; }
        .form-group input, .form-group select { padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #308e87; }
        
        .btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: #308e87; color: white; }
        .btn-primary:hover { background: #267872; }
        
        .table-container { overflow-x: auto; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f7fafc; }
        th { padding: 12px; text-align: left; font-weight: 600; color: #4a5568; font-size: 13px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        tr:hover { background: #f7fafc; }
        .news-thumb { width: 70px; height: 45px; object-fit: cover; border-radius: 4px; }
        .headline-cell { max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .generate-btn { padding: 6px 14px; background: #4299e1; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; }
        .generate-btn:hover { background: #3182ce; }
        
        .pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
        .pagination button { padding: 8px 14px; border: 1px solid #e2e8f0; background: white; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 14px; color: #555; }
        .pagination button:hover { background: #f7fafc; }
        .pagination button.active { background: #308e87; color: white; border-color: #308e87; }
        .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
        .pagination .nav-btn { padding: 8px 16px; font-weight: 600; }
        
        /* Card Controls - For All Cards */
        .card-controls { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; align-items: center; justify-content: center; max-width: 360px; }
        .card-controls .control-group { display: flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; }
        .card-controls label { font-size: 10px; font-weight: 600; color: #64748b; white-space: nowrap; }
        .card-controls input[type="range"] { width: 60px; cursor: pointer; }
        .card-controls input[type="color"] { width: 24px; height: 20px; border: none; border-radius: 4px; cursor: pointer; padding: 0; }
        .card-controls input[type="number"] { width: 40px; padding: 2px 4px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 11px; text-align: center; }
        
        /* Click-to-Edit Styles */
        .editable-image { cursor: pointer; transition: outline 0.2s; }
        .editable-image.editing { outline: 3px dashed #3b82f6; outline-offset: 2px; }
        .editable-text { cursor: pointer; transition: outline 0.2s; }
        .editable-text.editing { outline: 2px dashed #10b981; outline-offset: 2px; }
        
        /* Floating Edit Toolbar */
        .edit-toolbar { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 20px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); display: none; z-index: 1000; gap: 12px; align-items: center; }
        .edit-toolbar.active { display: flex; }
        .edit-toolbar .toolbar-group { display: flex; align-items: center; gap: 6px; padding: 0 12px; border-right: 1px solid #e2e8f0; }
        .edit-toolbar .toolbar-group:last-child { border-right: none; }
        .edit-toolbar label { font-size: 11px; font-weight: 600; color: #64748b; }
        .edit-toolbar input[type="range"] { width: 80px; }
        .edit-toolbar input[type="color"] { width: 30px; height: 26px; border: none; border-radius: 4px; cursor: pointer; }
        .edit-toolbar button { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; }
        .edit-toolbar .apply-btn { background: #10b981; color: white; }
        .edit-toolbar .close-btn { background: #ef4444; color: white; }
        
        @media (max-width: 1400px) { .photocards-container { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 1200px) { .card-design-1, .card-design-2, .card-design-3, .card-design-4 { width: 320px; height: 400px; } }
        @media (max-width: 768px) { 
            .photocards-container { grid-template-columns: 1fr; } 
            .card-design-1, .card-design-2, .card-design-3, .card-design-4 { width: 100%; max-width: 340px; height: 420px; } 
            .custom-section { grid-template-columns: 1fr; }
            .page-header, .section { padding: 20px; }
            .card4-controls { flex-direction: column; }
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="page-header">
        <h1><i class="fas fa-image"></i> Photo Card Generator</h1>
    </div>

    <!-- Custom Upload -->
    <div class="section">
        <h2><i class="fas fa-upload"></i> Custom Photo Card</h2>
        <div class="custom-section">
            <div class="form-group">
                <label>Upload Image</label>
                <input type="file" id="customImage" accept="image/*">
            </div>
            <div class="form-group">
                <label>News Title</label>
                <input type="text" id="customTitle" placeholder="সংবাদ শিরোনাম লিখুন...">
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" id="customCategory" placeholder="জাতীয়" value="জাতীয়">
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" id="customDate" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <button class="btn btn-primary" onclick="generateCustomCards()">
            <i class="fas fa-magic"></i> Generate Custom Cards
        </button>
    </div>

    <!-- Photo Cards -->
    <div class="section">
        <h2><i class="fas fa-th-large"></i> Generated Photo Cards</h2>
        <div class="photocards-container" id="photocardsContainer">
            <!-- Design 1 - Clean White & Blue -->
            <div class="photocard-wrapper">
                <div class="photocard-label">Design 1</div>
                <div class="card-design-1" id="card1">
                    
                    <img src="img/<?= $latest_news ? htmlspecialchars($latest_news['image_url']) : 'img/default.jpg' ?>" class="news-image" id="card1Image">
                  
                    <div class="title" id="card1Title"><?= $latest_news ? htmlspecialchars($latest_news['headline']) : 'সংবাদ শিরোনাম' ?></div>
                    <div class="footer">
                        <span style="margin-top: 8px;"><i class="fa-solid fa-link"></i> বিস্তারিত লিংকে</span>
                          <div class="logo-overlay"><?php if ($logoBase64): ?><img width="80px" src="<?= $logoBase64 ?>" alt="Logo"><?php elseif ($logoUrl): ?><img width="80px" src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo"><?php endif; ?></div>
                    </div>
                </div>
                <div class="card-controls">
                    <div class="control-group"><label>Image ←→</label><input type="range" min="-50" max="50" value="0" onchange="updateCardImage(1, this.value, 'x')"></div>
                    <div class="control-group"><label>Color</label><input type="color" value="#2563eb" onchange="updateCardHighlight(1, this.value)"></div>
                    <div class="control-group"><label>Words</label><input type="number" min="0" max="10" value="0" onchange="updateCardHighlightWords(1, this.value)"></div>
                </div>
                <button class="download-btn" onclick="downloadCard('card1', 'design1')"><i class="fas fa-download"></i> Download</button>
            </div>
            
            <!-- Design 2 - Red Gradient (Matching Reference) -->
            <div class="photocard-wrapper">
                <div class="photocard-label">Design 2</div>
                <div class="card-design-2" id="card2">
                    <div class="header">
                        <div class="logo-overlay"><?php if ($logoBase64): ?><img src="<?= $logoBase64 ?>" alt="Logo"><?php elseif ($logoUrl): ?><img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo"><?php endif; ?></div>
                        <div class="date-badge" id="card2Date"><?= $latest_news ? formatBanglaDate($latest_news['created_at']) : $defaultDate ?></div>
                    </div>
                    <div class="image-wrapper">
                        <img src="img/<?= $latest_news ? htmlspecialchars($latest_news['image_url']) : 'img/default.jpg' ?>" class="news-image" id="card2Image">
                    </div>
                    <div class="title-area">
                        <div class="title" id="card2Title"><?= $latest_news ? htmlspecialchars($latest_news['headline']) : 'সংবাদ শিরোনাম' ?></div>
                    </div>
                    <div class="bottom-bar">« নিউজ লিংক কমেন্টে »</div>
                </div>
                <div class="card-controls">
                    <div class="control-group"><label>Image ←→</label><input type="range" min="-50" max="50" value="0" onchange="updateCardImage(2, this.value, 'x')"></div>
                    <div class="control-group"><label>Color</label><input type="color" value="#FFD700" onchange="updateCardHighlight(2, this.value)"></div>
                    <div class="control-group"><label>Words</label><input type="number" min="0" max="10" value="0" onchange="updateCardHighlightWords(2, this.value)"></div>
                </div>
                <button class="download-btn" onclick="downloadCard('card2', 'design2')"><i class="fas fa-download"></i> Download</button>
            </div>
            
            <!-- Design 3 - Minimal Red Accent -->
            <div class="photocard-wrapper">
                <div class="photocard-label">Design 3</div>
                <div class="card-design-3" id="card3">
                    <div class="logo-top"><?php if ($logoBase64): ?><img src="<?= $logoBase64 ?>" alt="Logo"><?php elseif ($logoUrl): ?><img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo"><?php endif; ?></div>
                    <img src="img/<?= $latest_news ? htmlspecialchars($latest_news['image_url']) : 'img/default.jpg' ?>" class="news-image" id="card3Image">
                    <div class="content">
                        <div class="title" id="card3Title"><?= $latest_news ? htmlspecialchars($latest_news['headline']) : 'সংবাদ শিরোনাম' ?></div>
                        <div class="meta">
                            <span id="card3Date"><i class="far fa-calendar"></i> <?= $latest_news ? formatBanglaDate($latest_news['created_at']) : $defaultDate ?></span>
                            <span><i class="fa-solid fa-link"></i> বিস্তারিত কমেন্টে</span>
                        </div>
                    </div>
                </div>
                <div class="card-controls">
                    <div class="control-group"><label>Image ←→</label><input type="range" min="-50" max="50" value="0" onchange="updateCardImage(3, this.value, 'x')"></div>
                    <div class="control-group"><label>Color</label><input type="color" value="#dc2626" onchange="updateCardHighlight(3, this.value)"></div>
                    <div class="control-group"><label>Words</label><input type="number" min="0" max="10" value="0" onchange="updateCardHighlightWords(3, this.value)"></div>
                </div>
                <button class="download-btn" onclick="downloadCard('card3', 'design3')"><i class="fas fa-download"></i> Download</button>
            </div>
            
            <!-- Design 4 - Premium Minimal -->
            <div class="photocard-wrapper">
                <div class="photocard-label">Design 4</div>
                <div class="card-design-4" id="card4">
                    <div class="image-container">
                        <img src="img/<?= $latest_news ? htmlspecialchars($latest_news['image_url']) : 'img/default.jpg' ?>" class="news-image" id="card4Image">
                        <div class="logo-badge"><?php if ($logoBase64): ?><img src="<?= $logoBase64 ?>" alt="Logo"><?php elseif ($logoUrl): ?><img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo"><?php endif; ?><span><?= htmlspecialchars($portalName) ?></span></div>
                    </div>
                    <div class="content">
                        <div class="title" id="card4Title"><?= $latest_news ? htmlspecialchars($latest_news['headline']) : 'সংবাদ শিরোনাম' ?></div>
                        <div class="date-badge"><i class="far fa-calendar"></i> <span id="card4Date"><?= $latest_news ? formatBanglaDate($latest_news['created_at']) : $defaultDate ?></span></div>
                    </div>
                    <div class="accent-line"></div>
                </div>
                <div class="card-controls">
                    <div class="control-group"><label>Image ←→</label><input type="range" min="-50" max="50" value="0" onchange="updateCardImage(4, this.value, 'x')"></div>
                    <div class="control-group"><label>Color</label><input type="color" value="#0891b2" onchange="updateCardHighlight(4, this.value)"></div>
                    <div class="control-group"><label>Words</label><input type="number" min="0" max="10" value="0" onchange="updateCardHighlightWords(4, this.value)"></div>
                </div>
                <button class="download-btn" onclick="downloadCard('card4', 'design4')"><i class="fas fa-download"></i> Download</button>
            </div>
        </div>
    </div>

    <!-- News Table -->
    <div class="section">
        <h2><i class="fas fa-newspaper"></i> All News</h2>
        <div class="search-filters" style="display:flex;gap:15px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;">
                <label>From Date</label>
                <input type="date" id="dateFrom" style="padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;">
            </div>
            <div class="form-group" style="margin:0;">
                <label>To Date</label>
                <input type="date" id="dateTo" style="padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;">
            </div>
            <button class="btn btn-primary" onclick="filterByDate()" style="height:42px;">
                <i class="fas fa-search"></i> Search
            </button>
            <button class="btn" onclick="clearFilter()" style="height:42px;background:#718096;color:white;">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Headline</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="newsTableBody">
                    <tr><td colspan="5" style="text-align:center;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="pagination" id="pagination"></div>
    </div>

    <script>
        const portalName = <?= json_encode($portalName) ?>;
        const logoUrl = <?= json_encode($logoUrl) ?>;
        let allNews = [];
        let currentPage = 1;
        const perPage = 10;
        
        // Store original titles and highlight settings for all cards
        const cardOriginalTitles = {
            1: '<?= $latest_news ? addslashes($latest_news['headline']) : 'সংবাদ শিরোনাম' ?>',
            2: '<?= $latest_news ? addslashes($latest_news['headline']) : 'সংবাদ শিরোনাম' ?>',
            3: '<?= $latest_news ? addslashes($latest_news['headline']) : 'সংবাদ শিরোনাম' ?>',
            4: '<?= $latest_news ? addslashes($latest_news['headline']) : 'সংবাদ শিরোনাম' ?>'
        };
        const cardHighlightColors = { 1: '#2563eb', 2: '#fbbf24', 3: '#dc2626', 4: '#0891b2' };
        const cardHighlightWords = { 1: 0, 2: 0, 3: 0, 4: 0 };

        // Load news
        fetch('api.php?action=get_news')
            .then(r => r.json())
            .then(data => {
                allNews = Array.isArray(data) ? data : [];
                renderTable();
            });

        function renderTable() {
            const start = (currentPage - 1) * perPage;
            const pageData = allNews.slice(start, start + perPage);
            const tbody = document.getElementById('newsTableBody');
            
            if (!pageData.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No news found</td></tr>';
                return;
            }
            
            tbody.innerHTML = pageData.map(n => `
                <tr>
                    <td><img src="img/${n.image_url || 'img/default.jpg'}" class="news-thumb"></td>
                    <td class="headline-cell">${escapeHtml(n.headline)}</td>
                    <td>${escapeHtml(n.category_name || '-')}</td>
                    <td>${formatDate(n.created_at)}</td>
                    <td><button class="generate-btn" onclick='generateFromNews(${JSON.stringify(n).replace(/'/g, "&#39;")})'>Generate</button></td>
                </tr>
            `).join('');
            
            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(allNews.length / perPage);
            const container = document.getElementById('pagination');
            if (totalPages <= 1) { container.innerHTML = ''; return; }
            
            let startPage = Math.max(1, currentPage - 1);
            let endPage = Math.min(totalPages, startPage + 2);
            if (endPage - startPage < 2) startPage = Math.max(1, endPage - 2);
            
            let html = `<button class="nav-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&lt;&lt; Previous</button>`;
            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            }
            html += `<button class="nav-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Next &gt;&gt;</button>`;
            container.innerHTML = html;
        }

        function goToPage(page) { currentPage = page; renderTable(); }

        function generateFromNews(news) {
            const imgUrl = 'img/' + (news.image_url || 'default.jpg');
            updateCards(imgUrl, news.headline, news.category_name || 'জাতীয়', formatBanglaDate(news.created_at));
            document.getElementById('photocardsContainer').scrollIntoView({ behavior: 'smooth' });
        }

        function generateCustomCards() {
            const fileInput = document.getElementById('customImage');
            const title = document.getElementById('customTitle').value || 'সংবাদ শিরোনাম';
            const category = document.getElementById('customCategory').value || 'জাতীয়';
            const date = formatBanglaDate(document.getElementById('customDate').value);
            
            if (fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = e => updateCards(e.target.result, title, category, date);
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                updateCards(document.getElementById('card1Image').src, title, category, date);
            }
        }

        function updateCards(imgUrl, title, category, date) {
            // Update all cards with new content
            for (let i = 1; i <= 4; i++) {
                document.getElementById('card' + i + 'Image').src = imgUrl;
                document.getElementById('card' + i + 'Title').textContent = title;
                cardOriginalTitles[i] = title;
                if (document.getElementById('card' + i + 'Date')) {
                    document.getElementById('card' + i + 'Date').textContent = date;
                }
            }
            if (document.getElementById('card1Category')) {
                document.getElementById('card1Category').textContent = category;
            }
            if (document.getElementById('card3Date')) {
                document.getElementById('card3Date').textContent = date;
            }
        }

        function updateCardImage(cardNum, value, axis) {
            const img = document.getElementById('card' + cardNum + 'Image');
            if (axis === 'x') {
                img.style.objectPosition = (50 + parseInt(value)) + '% center';
            }
        }

        function updateCardHighlight(cardNum, color) {
            cardHighlightColors[cardNum] = color;
            applyHighlight(cardNum);
        }

        function updateCardHighlightWords(cardNum, words) {
            cardHighlightWords[cardNum] = parseInt(words) || 0;
            applyHighlight(cardNum);
        }

        function applyHighlight(cardNum) {
            const titleEl = document.getElementById('card' + cardNum + 'Title');
            const title = cardOriginalTitles[cardNum];
            const color = cardHighlightColors[cardNum];
            const wordCount = cardHighlightWords[cardNum];
            
            if (wordCount > 0 && title) {
                const words = title.split(' ');
                const half = Math.floor(words.length / 2);
                const start = Math.max(0, half - Math.floor(wordCount / 2));
                const end = Math.min(words.length, start + wordCount);
                
                let html = '';
                for (let i = 0; i < words.length; i++) {
                    if (i >= start && i < end) {
                        html += `<span class="highlight" style="color:${color}">${words[i]}</span> `;
                    } else {
                        html += words[i] + ' ';
                    }
                }
                titleEl.innerHTML = html.trim();
            } else {
                titleEl.textContent = title;
            }
        }

        async function downloadCard(cardId, name) {
            const card = document.getElementById(cardId);
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            btn.disabled = true;
            
            try {
                const clone = card.cloneNode(true);
                clone.style.position = 'absolute';
                clone.style.left = '-9999px';
                document.body.appendChild(clone);
                
                const images = clone.querySelectorAll('img');
                for (let img of images) {
                    if (!img.src.startsWith('data:')) {
                        try {
                            const response = await fetch('get_image_base64.php?url=' + encodeURIComponent(img.src));
                            const data = await response.json();
                            if (data.success) img.src = data.base64;
                        } catch(e) { console.log('Image load error:', e); }
                    }
                }
                
                await new Promise(r => setTimeout(r, 300));
                
                const canvas = await html2canvas(clone, { useCORS: true, allowTaint: true, scale: 2, logging: false, backgroundColor: null });
                document.body.removeChild(clone);
                
                const link = document.createElement('a');
                link.download = `photocard_${name}_${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            } catch(e) {
                console.error('Download error:', e);
                alert('Download failed. Please try again.');
            }
            
            btn.innerHTML = originalText;
            btn.disabled = false;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('en-GB');
        }

        function formatBanglaDate(dateStr) {
            const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            const banglaMonths = ['', 'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
            const d = new Date(dateStr);
            const day = String(d.getDate()).split('').map(c => banglaDigits[parseInt(c)]).join('');
            const month = banglaMonths[d.getMonth() + 1];
            const year = String(d.getFullYear()).split('').map(c => banglaDigits[parseInt(c)]).join('');
            return `${day} ${month}, ${year}`;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]));
        }

        let originalNews = [];
        
        function filterByDate() {
            const fromDate = document.getElementById('dateFrom').value;
            const toDate = document.getElementById('dateTo').value;
            if (!originalNews.length) originalNews = [...allNews];
            allNews = originalNews.filter(n => {
                const newsDate = n.created_at ? n.created_at.split(' ')[0] : '';
                if (fromDate && newsDate < fromDate) return false;
                if (toDate && newsDate > toDate) return false;
                return true;
            });
            currentPage = 1;
            renderTable();
        }
        
        function clearFilter() {
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            if (originalNews.length) { allNews = [...originalNews]; originalNews = []; }
            currentPage = 1;
            renderTable();
        }

        // ============ CLICK-TO-EDIT FUNCTIONALITY ============
        let activeElement = null;
        let activeType = null; // 'image' or 'text'
        let activeCardNum = null;

        // Initialize click handlers for all images and titles
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers to all card images
            for (let i = 1; i <= 4; i++) {
                const img = document.getElementById('card' + i + 'Image');
                const title = document.getElementById('card' + i + 'Title');
                
                if (img) {
                    img.classList.add('editable-image');
                    img.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectElement(this, 'image', i);
                    });
                }
                
                if (title) {
                    title.classList.add('editable-text');
                    title.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectElement(this, 'text', i);
                    });
                }
            }
            
            // Close toolbar when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.edit-toolbar') && !e.target.closest('.editable-image') && !e.target.closest('.editable-text')) {
                    closeToolbar();
                }
            });
        });

        function selectElement(el, type, cardNum) {
            // Remove previous selection
            document.querySelectorAll('.editing').forEach(e => e.classList.remove('editing'));
            
            // Set new selection
            el.classList.add('editing');
            activeElement = el;
            activeType = type;
            activeCardNum = cardNum;
            
            // Show appropriate toolbar
            showToolbar(type, cardNum);
        }

        function showToolbar(type, cardNum) {
            const toolbar = document.getElementById('editToolbar');
            const imageControls = document.getElementById('imageControls');
            const textControls = document.getElementById('textControls');
            const colorControls = document.getElementById('colorControls');
            
            toolbar.classList.add('active');
            
            if (type === 'image') {
                imageControls.style.display = 'flex';
                textControls.style.display = 'none';
                colorControls.style.display = 'none';
            } else {
                imageControls.style.display = 'none';
                textControls.style.display = 'flex';
                colorControls.style.display = 'flex';
            }
        }

        function closeToolbar() {
            document.getElementById('editToolbar').classList.remove('active');
            document.querySelectorAll('.editing').forEach(e => e.classList.remove('editing'));
            activeElement = null;
            activeType = null;
            activeCardNum = null;
        }

        // Image controls
        function adjustImageZoom(value) {
            if (activeElement && activeType === 'image') {
                activeElement.style.transform = `scale(${value / 100})`;
            }
        }

        function adjustImageX(value) {
            if (activeElement && activeType === 'image') {
                activeElement.style.objectPosition = `${50 + parseInt(value)}% center`;
            }
        }

        // Text controls
        function adjustTextSize(value) {
            if (activeElement && activeType === 'text') {
                activeElement.style.fontSize = value + 'px';
            }
        }

        function adjustTextPosition(value) {
            if (activeElement && activeType === 'text') {
                activeElement.style.transform = `translateY(${value}px)`;
            }
        }

        // Apply color to selected text
        function applyColorToSelection() {
            const selection = window.getSelection();
            const color = document.getElementById('selectionColor').value;
            
            if (selection.rangeCount > 0 && !selection.isCollapsed) {
                const range = selection.getRangeAt(0);
                const selectedText = range.toString();
                
                if (selectedText && activeElement && activeElement.contains(range.commonAncestorContainer)) {
                    const span = document.createElement('span');
                    span.className = 'highlight';
                    span.style.color = color;
                    
                    try {
                        range.surroundContents(span);
                        selection.removeAllRanges();
                    } catch(e) {
                        // If selection spans multiple elements, use alternative method
                        const content = range.extractContents();
                        span.appendChild(content);
                        range.insertNode(span);
                        selection.removeAllRanges();
                    }
                }
            } else {
                alert('Please select some text first, then click Apply Color');
            }
        }
    </script>

    <!-- Floating Edit Toolbar -->
    <div class="edit-toolbar" id="editToolbar">
        <div class="toolbar-group" id="imageControls" style="display:none;">
            <label>Zoom</label>
            <input type="range" min="100" max="200" value="100" oninput="adjustImageZoom(this.value)">
            <label>←→</label>
            <input type="range" min="-50" max="50" value="0" oninput="adjustImageX(this.value)">
        </div>
        <div class="toolbar-group" id="textControls" style="display:none;">
            <label>Size</label>
            <input type="range" min="12" max="30" value="18" oninput="adjustTextSize(this.value)">
            <label>↕</label>
            <input type="range" min="-20" max="20" value="0" oninput="adjustTextPosition(this.value)">
        </div>
        <div class="toolbar-group" id="colorControls" style="display:none;">
            <label>Color</label>
            <input type="color" id="selectionColor" value="#FFD700">
            <button class="apply-btn" onclick="applyColorToSelection()">Apply</button>
        </div>
        <div class="toolbar-group">
            <button class="close-btn" onclick="closeToolbar()"><i class="fas fa-times"></i></button>
        </div>
    </div>
</body>
</html>