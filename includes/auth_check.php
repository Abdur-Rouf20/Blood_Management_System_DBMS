<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login for any page
function require_login() {
    if (!isset($_SESSION['role'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Require specific role (donor or patient)
function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: ../auth/login.php");
        exit();
    }
}
?>
