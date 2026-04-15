<?php
require __DIR__ . '/../includes/init.php';

$pageTitle = 'Shopping Cart | ' . $siteName;

// Initialize variables
$cart_items = [];
$subtotal = 0.00;

// Fetch full cart details if items exist
if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    
    $stmt = $pdo->prepare("
        SELECT 
            i.inventory_id,
            d.name AS design_name,
            c.color_name,
            s.size_label,
            d.price
        FROM inventory i
        JOIN designs d ON i.design_id = d.design_id
        JOIN colors c ON i.color_id = c.color_id
        JOIN sizes s ON i.size_id = s.size_id
        WHERE i.inventory_id IN ($placeholders)
    ");
    
    $stmt->execute(array_keys($_SESSION['cart']));
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $item) {
        $inv_id = $item['inventory_id'];
        $qty = $_SESSION['cart'][$inv_id] ?? 0;
        
        if ($qty > 0) {
            $item_total = $item['price'] * $qty;
            $subtotal += $item_total;

            $cart_items[] = [
                'inventory_id' => $inv_id,
                'design_name'  => $item['design_name'],
                'color'        => $item['color_name'],
                'size'         => $item['size_label'],
                'price'        => $item['price'],
                'quantity'     => $qty,
                'item_total'   => $item_total
            ];
        }
    }
}

$tax = round($subtotal * $taxRate, 2);
$shipping = shipping_cost($subtotal);
$total = $subtotal + $tax + $shipping;
?>

<?php require __DIR__ . '/../includes/header.php'; ?>

<section class="content-panel">
    <h1>Shopping Cart</h1>

    <!-- Success / Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message" style="background:#d4edda; color:#155724; padding:12px 15px; margin:15px 0; border-radius:4px; border-left:4px solid #28a745;">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" style="background:#f8d7da; color:#721c24; padding:12px 15px; margin:15px 0; border-radius:4px; border-left:4px solid #dc3545;">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="<?= $basepath ?>index.php">Browse our catalog</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Variant</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['design_name']) ?></td>
                    <td><?= htmlspecialchars($item['color']) ?> / <?= htmlspecialchars($item['size']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['item_total'], 2) ?></td>
                    <td>
                        <a href="<?= $basepath ?>pages/remove_from_cart.php?inventory_id=<?= $item['inventory_id'] ?>" 
                           onclick="return confirm('Remove this item from cart?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
            <p>Tax (8.5%): $<?= number_format($tax, 2) ?></p>
            <p>Shipping: $<?= number_format($shipping, 2) ?></p>
            <p class="grand-total">Grand Total: $<?= number_format($total, 2) ?></p>
        </div>

        <a href="<?= $basepath ?>pages/checkout.php" class="button">Proceed to Checkout</a>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>

<!-- // ==================== SESSION DEBUG INFO ====================
echo '<div style="background:#fff3cd; border:2px solid #ffeaa7; padding:15px; margin:20px; font-family:monospace; font-size:0.95em;">';
echo '<strong>Session Debug:</strong><br>';
echo 'Session ID: ' . session_id() . '<br>';
echo 'Logged In: ' . (isLoggedIn() ? 'YES (Customer ID: ' . ($_SESSION['customer_id'] ?? 'N/A') . ')' : 'NO - Guest') . '<br>';
echo 'User Name: ' . htmlspecialchars(currentUserName()) . '<br>';
echo 'Cart Items: ' . (empty($_SESSION['cart']) ? 'Empty' : count($_SESSION['cart']) . ' variant(s)') . '<br>';
if (!empty($_SESSION['cart'])) {
    echo 'Cart Contents (inventory_id => qty):<br>';
    foreach ($_SESSION['cart'] as $inv_id => $qty) {
        echo " - $inv_id : $qty<br>";
    }
}
echo '</div>';
// =========================================================== -->
?>
