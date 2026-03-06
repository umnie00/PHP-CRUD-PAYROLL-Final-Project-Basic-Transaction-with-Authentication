<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    // SIMPLE but PROTECTED: hash password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    
    // Check if username exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() == 0) {
        // Insert new user
        $sql = "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password, $full_name, $role]);
        
        header("Location: login.php");
        exit();
    } else {
        $error = "Username already exists";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-box">
        <h2>Register</h2>
        
        <?php if (isset($error)): ?>
            <div class="msg-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <select name="role" required>
                <option value="employee">Employee</option>
                <option value="accountant">Accountant</option>
            </select>
            
            <button type="submit">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;">
            <a href="login.php">Already have account? Login</a>
        </p>
    </div>
</body>
</html>