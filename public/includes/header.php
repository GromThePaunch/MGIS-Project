<?php
if (!isset($siteName)) {
    require __DIR__ . '/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $siteName) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header class="site-header">
    <div class="brand-block">
        <a class="brand" href="/index.php"><?= htmlspecialchars($siteName) ?></a>
        <p class="tagline">Screen-printed shirts with inventory-aware ordering.</p>
    </div>
    <nav class="site-nav" aria-label="Primary Navigation">
        <a href="/index.php">Catalog</a>
        <a href="/pages/about.php">About</a>
        <a href="/pages/contact.php">Contact</a>
        <a href="/pages/cart.php">Cart</a>
        <a href="/pages/login.php">Login</a>
        <a href="/pages/profile.php">Profile</a>
    </nav>
</header>
<main class="page-shell">
