<?php
require 'db.php';

// Simple protection - only accountants
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    header("Location: login.php");
    exit();
}

$full_name = $_SESSION['full_name'];

// Get all records with employee names - SIMPLE join
$stmt = $pdo->query("
    SELECT payroll.*, users.full_name as employee_name 
    FROM payroll 
    JOIN users ON payroll.user_id = users.id 
    ORDER BY payroll.pay_date DESC
");
$records = $stmt->fetchAll();

// Get employee count
$emp_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();

// Simple totals
$total_payroll = 0;
foreach ($records as $r) {
    $total_payroll += $r['net_pay'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accountant Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Welcome, <?= htmlspecialchars($full_name) ?>!</h2>
                <span class="badge accountant">Accountant</span>
            </div>
            <div>
                <a href="add_payroll.php" class="btn btn-add">➕ Add Payroll</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
        
        <?php if (isset($_COOKIE['last_login'])): ?>
            <div class="msg-success">Last login: <?= $_COOKIE['last_login'] ?></div>
        <?php endif; ?>
        
        <!-- Simple summary -->
        <div class="summary">
            <div class="card">
                <div class="number"><?= $emp_count ?></div>
                <div>Employees</div>
            </div>
            <div class="card">
                <div class="number"><?= count($records) ?></div>
                <div>Payslips</div>
            </div>
            <div class="card">
                <div class="number">₱<?= number_format($total_payroll, 2) ?></div>
                <div>Total Payroll</div>
            </div>
        </div>
        
        <!-- All records with CRUD actions -->
        <h3>All Payroll Records</h3>
        
        <?php if ($records): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Position</th>
                <th>Date</th>
                <th>Net Pay</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($records as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= $row['pay_date'] ?></td>
                <td>₱<?= number_format($row['net_pay'], 2) ?></td>
                <td>
                    <a href="edit_payroll.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                    <a href="delete_payroll.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>No payroll records. <a href="add_payroll.php">Add one now</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>