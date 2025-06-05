<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

require_once 'connection.php';

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as &$order) {
    $stmtItems = $pdo->prepare("
        SELECT oi.quantity, oi.price, p.title, p.image 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmtItems->execute([$order['id']]);
    $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($orders);
