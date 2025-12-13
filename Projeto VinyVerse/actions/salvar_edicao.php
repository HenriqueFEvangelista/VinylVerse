<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/home.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$produto_id = intval($_POST['produto_id']);

try {

    // Confere se o produto é do usuário
    $check = $conn->prepare("SELECT id FROM produtos WHERE id = ? AND usuario_id = ?");
    $check->bind_param("ii", $produto_id, $usuario_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        die("Acesso negado.");
    }

    $conn->begin_transaction();

    /* =========================
       1. IMAGEM
    ========================== */

    $imagem_final = $_POST['imagem_atual'];

    // Upload local
    if (!empty($_FILES['imagemCapa']['name'])) {

        $ext = strtolower(pathinfo($_FILES['imagemCapa']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) die("Imagem inválida.");

        $novoNome = "capa_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagemCapa']['tmp_name'], "../assets/uploads/$novoNome");
        $imagem_final = $novoNome;
    }

    // Base64
    elseif (!empty($_POST['imagemURL']) && str_starts_with($_POST['imagemURL'], 'data:image')) {

        preg_match('/^data:image\/(\w+);base64,/', $_POST['imagemURL'], $type);
        $ext = strtolower($type[1]);

        $data = base64_decode(substr($_POST['imagemURL'], strpos($_POST['imagemURL'], ',') + 1));
        $novoNome = "capa_" . uniqid() . ".$ext";

        file_put_contents("../assets/uploads/$novoNome", $data);
        $imagem_final = $novoNome;
    }

    // URL normal
    elseif (!empty($_POST['imagemURL'])) {

        $headers = get_headers($_POST['imagemURL'], 1);
        if (!isset($headers['Content-Type']) || strpos($headers['Content-Type'], 'image/') !== 0) {
            die("URL inválida.");
        }

        $ext = explode('/', $headers['Content-Type'])[1];
        $novoNome = "capa_" . uniqid() . ".$ext";

        file_put_contents("../assets/uploads/$novoNome", file_get_contents($_POST['imagemURL']));
        $imagem_final = $novoNome;
    }

    // Remove imagem antiga
    if ($imagem_final !== $_POST['imagem_atual'] && !empty($_POST['imagem_atual'])) {
        @unlink("../assets/uploads/" . $_POST['imagem_atual']);
    }

    /* =========================
       2. PRODUTOS
    ========================== */

    $stmt = $conn->prepare("
        UPDATE produtos SET
        titulo_album=?, artista_banda=?, ano_lancamento=?, genero_musical=?,
        formato=?, continente=?, imagem_capa=?, observacoes=?
        WHERE id=? AND usuario_id=?
    ");

    $stmt->bind_param(
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

    $stmt->execute();

    /* =========================
       3. CAPA
    ========================== */

    $stmtCapa = $conn->prepare("
    UPDATE capa_info SET
    tipo_embalagem=?, condicao_capa=?, encarte_original=?, obi=?
    WHERE produto_id=?
");

$stmtCapa->bind_param(
    "ssssi",
    $_POST['embalagem'],
    $_POST['condicao_capa'],
    $_POST['encarte'],
    $_POST['obi'],
    $produto_id
);

$stmtCapa->execute();


    /* =========================
       4. DISCO
    ========================== */

   $stmtDisco = $conn->prepare("
    UPDATE disco_info SET
    condicao_disco=?, versao=?, codigo_catalogo=?
    WHERE produto_id=?
");

$stmtDisco->bind_param(
    "sssi",
    $_POST['condicao_disco'],
    $_POST['disco_versao'],
    $_POST['codigo_catalogo'],
    $produto_id
);

$stmtDisco->execute();


    /* =========================
       5. EDIÇÃO
    ========================== */

    $stmtEdicao = $conn->prepare("
    UPDATE edicao_info SET
    edicao_limitada=?, numero_edicao=?, versao=?, assinado=?
    WHERE produto_id=?
");

$stmtEdicao->bind_param(
    "ssssi",
    $_POST['edicao_limitada'],
    $_POST['numero_edicao'],
    $_POST['versao_edicao'],
    $_POST['assinado'],
    $produto_id
);

$stmtEdicao->execute();


    $conn->commit();
    header("Location: ../pages/home.php?edit=success");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Erro: " . $e->getMessage());
}
