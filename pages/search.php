<?php
require_once '../database/config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($query)) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, 
               COALESCE(AVG(r.rating), 0) AS avg_rating, 
               COUNT(r.id) AS review_count
        FROM products p
        LEFT JOIN reviews r ON p.id = r.product_id
        WHERE p.name LIKE ? OR p.color LIKE ?
        GROUP BY p.id
    ");
    $like = '%' . $query . '%';
    $stmt->execute([$like, $like]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = "Search Results";
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
    <style>
        .search-page { padding: 80px 0; background: var(--bg); }
        .search-form { max-width: 600px; margin: 0 auto 40px; display: flex; gap: 12px; }
        .search-form input { flex: 1; padding: 14px 20px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; font-size: 14px; outline: none; }
        .search-form input:focus { border-color: var(--accent); }
        .search-form button { padding: 14px 30px; }
        .search-results-count { color: var(--muted); text-align: center; margin-bottom: 40px; }
        .search-empty { text-align: center; color: var(--muted); font-size: 18px; padding: 60px 0; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .product-card { background: #0e0e0e; border-radius: var(--radius); overflow: hidden; transition: transform .3s, box-shadow .3s; border: 1px solid var(--line); }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.6); border-color: var(--accent); }
        .product-media { position: relative; aspect-ratio: 4/3.2; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #000; }
        .product-media img { width: 100%; height: 100%; object-fit: cover; }
        .product-badge { position: absolute; top: 16px; left: 16px; background: var(--accent); color: #000; padding: 4px 12px; font-size: 10px; font-weight: 700; letter-spacing: .2em; border-radius: 4px; text-transform: uppercase; }
        .product-info { padding: 20px 24px 24px; background: #000; }
        .product-color { color: var(--muted); font-size: 12px; margin: 0 0 4px; }
        .product-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .product-name { font-family: var(--font-display); font-size: 22px; letter-spacing: .04em; font-weight: 400; margin: 0; text-transform: uppercase; color: #fff; }
        .product-price { color: var(--accent); font-weight: 600; font-size: 16px; }
        .product-rating { display: flex; align-items: center; gap: 4px; color: #fff; }
        .star svg { width: 14px; height: 14px; display: block; }
        .star.is-filled { color: #fff; }
        .star:not(.is-filled) { color: rgba(255,255,255,.4); }
        .reviews { font-size: 12px; color: var(--muted); margin-left: 8px; }
        .product-actions { margin-top: 16px; }
        .btn--small { padding: 8px 16px; font-size: 10px; }
        @media (max-width: 640px) {
            .search-form { flex-direction: column; }
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="search-page">
    <div class="container">
        <h1 class="section-title" style="margin-bottom: 20px;">SEARCH</h1>

        <form class="search-form" action="search.php" method="get">
            <input type="text" name="q" placeholder="Search for shoes..." value="<?= e($query) ?>" required>
            <button type="submit" class="btn btn--primary">SEARCH <?= icon('arrow') ?></button>
        </form>

        <?php if (!empty($query)): ?>
            <p class="search-results-count">
                <?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> found for “<?= e($query) ?>”
            </p>
        <?php endif; ?>

        <?php if (empty($query)): ?>
            <div class="search-empty">
                <p>Enter a keyword to find your perfect pair.</p>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="search-empty">
                <p>No products found for “<?= e($query) ?>”. Try another keyword.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($results as $p): ?>
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
                            <div class="product-rating">
                                <?php
                                $avg = round($p['avg_rating'], 1);
                                $count = (int)$p['review_count'];
                                if ($count > 0) {
                                    echo star_row($avg);
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
                                <a href="/stride/add_to_cart.php?id=<?= e($p['id']) ?>&action=add" class="btn btn--primary btn--small">ADD TO CART</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>