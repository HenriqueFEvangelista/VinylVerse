<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Se não for POST, volta
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/home.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$produto_id = intval($_POST['produto_id']);

try {
    // Verifica se o produto pertence ao usuário
    $check = $conn->prepare("SELECT * FROM produtos WHERE id = ? AND usuario_id = ?");
    $check->bind_param("ii", $produto_id, $usuario_id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows === 0) {
        die("Acesso negado.");
    }

    // Começar transação
    $conn->begin_transaction();

    /* ---------------------------
        1. UPLOAD DA IMAGEM
    ---------------------------- */

    $imagem_final = $_POST['imagem_atual']; // caso não troque

    if (!empty($_FILES['imagemCapa']['name'])) {

        $nomeArquivo = $_FILES['imagemCapa']['name'];
        $tmp = $_FILES['imagemCapa']['tmp_name'];

        $ext = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

        // Extensões permitidas
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) {
            die("Formato de imagem inválido.");
        }

        // Novo nome único
        $novoNome = "capa_" . uniqid() . "." . $ext;

        $destino = "../assets/uploads/" . $novoNome;

        if (move_uploaded_file($tmp, $destino)) {

            // Apaga a imagem antiga
            if (!empty($_POST['imagem_atual']) && file_exists("../assets/uploads/" . $_POST['imagem_atual'])) {
                unlink("../assets/uploads/" . $_POST['imagem_atual']);
            }

            // Salva o novo nome
            $imagem_final = $novoNome;

        } else {
            die("Erro ao fazer upload da imagem.");
        }
    }


    /* ---------------------------
        2. UPDATE TABELA PRODUTOS
    ---------------------------- */

    $sql = $conn->prepare("
        UPDATE produtos
        SET titulo_album=?, artista_banda=?, ano_lancamento=?, genero_musical=?, formato=?, continente=?, imagem_capa=?, observacoes=?
        WHERE id=? AND usuario_id=?
    ");

    $sql->bind_param(
        "ssisssssii",
        $_POST['titulo'],
        $_POST['artista'],
        $_POST['ano'],
        $_POST['genero'],
        $_POST['formato'],
        $_POST['continente'],
        $imagem_final,
        $_POST['observacoes'],
        $produto_id,
        $usuario_id
    );

    $sql->execute();


    /* ---------------------------
        3. UPDATE CAPA_INFO
    ---------------------------- */

    $sqlCapa = $conn->prepare("
        UPDATE capa_info
        SET tipo_embalagem=?, condicao_capa=?, encarte_original=?, obi=?
        WHERE produto_id=?
    ");

    $sqlCapa->bind_param(
        "ssssi",
        $_POST['embalagem'],
        $_POST['condicao_capa'],
        $_POST['encarte'],
        $_POST['obi'],
        $produto_id
    );

    $sqlCapa->execute();


    /* ---------------------------
        4. UPDATE DISCO_INFO
    ---------------------------- */

    $sqlDisco = $conn->prepare("
        UPDATE disco_info
        SET condicao_disco=?, versao=?, codigo_catalogo=?
        WHERE produto_id=?
    ");

    $sqlDisco->bind_param(
        "sssi",
        $_POST['condicao_disco'],
        $_POST['disco_versao'],
        $_POST['codigo_catalogo'],
        $produto_id
    );

    $sqlDisco->execute();


    /* ---------------------------
        5. UPDATE EDICAO_INFO
    ---------------------------- */

    $sqlEdicao = $conn->prepare("
        UPDATE edicao_info
        SET edicao_limitada=?, numero_edicao=?, versao=?, assinado=?
        WHERE produto_id=?
    ");

    $sqlEdicao->bind_param(
        "ssssi",
        $_POST['edicao_limitada'],
        $_POST['numero_edicao'],
        $_POST['versao_edicao'],
        $_POST['assinado'],
        $produto_id
    );

    $sqlEdicao->execute();


    /* ---------------------------
        6. FINALIZA
    ---------------------------- */

    $conn->commit();

    header("Location: ../pages/home.php?edit=success");
    exit;

} catch (Exception $e) {

    $conn->rollback();

    die("Erro ao salvar edição: " . $e->getMessage());
}
