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
    <title>Order Confirmation — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .confirmation-page { padding: 80px 0; background: var(--bg); text-align: center; }
        .confirmation-box { max-width: 600px; margin: 0 auto; background: #0e0e0e; padding: 40px; border-radius: var(--radius); border: 1px solid var(--line); }
        .confirmation-box h1 { font-family: var(--font-display); color: #4ade80; margin-bottom: 16px; }
        .confirmation-box .order-details { text-align: left; margin-top: 20px; }
        .confirmation-box .order-details th { text-align: left; padding: 8px 0; font-weight: 600; color: var(--muted); }
        .confirmation-box .order-details td { padding: 8px 0; border-bottom: 1px solid var(--line); }
        .btn { margin-top: 20px; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="confirmation-page">
    <div class="container">
        <div class="confirmation-box">
            <h1>✅ THANK YOU!</h1>
            <p>Your order #<?= $order['id'] ?> has been placed successfully.</p>
            <p style="color: var(--muted);">A confirmation email has been sent to <?= e($order['email']) ?>.</p>

            <div class="order-details">
                <h3 style="font-family: var(--font-display); text-transform: uppercase; margin-top: 20px;">Order Summary</h3>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['product_name']) ?></td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="font-size: 18px; font-weight: 700; margin-top: 16px; border-top: 2px solid var(--accent); padding-top: 16px;">
                    Total: $<?= number_format($order['total'], 2) ?>
                </p>
            </div>

            <a href="shop.php" class="btn btn--primary">CONTINUE SHOPPING</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>