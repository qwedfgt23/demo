<?php
require_once 'connection.php';

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$image = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $uploadDir = '../img/blog/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $image = $imageName;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, image) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $image]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
