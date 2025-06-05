<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];

require_once 'connection.php'; // Подключение к БД, настрой свой путь

$sql = "SELECT cart.id as cart_id, cart.quantity, p.id as product_id, p.title, p.price, p.image 
        FROM cart 
        JOIN products p ON cart.product_id = p.id 
        WHERE cart.user_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
