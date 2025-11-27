<?php

include '../config/db.php';
include '../components/auth.php'; 

$usuario_id = $_SESSION['user_id'];

$sql = "
SELECT 
    p.id AS produto_id, p.titulo_album, p.artista_banda, p.imagem_capa, p.observacoes, p.formato,

    d.condicao_disco, d.versao, d.codigo_catalogo,

    c.tipo_embalagem, c.condicao_capa, c.encarte_original, c.obi,

    e.edicao_limitada, e.numero_edicao, e.assinado

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

                    <img src="../assets/uploads/<?= $row['imagem_capa'] ?>" 
                         class="card-img-top" 
                         alt="Capa">

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

    document.getElementById("modal_img").src = "../assets/uploads/" + data.imagem_capa;

    document.getElementById("modal_info").innerHTML = `
        <p><b>Título:</b> ${data.titulo_album}</p>
        <p><b>Artista:</b> ${data.artista_banda}</p>
        <p><b>Condição do Disco:</b> ${data.condicao_disco ?? "N/A"}</p>
        <p><b>Versão:</b> ${data.versao ?? "N/A"}</p>
        <p><b>Código:</b> ${data.codigo_catalogo ?? "N/A"}</p>
        <hr>
        <p><b>Embalagem:</b> ${data.tipo_embalagem ?? "N/A"}</p>
        <p><b>Condição da Capa:</b> ${data.condicao_capa ?? "N/A"}</p>
        <p><b>Encarte:</b> ${data.encarte_original}</p>
        <p><b>OBI:</b> ${data.obi}</p>
        <hr>
        <p><b>Edição Limitada:</b> ${data.edicao_limitada}</p>
        <p><b>N° Edição:</b> ${data.numero_edicao}</p>
        <p><b>Assinado:</b> ${data.assinado}</p>
        <hr>
        <p><b>Observações:</b><br> ${data.observacoes ?? "<i>Sem observações</i>"}</p>
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

function aplicarFiltros() {
    const termo = (inputPesquisa?.value || "").toLowerCase();
    const formatoSelecionado = selectFiltro?.value || "";

    const cards = document.querySelectorAll(".grid-card");

    cards.forEach(card => {
        const titulo = card.dataset.titulo;
        const artista = card.dataset.artista;
        const formato = card.dataset.formato;

        const matchPesquisa =
            titulo.includes(termo) ||
            artista.includes(termo);

        const matchFormato =
            formatoSelecionado === "" || formato === formatoSelecionado;

        card.parentElement.style.display = (matchPesquisa && matchFormato)
            ? "block"
            : "none";
    });
}

if (inputPesquisa) inputPesquisa.addEventListener("input", aplicarFiltros);
if (selectFiltro) selectFiltro.addEventListener("change", aplicarFiltros);
</script>
