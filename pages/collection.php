<?php
require_once '../database/config.php';

$pageTitle = "Collections";

$collections = [
    [
        'name'        => 'VELOCITY 01',
        'category'    => 'RUNNING',
        'description' => 'Ultra-light performance for everyday miles.',
        'price'       => 160,
        'image'       => '/stride/image/urban-collection.png',
        'bg'          => 'bg-white',
        'link'        => '/stride/pages/shop.php',
    ],
    [
        'name'        => 'AERO MAX',
        'category'    => 'RACING',
        'description' => 'Maximum energy return. Minimum weight.',
        'price'       => 210,
        'image'       => '/stride/image/performance-collection.png',
        'bg'          => 'bg-cream',
        'link'        => '/stride/pages/shop.php',
    ],
    [
        'name'        => 'STREETFORM',
        'category'    => 'LIFESTYLE',
        'description' => 'Performance comfort. Everyday style.',
        'price'       => 140,
        'image'       => '/stride/image/heritage-collection.png',
        'bg'          => 'bg-light',
        'link'        => '/stride/pages/shop.php',
    ],
];

include '../includes/header.php';
?>

<!-- ============ HERO ============ -->
<section class="coll-hero">
    <div class="coll-hero-inner">

        <div class="coll-hero-copy">
            <span class="coll-eyebrow">SPRING / SUMMER <?= date('Y') ?></span>
            <h1 class="coll-title">MOVE WITHOUT<br><span class="outline">LIMITS</span></h1>
            <p class="coll-lead">Engineered for every kind of motion.</p>
            <a href="/stride/pages/shop.php" class="coll-cta">
                SHOP THE DROP
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                </svg>
            </a>
        </div>

        <div class="coll-hero-visual">
            <img src="/stride/image/collectionpage.png" alt="StrideX flagship sneaker">
        </div>

    </div>
</section>

<!-- ============ 3-COLUMN FEATURED PRODUCTS ============ -->
<section class="coll-featured">
    <div class="coll-featured-grid">

        <?php foreach ($collections as $c): ?>
        <article class="coll-feature">
            <div class="coll-feature-text">
                <span class="coll-cat"><?= e($c['category']) ?></span>
                <h3 class="coll-name"><?= e($c['name']) ?></h3>
                <p class="coll-desc"><?= e($c['description']) ?></p>
                <div class="coll-price">$<?= number_format($c['price'], 0) ?></div>
                <a href="<?= e($c['link']) ?>" class="coll-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                    </svg>
                </a>
            </div>

            <div class="coll-feature-img">
                <img src="<?= e($c['image']) ?>" alt="<?= e($c['name']) ?>">
            </div>
        </article>
        <?php endforeach; ?>

    </div>
</section>

<!-- ============ BUILT FOR THE WAY YOU MOVE ============ -->
<section class="coll-why">
    <div class="coll-why-inner">

        <div class="coll-why-heading">
            <span class="coll-dash"></span>
            <h2>BUILT FOR THE<br>WAY YOU MOVE</h2>
        </div>

        <div class="coll-why-features">

            <div class="coll-why-item">
                <div class="coll-why-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M12 2C8 6 8 10 12 14s4 8 0 12"/>
                    </svg>
                </div>
                <h4>LIGHTNESS</h4>
                <p>Advanced materials and intelligent construction keep you moving with less weight.</p>
            </div>

            <div class="coll-why-item">
                <div class="coll-why-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="6" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="18" cy="6" r="2"/>
                        <circle cx="6" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="18" cy="12" r="2"/>
                        <circle cx="6" cy="18" r="2"/><circle cx="12" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>
                    </svg>
                </div>
                <h4>GRIP</h4>
                <p>Multi-surface traction keeps you stable, confident and in control — in all conditions.</p>
            </div>

            <div class="coll-why-item">
                <div class="coll-why-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>
                    </svg>
                </div>
                <h4>RESPONSE</h4>
                <p>Energy-return technology turns every step into forward motion.</p>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>