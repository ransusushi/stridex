<?php
require_once '../database/config.php';
requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $quantity = (int)$_POST['quantity'];

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required.';
    } else {
        $pdo = getConnection();
        $sql = "INSERT INTO products (name, price, quantity) 
                VALUES (:name, :price, :quantity)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Failed to add product.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product – Admin</title>
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-form { max-width: 600px; margin: 0 auto; background: #0e0e0e; padding: 40px; border-radius: var(--radius); border: 1px solid var(--line); }
        .admin-form label { display: block; font-size: 12px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .admin-form input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; font-size: 14px; outline: none; margin-bottom: 16px; }
        .admin-form input:focus { border-color: var(--accent); }
        .admin-form .btn { width: 100%; justify-content: center; }
        .admin-form .error { color: var(--accent); font-size: 14px; margin-bottom: 12px; }
        .admin-form .success { color: #4ade80; font-size: 14px; margin-bottom: 12px; }
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
        <h1 class="section-title">Add Product</h1>
        <div class="admin-form">
            <?php if ($error): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= e($success) ?></div>
            <?php endif; ?>
            <form method="post">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required>

                <label for="price">Price *</label>
                <input type="number" step="0.01" id="price" name="price" required>

                <label for="quantity">Quantity in Stock</label>
                <input type="number" id="quantity" name="quantity" value="0" min="0">

                <button type="submit" class="btn btn--primary">Add Product</button>
            </form>
            <p style="margin-top: 20px;"><a href="products.php">← Back to Products</a></p>
        </div>
    </div>
</section>

</body>
</html>