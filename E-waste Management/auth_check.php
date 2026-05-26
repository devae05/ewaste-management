<?php
// auth_check.php — Reusable authentication guard
// Include at the top of any page that requires login.
//
// Usage:
//   require_once 'auth_check.php';          // any logged-in user
//   require_once 'auth_check.php'; require_admin();  // admins only

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login_register.php?action=login");
        exit();
    }
}

function require_admin(): void {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        // Logged-in but not admin — send to user dashboard
        header("Location: user.php");
        exit();
    }
}
