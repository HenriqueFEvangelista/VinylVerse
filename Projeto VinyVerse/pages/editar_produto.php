<?php
session_start();
include '../config/db.php';
include '../components/KeyBoardESC.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID do produto não informado.");
}

$produto_id = intval($_GET['id']);
$usuario_id = $_SESSION['user_id'];

// Buscar dados completos
$sql = "
SELECT 
    p.*, 
    d.condicao_disco, d.versao AS disco_versao, d.codigo_catalogo,
    c.tipo_embalagem, c.condicao_capa, c.encarte_original, c.obi,
    e.edicao_limitada, e.numero_edicao, e.versao AS edicao_versao, e.assinado
FROM produtos p
LEFT JOIN disco_info d ON p.id = d.produto_id
LEFT JOIN capa_info c ON p.id = c.produto_id
LEFT JOIN edicao_info e ON p.id = e.produto_id
WHERE p.id = ? AND p.usuario_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $produto_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Produto não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>

<div class="container my-5">

    <button class="btn btn-danger mb-3" onclick="history.back()">
        <i class="bi bi-arrow-left"></i> Voltar
    </button>

    <button type="button" id="toggleTheme" class="btn btn-secondary position-fixed top-0 end-0 m-3">
        <i id="themeIcon" class="bi bi-moon-stars"></i>
    </button>

    <h2 class="mb-4">Editar Produto</h2>

    <form action="../actions/salvar_edicao.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="produto_id" value="<?= $produto_id ?>">
        <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($item['imagem_capa']) ?>">

        <!-- INFORMAÇÕES GERAIS -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Informações Gerais</div>

            <div class="card-body row g-3">

                <div class="col-md-6">
                    <label class="form-label">Título do Álbum</label>
                    <input type="text" class="form-control" name="titulo" value="<?= $item['titulo_album'] ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Artista / Banda</label>
                    <input type="text" class="form-control" name="artista" value="<?= $item['artista_banda'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Ano de Lançamento</label>
                    <input type="number" class="form-control" name="ano" min="1000" 
                           value="<?= $item['ano_lancamento'] ?>" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Gênero Musical</label>
                    <input type="text" class="form-control" name="genero" value="<?= $item['genero_musical'] ?>">
                </div>

            </div>
        </div>

        <!-- INFORMAÇÕES DO DISCO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Informações do Disco</div>

            <div class="card-body row g-3">

                <!-- formato -->
                <div class="col-md-6">
                    <label class="form-label">Formato</label>
                    <select class="form-select" name="formato">
                        <?php
                        $formatos = ["LP", "CD", "Set Box LP", "Set Box CD"];
                        foreach ($formatos as $f) {
                            $s = $item['formato'] == $f ? "selected" : "";
                            echo "<option value='$f' $s>$f</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- imagem -->
                <div class="col-md-6">
  <label class="form-label fw-bold">Imagem da Capa</label>

  <div class="d-flex align-items-center gap-2 mb-2">

    <!-- Botão Local -->
    <button type="button" id="btnLocal" class="btn btn-outline-secondary icon-btn active">
      <i class="bi bi-folder-fill"></i>
    </button>

    <!-- Botão URL -->
    <button type="button" id="btnURL" class="btn btn-outline-secondary icon-btn">
      <i class="bi bi-globe2"></i>
    </button>

    <!-- Preview -->
    <img
      id="previewImagem"
      src="../assets/uploads/<?= htmlspecialchars($item['imagem_capa']) ?>"
      width="70"
      height="70"
      class="rounded border"
      style="object-fit:cover"
      onerror="this.style.display='none'"
    >
  </div>

  <!-- Input LOCAL -->
  <input
    class="form-control"
    type="file"
    id="inputLocal"
    name="imagemCapa"
    accept="image/*"
  >

  <!-- Input URL -->
  <input
    class="form-control d-none mt-2"
    type="url"
    id="inputURL"
    name="imagemURL"
    placeholder="Cole o link da capa (http ou https)"
  >
</div>


                <!-- continente -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Continente de Origem</label>
                    <select class="form-select" name="continente">
                        <?php
                        $continentes = [
                            "nacional" => "Nacional",
                            "africa" => "África",
                            "america_sul" => "América do Sul",
                            "america_norte" => "América do Norte",
                            "asia" => "Ásia",
                            "europa" => "Europa",
                            "oceania" => "Oceania",
                            "antartida" => "Antártida",
                            "reino_unido" => "Reino Unido",
                            "japao" => "Japão",
                            "alemanha" => "Alemanha",
                            "estados_unidos" => "Estados Unidos",
                        ];
                        foreach ($continentes as $k => $v) {
                            $s = $item['continente'] == $k ? "selected" : "";
                            echo "<option value='$k' $s>$v</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- condição disco -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Condição do Disco</label>
                    <select class="form-select" name="condicao_disco">
                        <?php
                        $cond = [
                            "disco_poor" => "Poor",
                            "disco_fair" => "Fair",
                            "disco_good" => "Good",
                            "disco_vg" => "Very Good",
                            "disco_excellent" => "Excellent",
                            "disco_near_mint" => "Near Mint",
                            "disco_mint" => "Mint"
                        ];
                        foreach ($cond as $k => $v) {
                            $s = $item['condicao_disco'] == $k ? "selected" : "";
                            echo "<option value='$k' $s>$v</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- embalagem -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Embalagem</label>
                    <select class="form-select" name="embalagem">
                        <option value="Lacrado" <?= $item['tipo_embalagem']=='Lacrado'?'selected':'' ?>>Lacrado</option>
                        <option value="Aberto" <?= $item['tipo_embalagem']=='Aberto'?'selected':'' ?>>Aberto</option>
                    </select>
                </div>

                <!-- versão disco -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Versão (Disco)</label>
                    <input type="text" class="form-control" name="disco_versao" 
                           value="<?= $item['disco_versao'] ?>">
                </div>


            </div>
        </div>

        <!-- INFORMAÇÕES DA CAPA -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Informações da Capa</div>

            <div class="card-body row g-3">

                <div class="col-md-2">
                    <label class="form-label">Encarte Original?</label>
                    <select class="form-select" name="encarte">
                        <option <?= $item['encarte_original']=='Sim'?'selected':'' ?>>Sim</option>
                        <option <?= $item['encarte_original']=='Não'?'selected':'' ?>>Não</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Possui OBI?</label>
                    <select class="form-select" name="obi">
                        <option <?= $item['obi']=='Sim'?'selected':'' ?>>Sim</option>
                        <option <?= $item['obi']=='Não'?'selected':'' ?>>Não</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Condição da Capa</label>
                    <select class="form-select" name="condicao_capa">
                        <?php
                        $condCapa = [
                            "capa_poor" => "Poor",
                            "capa_fair" => "Fair",
                            "capa_good" => "Good",
                            "capa_vg" => "VG",
                            "capa_excellent" => "Excellent",
                            "capa_near_mint" => "Near Mint",
                            "capa_mint" => "Mint"
                        ];
                        foreach ($condCapa as $k => $v) {
                            $s = $item['condicao_capa'] == $k ? "selected" : "";
                            echo "<option value='$k' $s>$v</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>
        </div>

        <!-- INFORMAÇÕES DA EDIÇÃO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Informações da Edição</div>

            <div class="card-body row g-3">

                <div class="col-md-4">
                    <label class="form-label">Edição Limitada?</label>
                    <select class="form-select" name="edicao_limitada">
                        <option <?= $item['edicao_limitada']=='Não'?'selected':'' ?>>Não</option>
                        <option <?= $item['edicao_limitada']=='Sim'?'selected':'' ?>>Sim</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Número da Edição</label>
                    <input type="text" class="form-control" name="numero_edicao" 
                           value="<?= $item['numero_edicao'] ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Versão</label>
                    <select class="form-select" name="versao_edicao">
                        <option <?= $item['edicao_versao']=='Primeira Prensagem'?'selected':'' ?>>Primeira Prensagem</option>
                        <option <?= $item['edicao_versao']=='Reedição'?'selected':'' ?>>Reedição</option>
                        <option <?= $item['edicao_versao']=='Delux'?'selected':'' ?>>Delux</option>
                        <option <?= $item['edicao_versao']=='Super delux'?'selected':'' ?>>Super delux</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Assinado?</label>
                    <select class="form-select" name="assinado">
                        <option <?= $item['assinado']=='Não'?'selected':'' ?>>Não</option>
                        <option <?= $item['assinado']=='Sim'?'selected':'' ?>>Sim</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- OBSERVAÇÕES -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Observações</div>

            <div class="card-body">
                <textarea class="form-control" name="observacoes" rows="4"><?= $item['observacoes'] ?></textarea>
            </div>
        </div>

        <!-- BOTÕES -->
        <div class="text-end">
            <a href="home.php" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-success">Salvar Alterações</button>
        </div>

    </form>

</div>


<script>
  const btnLocal = document.getElementById("btnLocal");
  const btnURL = document.getElementById("btnURL");

  const inputLocal = document.getElementById("inputLocal");
  const inputURL = document.getElementById("inputURL");

  const preview = document.getElementById("previewImagem");

  // --- MODO LOCAL ---
  btnLocal.addEventListener("click", () => {
    btnLocal.classList.add("active");
    btnURL.classList.remove("active");

    inputLocal.classList.remove("d-none");
    inputURL.classList.add("d-none");

    inputURL.value = "";
  });

  // --- MODO URL ---
  btnURL.addEventListener("click", () => {
    btnURL.classList.add("active");
    btnLocal.classList.remove("active");

    inputURL.classList.remove("d-none");
    inputLocal.classList.add("d-none");

    inputLocal.value = "";
  });

  // --- PREVIEW ARQUIVO LOCAL ---
  inputLocal.addEventListener("change", () => {
    if (inputLocal.files && inputLocal.files[0]) {
      preview.src = URL.createObjectURL(inputLocal.files[0]);
      preview.style.display = "block";
    }
  });

  // --- PREVIEW URL ---
  inputURL.addEventListener("input", () => {
    if (inputURL.value.trim()) {
      preview.src = inputURL.value;
      preview.style.display = "block";
    }
  });

  /* =============================
          TEMA ESCURO
============================= */
const html = document.documentElement;
const themeBtn = document.getElementById("toggleTheme");
const themeIcon = document.getElementById("themeIcon");

const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
  html.setAttribute("data-bs-theme", savedTheme);
  themeIcon.className = savedTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars";
}

themeBtn.addEventListener("click", () => {
  const current = html.getAttribute("data-bs-theme");
  const newTheme = current === "light" ? "dark" : "light";

  html.setAttribute("data-bs-theme", newTheme);
  localStorage.setItem("theme", newTheme);

  themeIcon.className = newTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars";
});
</script>

</body>
</html>
