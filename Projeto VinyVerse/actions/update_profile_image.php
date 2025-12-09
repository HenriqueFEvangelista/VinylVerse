<?php
include '../components/auth.php';

$user_id = $_SESSION['user_id'];

$targetDir = "../assets/uploads/profiles/";
$targetFile = $targetDir . "user_$user_id.jpg";

// Criar pasta se não existir
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Verifica se arquivo foi enviado
if (!isset($_FILES["profile_image"]) || $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../pages/userAccount.php?error=no_file");
    exit;
}

// Verifica se é imagem válida
$check = getimagesize($_FILES["profile_image"]["tmp_name"]);
if ($check === false) {
    header("Location: ../pages/userAccount.php?error=invalid_image");
    exit;
}

// Aceitar apenas JPG, PNG, JPEG
$allowed = ['image/jpeg', 'image/png'];
if (!in_array($check['mime'], $allowed)) {
    header("Location: ../pages/userAccount.php?error=invalid_type");
    exit;
}

// Mover imagem
move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile);

header("Location: ../pages/userAccount.php?photo=updated");
exit;
