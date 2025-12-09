<?php
include '../components/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

// Verificação básica
if (empty($username) || empty($email)) {
    header("Location: ../pages/userAccount.php?error=empty_fields");
    exit;
}

// Validar formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/userAccount.php?error=invalid_email");
    exit;
}

// Atualizar no banco
$stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
$stmt->bind_param("ssi", $username, $email, $user_id);
$stmt->execute();
$stmt->close();

header("Location: ../pages/userAccount.php?updated=1");
exit;
