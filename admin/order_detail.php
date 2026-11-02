<?php
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../auth/auth_validation.php';
requireDatabase();
requireAdmin();

$pdo = getConnection();

// Get order ID
$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

// Fetch the order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch the items
$stmt = $pdo->prepare("
    SELECT oi.*, p.image AS product_image
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Order #" . $orderId;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — StrideX Admin</title>
    <link rel="stylesheet" href="/stride/css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .order-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; }
        .order-meta { color: var(--muted); font-size: 14px; line-height: 2; }
        .order-meta strong { color: var(--text); font-weight: 600; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; }
        .status-pending   { background: rgba(245,179,66,.15);  color: #f5b342; }
        .status-paid      { background: rgba(74,222,128,.15);  color: #4ade80; }
        .status-shipped   { background: rgba(96,165,250,.15);  color: #60a5fa; }
        .status-delivered { background: rgba(167,139,250,.15); color: #a78bfa; }
        .status-cancelled { background: rgba(239,68,68,.15);   color: #ef4444; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { text-align: left; padding: 14px 0; border-bottom: 1px solid var(--line); font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .items-table td { padding: 16px 0; border-bottom: 1px solid var(--line); font-size: 14px; vertical-align: middle; }
        .item-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; background: #111; }
        .items-table .price { color: var(--muted); }
        .items-table .subtotal { color: #4ade80; font-weight: 700; }

        .totals { margin-top: 30px; text-align: right; }
        .totals .row { display: flex; justify-content: flex-end; gap: 40px; padding: 8px 0; font-size: 14px; }
        .totals .label { color: var(--muted); }
        .totals .value { min-width: 140px; text-align: right; font-weight: 600; }
        .totals .grand-total { border-top: 2px solid var(--line); margin-top: 10px; padding-top: 16px; font-size: 20px; font-family: var(--font-display); letter-spacing: .04em; }
        .totals .grand-total .value { color: #4ade80; }

        .back-link { display: inline-block; color: var(--accent); font-size: 12px; text-transform: uppercase; letter-spacing: .2em; margin-bottom: 20px; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
        .txn { background: rgba(255,90,31,.12); color: #ff5a1f; padding: 3px 10px; border-radius: 4px; font-family: monospace; font-size: 13px; }
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
                <li><a href="/stride/admin/messages.php">Messages</a></li>
                <li><a href="/stride/auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">

        <a href="orders.php" class="back-link">← Back to Orders</a>

        <div class="order-header">
            <div>
                <h1 class="section-title" style="margin-bottom: 20px;">Order #<?= $order['id'] ?></h1>
                <div class="order-meta">
                    <div><strong>Customer:</strong> <?= e($order['first_name'] . ' ' . $order['last_name']) ?></div>
                    <div><strong>Email:</strong> <?= e($order['email']) ?></div>
                    <div><strong>Date:</strong> <?= date('F d, Y h:i A', strtotime($order['created_at'])) ?></div>
                    <div><strong>Payment:</strong> <?= e(strtoupper($order['payment_method'])) ?></div>
                    <div><strong>Transaction:</strong> <span class="txn"><?= e($order['transaction_id'] ?? 'N/A') ?></span></div>
                    <div><strong>Status:</strong>
                        <span class="status-badge status-<?= $order['status'] ?>">
                            <?= strtoupper($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <div class="order-meta" style="text-align: right;">
                    <div><strong>Ship to:</strong></div>
                    <div><?= e($order['address']) ?></div>
                    <div><?= e($order['city']) ?>, <?= e($order['postal']) ?></div>
                    <div><?= e($order['country']) ?></div>
                </div>
            </div>
        </div>

        <h2 style="font-family: var(--font-display); text-transform: uppercase; margin-top: 40px;">Items Ordered</h2>

        <?php if (empty($items)): ?>
            <p style="color: var(--muted); margin-top: 20px;">No items recorded for this order.</p>
        <?php else: ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 80px;"></th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['product_image'])): ?>
                                    <img src="/stride/<?= e($item['product_image']) ?>" alt="" class="item-thumb">
                                <?php else: ?>
                                    <div class="item-thumb" style="display:flex;align-items:center;justify-content:center;color:#555;font-size:10px;">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['product_id'])): ?>
                                    <a href="product_edit.php?id=<?= $item['product_id'] ?>" style="color: var(--accent);">
                                        <?= e($item['product_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= e($item['product_name']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="price">$<?= number_format($item['price'], 2) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td class="subtotal" style="text-align: right;">$<?= number_format($item['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="totals">
                <div class="row grand-total">
                    <span class="label">Total</span>
                    <span class="value">$<?= number_format($order['total'], 2) ?></span>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

</body>
</html>