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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reporter Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 60px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            border: 1px solid #f0f0f0;
        }
        
        .logo-container {
            text-align: center;
        }
        
        .logo-container img {
            max-width: 200px;
            height: auto;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header h1 {
            color: #1a1a1a;
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        input[type="text"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: Arial, sans-serif;
            background: #fafafa;
        }
        
        .input-wrapper input[type="password"],
        .input-wrapper input[type="text"] {
            padding-right: 45px;
        }
        
        input[type="text"]:focus, input[type="password"]:focus, input[type="tel"]:focus {
            outline: none;
            border-color: #4a90e2;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: #666;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: #4a90e2;
        }
        
        .toggle-password svg {
            width: 20px;
            height: 20px;
            display: block;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: #357abd;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .error-message {
            background: #fff5f5;
            color: #e53e3e;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #feb2b2;
            font-size: 14px;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        .success-message {
            background: #f0fdf4;
            color: #16a34a;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #86efac;
            font-size: 14px;
            display: none;
        }
        
        .success-message.show {
            display: block;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 40px 30px;
            }
            
            .logo-container img {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <?php if (!empty($basic_info['image'])): ?>
            <img src="<?php echo htmlspecialchars($basic_info['image']); ?>" alt="<?php echo htmlspecialchars($basic_info['news_portal_name'] ?? 'Logo'); ?>">
            <?php endif; ?>
        </div>
        
        <div class="login-header">
            <h1>Reporter Login</h1>
            <p>Please login to continue</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" required autofocus placeholder="Enter your phone number">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg id="eye-off-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
            Don't have an account? <a href="Registration.php" style="color: #4a90e2; text-decoration: none; font-weight: 600;">Register here</a>
        </div>
        
        <div class="login-footer">
            News Management System &copy; <span id="year"></span>
        </div>
    </div>
    
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
        
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.btn-login');
            const errorMsg = document.getElementById('errorMessage');
            const successMsg = document.getElementById('successMessage');
            
            // Hide previous messages
            errorMsg.classList.remove('show');
            successMsg.classList.remove('show');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../Admin/reporter_registration.php?action=login_reporter', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successMsg.textContent = data.message || 'Login successful! Redirecting...';
                    successMsg.classList.add('show');
                    
                    // Redirect to home page with reporter ID
                    setTimeout(() => {
                        window.location.href = '../Reporter/home.php?id=' + data.reporter_id;
                    }, 1000);
                } else {
                    errorMsg.textContent = data.message || 'Login failed. Please try again.';
                    errorMsg.classList.add('show');
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Login';
                }
            } catch (error) {
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.classList.add('show');
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        });
    </script>
</body>
</html>