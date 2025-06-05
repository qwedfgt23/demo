<?php
require_once 'connection.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, title, content, image, created_at FROM blog_posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($posts);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
