<?php
require_once 'database/config.php';

// Protect checkout: require login
if (!isLoggedIn()) {
    header('Location: login/login.php?redirect=checkout.php');
    exit;
}

// ✅ NEW: If admin, redirect to order management dashboard
if (isAdmin()) {
    // Show a message then redirect
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="refresh" content="2;url=admin/orders.php">
        <style>
            body { background: #000; color: #fff; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
            .box { background: #0e0e0e; padding: 40px; border-radius: 8px; border: 1px solid rgba(255,255,255,.12); }
            .box h1 { color: #ff5a1f; }
            .box p { color: #9a9a9a; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>🔐 Admin Access</h1>
            <p>You are logged in as admin.</p>
            <p>Redirecting to <strong>Order Management Dashboard</strong>...</p>
            <p style="font-size: 12px; margin-top: 20px;"><a href="admin/orders.php" style="color: #ff5a1f;">Click here if you are not redirected</a></p>
        </div>
    </body>
    </html>';
    exit;
}

// If cart is empty, redirect back
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $address    = trim($_POST['address']);
    $city       = trim($_POST['city']);
    $postal     = trim($_POST['postal']);
    $country    = $_POST['country'];
    $payment    = $_POST['payment'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($address) || empty($city) || empty($postal)) {
        $error = 'All fields are required.';
    } else {
        $pdo = getConnection();
        $pdo->beginTransaction();
        try {
            // Check stock
            $insufficient = false;
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
                $stmt->execute([$item['id']]);
                $stock = $stmt->fetchColumn();
                if ($stock < $item['quantity']) {
                    $insufficient = true;
                    $error = 'Insufficient stock for ' . $item['name'];
                    break;
                }
            }
            if ($insufficient) {
                $pdo->rollBack();
            } else {
                $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, first_name, last_name, email, address, city, postal, country, payment_method, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $first_name, $last_name, $email, $address, $city, $postal, $country, $payment, $total]);
                $order_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
                foreach ($cartItems as $item) {
                    $item_total = $item['price'] * $item['quantity'];
                    $stmt->execute([$order_id, $item['name'], $item['price'], $item['quantity'], $item_total]);
                }

                // Reduce stock
                foreach ($cartItems as $item) {
                    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['id']]);
                }

                $pdo->commit();
                unset($_SESSION['cart']);
                header('Location: order-confirmation.php?id=' . $order_id);
                exit;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Order failed: ' . $e->getMessage();
        }
    }
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

        <?php if ($error): ?>
            <div class="error-message" style="color: var(--accent); margin-bottom: 20px;"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="checkout-grid">
            <div class="checkout-form">
                <form method="post">
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
                            <select id="country" name="country" size="5">
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
                        <button type="submit" name="place_order" class="btn btn--primary">PLACE ORDER</button>
                    </div>
                </form>
            </div>

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