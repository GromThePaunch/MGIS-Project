<?php
// pages/add_to_cart.php
require __DIR__ . '/../includes/init.php';

// Security: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /index.php");
    exit;
}

// Get data from the form
$design_id = (int)($_POST['design_id'] ?? 0);
$color_id  = (int)($_POST['color_id'] ?? 0);
$size_id   = (int)($_POST['size_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));

// Basic validation
if ($design_id <= 0 || $color_id <= 0 || $size_id <= 0 || $quantity <= 0) {
    $_SESSION['error'] = "Invalid selection. Please try again.";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? $basepath . 'index.php'));
    exit;
}

// Find the correct inventory_id for this exact variant (design + color + size)
$stmt = $pdo->prepare("
    SELECT inventory_id, quantity 
    FROM inventory 
    WHERE design_id = ? 
      AND color_id = ? 
      AND size_id = ?
    LIMIT 1
");
$stmt->execute([$design_id, $color_id, $size_id]);
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$variant) {
    $_SESSION['error'] = "Sorry, this variant is not available.";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? $basepath . 'index.php'));
    exit;
}

// Check stock
if ($variant['quantity'] < $quantity) {
    $_SESSION['error'] = "Not enough stock. Only " . $variant['quantity'] . " available.";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? $basepath . 'index.php'));
    exit;
}

// Initialize cart in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart (using inventory_id as the unique key)
$inventory_id = $variant['inventory_id'];

if (isset($_SESSION['cart'][$inventory_id])) {
    $_SESSION['cart'][$inventory_id] += $quantity;
} else {
    $_SESSION['cart'][$inventory_id] = $quantity;
}

// Success message
$_SESSION['success'] = "Item added to your cart!";

// Redirect back to cart page
header("Location: " . $basepath . "pages/cart.php");
exit;
?>