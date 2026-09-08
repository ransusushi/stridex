<?php
session_start();
require_once 'database/config.php';

if (!isLoggedIn()) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'pages/cart.php';
    header('Location: auth/login.php?redirect=' . urlencode($redirect));
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

if (empty($id) && $action != 'clear') {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Only fetch product if we have an ID and it's not a clear action
if (!empty($id) && $action != 'clear') {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('Location: index.php');
        exit;
    }
}

switch ($action) {
    case 'add':
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                'id'       => $id,
                'name'     => $product['name'],
                'price'    => $product['price'],
                'image'    => $product['image'],
                'quantity' => $quantity
            ];
        }
        break;
    case 'remove':
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        break;
    case 'update':
        if (isset($_SESSION['cart'][$id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
        break;
    case 'clear':
        $_SESSION['cart'] = [];
        break;
}

// Redirect to cart page (absolute path)
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'pages/cart.php';
header('Location: ' . $redirect);
exit;