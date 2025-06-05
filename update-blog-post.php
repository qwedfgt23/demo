<?php
require_once 'connection.php';

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$image = null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID поста не передан']);
    exit;
}

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
    if ($image) {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $content, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

