<!DOCTYPE html>
<html lang="bn">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Portal Software - আপনার নিজস্ব নিউজ পোর্টাল তৈরি করুন</title>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Hind Siliguri', sans-serif;
      background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
      min-height: 100vh;
      color: white;
      overflow-x: hidden;
    }

    /* Animated Background */
    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      overflow: hidden;
    }

    .bg-animation .stars {
      position: absolute;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="white" opacity="0.3"/></svg>') repeat;
      background-size: 100px 100px;
      animation: twinkle 4s infinite;
    }

    @keyframes twinkle {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 0.8; }
    }

    .floating-shapes {
      position: absolute;
      width: 100%;
      height: 100%;
    }

    .shape {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(59, 130, 246, 0.2));
      filter: blur(40px);
      animation: float 8s infinite ease-in-out;
    }

    .shape:nth-child(1) { width: 300px; height: 300px; top: 10%; left: 10%; animation-delay: 0s; }
    .shape:nth-child(2) { width: 200px; height: 200px; top: 60%; right: 10%; animation-delay: 2s; }
    .shape:nth-child(3) { width: 250px; height: 250px; bottom: 10%; left: 30%; animation-delay: 4s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) scale(1); }
      50% { transform: translateY(-30px) scale(1.1); }
    }

    /* Hero Section */
    .hero-section {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
      z-index: 1;
    }

    .hero-container {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      gap: 80px;
    }

    .hero-content {
      animation: slideInLeft 1s ease-out;
    }

    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .hero-badge {
      display: inline-block;
      padding: 8px 20px;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(59, 130, 246, 0.3));
      border: 1px solid rgba(139, 92, 246, 0.5);
      border-radius: 50px;
      font-size: 14px;
      margin-bottom: 25px;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); }
      50% { box-shadow: 0 0 20px 5px rgba(139, 92, 246, 0.2); }
    }

    .hero-content h1 {
      font-size: 52px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 25px;
      background: linear-gradient(135deg, #fff 0%, #a78bfa 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-content p {
      font-size: 18px;
      line-height: 1.8;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 40px;
    }

    .btn-group {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .btn-primary {
      padding: 16px 40px;
      background: linear-gradient(135deg, #8B5CF6, #6366F1);
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(139, 92, 246, 0.5);
    }

    .btn-secondary {
      padding: 16px 40px;
      background: transparent;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.5);
    }

    /* Image Slider */
    .slider-container {
      position: relative;
      width: 100%;
      max-width: 600px;
      height: 400px;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
      animation: slideInRight 1s ease-out;
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .slider {
      display: flex;
      width: 400%;
      height: 100%;
      animation: slide 16s infinite ease-in-out;
    }

    @keyframes slide {
      0%, 20% { transform: translateX(0); }
      25%, 45% { transform: translateX(-25%); }
      50%, 70% { transform: translateX(-50%); }
      75%, 95% { transform: translateX(-75%); }
      100% { transform: translateX(0); }
    }

    .slide {
      width: 25%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .slide-content {
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      text-align: center;
    }

    .slide:nth-child(1) .slide-content {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .slide:nth-child(2) .slide-content {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .slide:nth-child(3) .slide-content {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .slide:nth-child(4) .slide-content {
      background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .slide-icon {
      font-size: 60px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .slide h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .slide p {
      font-size: 14px;
      opacity: 0.9;
    }

    .slider-dots {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      animation: dotPulse 16s infinite;
    }

    .dot:nth-child(1) { animation-delay: 0s; }
    .dot:nth-child(2) { animation-delay: 4s; }
    .dot:nth-child(3) { animation-delay: 8s; }
    .dot:nth-child(4) { animation-delay: 12s; }

    @keyframes dotPulse {
      0%, 20% { background: white; transform: scale(1.2); }
      25%, 100% { background: rgba(255, 255, 255, 0.5); transform: scale(1); }
    }

    /* Stats Section */
    .stats-section {
      padding: 80px 40px;
      position: relative;
      z-index: 1;
    }

    .stats-container {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }

    .stat-card {
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 30px;
      text-align: center;
      backdrop-filter: blur(10px);
      transition: all 0.4s ease;
    }

    .stat-card:hover {
      transform: translateY(-10px);
      border-color: rgba(139, 92, 246, 0.5);
      box-shadow: 0 20px 50px rgba(139, 92, 246, 0.3);
    }

    .stat-number {
      font-size: 48px;
      font-weight: 700;
      background: linear-gradient(135deg, #8B5CF6, #06b6d4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
    }

    .stat-label {
      font-size: 16px;
      color: rgba(255, 255, 255, 0.7);
    }

    /* Features Section */
    .features-section {
      padding: 100px 40px;
      position: relative;
      z-index: 1;
    }

    .section-header {
      text-align: center;
      margin-bottom: 80px;
    }

    .section-header h2 {
      font-size: 42px;
      font-weight: 700;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #fff 0%, #a78bfa 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .section-header p {
      font-size: 18px;
      color: rgba(255, 255, 255, 0.7);
      max-width: 600px;
      margin: 0 auto;
    }

    .features-grid {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .feature-card {
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 24px;
      padding: 40px 30px;
      position: relative;
      overflow: hidden;
      transition: all 0.5s ease;
    }

    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), transparent);
      opacity: 0;
      transition: opacity 0.5s ease;
    }

    .feature-card:hover {
      transform: translateY(-10px) scale(1.02);
      border-color: rgba(139, 92, 246, 0.5);
      box-shadow: 0 30px 60px rgba(139, 92, 246, 0.3);
    }

    .feature-card:hover::before {
      opacity: 1;
    }

    .feature-icon-wrapper {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 25px;
      position: relative;
      z-index: 1;
    }

    .feature-card:nth-child(1) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-card:nth-child(2) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-card:nth-child(3) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-card:nth-child(4) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-card:nth-child(5) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-card:nth-child(6) .feature-icon-wrapper {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .feature-icon-wrapper i {
      font-size: 32px;
      color: white;
    }

    .feature-card h3 {
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 15px;
      position: relative;
      z-index: 1;
    }

    .feature-card p {
      font-size: 15px;
      line-height: 1.7;
      color: rgba(255, 255, 255, 0.75);
      position: relative;
      z-index: 1;
    }

    /* How It Works Section */
    .how-it-works {
      padding: 100px 40px;
      position: relative;
      z-index: 1;
    }

    .steps-container {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 40px;
    }

    .step {
      display: flex;
      align-items: center;
      gap: 40px;
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 24px;
      padding: 40px;
      transition: all 0.4s ease;
    }

    .step:hover {
      transform: translateX(20px);
      border-color: rgba(139, 92, 246, 0.5);
    }

    .step:nth-child(even) {
      flex-direction: row-reverse;
    }

    .step:nth-child(even):hover {
      transform: translateX(-20px);
    }

    .step-number {
      width: 80px;
      height: 80px;
      min-width: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, #8B5CF6, #6366F1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      font-weight: 700;
      box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
    }

    .step-content h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .step-content p {
      font-size: 16px;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.7;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 100px 40px;
      position: relative;
      z-index: 1;
    }

    .testimonials-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .testimonial-card {
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03));
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 24px;
      padding: 35px;
      position: relative;
      transition: all 0.4s ease;
    }

    .testimonial-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .testimonial-card::before {
      content: '"';
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 80px;
      font-family: serif;
      color: rgba(139, 92, 246, 0.3);
      line-height: 1;
    }

    .testimonial-text {
      font-size: 15px;
      line-height: 1.8;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 25px;
    }

    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .author-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, #8B5CF6, #06b6d4);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: 600;
    }

    .author-info h4 {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 3px;
    }

    .author-info span {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.6);
    }

    /* CTA Section */
    .cta-section {
      padding: 100px 40px;
      position: relative;
      z-index: 1;
    }

    .cta-card {
      max-width: 900px;
      margin: 0 auto;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(59, 130, 246, 0.3));
      border: 1px solid rgba(139, 92, 246, 0.5);
      border-radius: 32px;
      padding: 60px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .cta-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
      animation: rotate 10s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .cta-card h2 {
      font-size: 36px;
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }

    .cta-card p {
      font-size: 18px;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 35px;
      position: relative;
      z-index: 1;
    }

    .cta-card .btn-group {
      justify-content: center;
      position: relative;
      z-index: 1;
    }

    /* Footer */
    .footer {
      padding: 40px 20px;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
      z-index: 1;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 20px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: #8B5CF6;
    }

    .footer p {
      color: rgba(255, 255, 255, 0.5);
      font-size: 14px;
    }

    .footer a {
      color: #8B5CF6;
      text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 50px;
      }

      .hero-content h1 {
        font-size: 38px;
      }

      .btn-group {
        justify-content: center;
      }

      .slider-container {
        margin: 0 auto;
      }

      .stats-container {
        grid-template-columns: repeat(2, 1fr);
      }

      .features-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .testimonials-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .hero-section {
        padding: 60px 20px;
      }

      .hero-content h1 {
        font-size: 28px;
      }

      .slider-container {
        height: 300px;
      }

      .stats-container {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
      }

      .stat-number {
        font-size: 36px;
      }

      .features-grid {
        grid-template-columns: 1fr;
      }

      .step {
        flex-direction: column !important;
        text-align: center;
      }

      .step:hover {
        transform: translateY(-10px) !important;
      }

      .cta-card {
        padding: 40px 20px;
      }

      .cta-card h2 {
        font-size: 26px;
      }
    }

    @media (max-width: 480px) {
      .btn-group {
        flex-direction: column;
        align-items: center;
      }

      .btn-primary, .btn-secondary {
        width: 100%;
        max-width: 280px;
        text-align: center;
      }

      .footer-links {
        flex-direction: column;
        gap: 15px;
      }
    }
  </style>
</head>

<body>
  <!-- Animated Background -->
  <div class="bg-animation">
    <div class="stars"></div>
    <div class="floating-shapes">
      <div class="shape"></div>
      <div class="shape"></div>
      <div class="shape"></div>
    </div>
  </div>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-container">
      <div class="hero-content">
        <div class="hero-badge">
          <i class="fas fa-rocket"></i>&nbsp; বাংলাদেশের সেরা নিউজ পোর্টাল সফটওয়্যার
        </div>
        <h1>আপনার নিজস্ব নিউজ পোর্টাল তৈরি করুন মাত্র মাত্র ২৪ ঘন্টায়!</h1>
        <p>আধুনিক ও শক্তিশালী নিউজ পোর্টাল সফটওয়্যার দিয়ে আজই আপনার অনলাইন সংবাদপত্র চালু করুন। 
           সম্পূর্ণ বাংলা ইন্টারফেস, মোবাইল-ফ্রেন্ডলি ডিজাইন এবং সহজ ব্যবস্থাপনা।</p>
        <div class="btn-group">
          <a href="register.php" class="btn-primary">
            <i class="fas fa-play"></i>&nbsp; এখনই কিনুন
          </a>
          <a href="Admin/index.php" class="btn-secondary">
            <i class="fas fa-sign-in-alt"></i>&nbsp; অ্যাডমিন লগইন
          </a>
        </div>
      </div>
      <div class="slider-container">
        <div class="slider">
          <div class="slide">
            <img src="img/template.jpeg" width="400px"   alt="Template Preview">
          </div>
          <div class="slide">
            <img src="img/template.jpeg" width="400px"   alt="Template Preview">
          </div>
          <div class="slide">
           <img src="img/template.jpeg" width="400px"   alt="Template Preview">
          </div>
          <div class="slide">
            <img src="img/template.jpeg" width="400px"   alt="Template Preview">
          </div>
        </div>
        <div class="slider-dots">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats-section">
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number">500+</div>
        <div class="stat-label">সক্রিয় পোর্টাল</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">10K+</div>
        <div class="stat-label">দৈনিক ভিজিটর</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">1000+</div>
        <div class="stat-label">রিপোর্টার</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">99%</div>
        <div class="stat-label">সন্তুষ্ট গ্রাহক</div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section">
    <div class="section-header">
      <h2>শক্তিশালী ফিচার সমূহ</h2>
      <p>আধুনিক প্রযুক্তি দিয়ে তৈরি সকল প্রয়োজনীয় ফিচার</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-newspaper"></i>
        </div>
        <h3>আনলিমিটেড নিউজ</h3>
        <p>সীমাহীন সংবাদ প্রকাশ করুন। ছবি, ভিডিও ও টেক্সট সহ বিভিন্ন ফরম্যাটে।</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-user-tie"></i>
        </div>
        <h3>রিপোর্টার ড্যাশবোর্ড</h3>
        <p>প্রতিটি রিপোর্টারের জন্য আলাদা প্যানেল। নিউজ সাবমিট ও ট্র্যাকিং।</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-video"></i>
        </div>
        <h3>ভিডিও ও পডকাস্ট</h3>
        <p>YouTube এম্বেড, পডকাস্ট ও মাল্টিমিডিয়া কন্টেন্ট সাপোর্ট।</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-wallet"></i>
        </div>
        <h3>আয় ব্যবস্থাপনা</h3>
        <p>রিপোর্টারদের পার নিউজ আয় ট্র্যাক করুন ও পেমেন্ট রিপোর্ট দেখুন।</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-palette"></i>
        </div>
        <h3>কাস্টম ব্র্যান্ডিং</h3>
        <p>আপনার লোগো, রং ও ব্র্যান্ড অনুযায়ী পোর্টাল কাস্টমাইজ করুন।</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrapper">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3>নিরাপদ ও দ্রুত</h3>
        <p>আধুনিক সিকিউরিটি ও অপ্টিমাইজড পারফরম্যান্স নিশ্চিত।</p>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-it-works">
    <div class="section-header">
      <h2>কিভাবে কাজ করে?</h2>
      <p>মাত্র ৩টি সহজ ধাপে আপনার পোর্টাল চালু করুন</p>
    </div>
    <div class="steps-container">
      <div class="step">
        <div class="step-number">১</div>
        <div class="step-content">
          <h3>রেজিস্ট্রেশন করুন</h3>
          <p>আপনার মৌলিক তথ্য দিয়ে ফ্রি অ্যাকাউন্ট তৈরি করুন। মাত্র ২ মিনিটে সম্পূর্ণ প্রক্রিয়া শেষ।</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">২</div>
        <div class="step-content">
          <h3>পোর্টাল সেটআপ করুন</h3>
          <p>আপনার পোর্টালের নাম, লোগো ও ক্যাটেগরি সেট করুন। সহজ ড্যাশবোর্ড থেকে সব ম্যানেজ করুন।</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">৩</div>
        <div class="step-content">
          <h3>সংবাদ প্রকাশ শুরু করুন</h3>
          <p>রিপোর্টার যোগ করুন ও সংবাদ প্রকাশ শুরু করুন। আপনার পোর্টাল এখন লাইভ!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials-section">
    <div class="section-header">
      <h2>গ্রাহকদের মতামত</h2>
      <p>যারা ইতিমধ্যে ব্যবহার করছেন তাদের অভিজ্ঞতা</p>
    </div>
    <div class="testimonials-grid">
      
      <div class="testimonial-card">
        <p class="testimonial-text">রিপোর্টার ম্যানেজমেন্ট সিস্টেম দারুণ। ২০+ রিপোর্টার সহজেই ম্যানেজ করতে পারছি। মোবাইল ফ্রেন্ডলি ডিজাইন এবং দ্রুত লোডিং স্পিড। আমাদের ভিজিটর সংখ্যা বেড়েছে ৩ গুণ!</p>
        <div class="testimonial-author">
          <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" width="60px" alt="Author Avatar" class="author-avatar">
          <div class="author-info">
            <h4>মোঃ মিজানুর রহমান</h4>
            <span>আলোক পত্রিকা</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="cta-card">
      <h2>আজই আপনার নিউজ পোর্টাল শুরু করুন!</h2>
      <p>রেজিস্ট্রেশন করুন এবং মাত্র ২৪ ঘন্টায় আপনার নিজস্ব নিউজ পোর্টাল চালু করুন।</p>
      <div class="btn-group">
        <a href="register.php" class="btn-primary">
          <i class="fas fa-user-plus"></i> রেজিস্ট্রেশন করুন
        </a>
        <a href="Admin/index.php" class="btn-secondary">
          <i class="fas fa-sign-in-alt"></i> অ্যাডমিন লগইন
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-links">
      <a href="register.php">রেজিস্ট্রেশন</a>
      <a href="Admin/index.php">অ্যাডমিন</a>
      <a href="Reporter/index.php">রিপোর্টার</a>
      <a href="https://techshilpo.com" target="_blank">TechShilpo</a>
    </div>
    <p>&copy; 2025 News Portal Software by <a href="https://techshilpo.com" target="_blank">TechShilpo</a></p>
  </footer>

  <script>
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Intersection Observer for animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    document.querySelectorAll('.feature-card, .stat-card, .step, .testimonial-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(30px)';
      el.style.transition = 'all 0.6s ease-out';
      observer.observe(el);
    });
  </script>
</body>

</html>
