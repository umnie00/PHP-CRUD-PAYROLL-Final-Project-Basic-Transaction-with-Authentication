<?php
// Redirect function
function redirectPage($page) { 
    header("Location: " . $page);
    exit();
}

// Check login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check role
function isAccountant() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'accountant';
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirectPage("login.php");
    }
}

// Require accountant
function requireAccountant() {
    requireLogin();
    if (!isAccountant()) {
        redirectPage("employee_dashboard.php");
    }
}

// Calculate payroll
function calculatePayroll($hours, $rate, $overtime = 0) {
    $regular_pay = $hours * $rate;
    $overtime_pay = $overtime * $rate * 1.5;
    $gross = $regular_pay + $overtime_pay;
    $tax = $gross * 0.10;
    $net = $gross - $tax;
    
    return [
        'gross' => $gross,
        'tax' => $tax,
        'net' => $net
    ];
}
?>  