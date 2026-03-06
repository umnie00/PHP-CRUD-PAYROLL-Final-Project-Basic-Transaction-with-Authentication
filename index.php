<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'accountant') {
        header("Location: accountant_dashboard.php");
    } else {
        header("Location: employee_dashboard.php");
    }
} else {
    header("Location: login.php");
}
exit();
?>