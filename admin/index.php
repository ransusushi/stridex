<?php
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../auth/auth_validation.php';

// Now requireAdmin() is available
requireAdmin();

$pdo = getConnection();

// ---------- Basic Stats ----------
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$productCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();

// ---------- Revenue Stats ----------
$revenue = getRevenueStats($pdo);

// ---------- Stock Update Handler ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = (int) $_POST['product_id'];
    $new_quantity = (int) $_POST['quantity'];
    if ($product_id > 0 && $new_quantity >= 0) {
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);
        header('Location: /stride/admin/index.php');
        exit;
    }
}

// ---------- Get products for stock overview ----------
$stmt = $pdo->query("SELECT id, name, quantity FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard – StrideX</title>
    <link rel="stylesheet" href="/stride/css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-top: 30px; }
        .admin-card { background: #0e0e0e; padding: 30px; border-radius: var(--radius); border: 1px solid var(--line); text-align: center; }
        .admin-card h3 { font-family: var(--font-display); font-size: 48px; margin: 0; }
        .admin-card p { color: var(--muted); font-size: 14px; text-transform: uppercase; letter-spacing: .2em; }
        .admin-card .revenue { color: #4ade80; }
        .admin-card .orders { color: #60a5fa; }
        .admin-nav { display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
        .admin-nav a { background: #0e0e0e; padding: 12px 24px; border-radius: var(--radius); border: 1px solid var(--line); color: var(--text); text-decoration: none; transition: all .25s; }
        .admin-nav a:hover { border-color: var(--accent); color: var(--accent); }

        .stock-table { width: 100%; border-collapse: collapse; margin-top: 40px; color: var(--text); }
        .stock-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .stock-table td { padding: 12px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .stock-table .low-stock { color: var(--accent); font-weight: 600; }
        .stock-table form { display: flex; gap: 8px; align-items: center; }
        .stock-table input[type="number"] { width: 60px; padding: 4px 8px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; text-align: center; }
        .stock-table .btn--small { padding: 4px 12px; font-size: 10px; }

        .recent-orders { margin-top: 50px; }
        .recent-orders h2 { font-family: var(--font-display); text-transform: uppercase; margin-bottom: 20px; }
        .recent-orders table { width: 100%; border-collapse: collapse; }
        .recent-orders th { text-align: left; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .recent-orders td { padding: 10px 0; border-bottom: 1px solid var(--line); }
        .status-pending { color: #f5b342; }
        .status-paid { color: #4ade80; }
        .status-shipped { color: #60a5fa; }
        .status-delivered { color: #a78bfa; }
        .no-orders { color: var(--muted); padding: 20px 0; text-align: center; }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="/stride/index.php" class="logo"><img src="/stride/image/stridex-logo.png" alt="StrideX" style="height:50px;width:auto;"></a>
        <nav class="main-nav">
            <ul>
                <li><a href="/stride/admin/index.php">Dashboard</a></li>
                <li><a href="/stride/admin/products.php">Products</a></li>
                <li><a href="/stride/admin/orders.php">Orders</a></li>
                <li><a href="/stride/auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Admin Dashboard</h1>
        <div class="admin-nav">
            <a href="/stride/admin/products.php">Manage Products</a>
            <a href="/stride/admin/orders.php">Manage Orders</a>
            <a href="/stride/auth/logout.php">Logout</a>
        </div>

        <!-- Stats Cards -->
        <div class="admin-grid">
            <div class="admin-card">
                <h3><?= $productCount ?></h3>
                <p>Products</p>
            </div>
            <div class="admin-card">
                <h3 class="orders"><?= $revenue['order_count'] ?></h3>
                <p>Orders Completed</p>
            </div>
            <div class="admin-card">
                <h3 class="revenue">$<?= number_format($revenue['total_revenue'], 2) ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="admin-card">
                <h3 class="revenue">$<?= number_format($revenue['monthly_revenue'], 2) ?></h3>
                <p>Revenue This Month</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <?php if (empty($revenue['recent_orders'])): ?>
                <p class="no-orders">No orders yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenue['recent_orders'] as $o): ?>
                            <tr>
                                <td>#<?= $o['id'] ?></td>
                                <td><?= htmlspecialchars($o['first_name'] ?? $o['last_name'] ?? 'Guest') ?></td>
                                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                <td>$<?= number_format($o['total'], 2) ?></td>
                                <td class="status-<?= $o['status'] ?>"><?= strtoupper($o['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Stock Overview -->
        <h2 style="font-family: var(--font-display); text-transform: uppercase; margin-top: 60px; margin-bottom: 20px;">Stock Overview</h2>
        <?php if (empty($products)): ?>
            <p>No products yet.</p>
        <?php else: ?>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Update Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td class="<?= $p['quantity'] <= 5 ? 'low-stock' : '' ?>">
                                <?= $p['quantity'] ?>
                                <?php if ($p['quantity'] <= 5): ?>
                                    ⚠️
                                <?php endif; ?>
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