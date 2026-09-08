<?php
// Fetch products from database for homepage
$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 3");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="products" id="shop">
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <article class="product-card">
                <div class="product-media <?= e($p['bg']) ?>">
                    <?php if (!empty($p['badge'])): ?>
                        <span class="product-badge"><?= e($p['badge']) ?></span>
                    <?php endif; ?>
                    <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                </div>
                <div class="product-info">
                    <p class="product-color"><?= e($p['color']) ?></p>
                    <div class="product-row">
                        <h3 class="product-name"><?= e($p['name']) ?></h3>
                        <span class="product-price">$<?= e($p['price']) ?></span>
                    </div>
                    <div class="product-rating">
                        <?= star_row((int)$p['rating']) ?>
                        <span class="reviews">(<?= (int)$p['reviews'] ?>)</span>
                    </div>
                    <p style="color: var(--muted); font-size: 12px; margin-top: 6px;">
                        <?= $p['quantity'] ?? 0 ?> in stock
                    </p>
                    <div class="product-actions" style="margin-top: 16px;">
                        <a href="add_to_cart.php?id=<?= e($p['id']) ?>&action=add" class="btn btn--primary" style="padding: 10px 20px; font-size: 10px;">ADD TO CART</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>