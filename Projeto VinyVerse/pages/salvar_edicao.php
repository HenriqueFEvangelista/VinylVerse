<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Verifica se veio por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: home.php");
    exit;
}

try {
    $conn->begin_transaction();

    // Dados principais
    $produto_id     = intval($_POST['produto_id']);
    $usuario_id     = $_SESSION['user_id'];

    // Impede editar itens de outro usuário
    $check = $conn->prepare("SELECT id FROM produtos WHERE id = ? AND usuario_id = ?");
    $check->bind_param("ii", $produto_id, $usuario_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        throw new Exception("Este produto não pertence ao usuário.");
    }

    // === DADOS DO FORMULÁRIO ===
    $titulo       = $_POST['titulo'];
    $artista      = $_POST['artista'];
    $ano          = $_POST['ano'];
    $genero       = $_POST['genero'];
    $formato      = $_POST['formato'];
    $continente   = $_POST['continente'];

    $cond_disco   = $_POST['condicao_disco'];
    $versao       = $_POST['versao'];
    $codigo       = $_POST['codigo_catalogo'];

    $embalagem    = $_POST['embalagem'];
    $cond_capa    = $_POST['condicao_capa'];
    $encarte      = $_POST['encarte'];
    $obi          = $_POST['obi'];

    $limitada     = $_POST['edicao_limitada'];
    $num_edicao   = $_POST['numero_edicao'];
    $prensagem    = $_POST['prensagem'];
    $assinado     = $_POST['assinado'];

    $observacoes  = $_POST['observacoes'];

    // === TRATAMENTO DA IMAGEM ===
    $imagem_atual = $_POST['imagem_atual'];
    $imagem_final = $imagem_atual;

    if (!empty($_FILES['imagemCapa']['name'])) {
        // Enviou nova imagem
        $ext = pathinfo($_FILES['imagemCapa']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid("capa_") . ".$ext";
        $destino = "../assets/uploads/" . $novo_nome;

        if (move_uploaded_file($_FILES['imagemCapa']['tmp_name'], $destino)) {

            // Apaga imagem antiga se existir
            if (!empty($imagem_atual) && file_exists("../assets/uploads/" . $imagem_atual)) {
                unlink("../assets/uploads/" . $imagem_atual);
            }

            $imagem_final = $novo_nome;
        }
    }

    // === UPDATE TABELA produtos ===
    $sql_prod = "
        UPDATE produtos SET
            titulo_album = ?, artista_banda = ?, ano_lancamento = ?, 
            genero_musical = ?, formato = ?, continente = ?, 
            imagem_capa = ?, observacoes = ?
        WHERE id = ? AND usuario_id = ?
    ";

    $stmt = $conn->prepare($sql_prod);
    $stmt->bind_param(
        "ssisssssii",
        $titulo, $artista, $ano, $genero, $formato, $continente,
        $imagem_final, $observacoes, $produto_id, $usuario_id
    );
    $stmt->execute();
    $stmt->close();

    // === UPDATE disco_info ===
    $sql_disco = "
        UPDATE disco_info SET
            condicao_disco = ?, versao = ?, codigo_catalogo = ?
        WHERE produto_id = ?
    ";

    $stmt = $conn->prepare($sql_disco);
    $stmt->bind_param("sssi", $cond_disco, $versao, $codigo, $produto_id);
    $stmt->execute();
    $stmt->close();

    // === UPDATE capa_info ===
    $sql_capa = "
        UPDATE capa_info SET
            tipo_embalagem = ?, condicao_capa = ?, encarte_original = ?, obi = ?
        WHERE produto_id = ?
    ";

    $stmt = $conn->prepare($sql_capa);
    $stmt->bind_param("ssssi", $embalagem, $cond_capa, $encarte, $obi, $produto_id);
    $stmt->execute();
    $stmt->close();

    // === UPDATE edicao_info ===
    $sql_edicao = "
        UPDATE edicao_info SET
            edicao_limitada = ?, numero_edicao = ?, prensagem = ?, assinado = ?
        WHERE produto_id = ?
    ";

    $stmt = $conn->prepare($sql_edicao);
    $stmt->bind_param("ssssi", $limitada, $num_edicao, $prensagem, $assinado, $produto_id);
    $stmt->execute();
    $stmt->close();

    // TUDO CERTO
    $conn->commit();

    header("Location: home.php?success=editado");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    echo "<h2 style='color:red; padding:20px;'>Erro ao editar: " . $e->getMessage() . "</h2>";
}
