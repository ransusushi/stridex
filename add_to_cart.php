<?php
require_once 'database/config.php'; 
if (!isLoggedIn()) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    header('Location: login/login.php?redirect=' . urlencode($redirect));
    exit;
}

// Get product id and action
$id = isset($_GET['id']) ? $_GET['id'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// If no id, redirect back
if (empty($id)) {
    header('Location: index.php');
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


switch ($action) {
    case 'add':
        // If product already in cart, increase quantity
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            // We need to fetch product details to store in cart
            // Since we don't have a central product list, we'll store minimal data
            // and later we can fetch from arrays. For simplicity, we'll store
            // id, name, price, image, and quantity.
            // We'll need a function to get product data by id.
            // Let's create getProductById() in config.php.
            $product = getProductById($id);
            if ($product) {
                $_SESSION['cart'][$id] = [
                    'id'       => $id,
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => $quantity
                ];
            }
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

// Redirect back to referring page or to cart
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cart.php';
header('Location: ' . $redirect);
exit;