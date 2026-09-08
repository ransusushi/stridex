
<?php
require_once '../database/config.php'; 
$pageTitle = "Men's Collection";
$pageHeading = "MEN'S SHOES";

$menProducts = [
    [
        'id'       => 'm1',  
        'name'     => 'STRIDEX DRIVE',
        'color'    => 'Midnight Red',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => '../men/bestseller.png',
        'bg'       => 'bg-red',
        'badge'    => 'BESTSELLER',
    ],
    [
        'id'       => 'm2',
        'name'     => 'STRIDEX STORM',
        'color'    => 'Cloud White',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => '../men/newdrop.png',
        'bg'       => 'bg-black',
        'badge'    => 'NEW DROP',
    ],
    [
        'id'       => 'm3',
        'name'     => 'STRIDEX VORTEX',
        'color'    => 'Carbon Gray',
        'price'    => '89.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => '../men/limited.png',
        'bg'       => 'bg-gray',
        'badge'    => 'LIMITED',
    ],
];
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

<?php include '../header.php'; ?>

<section class="shop-page-header" style="padding: 60px 0 20px;">
    <div class="container">
        <span class="eyebrow center">THE COLLECTION</span>
        <h1 class="section-title center"><?= e($pageHeading) ?></h1>
    </div>
</section>

<section class="products">
    <div class="product-grid">
        <?php foreach ($menProducts as $p): ?>
            <article class="product-card">
                <div class="product-media <?= e($p['bg']) ?>">
                    <?php if (!empty($p['badge'])): ?>
                        <span class="product-badge"><?= e($p['badge']) ?></span>
                    <?php endif; ?>
                    <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                </div>
                <div class="product-info">
                    <p style="color: var(--muted); font-size: 12px; margin-top: 6px;">
    <?= $p['quantity'] ?? 0 ?> in stock
</p>
                    <p class="product-color"><?= e($p['color']) ?></p>
                    <div class="product-row">
                        <h3 class="product-name"><?= e($p['name']) ?></h3>
                        <span class="product-price">$<?= e($p['price']) ?></span>
                    </div>
                    <div class="product-rating">
                        <?= star_row((int)$p['rating']) ?>
                        <span class="reviews">(<?= (int)$p['reviews'] ?>)</span>
                    </div>
                    <!-- ADD TO CART BUTTON -->
                    <div class="product-actions" style="margin-top: 16px;">
                        <a href="../add_to_cart.php?id=<?= e($p['id']) ?>&action=add" class="btn btn--primary" style="padding: 10px 20px; font-size: 10px;">ADD TO CART</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../footer.php'; ?>
</body>
</html>