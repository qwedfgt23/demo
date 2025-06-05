<?php
require_once 'connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;
$newStatus = $data['status'] ?? null;

$validStatuses = ['поступил', 'в обработке', 'в сборке', 'отправлен', 'выдан', 'отменён'];

if (!$orderId || !in_array($newStatus, $validStatuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
