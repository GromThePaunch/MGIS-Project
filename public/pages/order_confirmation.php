<?php
require __DIR__ . '/../includes/init.php';

if (!isLoggedIn()) {
    header("Location: {$basepath}pages/login.php");
    exit;
}

$order_id = (int) ($_GET['order_id'] ?? 0);
$customer_id = (int) $_SESSION['customer_id'];

if ($order_id <= 0) {
    header("Location: {$basepath}pages/profile.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT o.*,
            sa.street AS s_street, sa.city AS s_city, sa.state AS s_state, sa.zip AS s_zip,
            ba.street AS b_street, ba.city AS b_city, ba.state AS b_state, ba.zip AS b_zip
     FROM orders o
     LEFT JOIN addresses sa ON sa.address_id = o.shipping_addr_id
     LEFT JOIN addresses ba ON ba.address_id = o.billing_addr_id
     WHERE o.order_id = ? AND o.customer_id = ?"
);
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: {$basepath}pages/profile.php");
    exit;
}

$items_stmt = $pdo->prepare(
    "SELECT oi.quantity, oi.unit_price,
            d.name AS design_name, c.color_name, s.size_label
     FROM order_items oi
     JOIN inventory i ON i.inventory_id = oi.inventory_id
     JOIN designs d   ON d.design_id   = i.design_id
     JOIN colors c    ON c.color_id    = i.color_id
     JOIN sizes s     ON s.size_id     = i.size_id
     WHERE oi.order_id = ?"
);
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Order Confirmation | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Thank you for your order!</h1>
    <p>Order #<?= (int) $order['order_id'] ?> placed on
        <?= htmlspecialchars(date('M j, Y g:i a', strtotime($order['order_date']))) ?>.</p>
    <p>A confirmation email has been sent to your account email address.</p>

    <h2>Items</h2>
    <table class="cart-table">
        <thead>
            <tr><th>Item</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['design_name']) ?></td>
                <td><?= htmlspecialchars($it['color_name']) ?> / <?= htmlspecialchars($it['size_label']) ?></td>
                <td><?= (int) $it['quantity'] ?></td>
                <td>$<?= number_format((float) $it['unit_price'], 2) ?></td>
                <td>$<?= number_format((float) $it['unit_price'] * (int) $it['quantity'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <p>Subtotal: $<?= number_format((float) $order['subtotal'], 2) ?></p>
        <p>Tax: $<?= number_format((float) $order['tax_amount'], 2) ?></p>
        <p>Shipping: $<?= number_format((float) $order['shipping_cost'], 2) ?></p>
        <p class="grand-total">Grand Total: $<?= number_format((float) $order['grand_total'], 2) ?></p>
    </div>

    <h2>Shipping Address</h2>
    <p>
        <?= htmlspecialchars($order['s_street']) ?><br>
        <?= htmlspecialchars($order['s_city']) ?>, <?= htmlspecialchars($order['s_state']) ?>
        <?= htmlspecialchars($order['s_zip']) ?>
    </p>

    <h2>Payment</h2>
    <p><?= htmlspecialchars($order['card_type']) ?> ending in <?= htmlspecialchars($order['card_last4']) ?></p>

    <p>
        <a href="<?= $basepath ?>pages/profile.php" class="button">Back to Profile</a>
        <a href="<?= $basepath ?>index.php" class="button">Continue Shopping</a>
    </p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
