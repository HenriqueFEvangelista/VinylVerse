<?php

include '../config/db.php';
include '../components/auth.php'; 

$usuario_id = $_SESSION['user_id'];

$sql = "
SELECT 
    p.id AS produto_id, 
    p.titulo_album, 
    p.artista_banda, 
    p.imagem_capa, 
    p.observacoes, 
    p.formato, 
    p.continente,
    p.ano_lancamento,

    d.condicao_disco, 
    d.versao, 
    d.codigo_catalogo,

    c.tipo_embalagem, 
    c.condicao_capa, 
    c.encarte_original, 
    c.obi,

    e.edicao_limitada, 
    e.numero_edicao, 
    e.assinado

FROM produtos p
LEFT JOIN disco_info d ON p.id = d.produto_id
LEFT JOIN capa_info c  ON p.id = c.produto_id
LEFT JOIN edicao_info e ON p.id = e.produto_id

WHERE p.usuario_id = ?
ORDER BY p.id DESC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="../assets/css/gridView.css">

<div class="container mt-4">
    <div class="row g-4">

        <?php while ($row = $result->fetch_assoc()): ?>

            <?php
                $tituloEsc = htmlspecialchars(strtolower($row['titulo_album']));
                $artistaEsc = htmlspecialchars(strtolower($row['artista_banda']));
            ?>

            <div class="col-6 col-md-4 col-xl-2 item-card-wrapper">
                <div class="card grid-card"
                     data-formato="<?= $row['formato'] ?>"
                     data-titulo="<?= $tituloEsc ?>"
                     data-artista="<?= $artistaEsc ?>"
                     onclick="abrirDetalhes(<?= $row['produto_id'] ?>)"
                     data-bs-toggle="modal"
                     data-bs-target="#modalDetalhes">

                    <?php
                    $imgPath = !empty($row['imagem_capa'])
                        ? "../assets/uploads/" . $row['imagem_capa']
                        : "../assets/img/default_cover.png";
                    ?>

                    <img src="<?= $imgPath ?>" 
                        class="card-img-top" 
                        alt="Capa" 
                        style="object-fit: cover; height: 220px;">


                    <div class="card-body text-center">
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($row['titulo_album']) ?></h6>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($row['artista_banda']) ?></p>
                    </div>
                </div>
            </div>

            <script>
                window["produto_<?= $row['produto_id'] ?>"] = <?= json_encode($row) ?>;
            </script>

        <?php endwhile; ?>

    </div>

     <div id="msgVazio" class="alert alert-warning text-center mt-4" style="display:none;">
            Nenhum item encontrado. Adicione um novo produto!
        </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header text-white" style="background: #6c63ff;">
                <h5 class="modal-title">Detalhes do Item</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">

                <div class="col-md-4 text-center">
                    <img id="modal_img" class="modal-img mb-3">
                </div>

                <div class="col-md-8">
                    <div class="modal-info-wrapper">
                        <div id="modal_info"></div>
                    </div>
                </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                <button class="btn btn-primary" onclick="editarProduto()">Editar</button>
                <button class="btn btn-danger btn-excluir">Excluir</button>
            </div>

        </div>
    </div>
</div>


<script>
/* ==========================
      ABRIR DETALHES
========================== */
let produtoAtual = null;

function abrirDetalhes(id) {
    produtoAtual = id;
    const data = window["produto_" + id];

    // Fallback para imagem ausente
    const modalImg = data.imagem_capa && data.imagem_capa.trim() !== ""
        ? `../assets/uploads/${data.imagem_capa}`
        : `../assets/img/default_cover.png`;

    document.getElementById("modal_img").src = modalImg;

    const badge = (valor) => {
        if (!valor) return `<span class="badge bg-secondary">N/A</span>`;
        if (valor === "Sim") return `<span class="badge bg-success">Sim</span>`;
        if (valor === "Não") return `<span class="badge bg-danger">Não</span>`;
        return `<span class="badge bg-primary">${valor}</span>`;
    };

    document.getElementById("modal_info").innerHTML = `
        <h5 class="section-title">Informações do Álbum</h5>
        <p><b>Título:</b> ${data.titulo_album}</p>
        <p><b>Artista:</b> ${data.artista_banda}</p>
        <p><b>Origem:</b> ${data.continente || "N/A"}</p>
        <p><b>Formato:</b> ${data.formato}</p>
        <p><b>Ano:</b> ${data.ano_lancamento || "N/A"}</p>


        <hr>

        <h5 class="section-title">Disco</h5>
        <p><b>Condição do Disco:</b> ${data.condicao_disco || "N/A"}</p>
        <p><b>Versão:</b> ${data.versao || "N/A"}</p>
        <p><b>Código de Catálogo:</b> ${data.codigo_catalogo || "N/A"}</p>

        <hr>

        <h5 class="section-title">Capa e Embalagem</h5>
        <p><b>Tipo de Embalagem:</b> ${data.tipo_embalagem || "N/A"}</p>
        <p><b>Condição da Capa:</b> ${data.condicao_capa || "N/A"}</p>
        <p><b>Encarte Original:</b> ${badge(data.encarte_original)}</p>
        <p><b>OBI:</b> ${badge(data.obi)}</p>

        <hr>

        <h5 class="section-title">Edição</h5>
        <p><b>Edição Limitada:</b> ${badge(data.edicao_limitada)}</p>
        <p><b>Número da Edição:</b> ${data.numero_edicao || "N/A"}</p>
        <p><b>Assinado:</b> ${badge(data.assinado)}</p>

        <hr>

        <h5 class="section-title">Observações</h5>
        <p>${data.observacoes || "<i>Sem observações</i>"}</p>
    `;
}




/* ==========================
      EXCLUIR ITEM
========================== */
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("btn-excluir")) {

        if (!produtoAtual) return;

        if (!confirm("Tem certeza que deseja excluir este item?")) return;

        fetch("../actions/excluir_produto.php?id=" + produtoAtual)
        .then(r => r.text())
        .then(() => {
            alert("Produto excluído!");
            location.reload();
        });
    }
});


/* ==========================
      EDITAR ITEM
========================== */
function editarProduto() {
    if (!produtoAtual) return;

    window.location.href = "../pages/editar_produto.php?id=" + produtoAtual;
}


/* ==========================
      FILTRO (JS PURO)
========================== */

const inputPesquisa = document.getElementById("pesquisa");
const selectFiltro = document.getElementById("filtro");
const selectArtista = document.getElementById("filtroArtista");

function aplicarFiltros() {
    const termo = (inputPesquisa?.value || "").toLowerCase();
    const formatoSelecionado = selectFiltro?.value || "";
    const artistaSelecionado = selectArtista?.value || "";

    const cards = document.querySelectorAll(".grid-card");
    let totalVisiveis = 0;

    cards.forEach(card => {
        const titulo = card.dataset.titulo;
        const artista = card.dataset.artista;
        const formato = card.dataset.formato;

        const matchPesquisa =
            titulo.includes(termo) ||
            artista.includes(termo);

        const matchFormato =
            formatoSelecionado === "" || formato === formatoSelecionado;

        const matchArtista =
            artistaSelecionado === "" || artista === artistaSelecionado;

        const visivel = (matchPesquisa && matchFormato && matchArtista);

        card.parentElement.style.display = visivel ? "block" : "none";

        if (visivel) totalVisiveis++;
    });

    // mensagem vazio
    const msg = document.getElementById("msgVazio");
    msg.style.display = totalVisiveis === 0 ? "block" : "none";
}

if (inputPesquisa) inputPesquisa.addEventListener("input", aplicarFiltros);
if (selectFiltro) selectFiltro.addEventListener("change", aplicarFiltros);
if (selectArtista) selectArtista.addEventListener("change", aplicarFiltros);

aplicarFiltros();

</script>
