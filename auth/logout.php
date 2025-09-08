<?php //session_start(); session_unset(); session_destroy(); header('Location: /index.php'); exit(); ?>


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to home page
header("Location: /blood_management_system/index.php");
exit();
