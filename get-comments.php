<?php
require_once 'connection.php';

$product_id = $_GET['product_id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT id, user_id, comment, created_at FROM product_comments WHERE product_id = ? ORDER BY created_at DESC");
    $stmt->execute([$product_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($comments);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении комментариев: ' . $e->getMessage()]);
}
