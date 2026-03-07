<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <title>আমাদের সম্পর্কে - About Us | HINDUS NEWS</title>
    <meta charset="UTF-8">
    <meta name="description" content="হিন্দুস নিউজ সম্পর্কে জানুন - About Hindus News. আমরা একটি অরাজনৈতিক এবং মানবাধিকারের ভিত্তিতে পরিচালিত অনলাইন নিউজ পোর্টাল।">
    <meta name="keywords" content="About Us, আমাদের সম্পর্কে, Hindus News, হিন্দুস নিউজ, বাংলা নিউজ">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="logo.png" />
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="canonical" href="https://hindus-news.com/about-us.php">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Noto Sans Bengali', Arial, sans-serif; background: #f5f5f5; color: #333; line-height: 1.8; }
        .header-bar { background: linear-gradient(135deg, #ff0000, #cc0000); padding: 15px 0; text-align: center; }
        .header-bar a { color: white; text-decoration: none; font-size: 24px; font-weight: bold; }
        .header-bar a:hover { color: #fff; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .policy-card { background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); padding: 40px; }
        .policy-title { font-size: 28px; color: #ff0000; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .tagline { font-size: 20px; color: #ff0000; font-style: italic; text-align: center; margin-bottom: 20px; }
        .welcome-text { font-size: 16px; color: #666; text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0; }
        h3 { color: #333; font-size: 20px; margin: 25px 0 15px; display: flex; align-items: center; gap: 8px; }
        h3 i { color: #ff0000; }
        p { margin-bottom: 15px; text-align: justify; }
        ul { margin: 15px 0 15px 25px; }
        li { margin-bottom: 8px; }
        a { color: #ff0000; }
        .back-link { display: inline-block; margin: 20px 0; padding: 10px 25px; background: #ff0000; color: white; text-decoration: none; border-radius: 5px; }
        .back-link:hover { background: #cc0000; color: white; }
        .footer-simple { text-align: center; padding: 20px; color: #666; font-size: 14px; }
        .highlight-box { background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff0000; margin: 15px 0; font-style: italic; text-align: center; }
        .contact-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .contact-box p { margin-bottom: 10px; }
        .contact-box i { color: #ff0000; width: 25px; }
        .logo-center { text-align: center; margin-bottom: 20px; }
        .logo-center img { max-width: 200px; }
        .team-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .team-member { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; }
        .team-member:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    </style>
</head>
<body>
    <div class="header-bar">
        <a href="index.php"><i class="fas fa-home"></i> HINDUS NEWS</a>
    </div>
    
    <div class="container">
        <div class="policy-card">
            <div class="logo-center">
                <img src="https://www.hindus.news/_ipx/_/graphics/logolightmode.png" alt="Hindus News Logo">
            </div>
            
            <h1 class="policy-title" style="justify-content: center;"><i class="fas fa-info-circle"></i> আমাদের সম্পর্কে (About Us)</h1>
            <p class="tagline">"Voice of Hinduism"</p>
            <p class="welcome-text">স্বাগতম হিন্দুস নিউজ (Hindus News)-এ।</p>
            
            <h3><i class="fas fa-users"></i> আমাদের পরিচয়</h3>
            <p>"হিন্দুস নিউজ" একটি সম্পূর্ণ অরাজনৈতিক এবং মানবাধিকারের ভিত্তিতে পরিচালিত অনলাইন নিউজ পোর্টাল। আমরা কোনো বিশেষ রাজনৈতিক দল বা মতাদর্শের মুখপাত্র নই। আমাদের অবস্থান সর্বদা সত্য, ন্যায় এবং মানবাধিকারের পক্ষে।</p>
            
            <p>আমাদের প্রতিষ্ঠার মূল উদ্দেশ্য হলো বাংলাদেশের হিন্দু সম্প্রদায়সহ সকল সংখ্যালঘুদের কণ্ঠস্বর হয়ে ওঠা এবং তাদের ন্যায্য অধিকার রক্ষায় সোচ্চার থাকা।</p>
            
            <h3><i class="fas fa-bullseye"></i> আমাদের লক্ষ্য ও উদ্দেশ্য</h3>
            <p>আমাদের মূল লক্ষ্য অত্যন্ত সুস্পষ্ট—বাংলাদেশের সংখ্যালঘু হিন্দু সম্প্রদায়ের বাস্তব সমস্যা, তাদের ওপর হওয়া বিভিন্ন নির্যাতন, বৈষম্য এবং তাদের ন্যায্য অধিকারের বিষয়গুলো সাহসিকতার সাথে তুলে ধরা।</p>
            
            <p>আমরা বিশ্বাস করি, সমাজের পিছিয়ে পড়া বা নিপীড়িত জনগোষ্ঠীর কণ্ঠস্বর হয়ে ওঠাই সংবাদমাধ্যমের অন্যতম প্রধান দায়িত্ব। আমরা সেই দায়িত্ব পালনে প্রতিশ্রুতিবদ্ধ। আমরা কেবল সংবাদ পরিবেশন করি না, বরং সংবাদের পেছনের সত্য ঘটনাটি বস্তুনিষ্ঠভাবে পাঠকের সামনে উপস্থাপন করার চেষ্টা করি।</p>
            
            <h3><i class="fas fa-tasks"></i> আমরা যা করি</h3>
            <ul>
                <li><strong>সংবাদ প্রচার:</strong> জাতীয় ও আন্তর্জাতিক সংবাদ বস্তুনিষ্ঠভাবে প্রকাশ</li>
                <li><strong>সংখ্যালঘু সমস্যা:</strong> সংখ্যালঘুদের উপর নির্যাতন ও বৈষম্যের খবর তুলে ধরা</li>
                <li><strong>ধর্মীয় সংবাদ:</strong> হিন্দু ধর্ম সম্পর্কিত খবর ও তথ্য প্রকাশ</li>
                <li><strong>সাংস্কৃতিক কার্যক্রম:</strong> হিন্দু সম্প্রদায়ের সাংস্কৃতিক অনুষ্ঠান ও উৎসবের কভারেজ</li>
                <li><strong>মানবাধিকার:</strong> মানবাধিকার লঙ্ঘনের খবর প্রকাশ</li>
            </ul>
            
            <h3><i class="fas fa-handshake"></i> আমাদের অঙ্গীকার</h3>
            <p>আমরা সংবাদ পরিবেশনে নিরপেক্ষতা এবং সর্বোচ্চ পেশাদারিত্ব বজায় রাখতে বদ্ধপরিকর। আবেগের ঊর্ধ্বে উঠে সঠিক তথ্য ও প্রমাণভিত্তিক সংবাদ প্রচারই আমাদের নীতি। সাম্প্রদায়িক সম্প্রীতি বজায় রাখা এবং সংখ্যালঘুদের অধিকার রক্ষায় আমরা সর্বদা সোচ্চার থাকব।</p>
            
            <div class="highlight-box">
                সত্য ও ন্যায়ের পথে আমাদের এই যাত্রায় আপনারা আমাদের সঙ্গী হবেন, এটাই আমাদের প্রত্যাশা।
            </div>
            
            <h3><i class="fas fa-user-tie"></i> সম্পাদকীয় দল</h3>
            <div class="team-section">
                <div class="team-member">
                    <p><strong>প্রকাশক ও সম্পাদক:</strong> শুভ চন্দ্র দে</p>
                </div>
            </div>
            
            <h3><i class="fas fa-address-book"></i> যোগাযোগ</h3>
            <div class="contact-box">
                <p><i class="fas fa-globe"></i> <strong>ওয়েবসাইট:</strong> <a href="https://hindus-news.com" target="_blank">www.hindus-news.com</a></p>
                <p><i class="fas fa-envelope"></i> <strong>ইমেইল:</strong> <a href="mailto:hindusnewsbd@gmail.com">hindusnewsbd@gmail.com</a></p>
                <p><i class="fas fa-phone"></i> <strong>ফোন:</strong> <a href="tel:+8801890890920">+880 1890890920</a></p>
            </div>
            
            <h3><i class="fab fa-facebook"></i> সোশ্যাল মিডিয়া</h3>
            <p>আমাদের সাথে যুক্ত থাকুন সোশ্যাল মিডিয়ায়:</p>
            <ul>
                <li><a href="https://www.facebook.com/hindusnewsbd" target="_blank"><i class="fab fa-facebook"></i> Facebook Page</a></li>
                <li><a href="https://www.youtube.com/@hindusnewsbd" target="_blank"><i class="fab fa-youtube"></i> YouTube Channel</a></li>
            </ul>
            
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> হোম পেজে ফিরে যান</a>
        </div>
    </div>
    
    <div class="footer-simple">
        <p>© <?php echo date('Y'); ?> HINDUS NEWS. All Rights Reserved.</p>
    </div>
</body>
</html>
