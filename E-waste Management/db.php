<?php
// db.php — Central database connection
// Edit the constants below to match your environment.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // ← change in production
define('DB_NAME', 'ewaste');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // Do NOT expose connection errors to end-users in production
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}

$conn->set_charset("utf8mb4");
