<?php
require_once __DIR__ . '/reporter_connection.php';

$basic_info = [];
$error_message = '';

// Fetch basic_info for portal name/logo
if ($conn) {
    $bi_result = $conn->query("SELECT * FROM basic_info LIMIT 1");
    if ($bi_result && $bi_result->num_rows > 0) {
        $basic_info = $bi_result->fetch_assoc();
    }
}

// Handle AJAX login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $login_user_id = intval($_POST['user_id'] ?? $user_id);
    
    if (empty($phone_number) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'মোবাইল নম্বর ও পাসওয়ার্ড প্রদান করুন!']);
        exit;
    }
    
    if ($login_user_id <= 0 || !$conn) {
        echo json_encode(['success' => false, 'message' => 'পোর্টাল সনাক্ত করা যায়নি! URL এ user_id প্রয়োজন।']);
        exit;
    }
    
    // Search in this specific tenant database
    $stmt = $conn->prepare("SELECT id, name, mobile, password, is_active FROM reporter WHERE mobile = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'ডাটাবেস ত্রুটি!']);
        exit;
    }
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reporter = $result->fetch_assoc();
        
        if ($password === $reporter['password']) {
            if (isset($reporter['is_active']) && $reporter['is_active'] == 0) {
                echo json_encode(['success' => false, 'message' => 'আপনার অ্যাকাউন্ট এখনও অনুমোদন পেন্ডিংয়ে আছে। অনুগ্রহ করে অপেক্ষা করুন।']);
                exit;
            }
            
            $_SESSION['reporter_logged_in'] = true;
            $_SESSION['reporter_id'] = $reporter['id'];
            $_SESSION['reporter_name'] = $reporter['name'];
            $_SESSION['reporter_database'] = $tenant_database;
            $_SESSION['reporter_user_id'] = $login_user_id;
            
            echo json_encode([
                'success' => true, 
                'message' => 'লগইন সফল!',
                'reporter_id' => $reporter['id'],
                'user_id' => $login_user_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ভুল পাসওয়ার্ড!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বরে কোন রিপোর্টার নেই!']);
    }
    
    exit;
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
            <img src="../assets/images/logo.png" alt="Logo" onerror="this.style.display='none'">
        </div>
        
        <div class="login-header">
            <h1>রিপোর্টার প্যানেল</h1>
            <p>লগইন করতে তথ্য প্রদান করুন</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="phone_number">মোবাইল নম্বর</label>
                <input type="tel" id="phone_number" name="phone_number" required autofocus placeholder="আপনার মোবাইল নম্বর">
            </div>
            
            <div class="form-group">
                <label for="password">পাসওয়ার্ড</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="আপনার পাসওয়ার্ড">
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
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
            অ্যাকাউন্ট নেই? <a href="Registration.php?user_id=<?= $user_id ?>" style="color: #4a90e2; text-decoration: none; font-weight: 600;">রেজিস্ট্রেশন করুন</a>
        </div>

        <div class="login-footer">
            সংবাদ প্রশাসন সিস্টেম &copy; <span id="year"></span>
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
            submitBtn.textContent = 'লগইন হচ্ছে...';
            
            const formData = new FormData(this);
            formData.append('ajax_login', '1');
            formData.append('user_id', '<?= $user_id ?>');
            
            try {
                const response = await fetch('index.php?user_id=<?= $user_id ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successMsg.textContent = data.message || 'লগইন সফল!';
                    successMsg.classList.add('show');
                    
                    // Redirect to home page with user_id and reporter_id
                    setTimeout(() => {
                        window.location.href = 'home.php?user_id=' + data.user_id + '&reporter_id=' + data.reporter_id;
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