<?php
require_once '../database/config.php';
requireAdmin();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    cacheProducts(); 
}
header('Location: products.php');
exit;