<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Produto - VinilVerse</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

  <button type="button" id="btnSair" class="btn btn-danger position-fixed top-0 start-0 m-3 shadow-sm" onclick="window.history.back()">
    <i class="bi bi-arrow-left"></i> Voltar
  </button>

  <div class="container my-5">
    <h2 class="text-center mb-4">Cadastro de Produto</h2>

    <form id="formCadastro" method="POST" action="../actions/salvar_produto.php" enctype="multipart/form-data">

      <!-- INFORMAÇÕES GERAIS -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Informações Gerais</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Título do Álbum</label>
            <input type="text" class="form-control" name="titulo" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Artista / Banda</label>
            <input type="text" class="form-control" name="artista" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Ano de Lançamento</label>
            <input type="number" class="form-control" name="ano" min="1000" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Gênero Musical</label>
            <input type="text" class="form-control" name="genero" placeholder="Opcional">
          </div>
        </div>
      </div>

      <!-- INFORMAÇÕES DO DISCO -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Informações do Disco</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Formato</label>
            <select class="form-select" name="formato">
              <option value="LP">LP</option>
              <option value="CD">CD</option>
              <option value="Set Box LP">Set Box LP</option>
              <option value="Set Box CD">Set Box CD</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="imagemCapa" class="form-label fw-bold">Imagem da Capa</label>
            <input class="form-control" type="file" id="imagemCapa" name="imagemCapa" accept="image/*">
          </div>

          <div class="col-md-6">
  <label class="form-label fw-bold">Continente de origem</label>
  <select class="form-select" id="continente" name="continente">
    <option selected disabled>Selecione a origem...</option>

    <!-- CONTINENTES -->
    <option value="nacional">Nacional (Não Importado)</option>
    <option value="africa">África</option>
    <option value="america_sul">América do Sul</option>
    <option value="america_norte">América do Norte</option>
    <option value="asia">Ásia</option>
    <option value="europa">Europa</option>
    <option value="oceania">Oceania</option>
    <option value="antartida">Antártida</option>

    <!-- PAÍSES -->
    <option value="reino_unido">Reino Unido</option>
    <option value="japao">Japão</option>
    <option value="alemanha">Alemanha</option>
    <option value="estados_unidos">Estados Unidos</option>
  </select>
</div>


          <div class="col-md-6">
            <label class="form-label fw-bold">Condição do Disco</label>
            <select class="form-select" name="condicao_disco">
              <option selected disabled>Selecione...</option>
              <option value="disco_poor">Poor (Ruim)</option>
              <option value="disco_fair">Fair (Regular)</option>
              <option value="disco_good">Good (Bom)</option>
              <option value="disco_vg">Very Good (Muito Bom)</option>
              <option value="disco_excellent">Excellent (Excelente)</option>
              <option value="disco_near_mint">Near Mint (Quase Perfeito)</option>
              <option value="disco_mint">Mint (Perfeito / Novo)</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Embalagem</label>
            <select class="form-select" name="embalagem">
              <option value="Lacrado">Lacrado</option>
              <option value="Aberto">Aberto</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Código de Catálogo</label>
            <input type="text" class="form-control" name="codigo">
          </div>
        </div>
      </div>

      <!-- INFORMAÇÕES DA CAPA -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Informações da Capa</div>
        <div class="card-body row g-3">
          <div class="col-md-2">
            <label class="form-label">Com Encarte Original?</label>
            <select class="form-select" name="encarte">
              <option>Sim</option>
              <option>Não</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Possui Obi?</label>
            <select class="form-select" name="obi">
              <option>Sim</option>
              <option>Não</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Condição da Capa</label>
            <select class="form-select" name="condicao_capa">
              <option selected disabled>Selecione...</option>
              <option value="capa_poor">Poor (Ruim)</option>
              <option value="capa_fair">Fair (Regular)</option>
              <option value="capa_good">Good (Bom)</option>
              <option value="capa_vg">Very Good (Muito Bom)</option>
              <option value="capa_excellent">Excellent (Excelente)</option>
              <option value="capa_near_mint">Near Mint (Quase Perfeito)</option>
              <option value="capa_mint">Mint (Perfeito / Novo)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- INFORMAÇÕES DA EDIÇÃO -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Informações da Edição</div>
        <div class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label">Edição Limitada / Numerada?</label>
            <select class="form-select" name="limitada">
              <option>Não</option>
              <option>Sim</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Número da Edição</label>
            <input type="text" class="form-control" name="numero_edicao">
          </div>
          <div class="col-md-4">
            <label class="form-label">Versão</label>
            <select class="form-select" name="versao">
              <option>Primeira Prensagem</option>
              <option>Reedição</option>
              <option>Delux</option>
              <option>Super delux</option>
            </select>

          </div>
          <div class="col-md-4">
            <label class="form-label">Assinado pela Banda/Artista?</label>
            <select class="form-select" name="assinado">
              <option>Não</option>
              <option>Sim</option>
            </select>
          </div>
        </div>
      </div>

      <!-- OBSERVAÇÕES -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Observações</div>
        <div class="card-body">
          <label class="form-label">Observações do Produto (opcional)</label>
          <textarea class="form-control" name="observacoes" rows="4" placeholder="Ex: Pequenos riscos, capa levemente amarelada, edição rara, etc."></textarea>
        </div>
      </div>

      <div class="text-end">
        <button type="button" class="btn btn-success" id="abrirModalBtn">Cadastrar</button>
      </div>
    </form>
  </div>

  <!-- MODAL -->
  <div class="modal fade" id="confirmarCadastroModal" tabindex="-1" aria-labelledby="confirmarCadastroLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="confirmarCadastroLabel">Confirmar Cadastro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <h6 class="fw-bold mb-3">Confira os dados antes de confirmar:</h6>
              <div id="resumoCadastro"></div>
            </div>

            <div class="col-md-4">
              <div class="text-center">
                <img id="previewImagem" class="img-thumbnail mb-3 fade-in" style="max-width: 100%; display: none;">
              </div>

              <div class="card fade-in">
                <div class="card-header fw-bold">Observações</div>
                <div class="card-body">
                  <p id="previewObservacoes" class="text-muted">Nenhuma observação.</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Voltar</button>
          <button type="submit" form="formCadastro" class="btn btn-success">Confirmar Cadastro</button>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php include '../components/KeyBoardESC.php';?>

  <script>
    document.getElementById("abrirModalBtn").addEventListener("click", function () {
      const form = document.getElementById("formCadastro");
      const dados = new FormData(form);

      let resumoHTML = "<ul class='list-group mb-3'>";

      dados.forEach((valor, chave) => {
        if (chave !== "imagemCapa" && chave !== "observacoes") {
          resumoHTML += `
            <li class="list-group-item">
              <strong>${chave}:</strong> ${valor || "<em>não informado</em>"}
            </li>`;
        }
      });

      resumoHTML += "</ul>";
      document.getElementById("resumoCadastro").innerHTML = resumoHTML;

      const obs = dados.get("observacoes")?.trim();
      document.getElementById("previewObservacoes").innerHTML =
        obs ? obs : "<em>nenhuma observação</em>";

      const imagem = document.getElementById("imagemCapa").files[0];
      const imgPreview = document.getElementById("previewImagem");

      if (imagem) {
        imgPreview.src = URL.createObjectURL(imagem);
        imgPreview.style.display = "block";
      } else {
        imgPreview.style.display = "none";
      }

      new bootstrap.Modal(document.getElementById("confirmarCadastroModal")).show();
    });
  </script>

</body>
</html>
