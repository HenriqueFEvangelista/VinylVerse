<?php
session_start();
include '../config/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Inicia a transação
    $conn->begin_transaction();

    try {

        // ======== DADOS GERAIS ========
        $usuario_id     = $_SESSION['user_id'];
        $titulo_album   = $_POST['titulo'] ?? '';
        $artista_banda  = $_POST['artista'] ?? '';
        $ano_lancamento = $_POST['ano'] ?? '';
        $genero_musical = $_POST['genero'] ?? '';
        $formato        = $_POST['formato'] ?? '';
        $continente     = $_POST['continente'] ?? '';
        $observacoes    = $_POST['observacoes'] ?? '';

        // ======== UPLOAD DE IMAGEM ========
        $imagem_capa = null;

        if (!empty($_FILES['imagemCapa']['name'])) {

            $extensao = pathinfo($_FILES['imagemCapa']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid('capa_') . '.' . $extensao;
            $destino = "../assets/uploads/" . $nomeArquivo;

            if (move_uploaded_file($_FILES['imagemCapa']['tmp_name'], $destino)) {
                $imagem_capa = $nomeArquivo;
            }
        }

        // ======== INSERE EM PRODUTOS ========
        $sql_produto = "INSERT INTO produtos 
            (usuario_id, titulo_album, artista_banda, ano_lancamento, genero_musical, formato, continente, imagem_capa, observacoes)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_produto);
        $stmt->bind_param(
            "issssssss",
            $usuario_id,
            $titulo_album,
            $artista_banda,
            $ano_lancamento,
            $genero_musical,
            $formato,
            $continente,
            $imagem_capa,
            $observacoes
        );
        $stmt->execute();
        $produto_id = $stmt->insert_id;
        $stmt->close();

        // ======== INFORMAÇÕES DO DISCO ========
        $condicao_disco = $_POST['condicao_disco'] ?? '';
        $embalagem      = $_POST['embalagem'] ?? 'Aberto'; // Lacrado / Aberto
        $versao = $_POST['versao'] ?? '';
        $codigo_catalogo = $_POST['codigo'] ?? '';

        $sql_disco = "INSERT INTO disco_info (produto_id, condicao_disco, versao, codigo_catalogo)
                      VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_disco);
        $stmt->bind_param("isss", $produto_id, $condicao_disco, $versao, $codigo_catalogo);
        $stmt->execute();
        $stmt->close();

        // ======== INFORMAÇÕES DA CAPA ========
        $condicao_capa    = $_POST['condicao_capa'] ?? '';
        $encarte_original = $_POST['encarte'] ?? 'Não';
        $obi              = $_POST['obi'] ?? 'Não';

        $sql_capa = "INSERT INTO capa_info (produto_id, tipo_embalagem, condicao_capa, encarte_original, obi)
                     VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_capa);
        $stmt->bind_param("issss", $produto_id, $embalagem, $condicao_capa, $encarte_original, $obi);
        $stmt->execute();
        $stmt->close();

        // ======== INFORMAÇÕES DA EDIÇÃO ========
        $limitada       = $_POST['limitada'] ?? 'Não';
        $numero_edicao  = $_POST['numero_edicao'] ?? '';
        $versao_edicao = $_POST['versao'] ?? 'Reedição';
        $assinado       = $_POST['assinado'] ?? 'Não';

        $sql_edicao = "INSERT INTO edicao_info (produto_id, edicao_limitada, numero_edicao, versao, assinado)
               VALUES (?, ?, ?, ?, ?)";


        $stmt = $conn->prepare($sql_edicao);
        $stmt->bind_param("issss", $produto_id, $limitada, $numero_edicao, $versao_edicao, $assinado);
        $stmt->execute();
        $stmt->close();

        // ======== SUCESSO ========
        $conn->commit();

        header("Location: ../pages/home.php?success=cadastrado");
        exit();

    } catch (Exception $e) {

        $conn->rollback();

        echo "<div style='padding: 20px; color: red; font-weight: bold;'>
                Erro ao salvar produto: " . $e->getMessage() . "
              </div>";
    }

    $conn->close();

} else {
    header("Location: ../pages/home.php");
    exit();
}
?>
