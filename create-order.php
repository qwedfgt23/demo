<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный пользователь']);
    exit;
}

$userId = $_SESSION['user']['id'];

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Пустой список товаров']);
    exit;
}

require_once 'connection.php';

$items = $data['items'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'поступил')");
    $stmt->execute([$userId]);
    $orderId = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmtPrice->execute([$item['product_id']]);
        $price = $stmtPrice->fetchColumn();
        if ($price === false) {
            throw new Exception("Товар с ID {$item['product_id']} не найден");
        }
        $quantity = max(1, (int)$item['quantity']);
        $stmtItem->execute([$orderId, $item['product_id'], $quantity, $price]);
    }

    $pdo->commit();

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo json_encode(['success' => true, 'order_id' => $orderId]);


    echo json_encode(['success' => true, 'order_id' => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
