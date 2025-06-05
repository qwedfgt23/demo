<?php
require_once 'connection.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    echo "Заполните все поля.";
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    echo "Пользователь с таким email уже существует.";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$insert->execute([$name, $email, $hashedPassword]);

echo "Регистрация прошла успешно.";
