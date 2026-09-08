<?php
require_once '../database/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['pending', 'paid', 'shipped', 'delivered'];
    if (in_array($status, $allowed)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
header('Location: orders.php');
exit;