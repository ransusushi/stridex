<?php
$site = ['brand' => 'StrideX', 'tagline' => 'MOVE IN STYLE. LIVE WITHOUT LIMITS.', 'year' => date('Y')];
$navLinks = [
    ['label' => 'Home',        'href' => 'stridex.php'],
    ['label' => 'Shop',        'href' => '#shop'],
    ['label' => 'Men',         'href' => 'men.php'],
    ['label' => 'Women',       'href' => 'women.php'],
    ['label' => 'Collections', 'href' => '#collections'],
    ['label' => 'About Us',    'href' => '#about'],
];

// WOMEN'S PRODUCTS
$products = [
    [
        'name' => 'STRIDEX LUXE', 'color' => 'Rose Gold', 'price' => '99.99', 'rating' => 5, 'reviews' => 189,
        'image' => 'women/newdrop.png', 'bg' => 'bg-red', 'badge' => 'NEW DROP', 'swatches'=> ['#ff5a1f', '#fff', '#111'],
    ],
    [
        'name' => 'STRIDEX AURA', 'color' => 'Pearl White', 'price' => '84.99', 'rating' => 4, 'reviews' => 160,
        'image' => 'women/bestseller.png', 'bg' => 'bg-black', 'badge' => 'BESTSELLER', 'swatches'=> ['#fff', '#111', '#e8e8e8'],
    ],
    [
        'name' => 'STRIDEX FLOW', 'color' => 'Monochrome', 'price' => '89.99', 'rating' => 5, 'reviews' => 210,
        'image' => 'women/limited.png', 'bg' => 'bg-gray', 'badge' => 'LIMITED', 'swatches'=> ['#111', '#fff', '#e8e8e8'],
    ],
];

function icon(string $name): string {
    $icons = ['search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>', 'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>', 'bag' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"/><path d="M9 7a3 3 0 1 1 6 0"/></svg>'];
    return $icons[$name] ?? '';
}
function star_row(int $rating, int $max = 5): string {
    $html = '';
    for ($i = 1; $i <= $max; $i++) {
        $filled = $i <= $rating;
        $html .= '<span class="star ' . ($filled ? 'is-filled' : '') . '">' . ($filled ? '<svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18l-5.9 3 1.2-6.5L2.5 9.9 9.1 9Z"/></svg>' : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="m12 3 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18l-5.9 3 1.2-6.5L2.5 9.9 9.1 9Z"/></svg>') . '</span>';
    }
    return $html;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Women's Collection — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stridex.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="stride.php" class="logo"><img src="images/stridex-logo.png" alt="<?= e($site['brand']) ?>" style="height:50px;width:auto;"></a>
        <nav class="main-nav" aria-label="Primary">
            <ul>
                <?php foreach ($navLinks as $link): ?>
                    <li><a href="<?= e($link['href']) ?>"><?= e(strtoupper($link['label'])) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="header-actions">
            <button class="icon-btn" aria-label="Search"><?= icon('search') ?></button>
            <button class="icon-btn" aria-label="Account"><?= icon('user') ?></button>
            <button class="icon-btn cart" aria-label="Cart"><?= icon('bag') ?><span class="cart-count">0</span></button>
        </div>
    </div>
</header>

<section class="shop-page-header" id="shop">
    <div class="container">
        <span class="eyebrow center">THE COLLECTION</span>
        <h1 class="section-title center">WOMEN'S SHOES</h1>
    </div>
</section>

<section class="products">
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

</body>
</html>