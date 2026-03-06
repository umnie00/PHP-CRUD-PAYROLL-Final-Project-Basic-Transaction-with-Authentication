<?php
require 'db.php';

// Only accountants
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

// SIMPLE delete with prepared statement
$stmt = $pdo->prepare("DELETE FROM payroll WHERE id = ?");
$stmt->execute([$id]);

header("Location: accountant_dashboard.php");
exit();
?>