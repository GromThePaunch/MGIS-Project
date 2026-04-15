<?php
// includes/header.php
// init.php should already be required by the page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $siteName) ?></title>
    <link rel="stylesheet" href="<?= $basepath ?>assets/css/styles.css">
</head>
<body>
<header class="site-header">
    <div class="brand-block">
        <a class="brand" href="<?= $basepath ?>index.php"><?= htmlspecialchars($siteName) ?></a>
        <p class="tagline">Screen-printed shirts with inventory-aware ordering.</p>
    </div>
    
    <nav class="site-nav" aria-label="Primary Navigation">
        <a href="<?= $basepath ?>index.php">Catalog</a>
        <a href="<?= $basepath ?>pages/about.php">About</a>
        <a href="<?= $basepath ?>pages/contact.php">Contact</a>
        
        <a href="<?= $basepath ?>pages/cart.php" class="cart-link">
            Cart 
            <?php if (!empty($_SESSION['cart'])): ?>
                <span class="cart-count">(<?= array_sum($_SESSION['cart']) ?>)</span>
            <?php endif; ?>
        </a>

        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="<?= $basepath ?>pages/profile.php">Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></a>
            <a href="<?= $basepath ?>pages/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= $basepath ?>pages/login.php">Login</a>
            <a href="<?= $basepath ?>pages/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page-shell">