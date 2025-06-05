<?php
require_once 'connection.php';

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$category = $_POST['category'] ?? '';
$description = $_POST['description'] ?? '';
$image = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $uploadDir = '../img/products/';
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $image = $imageName;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO products (title, price, category, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $category, $description, $image]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
