<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

require_once 'connection.php';

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT c.product_id, c.quantity, p.title, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);

$items = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $items[] = [
        'product_id' => $row['product_id'],
        'title' => $row['title'],
        'price' => $row['price'],
        'image' => $row['image'],
        'quantity' => $row['quantity']
    ];
}

echo json_encode($items);
