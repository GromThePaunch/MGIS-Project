<?php
require __DIR__ . '/../includes/config.php';
$pageTitle = 'Profile | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Customer Profile</h1>
    <p>Use this page for account details, billing and shipping addresses, mock payment information, order history, and password reset actions.</p>
    <ul>
        <li>Email: demo@example.com</li>
        <li>Billing Address: 123 Demo Street, Rochester, NY 14623</li>
        <li>Shipping Address: 123 Demo Street, Rochester, NY 14623</li>
        <li>Mock Payment: Visa ending in 4242</li>
        <li>Recent Order: Order #1001, total $58.73</li>
    </ul>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
