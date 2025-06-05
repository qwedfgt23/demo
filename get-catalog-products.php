<?php
require_once 'connection.php';

$title = $_GET['title'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($title)) {
    $sql .= " AND title LIKE ?";
    $params[] = '%' . $title . '%';
}
if (!empty($category)) {
    $sql .= " AND category LIKE ?";
    $params[] = '%' . $category . '%';
}
if (!empty($minPrice)) {
    $sql .= " AND price >= ?";
    $params[] = $minPrice;
}
if (!empty($maxPrice)) {
    $sql .= " AND price <= ?";
    $params[] = $maxPrice;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($products);
