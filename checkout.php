<?php
require_once 'database/config.php';

// Get cart items
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// If cart is empty, redirect back to cart
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stridex.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="checkout-page">
    <div class="container">
        <h1 class="section-title" style="margin-bottom: 40px;">CHECKOUT</h1>

        <div class="checkout-grid">
            <!-- Billing Form -->
            <div class="checkout-form">
                <form action="order-confirmation.php" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">FIRST NAME</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">LAST NAME</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">EMAIL ADDRESS</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">ADDRESS</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">CITY</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="postal">POSTAL CODE</label>
                            <input type="text" id="postal" name="postal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="country">COUNTRY</label>
                            <select id="country" name="country">
                                <option value="US">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                                <option value="PH">Philippines</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment">PAYMENT METHOD</label>
                            <select id="payment" name="payment">
                                <option value="card">Credit Card</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                    </div>

                    <div class="checkout-actions">
                        <a href="cart.php" class="btn btn--ghost">← BACK TO CART</a>
                        <button type="submit" class="btn btn--primary">PLACE ORDER</button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3>YOUR ORDER</h3>
                <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="order-total">
                    <span>TOTAL</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                <p style="color: var(--muted); font-size: 12px; margin-top: 16px;">Your order will be processed securely.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>