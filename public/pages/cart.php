<?php
require __DIR__ . '/../includes/config.php';
$pageTitle = 'Cart | ' . $siteName;
$subtotal = 44.00;
$tax = round($subtotal * $taxRate, 2);
$shipping = shipping_cost($subtotal);
$total = $subtotal + $tax + $shipping;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Shopping Cart</h1>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Variant</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>City Grid Tee</td>
                <td>Black / M</td>
                <td>2</td>
                <td>$44.00</td>
            </tr>
        </tbody>
    </table>
    <div class="totals">
        <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
        <p>Tax (8.5%): $<?= number_format($tax, 2) ?></p>
        <p>Shipping: $<?= number_format($shipping, 2) ?></p>
        <p class="grand-total">Grand Total: $<?= number_format($total, 2) ?></p>
    </div>
    <p>Next step: replace this placeholder with session or database-backed cart actions for update and remove flows.</p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
