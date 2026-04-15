<?php
// includes/init.php
// This file is included at the top of EVERY page

// Start session ONLY once (safe even if included multiple times)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config and other common files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database_connection.php';

// Helper function to check login status
function isLoggedIn() {
    return isset($_SESSION['customer_id']) && $_SESSION['customer_id'] > 0;
}

function currentUserName() {
    return $_SESSION['first_name'] ?? 'Guest';
}
?>