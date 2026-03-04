<?php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/data.php';
$productId = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$product = $products[array_search($productId, array_column($products, 'id')) ?: 0];
$pageTitle = $product['name'] . ' | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="detail-layout">
    <div class="preview-panel">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p>Show the real shirt mockup here for each color variant.</p>
        <div class="color-swatches">
            <?php foreach ($shirtColors as $color): ?>
                <span class="swatch" title="<?= htmlspecialchars($color['name']) ?>" style="background: <?= htmlspecialchars($color['hex']) ?>;"></span>
            <?php endforeach; ?>
        </div>
    </div>
    <form class="detail-form">
        <label>
            Color
            <select name="color">
                <?php foreach ($shirtColors as $color): ?>
                    <option value="<?= htmlspecialchars($color['name']) ?>"><?= htmlspecialchars($color['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Size
            <select name="size">
                <?php foreach ($sizes as $size): ?>
                    <option value="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Quantity
            <input type="number" name="quantity" min="1" max="10" value="1">
        </label>
        <p class="price">$<?= number_format($product['price'], 2) ?></p>
        <button class="button" type="button">Add to Cart</button>
        <p>This page is scaffolded for color preview, size selection, quantity input, and add-to-cart wiring.</p>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
