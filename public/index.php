<?php
require __DIR__ . '/includes/init.php';
$pageTitle = 'Catalog | ' . $siteName;

// Fetch active designs (products) from the database
$stmt = $pdo->prepare("
    SELECT 
        d.design_id AS id,
        d.name,
        d.description,
        d.price,
        d.image_base_url,
        d.is_active,
        CASE 
            WHEN d.price < 20 THEN 'Sale' 
            ELSE '' 
        END AS badge
    FROM designs d
    WHERE d.is_active = TRUE
    ORDER BY d.name ASC
");

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no products yet, provide fallback scaffold data (for development)
if (empty($products)) {
    $products = [
        [
            'id' => 1,
            'name' => 'MGIS Logo Tee',
            'description' => 'Single-color print on a standard tee with four color choices and sizes S through 2XL.',
            'price' => 19.99,
            'badge' => 'New'
        ],
    ];
}

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
            <a class="button" href="pages/product.php?id=<?= urlencode((string) $product['id']) ?>">View Item</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
