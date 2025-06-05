<?php
require_once 'connection.php';

header('Content-Type: application/json');

try {
    // Получаем список заказов
    $stmt = $pdo->query("
        SELECT o.id AS order_id, o.user_id, o.status, o.created_at,
               oi.product_id, oi.quantity, oi.price,
               p.title AS product_name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        ORDER BY o.created_at DESC
    ");

    $orders = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $orderId = $row['order_id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'id' => $orderId,
                'user_id' => $row['user_id'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }
        $orders[$orderId]['items'][] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }

    echo json_encode(['success' => true, 'orders' => array_values($orders)]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
