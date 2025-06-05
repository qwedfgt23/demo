<?php
require_once 'connection.php';

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$category = $_POST['category'] ?? '';
$description = $_POST['description'] ?? '';
$image = null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID товара не передан']);
    exit;
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $uploadDir = '../img/products/';
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $image = $imageName;
    }
}

try {
    if ($image) {
        $stmt = $pdo->prepare("UPDATE products SET title = ?, price = ?, category = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $price, $category, $description, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET title = ?, price = ?, category = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $price, $category, $description, $id]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
