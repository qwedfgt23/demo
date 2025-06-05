<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? 0;
$user_id = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $pdo->prepare("DELETE FROM favorites WHERE id = ?")->execute([$existing['id']]);
        echo json_encode(['success' => true, 'favorited' => false]);
    } else {
        $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $product_id]);
        echo json_encode(['success' => true, 'favorited' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка переключения избранного']);
}
