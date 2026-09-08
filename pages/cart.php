<?php
require_once '../database/config.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php?redirect=cart.php');
    exit;
}

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart – <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .cart-page { padding: 80px 0; background: var(--bg); }
        .cart-table { width: 100%; border-collapse: collapse; color: var(--text); }
        .cart-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .cart-table td { padding: 16px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .cart-item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .cart-item-name { font-weight: 600; }
        .cart-item-price { color: var(--accent); font-weight: 600; }
        .cart-quantity-input { width: 60px; padding: 6px 10px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: 4px; color: #fff; text-align: center; }
        .cart-remove { color: var(--muted); font-size: 12px; transition: color .25s; }
        .cart-remove:hover { color: var(--accent); }
        .cart-total-row { font-size: 18px; font-weight: 700; }
        .cart-empty { color: var(--muted); font-size: 18px; text-align: center; padding: 60px 0; }
        .cart-actions { display: flex; gap: 16px; justify-content: flex-end; margin-top: 30px; flex-wrap: wrap; }
        .btn--outline { background: transparent; color: #fff; border: 1px solid var(--line); }
        .btn--outline:hover { border-color: var(--accent); color: var(--accent); }
        @media (max-width: 640px) {
            .cart-table { display: block; overflow-x: auto; }
            .cart-item-img { width: 40px; height: 40px; }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <h1 class="section-title" style="margin-bottom: 40px;">YOUR CART</h1>

        <?php if (empty($cartItems)): ?>
            <div class="cart-empty">
                <p>Your cart is empty.</p>
                <a href="shop.php" class="btn btn--primary" style="margin-top: 20px;">START SHOPPING</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $id => $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <img src="../<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="cart-item-img">
                                    <span class="cart-item-name"><?= e($item['name']) ?></span>
                                </div>
                            </td>
                            <td class="cart-item-price">$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <form method="get" action="/stride/add_to_cart.php" style="display: flex; align-items: center; gap: 8px;">
                                    <input type="hidden" name="id" value="<?= e($id) ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="redirect" value="pages/cart.php">
                                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" class="cart-quantity-input">
                                    <button type="submit" class="btn btn--primary" style="padding: 6px 12px; font-size: 10px;">Update</button>
                                </form>
                            </td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td>
                                <a href="/stride/add_to_cart.php?id=<?= e($id) ?>&action=remove&redirect=pages/cart.php" class="cart-remove">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="cart-total-row">
                        <td colspan="3" style="text-align: right;">Total</td>
                        <td>$<?= number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <a href="/stride/add_to_cart.php?action=clear&redirect=pages/cart.php" class="btn btn--outline">CLEAR CART</a>
                <a href="checkout.php" class="btn btn--primary">PROCEED TO CHECKOUT</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>