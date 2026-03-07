<?php
include 'connection.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $success_message = 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।';
    } else {
        $error_message = 'অনুগ্রহ করে সকল প্রয়োজনীয় তথ্য পূরণ করুন।';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <title>যোগাযোগ - Contact Us | HINDUS NEWS</title>
    <meta charset="UTF-8">
    <meta name="description" content="হিন্দুস নিউজের সাথে যোগাযোগ করুন - Contact Hindus News. আমাদের সাথে যোগাযোগের জন্য ইমেইল, ফোন এবং যোগাযোগ ফর্ম ব্যবহার করুন।">
    <meta name="keywords" content="Contact Us, যোগাযোগ, Hindus News, হিন্দুস নিউজ, যোগাযোগ ফর্ম">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="logo.png" />
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="canonical" href="https://hindus-news.com/contact.php">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Noto Sans Bengali', Arial, sans-serif; background: #f5f5f5; color: #333; line-height: 1.8; }
        .header-bar { background: linear-gradient(135deg, #ff0000, #cc0000); padding: 15px 0; text-align: center; }
        .header-bar a { color: white; text-decoration: none; font-size: 24px; font-weight: bold; }
        .header-bar a:hover { color: #fff; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .policy-card { background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); padding: 40px; }
        .policy-title { font-size: 28px; color: #ff0000; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        h3 { color: #333; font-size: 20px; margin: 25px 0 15px; display: flex; align-items: center; gap: 8px; }
        h3 i { color: #ff0000; }
        p { margin-bottom: 15px; }
        a { color: #ff0000; }
        .back-link { display: inline-block; margin: 20px 0; padding: 10px 25px; background: #ff0000; color: white; text-decoration: none; border-radius: 5px; }
        .back-link:hover { background: #cc0000; color: white; }
        .footer-simple { text-align: center; padding: 20px; color: #666; font-size: 14px; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
        @media (max-width: 768px) { .contact-grid { grid-template-columns: 1fr; } }
        .contact-info { background: #f8f9fa; padding: 25px; border-radius: 10px; }
        .contact-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0; }
        .contact-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .contact-icon { width: 50px; height: 50px; background: #ff0000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .contact-text h4 { margin: 0 0 5px; color: #333; font-size: 16px; }
        .contact-text p { margin: 0; color: #666; }
        .contact-text a { color: #ff0000; text-decoration: none; }
        .contact-text a:hover { text-decoration: underline; }
        .contact-form { background: #fff; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #ff0000; }
        .form-group textarea { min-height: 150px; resize: vertical; }
        .submit-btn { background: #ff0000; color: white; border: none; padding: 15px 30px; font-size: 16px; border-radius: 8px; cursor: pointer; width: 100%; transition: background 0.3s; }
        .submit-btn:hover { background: #cc0000; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .social-links { display: flex; gap: 15px; margin-top: 20px; }
        .social-link { width: 45px; height: 45px; background: #ff0000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; transition: transform 0.3s, background 0.3s; }
        .social-link:hover { transform: scale(1.1); background: #cc0000; color: white; }
        .social-link.facebook { background: #1877f2; }
        .social-link.youtube { background: #ff0000; }
        .social-link.twitter { background: #1da1f2; }
        .map-container { margin-top: 30px; border-radius: 10px; overflow: hidden; }
    </style>
</head>
<body>
    <div class="header-bar">
        <a href="index.php"><i class="fas fa-home"></i> HINDUS NEWS</a>
    </div>
    
    <div class="container">
        <div class="policy-card">
            <h1 class="policy-title"><i class="fas fa-envelope"></i> যোগাযোগ করুন (Contact Us)</h1>
            <p>আমাদের সাথে যোগাযোগ করতে নিচের যেকোনো মাধ্যম ব্যবহার করতে পারেন। আমরা সর্বদা আপনাদের সেবায় নিয়োজিত।</p>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <h3 style="margin-top: 0;"><i class="fas fa-address-card"></i> যোগাযোগের তথ্য</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-text">
                            <h4>ইমেইল</h4>
                            <p><a href="mailto:hindusnewsbd@gmail.com">hindusnewsbd@gmail.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <div class="contact-text">
                            <h4>ফোন</h4>
                            <p><a href="tel:+8801890890920">+880 1890890920</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-globe"></i></div>
                        <div class="contact-text">
                            <h4>ওয়েবসাইট</h4>
                            <p><a href="https://hindus-news.com" target="_blank">www.hindus-news.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-clock"></i></div>
                        <div class="contact-text">
                            <h4>কার্যসময়</h4>
                            <p>২৪ ঘণ্টা, সপ্তাহে ৭ দিন</p>
                        </div>
                    </div>
                    
                    <h4 style="margin-top: 20px; margin-bottom: 10px;">সোশ্যাল মিডিয়া</h4>
                    <div class="social-links">
                        <a href="https://www.facebook.com/hindusnewsbd" target="_blank" class="social-link facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.youtube.com/@hindusnewsbd" target="_blank" class="social-link youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3 style="margin-top: 0;"><i class="fas fa-paper-plane"></i> বার্তা পাঠান</h3>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> আপনার নাম *</label>
                            <input type="text" id="name" name="name" placeholder="আপনার পূর্ণ নাম লিখুন" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> ইমেইল ঠিকানা *</label>
                            <input type="email" id="email" name="email" placeholder="example@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject"><i class="fas fa-tag"></i> বিষয়</label>
                            <select id="subject" name="subject">
                                <option value="general">সাধারণ জিজ্ঞাসা</option>
                                <option value="news">সংবাদ সম্পর্কিত</option>
                                <option value="advertising">বিজ্ঞাপন</option>
                                <option value="complaint">অভিযোগ</option>
                                <option value="suggestion">পরামর্শ</option>
                                <option value="other">অন্যান্য</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment"></i> আপনার বার্তা *</label>
                            <textarea id="message" name="message" placeholder="আপনার বার্তা এখানে লিখুন..." required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> বার্তা পাঠান</button>
                    </form>
                </div>
            </div>
            
            <h3><i class="fas fa-info-circle"></i> গুরুত্বপূর্ণ তথ্য</h3>
            <ul>
                <li><strong>সংবাদ পাঠাতে:</strong> hindusnewsbd@gmail.com এ ইমেইল করুন</li>
                <li><strong>বিজ্ঞাপনের জন্য:</strong> বিজ্ঞাপন সংক্রান্ত তথ্যের জন্য ইমেইল করুন</li>
                <li><strong>অভিযোগ:</strong> কোনো সংবাদ সম্পর্কে অভিযোগ থাকলে জানান</li>
            </ul>
            
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> হোম পেজে ফিরে যান</a>
        </div>
    </div>
    
    <div class="footer-simple">
        <p>© <?php echo date('Y'); ?> HINDUS NEWS. All Rights Reserved.</p>
    </div>
</body>
</html>
