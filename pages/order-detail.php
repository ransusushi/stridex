<?php
require_once '../database/config.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php?redirect=order-detail.php?id=' . ($_GET['id'] ?? ''));
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$order_id = (int)$_GET['id'];
$pdo = getConnection();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Details – <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .order-detail-page { padding: 80px 0; background: var(--bg); }
        .order-detail-box { max-width: 700px; margin: 0 auto; background: #0e0e0e; padding: 30px; border-radius: var(--radius); border: 1px solid var(--line); }
        .order-detail-box h1 { font-family: var(--font-display); text-transform: uppercase; margin-bottom: 20px; }
        .order-detail-box .meta { margin-bottom: 20px; color: var(--muted); }
        .order-detail-box .meta strong { color: var(--text); }
        .order-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--line); }
        .order-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 18px; padding-top: 16px; border-top: 2px solid var(--accent); margin-top: 16px; }
        .btn-back { margin-top: 20px; }
        .status-pending { color: #f5b342; }
        .status-paid { color: #4ade80; }
        .status-shipped { color: #60a5fa; }
        .status-delivered { color: #a78bfa; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="order-detail-page">
    <div class="container">
        <div class="order-detail-box">
            <h1>Order #<?= $order['id'] ?></h1>
            <div class="meta">
                <p><strong>Date:</strong> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                <p><strong>Status:</strong> <span class="status-<?= $order['status'] ?>"><?= strtoupper($order['status']) ?></span></p>
                <p><strong>Shipping Address:</strong> <?= e($order['address']) . ', ' . e($order['city']) . ', ' . e($order['postal']) . ', ' . e($order['country']) ?></p>
                <p><strong>Payment Method:</strong> <?= e($order['payment_method']) ?></p>
            </div>

            <h3 style="font-family: var(--font-display); text-transform: uppercase; margin-top: 30px;">Items Purchased</h3>
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <span><?= e($item['product_name']) ?> × <?= (int)$item['quantity'] ?></span>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <span>Total</span>
                <span>$<?= number_format($order['total'], 2) ?></span>
            </div>

            <a href="profile.php" class="btn btn--primary btn-back">← Back to Profile</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>