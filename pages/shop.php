<?php
require_once '../database/config.php';

$pageTitle = "Shop All";
$pageHeading = "ALL PRODUCTS";

// Load all products — DB first, cache as fallback
$allProducts = loadProducts();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="shop-page-header" style="padding: 60px 0 20px;">
    <div class="container">
        <span class="eyebrow center">THE COLLECTION</span>
        <h1 class="section-title center"><?= e($pageHeading) ?></h1>
    </div>
</section>

<section class="products" style="padding-top: 20px;">
    <div class="product-grid">
        <?php if (empty($allProducts)): ?>
            <p style="color: var(--muted); text-align: center; grid-column: 1 / -1;">No products yet.</p>
        <?php else: ?>
            <?php foreach ($allProducts as $p): ?>
                <article class="product-card">
                    <div class="product-media <?= e($p['bg'] ?? 'bg-black') ?>">
                        <?php if (!empty($p['badge'])): ?>
                            <span class="product-badge"><?= e($p['badge']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($p['image']) && file_exists('../' . $p['image'])): ?>
                            <img src="../<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                        <?php else: ?>
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#111; color:#666; font-size:14px;">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <p class="product-color"><?= e($p['color'] ?? 'N/A') ?></p>
                        <div class="product-row">
                            <h3 class="product-name"><?= e($p['name']) ?></h3>
                            <span class="product-price">$<?= e($p['price']) ?></span>
                        </div>
                        <p style="color: var(--muted); font-size: 12px; margin-top: 6px;">
                            <?= $p['quantity'] ?? 0 ?> in stock
                        </p>
                        <div class="product-actions" style="margin-top: 16px;">
                            <a href="/stride/add_to_cart.php?id=<?= e($p['id']) ?>&action=add" class="btn btn--primary" style="padding: 10px 20px; font-size: 10px;">ADD TO CART</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>