<?php
require_once '../database/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = (int)$_GET['id'];
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
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
    <title>Receipt — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .receipt-page { padding: 60px 0; background: var(--bg); }
        .receipt { max-width: 480px; margin: 0 auto; background: #0e0e0e; border: 1px solid var(--line); border-radius: var(--radius); padding: 32px 28px; position: relative; }

        /* Notch effect at top and bottom */
        .receipt::before, .receipt::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 12px;
            background:
                radial-gradient(circle at 6px 6px, var(--bg) 6px, transparent 7px) repeat-x;
            background-size: 16px 16px;
        }
        .receipt::before { top: -6px; }
        .receipt::after  { bottom: -6px; transform: rotate(180deg); }

        .receipt-header { text-align: center; padding-bottom: 20px; border-bottom: 1px dashed var(--line); }
        .receipt-header .logo-text {
            font-family: var(--font-display);
            font-size: 32px;
            color: #fff;
            letter-spacing: .05em;
            margin: 0;
        }
        .receipt-header .tagline {
            color: var(--muted);
            font-size: 11px;
            letter-spacing: .25em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .receipt-status {
            text-align: center;
            padding: 16px 0;
        }
        .receipt-status .check {
            display: inline-block;
            width: 44px;
            height: 44px;
            background: #4ade80;
            color: #000;
            border-radius: 50%;
            font-size: 24px;
            line-height: 44px;
            margin-bottom: 8px;
        }
        .receipt-status .msg {
            color: #4ade80;
            font-family: var(--font-display);
            font-size: 24px;
            letter-spacing: .05em;
            margin: 0;
        }

        .receipt-txn {
            text-align: center;
            padding: 14px 0 18px;
            border-bottom: 1px dashed var(--line);
            margin-bottom: 20px;
        }
        .receipt-txn .label {
            color: var(--muted);
            font-size: 10px;
            letter-spacing: .3em;
            text-transform: uppercase;
            margin: 0;
        }
        .receipt-txn .value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 18px;
            font-weight: 600;
            color: var(--accent);
            margin: 4px 0 0;
            letter-spacing: .05em;
        }

        .receipt-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .receipt-meta .row { display: flex; justify-content: space-between; }
        .receipt-meta .row .k { color: var(--muted); }
        .receipt-meta .row .v { color: var(--text); text-align: right; }

        .receipt-items {
            border-top: 1px dashed var(--line);
            border-bottom: 1px dashed var(--line);
            padding: 16px 0;
            margin-bottom: 16px;
        }
        .receipt-items .title {
            color: var(--muted);
            font-size: 10px;
            letter-spacing: .3em;
            text-transform: uppercase;
            margin: 0 0 12px;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            padding: 4px 0;
            color: var(--text);
        }
        .receipt-item .qty { color: var(--muted); }

        .receipt-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font-display);
            font-size: 24px;
            color: #fff;
            padding-top: 8px;
        }
        .receipt-total .amount { color: var(--accent); }

        .receipt-actions {
            margin-top: 28px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .receipt-actions .btn { justify-content: center; }
        .btn--ghost {
            background: transparent; color: #fff;
            border: 1px solid rgba(255,255,255,.35);
        }
        .btn--ghost:hover { border-color: var(--accent); color: var(--accent); }

        .receipt-footer {
            text-align: center;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: .15em;
            text-transform: uppercase;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px dashed var(--line);
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="receipt-page">
    <div class="container">
        <div class="receipt">

            <!-- Header -->
            <div class="receipt-header">
                <p class="logo-text"><?= e($site['brand']) ?></p>
                <p class="tagline"><?= e($site['tagline']) ?></p>
            </div>

            <!-- Success -->
            <div class="receipt-status">
                <div class="check">✓</div>
                <p class="msg">PAYMENT SUCCESSFUL</p>
            </div>

            <!-- Transaction ID -->
            <div class="receipt-txn">
                <p class="label">Transaction ID</p>
                <p class="value"><?= e($order['transaction_id'] ?? 'TXN-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT)) ?></p>
            </div>

            <!-- Order Meta -->
            <div class="receipt-meta">
                <div class="row"><span class="k">Order No.</span><span class="v">#<?= $order['id'] ?></span></div>
                <div class="row"><span class="k">Date</span><span class="v"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></span></div>
                <div class="row"><span class="k">Customer</span><span class="v"><?= e($order['first_name'] . ' ' . $order['last_name']) ?></span></div>
                <div class="row"><span class="k">Email</span><span class="v"><?= e($order['email']) ?></span></div>
                <div class="row"><span class="k">Payment</span><span class="v"><?= e(strtoupper($order['payment_method'])) ?></span></div>
                <div class="row"><span class="k">Status</span><span class="v"><?= e(strtoupper($order['status'])) ?></span></div>
            </div>

            <!-- Items -->
            <div class="receipt-items">
                <p class="title">Items Purchased</p>
                <?php foreach ($items as $item): ?>
                    <div class="receipt-item">
                        <span><?= e($item['product_name']) ?> <span class="qty">× <?= (int)$item['quantity'] ?></span></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Total -->
            <div class="receipt-total">
                <span>TOTAL</span>
                <span class="amount">$<?= number_format($order['total'], 2) ?></span>
            </div>

            <!-- Actions -->
            <div class="receipt-actions">
                <a href="order-detail.php?id=<?= $order['id'] ?>" class="btn btn--primary">VIEW FULL ORDER</a>
                <a href="shop.php" class="btn btn--ghost">CONTINUE SHOPPING</a>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                Thank you for shopping with <?= e($site['brand']) ?>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>