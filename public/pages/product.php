<?php
// =============================================
// PRODUCT DETAIL PAGE - Database Connected + Session Cart
// =============================================
require __DIR__ . '/../includes/init.php';   // ← Changed to init.php

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// 1. Fetch the specific design
$stmt = $pdo->prepare("
    SELECT 
        design_id AS id,
        name,
        description,
        price,
        image_base_url
    FROM designs 
    WHERE design_id = ? AND is_active = TRUE
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: /index.php");
    exit;
}

$pageTitle = htmlspecialchars($product['name']) . ' | ' . $siteName;
require __DIR__ . '/../includes/header.php';

// 2. Fetch colors and sizes (your existing code - unchanged)
$stmt = $pdo->query("
    SELECT color_id, color_name, hex_code 
    FROM colors 
    ORDER BY color_name
");
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT size_id, size_label 
    FROM sizes 
    ORDER BY sort_order
");
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Pre-load inventory for this design
$stmt = $pdo->prepare("
    SELECT 
        i.color_id,
        i.size_id,
        i.quantity
    FROM inventory i
    WHERE i.design_id = ?
");
$stmt->execute([$productId]);
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stockData = [];
foreach ($inventory as $item) {
    $key = $item['color_id'] . '_' . $item['size_id'];
    $stockData[$key] = (int)$item['quantity'];
}
?>

<section class="detail-layout">
    <div class="preview-panel">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        
        <div class="shirt-mockup" style="background: #f8f1e3; padding: 40px; text-align: center; border: 2px dashed #ccc; margin-bottom: 20px;">
            <p style="margin:0; font-size: 1.1em; color:#666;">
                🧥 Real shirt mockup with design will go here<br>
                (image_base_url: <?= htmlspecialchars($product['image_base_url']) ?>)
            </p>
        </div>

        <p><?= htmlspecialchars($product['description'] ?? 'Premium screen-printed T-shirt.') ?></p>

        <h3>Available Colors</h3>
        <div class="color-swatches">
            <?php foreach ($colors as $color): ?>
                <span class="swatch" 
                      title="<?= htmlspecialchars($color['color_name']) ?>" 
                      style="background: <?= htmlspecialchars($color['hex_code']) ?>;"
                      data-color-id="<?= $color['color_id'] ?>"
                      onclick="selectColor(this)"></span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Updated Form -->
    <?php if (isset($_SESSION['success'])): ?>
        <div style="background:#d4edda; color:#155724; padding:12px; margin:15px 0; border-radius:4px;">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="background:#f8d7da; color:#721c24; padding:12px; margin:15px 0; border-radius:4px;">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?> 
    <form id="addToCartForm" class="detail-form" method="POST" action="<?= $basepath ?>pages/add_to_cart.php">
        <input type="hidden" name="design_id" value="<?= $product['id'] ?>">

        <label>
            Color
            <select name="color_id" id="colorSelect" required onchange="updateAvailableSizes()">
                <?php foreach ($colors as $color): ?>
                    <option value="<?= $color['color_id'] ?>">
                        <?= htmlspecialchars($color['color_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Size
            <select name="size_id" id="sizeSelect" required>
                <?php foreach ($sizes as $size): ?>
                    <option value="<?= $size['size_id'] ?>">
                        <?= htmlspecialchars($size['size_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Quantity
            <input type="number" name="quantity" id="quantity" min="1" max="10" value="1">
        </label>

        <p class="price">$<?= number_format($product['price'], 2) ?></p>
        
        <button class="button" type="submit">Add to Cart</button>
        
        <p class="stock-note" id="stockNote"></p>
        
        <small>This product page is now fully connected to the database.</small>
    </form>
</section>

<script>
// Stock data from PHP → JavaScript (your existing JS - unchanged)
const stockData = <?= json_encode($stockData) ?>;

function updateAvailableSizes() {
    const colorId = parseInt(document.getElementById('colorSelect').value);
    const sizeSelect = document.getElementById('sizeSelect');
    const stockNote = document.getElementById('stockNote');
    
    let hasStock = false;
    
    Array.from(sizeSelect.options).forEach(option => {
        const sizeId = parseInt(option.value);
        const key = colorId + '_' + sizeId;
        const qty = stockData[key] || 0;
        
        if (qty > 0) {
            option.disabled = false;
            option.style.color = '#000';
            hasStock = true;
        } else {
            option.disabled = true;
            option.style.color = '#999';
        }
    });
    
    if (!sizeSelect.options[sizeSelect.selectedIndex] || sizeSelect.options[sizeSelect.selectedIndex].disabled) {
        for (let i = 0; i < sizeSelect.options.length; i++) {
            if (!sizeSelect.options[i].disabled) {
                sizeSelect.selectedIndex = i;
                break;
            }
        }
    }
    
    stockNote.textContent = hasStock ? '✅ In stock' : '❌ Out of stock for selected color/size';
    stockNote.style.color = hasStock ? 'green' : 'red';
}

function selectColor(el) {
    document.querySelectorAll('.swatch').forEach(s => s.style.border = '2px solid transparent');
    el.style.border = '2px solid #000';
    document.getElementById('colorSelect').value = el.dataset.colorId;
    updateAvailableSizes();
}

window.onload = () => {
    updateAvailableSizes();
};
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>