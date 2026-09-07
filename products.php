<!-- ============ FEATURED PRODUCTS ============ -->
<section class="products" id="shop">
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <article class="product-card">
                <div class="product-media <?= e($p['bg']) ?>">
                    <?php if (!empty($p['badge'])): ?><span class="product-badge"><?= e($p['badge']) ?></span><?php endif; ?>
                    <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                    <div class="swatches">
                        <?php foreach ($p['swatches'] as $i => $swatch): ?><span class="swatch <?= $i === 0 ? 'is-active' : '' ?>" style="background:<?= e($swatch) ?>"></span><?php endforeach; ?>
                    </div>
                </div>
                <div class="product-info">
                    <p class="product-color"><?= e($p['color']) ?></p>
                    <div class="product-row"><h3 class="product-name"><?= e($p['name']) ?></h3><span class="product-price">$<?= e($p['price']) ?></span></div>
                    <div class="product-rating"><?= star_row((int)$p['rating']) ?><span class="reviews">(<?= (int)$p['reviews'] ?>)</span></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>