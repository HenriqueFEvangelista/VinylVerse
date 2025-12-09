<?php
include '../components/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$current = $_POST['current'] ?? '';
$newpass = $_POST['newpass'] ?? '';

// Garantir que não veio vazio
if (empty($current) || empty($newpass)) {
    header("Location: ../pages/userAccount.php?error=empty_fields");
    exit;
}

// Buscar senha atual
$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

// Verificar senha atual
if (!password_verify($current, $hash)) {
    header("Location: ../pages/userAccount.php?error=wrong_password");
    exit;
}

// Criar hash da nova senha
$newHash = password_hash($newpass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $newHash, $user_id);
$stmt->execute();
$stmt->close();

header("Location: ../pages/userAccount.php?password=changed");
exit;
