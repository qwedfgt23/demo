<?php
require_once 'connection.php';

// Получаем JSON из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

$product_id = $data['product_id'] ?? 0;
$comment = trim($data['comment'] ?? '');
$user_id = 1; // Пока заглушка — заменить на ID текущего пользователя из сессии

if (!$product_id || !$comment) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO product_comments (product_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$product_id, $user_id, $comment]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка добавления комментария: ' . $e->getMessage()]);
}
