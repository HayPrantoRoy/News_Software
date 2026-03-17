<!DOCTYPE html>
<html lang="bn">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechShilpo</title>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap"
    rel="stylesheet">
    <link rel="stylesheet" href="css/style1.css">
    
    <style>
        /* About Section Styles */
.about-section {
  padding: 80px 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.about-container {
  max-width: 1200px;
  margin: 0 auto;
}

.about-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-top: 40px;
}

.about-text h3 {
  color: #2c3e50;
  font-size: 24px;
  margin: 30px 0 15px 0;
  font-weight: 600;
}

.about-text h3:first-child {
  margin-top: 0;
}

.about-text p {
  color: #555;
  line-height: 1.8;
  margin-bottom: 20px;
  text-align: justify;
}

.about-features {
  list-style: none;
  padding: 0;
  margin: 20px 0;
}

.about-features li {
  padding: 12px 0 12px 35px;
  position: relative;
  color: #555;
  line-height: 1.6;
}

.about-features li:before {
  content: "✓";
  position: absolute;
  left: 0;
  color: #4CAF50;
  font-weight: bold;
  font-size: 20px;
}

.team-section {
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.team-section h3 {
  color: #2c3e50;
  font-size: 24px;
  margin-bottom: 30px;
  font-weight: 600;
  text-align: center;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.team-card {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 15px;
  padding: 20px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.team-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.team-member {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.member-photo {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 15px;
  border: 4px solid white;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.member-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.team-member h4 {
  color: #2c3e50;
  font-size: 18px;
  margin-bottom: 8px;
  font-weight: 600;
}

.member-designation {
  color: #667eea;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 10px;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.member-contact {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 13px;
  color: #555;
}

.member-contact span {
  background: white;
  padding: 5px 10px;
  border-radius: 5px;
  font-family: 'Hind Siliguri', sans-serif;
}

.mission-vision {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-top: 20px;
}

.mv-card {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  padding: 20px;
  border-radius: 10px;
  color: white;
}

.mv-card h4 {
  font-size: 18px;
  margin-bottom: 10px;
  font-weight: 600;
}

.mv-card p {
  color: rgba(255, 255, 255, 0.95);
  line-height: 1.6;
  margin: 0;
}

/* Contact Section Styles */
.contact-section {
  padding: 80px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.contact-container {
  max-width: 1200px;
  margin: 0 auto;
}

.contact-section .section-title {
  color: white;
}

.contact-section .section-subtitle {
  color: rgba(255, 255, 255, 0.9);
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-top: 40px;
}

.contact-info {
  display: grid;
  gap: 20px;
}

.contact-card {
  background: white;
  padding: 25px;
  border-radius: 15px;
  display: flex;
  align-items: flex-start;
  gap: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.contact-card:hover {
  transform: translateY(-5px);
}

.contact-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.contact-icon svg {
  stroke: white;
}

.contact-details h4 {
  color: #2c3e50;
  font-size: 18px;
  margin-bottom: 8px;
  font-weight: 600;
}

.contact-details p {
  color: #555;
  margin: 5px 0;
  line-height: 1.6;
}

.contact-details a {
  color: #667eea;
  text-decoration: none;
  transition: color 0.3s ease;
}

.contact-details a:hover {
  color: #764ba2;
}

.contact-form-wrapper {
  background: white;
  padding: 35px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.contact-form-wrapper h3 {
  color: #2c3e50;
  font-size: 24px;
  margin-bottom: 25px;
  font-weight: 600;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: #2c3e50;
  font-weight: 500;
  margin-bottom: 8px;
  font-size: 15px;
}

.form-group input,
.form-group textarea {
  padding: 12px 15px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-family: 'Hind Siliguri', sans-serif;
  font-size: 15px;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.btn-submit {
  padding: 15px 35px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-family: 'Hind Siliguri', sans-serif;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.btn-submit:hover {
  transform: translateY(-2px);
}

.btn-submit .btn-text {
  position: relative;
  z-index: 1;
}

.btn-submit .btn-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s ease;
}

.btn-submit:hover .btn-shine {
  left: 100%;
}

.social-links {
  margin-top: 60px;
  text-align: center;
}

.social-links h3 {
  color: white;
  font-size: 24px;
  margin-bottom: 25px;
  font-weight: 600;
}

.social-icons {
  display: flex;
  justify-content: center;
  gap: 20px;
}

.social-icon {
  width: 50px;
  height: 50px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease, background-color 0.3s ease;
  text-decoration: none;
}

.social-icon:hover {
  transform: translateY(-5px);
}

.social-icon.facebook:hover {
  background-color: #1877f2;
}

.social-icon.facebook:hover svg {
  fill: white;
}

.social-icon.twitter:hover {
  background-color: #1da1f2;
}

.social-icon.twitter:hover svg {
  fill: white;
}

.social-icon.linkedin:hover {
  background-color: #0077b5;
}

.social-icon.linkedin:hover svg {
  fill: white;
}

.social-icon.youtube:hover {
  background-color: #ff0000;
}

.social-icon.youtube:hover svg {
  fill: white;
}

.social-icon svg {
  fill: #667eea;
  transition: fill 0.3s ease;
}

/* Footer Styles */
.footer {
  background: #2c3e50;
  padding: 30px 20px;
  text-align: center;
}

.footer-content p {
  color: white;
  margin: 5px 0;
  font-size: 14px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .about-content {
    grid-template-columns: 1fr;
  }

  .contact-content {
    grid-template-columns: 1fr;
  }

  .about-text h3 {
    font-size: 20px;
  }

  .team-section h3,
  .contact-form-wrapper h3,
  .social-links h3 {
    font-size: 20px;
  }

  .team-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
  }

  .member-photo {
    width: 100px;
    height: 100px;
  }

  .team-member h4 {
    font-size: 16px;
  }

  .member-designation {
    font-size: 13px;
    min-height: 35px;
  }

  .mission-vision {
    gap: 15px;
  }

  .contact-form-wrapper {
    padding: 25px;
  }

  .social-icons {
    flex-wrap: wrap;
  }
}

@media (max-width: 480px) {
  .about-section,
  .contact-section {
    padding: 50px 15px;
  }

  .about-text h3 {
    font-size: 18px;
    margin: 20px 0 10px 0;
  }

  .team-section,
  .contact-form-wrapper {
    padding: 20px;
  }

  .team-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .member-photo {
    width: 90px;
    height: 90px;
  }

  .team-member h4 {
    font-size: 16px;
  }

  .member-designation {
    font-size: 12px;
  }

  .contact-icon {
    width: 50px;
    height: 50px;
  }

  .contact-icon svg {
    width: 30px;
    height: 30px;
  }

  .social-icon {
    width: 45px;
    height: 45px;
  }
}
    </style>
    
</head>

<body>
  <header>
    <div class="logo-header">
      <img src="logo/techshilpo.png" alt="TechShilpo Logo">
    </div>
    <nav>
      <ul>
        <li><a href="#home">হোম</a></li>
        <li><a href="#software">সফটওয়্যার</a></li>
        <li><a href="#training">ট্রেনিং</a></li>
        <li><a href="#about">আমাদের সম্পর্কে</a></li>
        <li><a href="#contact">যোগাযোগ</a></li>
      </ul>
    </nav>
  </header>

  <section class="main-content" id="home">
    <div class="content-left">
      <h1>TechShilpo - প্রযুক্তির বিশ্বে আপনার নির্ভরযোগ্য সঙ্গী</h1>
      <p>আমরা প্রদান করি উচ্চমানের সফটওয়্যার সমাধান এবং কার্যকরী প্রশিক্ষণ প্রোগ্রাম যা আপনার ক্যারিয়ার এবং ব্যবসাকে
        এগিয়ে নেবে প্রযুক্তির অগ্রযাত্রায়।</p>
      <div class="btn-group">
        <a href="#software" class="btn-primary">সফটওয়্যার</a>
        <a href="#training" class="btn-secondary">ট্রেনিং শুরু করুন</a>
      </div>
    </div>
    <div class="content-right">
      <div class="code-editor-container">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>

        <div class="editor-header">
          <div class="traffic-lights">
            <div class="light red"></div>
            <div class="light yellow"></div>
            <div class="light green"></div>
          </div>
          <div class="editor-title">main.js - TechShilpo</div>
        </div>

        <div class="code-content">
          <div class="code-line">
            <span class="line-number">1</span>
            <span class="keyword">const</span> <span class="variable">techShilpo</span> <span class="operator">=</span>
            <span class="function">require</span><span class="operator">(</span><span
              class="string">'techshilpo'</span><span class="operator">);</span>
          </div>
          <div class="code-line">
            <span class="line-number">2</span>
            <span class="comment">// আধুনিক সফটওয়্যার সমাধান</span>
          </div>
          <div class="code-line">
            <span class="line-number">3</span>
            <span class="keyword">function</span> <span class="function">createSolution</span><span class="operator">()
              {</span>
          </div>
          <div class="code-line">
            <span class="line-number">4</span>
            &nbsp;&nbsp;<span class="keyword">return</span> <span class="variable">techShilpo</span><span
              class="operator">.</span><span class="function">build</span><span class="operator">({</span>
          </div>

          <div class="code-line">
            <span class="line-number">5</span>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="variable">quality</span><span class="operator">:</span> <span
              class="string">'premium'</span><span class="operator">,</span>
          </div>
          <div class="code-line">
            <span class="line-number">6</span>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="variable">training</span><span class="operator">:</span> <span
              class="keyword">"Javascript,Python,PHP & C#"</span><span class="operator">,</span>
          </div>
          <div class="code-line">
            <span class="line-number">7</span>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="variable">support</span><span class="operator">:</span> <span
              class="string">'24/7'</span>
          </div>

          <div class="code-line">
            <span class="line-number">8</span>
            &nbsp;&nbsp;<span class="operator">});</span>
            <span class="cursor"></span>
          </div>

        </div>
      </div>
    </div>
  </section>
  
  <!-- Software Section -->
  <section id="software" class="software-section">
    <div class="software-container">
      <h2 class="section-title">আমাদের সফটওয়্যার সমূহ</h2>
      <p class="section-subtitle">আধুনিক ও কার্যকর সফটওয়্যার সমাধান যা আপনার ব্যবসায়িক চাহিদা পূরণ করবে</p>

      <div class="software-grid">
          
          <?php
include 'connection.php';

// Fetch active softwares
$sql = "SELECT * FROM softwares WHERE is_active = 1 ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <!-- Software Card -->
        <div class="software-card">
          <div class="card-image">
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
          </div>
          <div class="card-content">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo htmlspecialchars($row['description']); ?></p>

            <!-- Package Selection -->
            <div class="package-selection">
              
              <?php if($row['monthly_amount']!=-1){ ?>
              <label class="radio-option">
                <input type="radio" name="package-<?php echo $row['id']; ?>" value="monthly" checked>
                <span class="radio-custom"></span>
                <span class="radio-label">মাসিক - <span style="font-family: sans-serif;">৳<?php echo number_format($row['monthly_amount']); ?></span></span>
              </label>
              <?php } ?>
              
              <label class="radio-option">
                <input type="radio" name="package-<?php echo $row['id']; ?>" value="yearly" <?php if($row['monthly_amount']==-1){ echo 'checked'; }?> >
                <span class="radio-custom"></span>
                <span class="radio-label">বার্ষিক - <span style="font-family: sans-serif;">৳<?php echo number_format($row['yearly_amount']); ?></span></span>
              </label>
            </div>

            <!-- Tags -->
            <div class="card-features">
              <?php
              if (!empty($row['tags'])) {
                  $tags = explode(',', $row['tags']);
                  foreach ($tags as $tag) {
                      echo '<span class="feature-tag">' . htmlspecialchars(trim($tag)) . '</span>';
                  }
              }
              ?>
            </div>

            <!-- Buy Button -->
            
            <a href="<?php echo $row['demo_link']; ?>"
   class="btn-kinun">
    <span class="btn-text">ডেমো দেখুন</span>
    <span class="btn-shine"></span>
</a>
<br>
            
            <a href="javascript:void(0);" 
   onclick="goToRegister('<?php echo $row['registration_url']; ?>', '<?php echo $row['id']; ?>')" 
   class="btn-kinun">
    <span class="btn-text">কিনুন</span>
    <span class="btn-shine"></span>
</a>
          </div>
        </div>
        <?php
    }
} else {
    echo "<p>কোন সফটওয়্যার পাওয়া যায়নি</p>";
}
?>

          
     
      </div>
    </div>
  </section>
  
  
  <!-- Training Section -->
<section id="training" class="training-section">
    <div class="training-container">
      <h2 class="section-title">আমাদের প্রশিক্ষণ কোর্স সমূহ</h2>
      <p class="section-subtitle">দক্ষ প্রশিক্ষকদের তত্ত্বাবধানে আধুনিক প্রযুক্তি শিখুন এবং ক্যারিয়ার গড়ুন</p>

      <div class="training-grid">
          
          <?php
// Fetch active courses
$sql_courses = "SELECT * FROM courses WHERE is_active = 1 ORDER BY id ASC";
$result_courses = $conn->query($sql_courses);

if ($result_courses->num_rows > 0) {
    while ($course = $result_courses->fetch_assoc()) {
        ?>
        <!-- Course Card -->
        <div class="training-card">
          <div class="card-image">
            <img src="<?php echo htmlspecialchars($course['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($course['title']); ?>">
            <div class="duration-badge"><?php echo htmlspecialchars($course['duration']); ?></div>
          </div>
          <div class="card-content">
            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
            <p><?php echo htmlspecialchars($course['description']); ?></p>

            <?php if (!empty($course['instructor'])): ?>
            <div class="instructor-info">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>প্রশিক্ষক: <?php echo htmlspecialchars($course['instructor']); ?></span>
            </div>
            <?php endif; ?>

            <!-- Course Fee -->
            <div class="course-fee">
              <span class="fee-label">কোর্স ফি:</span>
              <span class="fee-amount" style="font-family: sans-serif;"><s>৳<?php echo number_format($course['original_course_fee']); ?></s></span>
              <span class="fee-amount" style="font-family: sans-serif;">৳<?php echo number_format($course['course_fee']); ?></span>
            </div>

            <!-- Tags -->
            <div class="card-features">
              <?php
              if (!empty($course['tags'])) {
                  $tags = explode(',', $course['tags']);
                  foreach ($tags as $tag) {
                      echo '<span class="feature-tag">' . htmlspecialchars(trim($tag)) . '</span>';
                  }
              }
              ?>
            </div>

            <!-- Enroll Button -->
            <a href="enroll.php?course_id=<?php echo $course['id']; ?>" 
               class="btn-enroll">
                <span class="btn-text">এনরোল করুন</span>
                <span class="btn-shine"></span>
            </a>
          </div>
        </div>
        <?php
    }
} else {
    echo "<p class='no-courses'>কোন কোর্স পাওয়া যায়নি</p>";
}
?>

      </div>
    </div>
  </section>

  <!-- About Us Section -->
  <section id="about" class="about-section">
    <div class="about-container">
      <h2 class="section-title" style="color:#000 !important;">আমাদের সম্পর্কে</h2>
      <p class="section-subtitle">TechShilpo - প্রযুক্তি ও শিক্ষার মাধ্যমে ভবিষ্যৎ তৈরি করছি</p>

      <div class="about-content">
        <div class="about-text">
          <h3>আমরা কারা</h3>
          <p>TechShilpo একটি অগ্রগামী প্রযুক্তি প্রতিষ্ঠান যা উচ্চমানের সফটওয়্যার সমাধান এবং পেশাদার প্রশিক্ষণ সেবা প্রদান করে। আমরা বিশ্বাস করি যে প্রযুক্তির সঠিক ব্যবহার এবং দক্ষ জনশক্তি যেকোনো ব্যবসা বা ক্যারিয়ারকে সফলতার শিখরে পৌঁছাতে পারে।</p>

          <h3>আমাদের লক্ষ্য</h3>
          <p>আমাদের প্রধান লক্ষ্য হলো বাংলাদেশের তরুণ প্রজন্মকে আধুনিক প্রযুক্তিতে দক্ষ করে তোলা এবং ব্যবসায়িক প্রতিষ্ঠানগুলোকে কার্যকর সফটওয়্যার সমাধান প্রদান করা। আমরা চাই প্রতিটি শিক্ষার্থী এবং উদ্যোক্তা যেন প্রযুক্তির শক্তি কাজে লাগিয়ে তাদের স্বপ্ন বাস্তবায়ন করতে পারে।</p>

          <h3>কেন আমাদের বেছে নেবেন</h3>
          <ul class="about-features">
            <li>অভিজ্ঞ ও দক্ষ প্রশিক্ষক টিম</li>
            <li>আধুনিক ও কার্যকর সফটওয়্যার সমাধান</li>
            <li>হাতে-কলমে প্রশিক্ষণ পদ্ধতি</li>
            <li>২৪/৭ সাপোর্ট সেবা</li>
            <li>সাশ্রয়ী মূল্যে প্রিমিয়াম সেবা</li>
            <li>ক্যারিয়ার গাইডেন্স ও চাকরির সহায়তা</li>
          </ul>
        </div>

        <div class="team-section">
          <h3>আমাদের টিম</h3>
          
          <div class="team-grid">
            <!-- Team Member 1 -->
            <div class="team-card">
              <div class="team-member">
                <div class="member-photo">
                  <img src="https://birganjcitycare.com/img/sohag.jpg" alt="Md. Sohag Ali">
                </div>
                <h4>মোঃ সোহাগ আলী</h4>
                <p class="member-designation">Founder & CEO</p>
                <div class="member-contact">
                  <span>📞 +88 017-458-30123</span>
                </div>
              </div>
            </div>

            <!-- Team Member 2 -->
            <div class="team-card">
              <div class="team-member">
                <div class="member-photo">
                  <img src="https://techshilpo.com/Admin/imageUpload/uploads/1743768336_IMG-20250404-WA0005[1].jpg" alt="Md. Mizanur Rahman">
                </div>
                <h4>মোঃ মিজানুর রহমান</h4>
                <p class="member-designation">Chief Technical Officer (CTO)</p>
                <div class="member-contact">
                  <span>📞 +88 017-402-46091</span>
                </div>
              </div>
            </div>

            <!-- Team Member 3 -->
            <div class="team-card">
              <div class="team-member">
                <div class="member-photo">
                  <img src="https://birganjcitycare.com/img/pranto.jpg" alt="Pranto Roy">
                </div>
                <h4>প্রান্ত রায়</h4>
                <p class="member-designation">Chief Software Engineer</p>
                <div class="member-contact">
                  <span>📞 +88 018-424-94422</span>
                </div>
              </div>
            </div>

            <!-- Team Member 4 -->
            <div class="team-card">
              <div class="team-member">
                <div class="member-photo">
                  <img src="https://birganjcitycare.com/img/arafat.jpg" alt="Yeasin Arafat">
                </div>
                <h4>ইয়াসিন আরাফাত</h4>
                <p class="member-designation">Sr. Software Engineer</p>
                <div class="member-contact">
                  <span>📞 +88 017-850-34969</span>
                </div>
              </div>
            </div>

            <!-- Team Member 5 -->
            <div class="team-card">
              <div class="team-member">
                <div class="member-photo">
                  <img src="https://birganjcitycare.com/img/md_abdur_hakim.jpg" alt="Abdur Hakim">
                </div>
                <h4>আব্দুল হাকিম</h4>
                <p class="member-designation">HR Director</p>
                <div class="member-contact">
                  <span>📞 +88 017-183-02070</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mission-vision">
            <div class="mv-card">
              <h4>আমাদের দৃষ্টিভঙ্গি</h4>
              <p>বাংলাদেশকে একটি প্রযুক্তি সমৃদ্ধ দেশ হিসেবে গড়ে তুলতে অবদান রাখা এবং প্রতিটি তরুণকে দক্ষ প্রযুক্তিবিদ হিসেবে তৈরি করা।</p>
            </div>
            <div class="mv-card">
              <h4>আমাদের মূল্যবোধ</h4>
              <p>গুণমান, নির্ভরযোগ্যতা, উদ্ভাবন এবং গ্রাহক সন্তুষ্টি - এই চারটি স্তম্ভের উপর আমরা আমাদের সকল সেবা প্রদান করি।</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact-section">
    <div class="contact-container">
      <h2 class="section-title">যোগাযোগ করুন</h2>
      <p class="section-subtitle">আমরা সবসময় আপনার সেবায় প্রস্তুত</p>

      <div class="contact-content">
        <div class="contact-info">
          <div class="contact-card">
            <div class="contact-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
            </div>
            <div class="contact-details">
              <h4>ফোন</h4>
              <p><a href="tel:01306324739" style="font-family: sans-serif;">01306324739</a></p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </div>
            <div class="contact-details">
              <h4>ইমেইল</h4>
              <p><a href="mailto:techshilpo24@gmail.com
">techshilpo24@gmail.com
</a></p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </div>
            <div class="contact-details">
              <h4>ঠিকানা</h4>
              <p>বীরগঞ্জ, রংপুর, বাংলাদেশ</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="contact-details">
              <h4>কর্মঘণ্টা</h4>
              <p>শনিবার - বৃহস্পতিবার</p>
              <p style="font-family: sans-serif;">৯:০০ AM - ৬:০০ PM</p>
            </div>
          </div>
        </div>

        <div class="contact-form-wrapper">
          <h3>আমাদের মেসেজ পাঠান</h3>
          <form class="contact-form" action="contact_submit.php" method="POST">
            <div class="form-group">
              <label for="name">আপনার নাম *</label>
              <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
              <label for="email">ইমেইল *</label>
              <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
              <label for="phone">ফোন নম্বর *</label>
              <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
              <label for="subject">বিষয়</label>
              <input type="text" id="subject" name="subject">
            </div>

            <div class="form-group">
              <label for="message">আপনার বার্তা *</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn-submit">
              <span class="btn-text">পাঠান</span>
              <span class="btn-shine"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="social-links">
        <h3>আমাদের সাথে যুক্ত থাকুন</h3>
        <div class="social-icons">
          <a href="https://www.facebook.com/profile.php?id=61576074611808" class="social-icon facebook" title="Facebook">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>
          <a href="https://www.facebook.com/profile.php?id=61583126177004" class="social-icon facebook" title="Facebook">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>
          <a href="https://www.linkedin.com/in/tech-shilpo-33a897391/" class="social-icon linkedin" title="LinkedIn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
          </a>
          <a href="https://www.youtube.com/@TechShilpo-24" class="social-icon youtube" title="YouTube">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-content">
      <p>&copy; 2025 TechShilpo. সর্বস্বত্ব সংরক্ষিত।</p>
      <p>Made with ❤️ in Bangladesh</p>
    </div>
  </footer>
  
</body>

</html>

<script>
function goToRegister(baseUrl, sid) {
    // find selected package radio for this software
    let selected = document.querySelector('input[name="package-' + sid + '"]:checked');
    let pkg = selected ? selected.value : 'monthly';

    // redirect with params
    window.location.href = baseUrl + "?active=register&sid=" + sid + "&p=" + pkg;
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>