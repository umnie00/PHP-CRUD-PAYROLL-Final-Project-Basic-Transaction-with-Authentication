<?php
require 'db.php';

// Simple protection
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only employees can be here
if (isset($_SESSION['role']) && $_SESSION['role'] == 'accountant') {
    header("Location: accountant_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// SIMPLE query - get only this user's records
$stmt = $pdo->prepare("SELECT * FROM payroll WHERE user_id = ? ORDER BY pay_date DESC");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();

// Simple totals
$total = 0;
$hours = 0;
foreach ($records as $r) {
    $total += $r['net_pay'];
    $hours += $r['hours_worked'];
}

// Latest record
$latest = !empty($records) ? $records[0] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Welcome, <?= htmlspecialchars($full_name) ?>!</h2>
                <span class="badge employee">Employee</span>
            </div>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
        
        <?php if (isset($_COOKIE['last_login'])): ?>
            <div class="msg-success">Last login: <?= $_COOKIE['last_login'] ?></div>
        <?php endif; ?>
        
        <!-- Simple summary -->
        <div class="summary">
            <div class="card">
                <div class="number"><?= count($records) ?></div>
                <div>Total Payslips</div>
            </div>
            <div class="card">
                <div class="number">₱<?= number_format($total, 2) ?></div>
                <div>Total Earned</div>
            </div>
            <div class="card">
                <div class="number"><?= $hours ?> hrs</div>
                <div>Total Hours</div>
            </div>
        </div>
        
        <!-- Latest payslip -->
        <?php if ($latest): ?>
        <div class="latest">
            <h3>Latest Payslip</h3>
            <p><strong>Date:</strong> <?= $latest['pay_date'] ?></p>
            <p><strong>Position:</strong> <?= htmlspecialchars($latest['position']) ?></p>
            
            <div class="payslip-grid">
                <div class="payslip-item">
                    <div>Hours</div>
                    <div class="value"><?= $latest['hours_worked'] ?></div>
                </div>
                <div class="payslip-item">
                    <div>Rate</div>
                    <div class="value">₱<?= $latest['hourly_rate'] ?></div>
                </div>
                <div class="payslip-item">
                    <div>Overtime</div>
                    <div class="value"><?= $latest['overtime_hours'] ?></div>
                </div>
                <div class="payslip-item">
                    <div>Gross</div>
                    <div class="value">₱<?= number_format($latest['gross_pay'], 2) ?></div>
                </div>
                <div class="payslip-item">
                    <div>Tax</div>
                    <div class="value">₱<?= number_format($latest['tax_amount'], 2) ?></div>
                </div>
                <div class="payslip-item net">
                    <div>Net Pay</div>
                    <div class="value">₱<?= number_format($latest['net_pay'], 2) ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- All records - READ ONLY, no actions -->
        <h3>My Payslips</h3>
        
        <?php if ($records): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Position</th>
                <th>Hours</th>
                <th>Rate</th>
                <th>Gross</th>
                <th>Net Pay</th>
            </tr>
            <?php foreach ($records as $row): ?>
            <tr>
                <td><?= $row['pay_date'] ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= $row['hours_worked'] ?></td>
                <td>₱<?= $row['hourly_rate'] ?></td>
                <td>₱<?= number_format($row['gross_pay'], 2) ?></td>
                <td><strong>₱<?= number_format($row['net_pay'], 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>No payroll records yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>