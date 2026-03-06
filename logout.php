<?php
session_start();
// Clear remember me cookie
setcookie('remember', '', time() - 3600, '/');
session_destroy();
header("Location: login.php");
exit();
?>