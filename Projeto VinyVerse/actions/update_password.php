<?php
include '../components/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$current = $_POST['current'];
$newpass = $_POST['newpass'];

// Buscar senha atual
$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($current, $hash)) {
    die("Senha atual incorreta!");
}

$newHash = password_hash($newpass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $newHash, $user_id);
$stmt->execute();

header("Location: userAccount.php?password=changed");
exit;
