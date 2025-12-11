<?php
include '../components/auth.php'; // Garante que o usuário está logado
include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Diretório onde as fotos serão armazenadas
$targetDir = "../assets/uploads/profiles/";

// Criar pasta se não existir
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Verifica se o arquivo foi enviado corretamente
if (
    !isset($_FILES["profile_image"]) ||
    $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK
) {
    header("Location: ../pages/userAccount.php?error=no_file");
    exit;
}

$file = $_FILES["profile_image"];
$tmp  = $file["tmp_name"];

// Verifica se realmente é um upload via HTTP POST
if (!is_uploaded_file($tmp)) {
    header("Location: ../pages/userAccount.php?error=invalid_upload");
    exit;
}

// Verifica arquivo vazio
if ($file["size"] <= 0) {
    header("Location: ../pages/userAccount.php?error=empty_file");
    exit;
}

// Limita tamanho (2MB)
$maxSize = 2 * 1024 * 1024;
if ($file["size"] > $maxSize) {
    header("Location: ../pages/userAccount.php?error=too_big");
    exit;
}

// Verifica se é imagem real
$info = getimagesize($tmp);
if ($info === false) {
    header("Location: ../pages/userAccount.php?error=invalid_image");
    exit;
}

$mime = $info["mime"];

// Tipos de imagem permitidos
$allowedMimes = [
    "image/jpeg" => ".jpg",
    "image/png"  => ".png",
    "image/webp" => ".webp",
    "image/gif"  => ".gif"
];

// Verifica MIME permitido
if (!array_key_exists($mime, $allowedMimes)) {
    header("Location: ../pages/userAccount.php?error=invalid_type");
    exit;
}

// Define extensão com base no MIME
$ext = $allowedMimes[$mime];

// Caminho final da nova imagem
$targetFile = $targetDir . "user_" . $user_id . $ext;

// Remove versões antigas (caso mude o tipo)
foreach ([".jpg", ".png", ".webp", ".gif"] as $e) {
    $old = $targetDir . "user_" . $user_id . $e;
    if (file_exists($old)) {
        unlink($old);
    }
}

// Move arquivo com segurança
if (!move_uploaded_file($tmp, $targetFile)) {
    header("Location: ../pages/userAccount.php?error=upload_fail");
    exit;
}

// Ajusta permissões
chmod($targetFile, 0644);

// Sucesso
header("Location: ../pages/userAccount.php?photo=updated");
exit;

