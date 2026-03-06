<?php
require 'db.php';

// Only accountants
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

// Get the record
$stmt = $pdo->prepare("SELECT * FROM payroll WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    header("Location: accountant_dashboard.php");
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
    
    // SIMPLE update with prepared statement
    $sql = "UPDATE payroll SET 
            user_id=?, position=?, hours_worked=?, hourly_rate=?,
            overtime_hours=?, gross_pay=?, tax_amount=?, net_pay=?, pay_date=?
            WHERE id=?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $position, $hours, $rate, $overtime, $gross, $tax, $net, $pay_date, $id]);
    
    header("Location: accountant_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payroll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Edit Payroll</h2>
            
            <form method="post">
                <div class="form-group">
                    <label>Employee:</label>
                    <select name="user_id" required>
                        <option value="">Select</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= $emp['id'] == $record['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" value="<?= htmlspecialchars($record['position']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Hours Worked:</label>
                    <input type="number" name="hours_worked" step="0.5" value="<?= $record['hours_worked'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Hourly Rate:</label>
                    <input type="number" name="hourly_rate" step="0.01" value="<?= $record['hourly_rate'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Overtime Hours:</label>
                    <input type="number" name="overtime_hours" step="0.5" value="<?= $record['overtime_hours'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Pay Date:</label>
                    <input type="date" name="pay_date" value="<?= $record['pay_date'] ?>" required>
                </div>
                
                <button type="submit" class="btn btn-add">Update</button>
                <a href="accountant_dashboard.php" class="btn" style="background: #999;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>