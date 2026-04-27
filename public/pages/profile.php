<?php
require __DIR__ . '/../includes/init.php';

if (!isLoggedIn()) {
    header("Location: {$basepath}pages/login.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];

$stmt = $pdo->prepare(
    "SELECT customer_id, first_name, last_name, email, phone, created_at
     FROM customers WHERE customer_id = ?"
);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    session_destroy();
    header("Location: {$basepath}pages/login.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT address_id, address_type, street, city, state, zip
     FROM addresses WHERE customer_id = ?
     ORDER BY address_type"
);
$stmt->execute([$customer_id]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare(
    "SELECT order_id, grand_total, order_status, order_date
     FROM orders WHERE customer_id = ?
     ORDER BY order_date DESC
     LIMIT 5"
);
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Profile | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Welcome, <?= htmlspecialchars($customer['first_name']) ?></h1>

    <h2>Account Details</h2>
    <ul>
        <li>Name: <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></li>
        <li>Email: <?= htmlspecialchars($customer['email']) ?></li>
        <li>Phone: <?= htmlspecialchars($customer['phone'] ?? '—') ?></li>
        <li>Member since: <?= htmlspecialchars(date('M j, Y', strtotime($customer['created_at']))) ?></li>
    </ul>

    <h2>Addresses</h2>
    <?php if (empty($addresses)): ?>
        <p>No addresses on file.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($addresses as $a): ?>
                <li>
                    <strong><?= htmlspecialchars(ucfirst($a['address_type'])) ?>:</strong>
                    <?= htmlspecialchars($a['street']) ?>,
                    <?= htmlspecialchars($a['city']) ?>,
                    <?= htmlspecialchars($a['state']) ?>
                    <?= htmlspecialchars($a['zip']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Recent Orders</h2>
    <?php if (empty($orders)): ?>
        <p>You have no orders yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($orders as $o): ?>
                <li>
                    Order #<?= (int) $o['order_id'] ?> —
                    <?= htmlspecialchars(date('M j, Y', strtotime($o['order_date']))) ?> —
                    $<?= number_format((float) $o['grand_total'], 2) ?> —
                    <?= htmlspecialchars(ucfirst($o['order_status'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>
        <a href="<?= $basepath ?>pages/change_password.php" class="button">Change Password</a>
        <a href="<?= $basepath ?>pages/logout.php" class="button">Logout</a>
    </p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
