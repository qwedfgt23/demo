<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['cart_id']) || empty($data['quantity']) || $data['quantity'] < 1) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

require_once 'connection.php';

$sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$data['quantity'], $data['cart_id'], $_SESSION['user']['id']]);

echo json_encode(['success' => $success]);
