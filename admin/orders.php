<?php
require_once '../database/config.php';
requireAdmin();

$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Orders – Admin</title>
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-table { width: 100%; border-collapse: collapse; color: var(--text); }
        .admin-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .admin-table td { padding: 12px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .status-pending { color: #f5b342; }
        .status-paid { color: #4ade80; }
        .status-shipped { color: #60a5fa; }
        .status-delivered { color: #a78bfa; }
        .btn--small { padding: 4px 12px; font-size: 10px; }
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
                <a href="/stride/auth/logout.php">Logout</a>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Orders</h1>
        <?php if (empty($orders)): ?>
            <p>No orders yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= e($o['first_name']) . ' ' . e($o['last_name']) ?></td>
                            <td>$<?= number_format($o['total'], 2) ?></td>
                            <td class="status-<?= $o['status'] ?>"><?= strtoupper($o['status']) ?></td>
                            <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            <td>
                                <form method="post" action="order_update.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status">
                                        <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="paid" <?= $o['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    </select>
                                    <button type="submit" class="btn btn--primary btn--small">Update</button>
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