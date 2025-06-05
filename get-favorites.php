<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.price, p.image
    FROM favorites f
    JOIN products p ON f.product_id = p.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($favorites);
