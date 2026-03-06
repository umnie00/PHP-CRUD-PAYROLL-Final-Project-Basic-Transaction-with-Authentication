<?php
require 'db.php';

// Check remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember'])) {
    $user_id = $_COOKIE['remember'];
    // Simple check - just verify user exists
    $check = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $check->execute([$user_id]);
    $user = $check->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] == 'accountant') {
            header("Location: accountant_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // SIMPLE but PROTECTED: use password_verify
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        // Simple remember me - just store user id
        if ($remember) {
            setcookie('remember', $user['id'], time() + (86400 * 30), '/');
        }
        
        // Last login cookie
        setcookie('last_login', date('F j, Y'), time() + (86400 * 30), '/');
        
        // Redirect based on role
        if ($user['role'] == 'accountant') {
            header("Location: accountant_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-box">
        <h2>Login</h2>
        
        <?php if (isset($_COOKIE['last_login'])): ?>
            <div class="msg-success">Last login: <?= $_COOKIE['last_login'] ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="msg-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <div style="margin: 10px 0;">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;">
            <a href="register.php">No account? Register</a>
        </p>
    </div>
</body>
</html>