<?php
// Fetch products with average rating and review count
try {
    $pdo = getConnection();
    $stmt = $pdo->query("
        SELECT p.*, 
               COALESCE(AVG(r.rating), 0) AS avg_rating, 
               COUNT(r.id) AS review_count
        FROM products p
        LEFT JOIN reviews r ON p.id = r.product_id
        GROUP BY p.id
        ORDER BY p.id DESC LIMIT 3
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>
<section class="products" id="shop">
    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p style="color: var(--muted); padding: 40px; text-align: center; grid-column: 1 / -1;">No products yet.</p>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <article class="product-card">
                    <div class="product-media <?= e($p['bg'] ?? 'bg-black') ?>">
                        <?php if (!empty($p['badge'])): ?>
                            <span class="product-badge"><?= e($p['badge']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                            <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
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
                        <div class="product-rating">
                            <?php
                            $avg = round($p['avg_rating'], 1);
                            $count = (int)$p['review_count'];
                            if ($count > 0) {
                                echo star_row($avg); // assumes star_row accepts float and shows filled stars proportionally
                                echo ' <span class="reviews">(' . $count . ')</span>';
                            } else {
                                echo '<span class="reviews">No reviews</span>';
                            }
                            ?>
                        </div>
                        <p style="color: var(--muted); font-size: 12px; margin-top: 6px;">
                            <?= $p['quantity'] ?? 0 ?> in stock
                        </p>
                        <div class="product-actions" style="margin-top: 16px;">
                            <a href="product.php?id=<?= e($p['id']) ?>" class="btn btn--primary" style="padding: 10px 20px; font-size: 10px;">VIEW DETAILS</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>  