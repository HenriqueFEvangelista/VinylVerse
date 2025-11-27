<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID do produto não informado.");
}

$produto_id = intval($_GET['id']);
$usuario_id = $_SESSION['user_id'];

// Busca todos os dados do item
$sql = "
SELECT 
    p.*, 
    d.condicao_disco, d.versao, d.codigo_catalogo,
    c.tipo_embalagem, c.condicao_capa, c.encarte_original, c.obi,
    e.edicao_limitada, e.numero_edicao, e.prensagem, e.assinado
FROM produtos p
LEFT JOIN disco_info d ON p.id = d.produto_id
LEFT JOIN capa_info c ON p.id = c.produto_id
LEFT JOIN edicao_info e ON p.id = e.produto_id
WHERE p.id = ? AND p.usuario_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Produto não encontrado ou não pertence ao usuário.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">
    <h3 class="mb-4">Editar Produto</h3>

    <form action="../actions/salvar_edicao.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow">

        <input type="hidden" name="produto_id" value="<?= $produto_id ?>">
        <input type="hidden" name="imagem_atual" value="<?= $item['imagem_capa'] ?>">

        <div class="row g-3">

            <!-- TÍTULO -->
            <div class="col-md-6">
                <label class="form-label">Título do Álbum</label>
                <input type="text" name="titulo" class="form-control" value="<?= $item['titulo_album'] ?>" required>
            </div>

            <!-- ARTISTA -->
            <div class="col-md-6">
                <label class="form-label">Artista / Banda</label>
                <input type="text" name="artista" class="form-control" value="<?= $item['artista_banda'] ?>" required>
            </div>

            <!-- ANO -->
            <div class="col-md-4">
                <label class="form-label">Ano</label>
                <input type="number" name="ano" class="form-control" value="<?= $item['ano_lancamento'] ?>">
            </div>

            <!-- GÊNERO -->
            <div class="col-md-8">
                <label class="form-label">Gênero Musical</label>
                <input type="text" name="genero" class="form-control" value="<?= $item['genero_musical'] ?>">
            </div>

            <!-- FORMATO -->
            <div class="col-md-4">
                <label class="form-label">Formato</label>
                <select name="formato" class="form-select">
                    <option value="LP" <?= $item['formato']=='LP'?'selected':'' ?>>LP</option>
                    <option value="CD" <?= $item['formato']=='CD'?'selected':'' ?>>CD</option>
                    <option value="Set Box LP" <?= $item['formato']=='Set Box LP'?'selected':'' ?>>Set Box LP</option>
                    <option value="Set Box CD" <?= $item['formato']=='Set Box CD'?'selected':'' ?>>Set Box CD</option>
                </select>
            </div>

            <!-- CONTINENTE -->
            <div class="col-md-4">
                <label class="form-label">Continente</label>
                <select name="continente" class="form-select">
                    <?php
                    $conts = [
                        "nacional" => "Nacional",
                        "africa" => "África",
                        "america_sul" => "América do Sul",
                        "america_norte" => "América do Norte",
                        "asia" => "Ásia",
                        "europa" => "Europa",
                        "oceania" => "Oceania",
                        "antartida" => "Antártida"
                    ];
                    foreach ($conts as $key => $label) {
                        $sel = $item['continente'] == $key ? "selected" : "";
                        echo "<option value='$key' $sel>$label</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- IMAGEM -->
            <div class="col-md-4">
                <label class="form-label">Imagem da Capa</label><br>
                
                <img src="../assets/uploads/<?= $item['imagem_capa'] ?>" width="120" class="rounded mb-2">
                
                <input type="file" name="imagemCapa" class="form-control">
            </div>

            <!-- DISCO -->
            <hr class="mt-4">

            <div class="col-md-4">
                <label class="form-label">Condição do Disco</label>
                <input type="text" name="condicao_disco" class="form-control" value="<?= $item['condicao_disco'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Versão</label>
                <input type="text" name="versao" class="form-control" value="<?= $item['versao'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Código de Catálogo</label>
                <input type="text" name="codigo_catalogo" class="form-control" value="<?= $item['codigo_catalogo'] ?>">
            </div>

            <!-- CAPA -->
            <hr class="mt-4">

            <div class="col-md-4">
                <label class="form-label">Tipo de embalagem</label>
                <input type="text" name="embalagem" class="form-control" value="<?= $item['tipo_embalagem'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Condição da Capa</label>
                <input type="text" name="condicao_capa" class="form-control" value="<?= $item['condicao_capa'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Encarte Original</label>
                <select name="encarte" class="form-select">
                    <option value="Sim" <?= $item['encarte_original']=='Sim'?'selected':'' ?>>Sim</option>
                    <option value="Não" <?= $item['encarte_original']=='Não'?'selected':'' ?>>Não</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">OBI</label>
                <select name="obi" class="form-select">
                    <option value="Sim" <?= $item['obi']=='Sim'?'selected':'' ?>>Sim</option>
                    <option value="Não" <?= $item['obi']=='Não'?'selected':'' ?>>Não</option>
                </select>
            </div>

            <!-- EDIÇÃO -->
            <hr class="mt-4">

            <div class="col-md-4">
                <label class="form-label">Edição Limitada</label>
                <select name="edicao_limitada" class="form-select">
                    <option value="Sim" <?= $item['edicao_limitada']=='Sim'?'selected':'' ?>>Sim</option>
                    <option value="Não" <?= $item['edicao_limitada']=='Não'?'selected':'' ?>>Não</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Número da Edição</label>
                <input type="text" name="numero_edicao" class="form-control" value="<?= $item['numero_edicao'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Prensagem</label>
                <input type="text" name="prensagem" class="form-control" value="<?= $item['prensagem'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Assinado</label>
                <select name="assinado" class="form-select">
                    <option value="Sim" <?= $item['assinado']=='Sim'?'selected':'' ?>>Sim</option>
                    <option value="Não" <?= $item['assinado']=='Não'?'selected':'' ?>>Não</option>
                </select>
            </div>

            <!-- OBSERVAÇÕES -->
            <div class="col-12 mt-3">
                <label class="form-label fw-bold">Observações</label>
                <textarea name="observacoes" class="form-control" rows="4"><?= $item['observacoes'] ?></textarea>
            </div>

        </div>

        <div class="text-end mt-4">
            <a href="home.php" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-success">Salvar Alterações</button>
        </div>

    </form>

</div>

</body>
</html>
