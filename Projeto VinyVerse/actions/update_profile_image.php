<?php
include '../components/auth.php';

$user_id = $_SESSION['user_id'];

$targetDir = "../assets/uploads/profiles/";
$targetFile = $targetDir . "user_$user_id.jpg";

// Criar pasta se não existir
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile);

header("Location: userAccount.php?photo=updated");
exit;
