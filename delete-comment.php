<?php
require_once 'connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$comment_id = $data['comment_id'] ?? 0;

try {
    $stmt = $pdo->prepare("DELETE FROM product_comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
