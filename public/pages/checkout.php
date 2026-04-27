<?php
require __DIR__ . '/../includes/init.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Please log in to checkout.";
    header("Location: {$basepath}pages/login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: {$basepath}pages/cart.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];
$error = "";

$cart_ids = array_keys($_SESSION['cart']);
$placeholders = str_repeat('?,', count($cart_ids) - 1) . '?';
$stmt = $pdo->prepare("
    SELECT i.inventory_id, i.quantity AS in_stock,
           d.name AS design_name, d.price,
           c.color_name, s.size_label
    FROM inventory i
    JOIN designs d ON i.design_id = d.design_id
    JOIN colors c ON i.color_id = c.color_id
    JOIN sizes s ON i.size_id = s.size_id
    WHERE i.inventory_id IN ($placeholders)
");
$stmt->execute($cart_ids);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_items = [];
$subtotal = 0.00;
foreach ($rows as $r) {
    $qty = (int) $_SESSION['cart'][$r['inventory_id']];
    $line = (float) $r['price'] * $qty;
    $subtotal += $line;
    $cart_items[] = [
        'inventory_id' => (int) $r['inventory_id'],
        'design_name'  => $r['design_name'],
        'color_name'   => $r['color_name'],
        'size_label'   => $r['size_label'],
        'price'        => (float) $r['price'],
        'quantity'     => $qty,
        'in_stock'     => (int) $r['in_stock'],
        'line_total'   => $line,
    ];
}

$tax = round($subtotal * $taxRate, 2);
$shipping = shipping_cost($subtotal);
$grand_total = $subtotal + $tax + $shipping;

$cust_stmt = $pdo->prepare("SELECT first_name, last_name, email FROM customers WHERE customer_id = ?");
$cust_stmt->execute([$customer_id]);
$customer = $cust_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ship_street = trim($_POST['ship_street'] ?? '');
    $ship_city   = trim($_POST['ship_city']   ?? '');
    $ship_state  = strtoupper(trim($_POST['ship_state'] ?? ''));
    $ship_zip    = trim($_POST['ship_zip']    ?? '');

    $billing_same = isset($_POST['billing_same']);
    if ($billing_same) {
        $bill_street = $ship_street;
        $bill_city   = $ship_city;
        $bill_state  = $ship_state;
        $bill_zip    = $ship_zip;
    } else {
        $bill_street = trim($_POST['bill_street'] ?? '');
        $bill_city   = trim($_POST['bill_city']   ?? '');
        $bill_state  = strtoupper(trim($_POST['bill_state'] ?? ''));
        $bill_zip    = trim($_POST['bill_zip']    ?? '');
    }

    $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $card_type   = trim($_POST['card_type'] ?? '');
    $card_last4  = substr($card_number, -4);

    if (empty($ship_street) || empty($ship_city) || empty($ship_state) || empty($ship_zip)
        || empty($bill_street) || empty($bill_city) || empty($bill_state) || empty($bill_zip)) {
        $error = "All address fields are required.";
    } elseif (strlen($ship_state) !== 2 || strlen($bill_state) !== 2) {
        $error = "State must be a 2-letter abbreviation.";
    } elseif (strlen($card_number) < 13 || strlen($card_number) > 19) {
        $error = "Please enter a valid card number.";
    } elseif (empty($card_type)) {
        $error = "Please select a card type.";
    } else {
        try {
            $pdo->beginTransaction();

            $check = $pdo->prepare("SELECT inventory_id, quantity FROM inventory WHERE inventory_id = ? FOR UPDATE");
            foreach ($cart_items as $ci) {
                $check->execute([$ci['inventory_id']]);
                $row = $check->fetch(PDO::FETCH_ASSOC);
                if (!$row || (int) $row['quantity'] < $ci['quantity']) {
                    throw new RuntimeException("Sorry, '{$ci['design_name']} ({$ci['color_name']}/{$ci['size_label']})' is no longer available in the requested quantity.");
                }
            }

            $addr_ins = $pdo->prepare(
                "INSERT INTO addresses (customer_id, address_type, street, city, state, zip)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $addr_ins->execute([$customer_id, 'shipping', $ship_street, $ship_city, $ship_state, $ship_zip]);
            $shipping_addr_id = (int) $pdo->lastInsertId();

            if ($billing_same) {
                $billing_addr_id = $shipping_addr_id;
            } else {
                $addr_ins->execute([$customer_id, 'billing', $bill_street, $bill_city, $bill_state, $bill_zip]);
                $billing_addr_id = (int) $pdo->lastInsertId();
            }

            $order_ins = $pdo->prepare(
                "INSERT INTO orders
                    (customer_id, shipping_addr_id, billing_addr_id,
                     subtotal, tax_amount, shipping_cost, grand_total,
                     card_last4, card_type, order_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid')"
            );
            $order_ins->execute([
                $customer_id, $shipping_addr_id, $billing_addr_id,
                $subtotal, $tax, $shipping, $grand_total,
                $card_last4, $card_type,
            ]);
            $order_id = (int) $pdo->lastInsertId();

            $item_ins = $pdo->prepare(
                "INSERT INTO order_items (order_id, inventory_id, quantity, unit_price)
                 VALUES (?, ?, ?, ?)"
            );
            $inv_dec = $pdo->prepare(
                "UPDATE inventory SET quantity = quantity - ? WHERE inventory_id = ?"
            );
            foreach ($cart_items as $ci) {
                $item_ins->execute([$order_id, $ci['inventory_id'], $ci['quantity'], $ci['price']]);
                $inv_dec->execute([$ci['quantity'], $ci['inventory_id']]);
            }

            $pdo->commit();

            if (!empty($customer['email'])) {
                $to = $customer['email'];
                $subject = "Order #{$order_id} confirmation - {$siteName}";
                $body  = "Hi " . $customer['first_name'] . ",\n\n";
                $body .= "Thanks for your order! Here is your receipt.\n\n";
                $body .= "Order #: {$order_id}\n";
                $body .= "Date: " . date('M j, Y g:i a') . "\n\n";
                $body .= "Items:\n";
                foreach ($cart_items as $ci) {
                    $body .= sprintf(
                        " - %s (%s / %s) x%d @ \$%.2f = \$%.2f\n",
                        $ci['design_name'], $ci['color_name'], $ci['size_label'],
                        $ci['quantity'], $ci['price'], $ci['line_total']
                    );
                }
                $body .= "\n";
                $body .= "Subtotal: \$" . number_format($subtotal, 2) . "\n";
                $body .= "Tax:      \$" . number_format($tax, 2) . "\n";
                $body .= "Shipping: \$" . number_format($shipping, 2) . "\n";
                $body .= "Total:    \$" . number_format($grand_total, 2) . "\n\n";
                $body .= "Charged to {$card_type} ending in {$card_last4}.\n\n";
                $body .= "Ship to: {$ship_street}, {$ship_city}, {$ship_state} {$ship_zip}\n\n";
                $body .= "Thanks,\n{$siteName}\n";

                $headers  = "From: noreply@rit.edu\r\n";
                $headers .= "Reply-To: noreply@rit.edu\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                @mail($to, $subject, $body, $headers);
            }

            unset($_SESSION['cart']);
            header("Location: {$basepath}pages/order_confirmation.php?order_id={$order_id}");
            exit;

        } catch (RuntimeException $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Checkout failed: " . $e->getMessage());
            $error = "Sorry, something went wrong while placing your order. Please try again.";
        }
    }
}

$pageTitle = 'Checkout | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Checkout</h1>

    <?php if ($error): ?>
        <p style="color:#c00; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <h2>Order Summary</h2>
    <table class="cart-table">
        <thead>
            <tr><th>Item</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
        <?php foreach ($cart_items as $ci): ?>
            <tr>
                <td><?= htmlspecialchars($ci['design_name']) ?></td>
                <td><?= htmlspecialchars($ci['color_name']) ?> / <?= htmlspecialchars($ci['size_label']) ?></td>
                <td><?= $ci['quantity'] ?></td>
                <td>$<?= number_format($ci['price'], 2) ?></td>
                <td>$<?= number_format($ci['line_total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="totals">
        <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
        <p>Tax (8.5%): $<?= number_format($tax, 2) ?></p>
        <p>Shipping: $<?= number_format($shipping, 2) ?></p>
        <p class="grand-total">Grand Total: $<?= number_format($grand_total, 2) ?></p>
    </div>

    <form method="POST" class="detail-form" style="max-width:600px;">
        <h2>Shipping Address</h2>
        <label>Street<br>
            <input type="text" name="ship_street" value="<?= htmlspecialchars($_POST['ship_street'] ?? '') ?>" required>
        </label><br>
        <label>City<br>
            <input type="text" name="ship_city" value="<?= htmlspecialchars($_POST['ship_city'] ?? '') ?>" required>
        </label><br>
        <label>State (2 letters)<br>
            <input type="text" name="ship_state" maxlength="2" value="<?= htmlspecialchars($_POST['ship_state'] ?? '') ?>" required>
        </label><br>
        <label>Zip<br>
            <input type="text" name="ship_zip" value="<?= htmlspecialchars($_POST['ship_zip'] ?? '') ?>" required>
        </label><br><br>

        <label>
            <input type="checkbox" name="billing_same" value="1" <?= isset($_POST['billing_same']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : '' ?>>
            Billing address is the same as shipping
        </label>

        <h2>Billing Address</h2>
        <p style="color:#666; font-size:0.9em;">Fill out only if different from shipping (uncheck box above).</p>
        <label>Street<br>
            <input type="text" name="bill_street" value="<?= htmlspecialchars($_POST['bill_street'] ?? '') ?>">
        </label><br>
        <label>City<br>
            <input type="text" name="bill_city" value="<?= htmlspecialchars($_POST['bill_city'] ?? '') ?>">
        </label><br>
        <label>State (2 letters)<br>
            <input type="text" name="bill_state" maxlength="2" value="<?= htmlspecialchars($_POST['bill_state'] ?? '') ?>">
        </label><br>
        <label>Zip<br>
            <input type="text" name="bill_zip" value="<?= htmlspecialchars($_POST['bill_zip'] ?? '') ?>">
        </label><br><br>

        <h2>Payment (Mock)</h2>
        <label>Card Type<br>
            <select name="card_type" required>
                <option value="">-- Select --</option>
                <?php foreach (['Visa', 'Mastercard', 'Amex', 'Discover'] as $ct): ?>
                    <option value="<?= $ct ?>" <?= ($_POST['card_type'] ?? '') === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Card Number<br>
            <input type="text" name="card_number" inputmode="numeric" maxlength="19" placeholder="1234 5678 9012 3456" required>
        </label><br><br>

        <button type="submit" class="button">Place Order ($<?= number_format($grand_total, 2) ?>)</button>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
