<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['favorited' => false]);
    exit;
}

$product_id = $_GET['product_id'] ?? 0;
$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$isFavorite = $stmt->fetchColumn();

echo json_encode(['favorited' => (bool)$isFavorite]);
