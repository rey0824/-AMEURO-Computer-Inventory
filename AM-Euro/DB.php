<?php
// Database connection and utility functions for AmEuro System

$db_host = "localhost";
$db_user = "root"; 
$db_pass = ""; 
$db_name = "ameuro";

// Enable mysqli error reporting (throws exceptions)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database Connection failed: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sanitize a string for safe input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['emp_ID']);
}

// Redirect to a different URL
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Require user to be logged in, otherwise redirect to login
function require_login() {
    if (!is_logged_in()) {
        if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
            redirect('login.php'); 
        } else {
            die("Error: Could not verify login status.");
        }
    }
}

// Get current page filename for navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>