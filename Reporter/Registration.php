<?php
require_once __DIR__ . '/reporter_connection.php';

// Fetch basic_info for dynamic logo and portal name
$basic_info = [];
if ($conn) {
    $basic_info_result = $conn->query("SELECT * FROM basic_info LIMIT 1");
    if ($basic_info_result && $basic_info_result->num_rows > 0) {
        $basic_info = $basic_info_result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reporter Registration</title>
<?php if (!empty($basic_info['image'])): ?>
<link rel="icon" href="<?php echo htmlspecialchars($basic_info['image']); ?>">
<?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f5f7;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
  }

  .auth-container {
    background-color: #ffffff;
    padding: 40px 30px;
    border-radius: 16px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
  }

  .logo-section {
    text-align: center;
    margin-bottom: 30px;
  }

  .logo {
    width: 200px;
    margin-bottom: 10px;
  }

  .logo-section h2 {
    font-weight: 600;
    font-size: 1.6rem;
    color: #535353ff;
  }

  .form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 6px;
    font-size: 0.9rem;
  }

  .form-control {
    border: 1px solid #ced4da;
    border-radius: 12px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-color: #fdfdfd;
  }

  .form-control:focus {
    border-color: #308e87;
    box-shadow: 0 0 0 3px rgba(48, 142, 135, 0.2);
  }

  .file-input-container {
    position: relative;
    border: 2px dashed #ced4da;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
  }

  .file-input-container:hover {
    border-color: #308e87;
    background-color: #f0fdfd;
  }

  .file-input-container input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
  }

  .file-input-container span {
    font-size: 0.95rem;
    color: #555;
  }

  .img-preview {
    margin-top: 12px;
    max-width: 100%;
    border-radius: 10px;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .btn-submit {
    width: 100%;
    padding: 14px;
    background-color: #308e87;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    margin-top: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(48, 142, 135, 0.3);
  }

  .btn-submit:hover {
    background-color: #27756e;
    box-shadow: 0 6px 12px rgba(48, 142, 135, 0.4);
  }

  .mb-3 {
    margin-bottom: 18px;
  }

  textarea.form-control {
    resize: vertical;
    min-height: 90px;
  }

  @media (max-width: 576px) {
    .auth-container { padding: 30px 20px; }
  }
</style>
</head>
<body>

<div class="auth-container">
  <div class="logo-section">
    
    <h2>Reporter Registration</h2>
  </div>

  <div id="message-container" style="margin-bottom: 20px; display: none;">
    <div id="alert-message" style="padding: 12px; border-radius: 8px; text-align: center;"></div>
  </div>

  <form id="registrationForm" action="../Admin/reporter_registration.php?action=create_reporter" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" class="form-control" name="name" placeholder="Enter your full name" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Phone Number</label>
      <input type="text" class="form-control" name="mobile" placeholder="Enter your phone number" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" class="form-control" name="password" placeholder="Create a strong password" required>
    </div>

    <div class="mb-3">
      <label class="form-label">ID Card Number</label>
      <input type="text" class="form-control" name="id_card" placeholder="Enter your ID card number" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Address</label>
      <textarea class="form-control" name="address" rows="3" placeholder="Enter your complete address" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Photo</label>
      <div class="file-input-container">
        <span>Click or drag your photo here</span>
        <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'photo-preview')" required>
      </div>
      <img id="photo-preview" class="img-preview">
    </div>

    <div class="mb-3">
      <label class="form-label">ID Card Photo</label>
      <div class="file-input-container">
        <span>Click or drag your ID card here</span>
        <input type="file" name="id_card_photo" accept="image/*" onchange="previewImage(event, 'idcard-preview')" required>
      </div>
      <img id="idcard-preview" class="img-preview">
    </div>

    <button type="submit" class="btn-submit" id="submitBtn">Register</button>
  </form>

  <div style="text-align: center; margin-top: 20px; color: #666; font-size: 0.9rem;">
    Already have an account? <a href="index.php?user_id=<?= $user_id ?>" style="color: #308e87; text-decoration: none; font-weight: 600;">Login here</a>
  </div>
</div>

<script>
  function previewImage(event, previewId) {
    const reader = new FileReader();
    const preview = document.getElementById(previewId);
    reader.onload = function(){
      preview.src = reader.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  // Handle form submission with AJAX
  document.getElementById('registrationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const messageContainer = document.getElementById('message-container');
    const alertMessage = document.getElementById('alert-message');
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Registering...';
    
    // Hide previous messages
    messageContainer.style.display = 'none';
    
    const formData = new FormData(this);
    
    try {
      const response = await fetch('../Admin/reporter_registration.php?action=create_reporter&user_id=<?= $user_id ?>', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Show success message
        alertMessage.textContent = data.message;
        alertMessage.style.backgroundColor = '#d4edda';
        alertMessage.style.color = '#155724';
        alertMessage.style.border = '1px solid #c3e6cb';
        messageContainer.style.display = 'block';
        
        // Redirect to login page
        setTimeout(() => {
          window.location.href = 'index.php?user_id=<?= $user_id ?>';
        }, 1500);
      } else {
        // Show error message
        alertMessage.textContent = data.message || 'Registration failed. Please try again.';
        alertMessage.style.backgroundColor = '#f8d7da';
        alertMessage.style.color = '#721c24';
        alertMessage.style.border = '1px solid #f5c6cb';
        messageContainer.style.display = 'block';
        
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Register';
      }
    } catch (error) {
      // Show error message
      alertMessage.textContent = 'An error occurred. Please try again.';
      alertMessage.style.backgroundColor = '#f8d7da';
      alertMessage.style.color = '#721c24';
      alertMessage.style.border = '1px solid #f5c6cb';
      messageContainer.style.display = 'block';
      
      // Re-enable submit button
      submitBtn.disabled = false;
      submitBtn.textContent = 'Register';
      console.error('Registration error:', error);
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>