<?php
// pages/remove_from_cart.php
require __DIR__ . '/../includes/init.php';

// Only allow GET requests with inventory_id
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['inventory_id'])) {
    header("Location: " . $basepath . "pages/cart.php");
    exit;
}

$inventory_id = (int)$_GET['inventory_id'];

// Remove item from session cart if it exists
if (isset($_SESSION['cart'][$inventory_id])) {
    unset($_SESSION['cart'][$inventory_id]);
    
    // Optional: Clean up empty cart array
    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    
    $_SESSION['success'] = "Item removed from cart.";
} else {
    $_SESSION['error'] = "Item not found in cart.";
}

// Redirect back to cart page
header("Location: " . $basepath . "pages/cart.php");
exit;
?>