<?php
require_once '../database/config.php';
requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name']);
    $price     = $_POST['price'];
    $quantity  = (int)$_POST['quantity'];
    $imagePath = null;

    if (empty($name) || empty($price)) {
        $error = 'Name and price are required.';
    } else {
        // ---------- Image upload ----------
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Image must be JPG, PNG or WEBP.';
            } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
                $error = 'Image must be under 10 MB.';
            } else {
                $uploadDir = __DIR__ . '/../image/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);   // create folder automatically
                }
                $fileName = uniqid('prod_') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = 'image/products/' . $fileName;

                } else {
                    $error = 'Failed to save the image.';
                }
            }
        }

        // ---------- Insert product ----------
        if (!$error) {
            $pdo  = getConnection();
            $stmt = $pdo->prepare("INSERT INTO products (name, price, quantity, image) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $quantity, $imagePath])) {
                $success = 'Product added successfully!';
            } else {
                $error = 'Failed to add product.';
            }
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
        .admin-form input[type="file"] { padding: 10px; cursor: pointer; }
        .admin-form input[type="file"]::file-selector-button { background: #fff; color: #000; border: 0; padding: 6px 14px; border-radius: 4px; font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; cursor: pointer; margin-right: 12px; }
        .admin-form .btn { width: 100%; justify-content: center; }
        .admin-form .error { color: var(--accent); font-size: 14px; margin-bottom: 12px; }
        .admin-form .success { color: #4ade80; font-size: 14px; margin-bottom: 12px; }
        #image-preview { display: none; height: 120px; width: auto; margin-bottom: 16px; border-radius: 6px; border: 1px solid var(--line); }
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
                <li><a href="../auth/logout.php">Logout</a></li>

            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Add Product V2</h1>

        <div class="admin-form">
            <?php if ($error): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= e($success) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required>

                <label for="price">Price *</label>
                <input type="number" step="0.01" id="price" name="price" required>

                <label for="quantity">Quantity in Stock</label>
                <input type="number" id="quantity" name="quantity" value="0" min="0">

                <label for="image">Product Image</label>
                <img id="image-preview" alt="Preview">
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp"
                       onchange="const p=document.getElementById('image-preview'); if(this.files[0]){p.src=URL.createObjectURL(this.files[0]); p.style.display='block';}">

                <button type="submit" class="btn btn--primary">Add Product</button>
            </form>
            <p style="margin-top: 20px;"><a href="products.php">← Back to Products</a></p>
        </div>
    </div>
</section>

</body>
</html>
