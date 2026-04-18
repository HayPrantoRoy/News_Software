<?php
session_start();

// Master database connection
$master_host = 'localhost';
$master_user = 'root';
$master_pass = '';
$master_db = 'master_news_software_db';

$error_message = '';

// Handle AJAX login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    
    $number = trim($_POST['number'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Connect to master database and verify credentials
    $master_conn = new mysqli($master_host, $master_user, $master_pass, $master_db);
    if ($master_conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'মাস্টার ডাটাবেস সংযোগ ব্যর্থ!']);
        exit;
    }
    
    // Check number and password from master users table
    $stmt = $master_conn->prepare("SELECT id, number, password, database_name, is_active FROM users WHERE number = ?");
    $stmt->bind_param("s", $number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ভুল নম্বর!']);
        $master_conn->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is active
    if (!$user['is_active']) {
        echo json_encode(['success' => false, 'message' => 'এই অ্যাকাউন্ট নিষ্ক্রিয়!']);
        $master_conn->close();
        exit;
    }
    
    // Verify password
    if ($password === $user['password']) {
        $tenant_db = $user['database_name'];
        
        // Store in session
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['number'] = $user['number'];
        $_SESSION['username'] = $user['number'];
        $_SESSION['role_name'] = 'Super Admin';
        $_SESSION['is_super_admin'] = true;
        $_SESSION['tenant_database'] = $tenant_db;
        $_SESSION['current_user_id'] = $user['id'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'লগইন সফল!',
            'user_id' => $user['id'],
            'database_name' => $tenant_db,
            'number' => $user['number']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ভুল পাসওয়ার্ড!']);
    }
    
    $master_conn->close();
    exit;
}

// If already logged in, redirect to Admin dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: Admin/dashboard.php');
    exit;
}

// Optional: Logout functionality
if (isset($_GET['logout'])) {
    echo "<script>
        localStorage.removeItem('admin_number');
        localStorage.removeItem('admin_database');
        localStorage.removeItem('admin_user_id');
        window.location.href = 'index.php?logged_out=1';
    </script>";
    session_destroy();
    exit;
}

// Handle logged out redirect
if (isset($_GET['logged_out'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - প্রশাসন প্যানেল</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'SolaimanLipi', 'Nikosh', 'Kalpurush', Arial, sans-serif;
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
        
        input[type="text"], input[type="password"] {
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
        
        input[type="text"]:focus, input[type="password"]:focus {
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
            background: #f0fff4;
            color: #38a169;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #9ae6b4;
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
        
        <div class="login-header">
            <h1>প্রশাসন প্যানেল</h1>
            <p>লগইন করতে তথ্য প্রদান করুন</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="number">নম্বর</label>
                <input type="text" id="number" name="number" required autofocus placeholder="আপনার নম্বর লিখুন">
            </div>
            
            <div class="form-group">
                <label for="password">পাসওয়ার্ড</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="আপনার পাসওয়ার্ড লিখুন">
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
            
            <button type="submit" class="btn-login">লগইন করুন</button>
        </form>
        
        <div class="login-footer">
            সংবাদ প্রশাসন সিস্টেম &copy; <span id="year"></span>
        </div>
    </div>
    
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
        
        // Load saved number from localStorage
        const savedNumber = localStorage.getItem('admin_number');
        if (savedNumber) {
            document.getElementById('number').value = savedNumber;
        }
        
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
            submitBtn.textContent = 'লগইন হচ্ছে...';
            
            const formData = new FormData(this);
            formData.append('ajax_login', '1');
            
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Store in localStorage for future use
                    localStorage.setItem('admin_number', data.number);
                    localStorage.setItem('admin_database', data.database_name);
                    localStorage.setItem('admin_user_id', data.user_id);
                    
                    successMsg.textContent = data.message || 'লগইন সফল!';
                    successMsg.classList.add('show');
                    
                    // Redirect to Admin dashboard
                    setTimeout(() => {
                        window.location.href = 'Admin/dashboard.php';
                    }, 1000);
                } else {
                    errorMsg.textContent = data.message || 'লগইন ব্যর্থ!';
                    errorMsg.classList.add('show');
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'লগইন করুন';
                }
            } catch (error) {
                errorMsg.textContent = 'একটি ত্রুটি ঘটেছে। আবার চেষ্টা করুন।';
                errorMsg.classList.add('show');
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'লগইন করুন';
            }
        });
    </script>
</body>
</html>
