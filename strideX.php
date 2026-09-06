<?php
/**
 * StrideX — Full Landing Page with Footer Subscription
 */


$site = [
    'brand'   => 'StrideX',
    'tagline' => 'MOVE IN STYLE. LIVE WITHOUT LIMITS.',
    'year'    => date('Y'),
];


$navLinks = [
    ['label' => 'Home',        'href' => 'stridex.php'],
    ['label' => 'Shop',        'href' => '#shop'],
    ['label' => 'Men',         'href' => 'men.php'],
    ['label' => 'Women',       'href' => 'women.php'],
    ['label' => 'Collections', 'href' => '#collections'],
    ['label' => 'About Us',    'href' => '#about'],
];

// ---------- Featured products ----------
$products = [
    [
        'name'    => 'STRIDEX URBAN',
        'color'   => 'Midnight Red',
        'price'   => '79.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/bestseller.png',
        'bg'      => 'bg-red',
        'badge'   => 'BESTSELLER',
        'swatches'=> ['#c8102e', '#111', '#e8e8e8'],
    ],
    [
        'name'    => 'STRIDEX FLEX',
        'color'   => 'Cloud White',
        'price'   => '79.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/newdrop.png',
        'bg'      => 'bg-black',
        'badge'   => 'NEW DROP',
        'swatches'=> ['#ffffff', '#111', '#e8e8e8'],
    ],
    [
        'name'    => 'STRIDEX CORE',
        'color'   => 'Carbon Gray',
        'price'   => '89.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/limited.png',
        'bg'      => 'bg-gray',
        'badge'   => 'LIMITED',
        'swatches'=> ['#ff5a1f', '#111', '#e8e8e8'],
    ],
];

// ---------- Feature bullets ----------
$features = [
    ['icon'  => 'award', 'title' => 'PREMIUM QUALITY', 'text'  => 'High-grade materials for lasting comfort and durability.'],
    ['icon'  => 'bolt',  'title' => 'PERFORMANCE',     'text'  => 'Engineered for speed, agility, and stability on any surface.'],
    ['icon'  => 'box',   'title' => 'LIGHTWEIGHT',     'text'  => 'Move faster with precision-engineered, minimal weight design.'],
    ['icon'  => 'return','title' => 'EASY RETURNS',    'text'  => '30-day return policy for completely worry-free shopping.'],
];

// ---------- Footer ----------
$footerGroups = [
    'SHOP' => [
        ['label' => 'Men', 'href' => '#men'],
        ['label' => 'Women', 'href' => '#women'],
        ['label' => 'Collections', 'href' => '#collections'],
    ],
    'COMPANY' => [
        ['label' => 'About Us', 'href' => '#about'],
        ['label' => 'Our Mission', 'href' => '#mission'],
        ['label' => 'Why StrideX', 'href' => '#why'],
    ],
    'SUPPORT' => [
        ['label' => 'Contact', 'href' => '#contact'],
        ['label' => 'Shipping & Returns', 'href' => '#shipping'],
        ['label' => 'FAQ', 'href' => '#faq'],
    ],
    'FOLLOW US' => [
        ['label' => 'Instagram', 'href' => '#'],
        ['label' => 'Facebook', 'href' => '#'],
        ['label' => 'TikTok', 'href' => '#'],
    ],
];

// ---------- Helpers ----------
function icon(string $name): string {
    $icons = [
        'award'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="9" r="5"/><path d="M8.5 13 7 21l5-3 5 3-1.5-8"/></svg>',
        'bolt'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/></svg>',
        'box'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg>',
        'return' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>',
        'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
        'user'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
        'bag'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"/><path d="M9 7a3 3 0 1 1 6 0"/></svg>',
        'arrow'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>',
        'send'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>', // <-- Added for subscribe button
    ];
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
    <title><?= e($site['brand']) ?> — Built for the Streets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stridex.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="#home" class="logo"><img src="image/stridex-logo.png" alt="<?= e($site['brand']) ?>" style="height:50px;width:auto;"></a>
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

<!-- ============ HERO ============ -->
<section class="hero" id="home">
    <div class="container hero-inner">
        <div class="hero-copy">
            <span class="eyebrow">EVERY STEP COUNTS.</span>
            <h1 class="hero-title">BUILT<br><span class="outline">FOR THE</span><br>STREETS</h1>
            <p class="hero-lead">Performance. Comfort. Style. Every step you take,<br>we&rsquo;ve got your back.</p>
            <div class="hero-ctas">
                <a href="#shop" class="btn btn--primary">SHOP NOW <?= icon('arrow') ?></a>
                <a href="#collections" class="btn btn--ghost">EXPLORE COLLECTION</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><span class="stat-num">12K+</span><span class="stat-label">ATHLETES</span></div>
                <div class="stat"><span class="stat-num">98%</span><span class="stat-label">SATISFACTION</span></div>
                <div class="stat"><span class="stat-num">50+</span><span class="stat-label">STYLES</span></div>
            </div>
        </div>
    </div>
</section>

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

<!-- ============ MISSION ============ -->
<section class="mission" id="mission">
    <div class="mission-inner">
        <div class="mission-visual"><img src="image/mission-shoes.png" alt="StrideX flyknit sneaker"></div>
        <div class="mission-copy">
            <h2 class="section-title">OUR MISSION</h2><div class="rule"></div>
            <p>StrideX is committed to providing high-quality sports footwear that combines comfort, durability, and modern design. We aim to empower athletes, students, and active individuals to perform at their best by delivering shoes that support every step of their journey.</p>
        </div>
    </div>
</section>

<!-- ============ WHY STRIDEX ============ -->
<section class="why" id="why">
    <div class="container">
        <p class="eyebrow center">THE DIFFERENCE</p><h2 class="section-title center">WHY CHOOSE STRIDEX?</h2>
        <div class="feature-grid">
            <?php foreach ($features as $f): ?>
                <div class="feature"><div class="feature-icon"><?= icon($f['icon']) ?></div><h3 class="feature-title"><?= e($f['title']) ?></h3><p class="feature-text"><?= e($f['text']) ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CTA BANNER ============ -->
<section class="cta" id="about">
    <div class="container cta-inner">
        <div class="cta-copy">
            <span class="eyebrow">MOVE IN STYLE.</span><h2 class="cta-title">LIVE WITHOUT<br>LIMITS.</h2>
            <p class="cta-lead">StrideX is here to keep you moving with confidence,<br>comfort, and unstoppable energy.</p>
            <a href="#shop" class="btn btn--primary">JOIN THE MOVEMENT <?= icon('arrow') ?></a>
        </div>
        <div class="cta-visual" aria-hidden="true"><img src="image/stridex-cta-shoes.png" alt="StrideX sneaker"></div>
    </div>
</section>

<!-- ============ FOOTER (UPDATED with subscription) ============ -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand-custom">
            <img src="image/stridex-logo.png" alt="<?= e($site['brand']) ?>" class="footer-logo-img">
            <p class="footer-tag"><?= e($site['tagline']) ?></p>
            <!-- Add description -->
        </div>
        <div class="footer-links">
            <?php foreach ($footerGroups as $title => $links): ?>
                <div class="footer-col">
                    <h4 class="footer-heading"><?= e($title) ?></h4>
                    <ul>
                        <?php foreach ($links as $link): ?>
                            <li><a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
            <!-- NEW: GET THE DROP column -->
            <div class="footer-col footer-subscribe">
                <h4 class="footer-heading">GET THE DROP</h4>
                <form class="subscribe-form" action="#" method="post">
                    <input type="email" placeholder="your@email.com" required>
                    <button type="submit" aria-label="Subscribe"><?= icon('send') ?></button>
                </form>
            </div>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= e((string)$site['year']) ?> <?= e(strtoupper($site['brand'])) ?>. ALL RIGHTS RESERVED.</p>
        <p class="legal">
            <a href="#">PRIVACY</a>
            <span>&middot;</span>
            <a href="#">TERMS</a>
            <span>&middot;</span>
            <a href="#">COOKIES</a>
        </p>
    </div>
</footer>

</body>
</html>