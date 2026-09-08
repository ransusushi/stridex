<?php
require_once '../database/config.php';
requireAdmin();

$pdo = getConnection();

// Count products
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$productCount = $stmt->fetchColumn();

// Count orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$orderCount = $stmt->fetchColumn();

// Count users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();

// Get all products with stock and image
$stmt = $pdo->query("SELECT id, name, image, quantity FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle quick stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = (int)$_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];
    if ($product_id > 0 && $new_quantity >= 0) {
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard – StrideX</title>
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 30px; }
        .admin-card { background: #0e0e0e; padding: 30px; border-radius: var(--radius); border: 1px solid var(--line); text-align: center; }
        .admin-card h3 { font-family: var(--font-display); font-size: 48px; color: var(--accent); margin: 0; }
        .admin-card p { color: var(--muted); font-size: 14px; text-transform: uppercase; letter-spacing: .2em; }
        .admin-nav { display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
        .admin-nav a { background: #0e0e0e; padding: 12px 24px; border-radius: var(--radius); border: 1px solid var(--line); color: var(--text); text-decoration: none; transition: all .25s; }
        .admin-nav a:hover { border-color: var(--accent); color: var(--accent); }
        .stock-table { width: 100%; border-collapse: collapse; margin-top: 40px; color: var(--text); }
        .stock-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .stock-table td { padding: 12px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .stock-table .product-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; background: #111; }
        .stock-table .low-stock { color: var(--accent); font-weight: 600; }
        .stock-table form { display: flex; gap: 8px; align-items: center; }
        .stock-table input[type="number"] { width: 60px; padding: 4px 8px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; text-align: center; }
        .stock-table .btn--small { padding: 4px 12px; font-size: 10px; }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="../index.php" class="logo"><img src="/stride/image/stridex-logo.png" alt="StrideX" style="height:50px;width:auto;"></a>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Admin Dashboard</h1>
        <div class="admin-nav">
            <a href="products.php">Manage Products</a>
            <a href="orders.php">Manage Orders</a>
            <a href="/stride/auth/logout.php">Logout</a>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <h3><?= $productCount ?></h3>
                <p>Products</p>
            </div>
            <div class="admin-card">
                <h3><?= $orderCount ?></h3>
                <p>Orders</p>
            </div>
            <div class="admin-card">
                <h3><?= $userCount ?></h3>
                <p>Users</p>
            </div>
        </div>

        <!-- Stock Overview with Images -->
        <h2 style="font-family: var(--font-display); text-transform: uppercase; margin-top: 60px; margin-bottom: 20px;">Stock Overview</h2>
        <?php if (empty($products)): ?>
            <p>No products yet.</p>
        <?php else: ?>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Update Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['image']) && file_exists('../' . $p['image'])): ?>
                                    <img src="../<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" class="product-thumb">
                                <?php else: ?>
                                    <div style="width:50px; height:50px; background:#111; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#666; font-size:10px;">No img</div>
                                <?php endif; ?>
                            </td>
                            <td><?= e($p['name']) ?></td>
                            <td class="<?= $p['quantity'] <= 5 ? 'low-stock' : '' ?>">
                                <?= $p['quantity'] ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $p['quantity'] ?>" min="0">
                                    <button type="submit" name="update_stock" class="btn btn--primary btn--small">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

</body>
</html>