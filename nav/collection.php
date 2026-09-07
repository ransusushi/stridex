<?php
require_once '../config.php';
$pageTitle = "Collections";
$pageHeading = "OUR COLLECTIONS";

$collections = [
    [
        'name'        => 'URBAN SERIES',
        'description' => 'Built for the city streets. Comfort meets style in every step.',
        'image'       => '../image/urban-collection.png',
        'badge'       => 'BESTSELLER',
        'link'        => '#shop',
        'bg'          => 'bg-red',
    ],
    [
        'name'        => 'PERFORMANCE PRO',
        'description' => 'Engineered for athletes who demand speed, agility, and durability.',
        'image'       => '../image/performance-collection.png',
        'badge'       => 'NEW DROP',
        'link'        => '#shop',
        'bg'          => 'bg-black',
    ],
    [
        'name'        => 'LIMITED EDITION',
        'description' => 'Exclusive designs crafted in small batches. Own a piece of history.',
        'image'       => '../image/limited-collection.png',
        'badge'       => 'LIMITED',
        'link'        => '#shop',
        'bg'          => 'bg-gray',
    ],
    [
        'name'        => 'HERITAGE CLASSICS',
        'description' => 'Timeless designs that never go out of style. Modern comfort, classic look.',
        'image'       => '../image/heritage-collection.png',
        'badge'       => 'CLASSIC',
        'link'        => '#shop',
        'bg'          => 'bg-dark',
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
    <link rel="stylesheet" href="../nav/collection.css">

</head>
<body>

<?php include '../header.php'; ?>

<!-- ============ COLLECTIONS HERO ============ -->
<section class="collections-hero" id="collections">
    <div class="container">
        <span class="eyebrow center">EXPLORE</span>
        <h1 class="section-title center">OUR COLLECTIONS</h1>
        <p class="collections-subtitle center">Discover our curated collections – each designed with purpose,<br>crafted for performance, and built to inspire.</p>
    </div>
</section>

<!-- ============ COLLECTIONS GRID ============ -->
<section class="collections-grid-section">
    <div class="container">
        <div class="collections-grid">
            <?php foreach ($collections as $c): ?>
                <a href="<?= e($c['link']) ?>" class="collection-card">
                    <div class="collection-card-media <?= e($c['bg']) ?>">
                        <?php if (!empty($c['badge'])): ?>
                            <span class="collection-badge"><?= e($c['badge']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($c['image']) && file_exists(__DIR__ . '/' . $c['image'])): ?>
                            <img src="<?= e($c['image']) ?>" alt="<?= e($c['name']) ?>">
                        <?php else: ?>
                            <div class="collection-card-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <circle cx="8.5" cy="10" r="1.5"/>
                                    <path d="M21 16l-5-5-4 4-3-3-6 6"/>
                                </svg>
                                <span class="collection-card-placeholder-label">Image coming soon</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="collection-card-info">
                        <h3 class="collection-card-title"><?= e($c['name']) ?></h3>
                        <p class="collection-card-desc"><?= e($c['description']) ?></p>
                        <span class="collection-card-link">EXPLORE COLLECTION →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<?php include '../footer.php'; ?>
</body>
</html>