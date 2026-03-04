<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';
$pageTitle = 'Catalog | ' . $siteName;
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">Spring 2026 Team Project</p>
        <h1>Build a catalog that already matches the assignment requirements.</h1>
        <p>Start here with nine designs, shared navigation, sale messaging, and item links you can extend into real database-backed views.</p>
    </div>
    <aside class="alert-card">
        <h2>Store Alerts</h2>
        <p>Flat-rate shipping is scaffolded in code and sales tax is set to 8.5%.</p>
        <p>Inventory must be enforced per design, color, and size variant.</p>
    </aside>
</section>
<section class="catalog-grid">
    <?php foreach ($products as $product): ?>
        <article class="product-card">
            <span class="badge"><?= htmlspecialchars($product['badge']) ?></span>
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p>Single-color print on a standard tee with four color choices and sizes S through 2XL.</p>
            <p class="price">$<?= number_format($product['price'], 2) ?></p>
            <a class="button" href="/pages/product.php?id=<?= urlencode((string) $product['id']) ?>">View Item</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
