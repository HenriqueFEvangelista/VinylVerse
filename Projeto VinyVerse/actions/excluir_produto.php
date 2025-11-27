<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID não fornecido.");
}

$produto_id = intval($_GET['id']);
$usuario_id = $_SESSION['user_id'];

// Verifica se o item pertence ao usuário
$sql = "SELECT imagem_capa FROM produtos WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Item não encontrado.");
}

$item = $result->fetch_assoc();

// Exclui a imagem
if (!empty($item['imagem_capa'])) {
    $arquivo = "../assets/uploads/" . $item['imagem_capa'];
    if (file_exists($arquivo)) {
        unlink($arquivo);
    }
}

// Remove registros das tabelas filhas
$conn->query("DELETE FROM disco_info WHERE produto_id = $produto_id");
$conn->query("DELETE FROM capa_info WHERE produto_id = $produto_id");
$conn->query("DELETE FROM edicao_info WHERE produto_id = $produto_id");

// Remove produto
$conn->query("DELETE FROM produtos WHERE id = $produto_id");

header("Location: home.php?deleted=1");
exit;
?>
