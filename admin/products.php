<?php
require_once '../database/config.php';
requireAdmin();

$pdo = getConnection();

// Handle quick stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = (int)$_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];
    if ($product_id > 0 && $new_quantity >= 0) {
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);
        header('Location: products.php');
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Products – Admin</title>
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-table { width: 100%; border-collapse: collapse; color: var(--text); }
        .admin-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .admin-table td { padding: 12px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .admin-table .low-stock { color: var(--accent); font-weight: 600; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn--small { padding: 6px 12px; font-size: 10px; }
        .btn--danger { background: #ff5a1f; color: #fff; }
        .btn--danger:hover { background: #e04a10; }
        .stock-form { display: flex; gap: 6px; align-items: center; }
        .stock-form input[type="number"] { width: 60px; padding: 4px 8px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; text-align: center; }
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
                <li><a href="../login/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Products</h1>
        <div style="margin-bottom: 20px;">
            <a href="product_add.php" class="btn btn--primary">Add New Product</a>
        </div>

        <?php if (empty($products)): ?>
            <p>No products yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Update Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= e($p['name']) ?></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td class="<?= $p['quantity'] <= 5 ? 'low-stock' : '' ?>">
                                <?= $p['quantity'] ?>
                                <?php if ($p['quantity'] <= 5): ?>
                                    ⚠️
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" class="stock-form">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $p['quantity'] ?>" min="0">
                                    <button type="submit" name="update_stock" class="btn btn--primary btn--small">Update</button>
                                </form>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn--primary btn--small">Edit</a>
                                    <a href="product_delete.php?id=<?= $p['id'] ?>" class="btn btn--danger btn--small" onclick="return confirm('Delete this product?')">Delete</a>
                                </div>
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