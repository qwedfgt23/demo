<?php
require_once 'connection.php';

$product_id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT id, title, description, price, image, category FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Товар не найден']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка получения товара: ' . $e->getMessage()]);
}
