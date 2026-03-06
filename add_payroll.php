<?php
require 'db.php';

// Only accountants
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    header("Location: login.php");
    exit();
}

// Get employees for dropdown
$employees = $pdo->query("SELECT id, full_name FROM users WHERE role = 'employee'")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $position = $_POST['position'];
    $hours = $_POST['hours_worked'];
    $rate = $_POST['hourly_rate'];
    $overtime = $_POST['overtime_hours'] ?: 0;
    $pay_date = $_POST['pay_date'];
    
    // Simple calculation
    $regular = $hours * $rate;
    $overtime_pay = $overtime * $rate * 1.5;
    $gross = $regular + $overtime_pay;
    $tax = $gross * 0.10;
    $net = $gross - $tax;
    
    // SIMPLE insert with prepared statement
    $sql = "INSERT INTO payroll (user_id, position, hours_worked, hourly_rate, overtime_hours, gross_pay, tax_amount, net_pay, pay_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $position, $hours, $rate, $overtime, $gross, $tax, $net, $pay_date]);
    
    header("Location: accountant_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Payroll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Add Payroll</h2>
            
            <form method="post">
                <div class="form-group">
                    <label>Employee:</label>
                    <select name="user_id" required>
                        <option value="">Select</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" required>
                </div>
                
                <div class="form-group">
                    <label>Hours Worked:</label>
                    <input type="number" name="hours_worked" step="0.5" required>
                </div>
                
                <div class="form-group">
                    <label>Hourly Rate:</label>
                    <input type="number" name="hourly_rate" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Overtime Hours:</label>
                    <input type="number" name="overtime_hours" step="0.5" value="0">
                </div>
                
                <div class="form-group">
                    <label>Pay Date:</label>
                    <input type="date" name="pay_date" required>
                </div>
                
                <button type="submit" class="btn btn-add">Save</button>
                <a href="accountant_dashboard.php" class="btn" style="background: #999;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>