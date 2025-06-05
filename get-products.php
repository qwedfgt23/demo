<?php
require_once 'connection.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, title, price, category, description, image FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
