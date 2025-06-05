<?php
require 'connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$role = $data['role'] ?? '';

if (!$id || !in_array($role, ['user', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    exit;
}

$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$role, $id]);

echo json_encode(['success' => true]);
