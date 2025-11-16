<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];

$sql = "
SELECT 
    p.id AS produto_id, p.titulo_album, p.artista_banda, p.imagem_capa, p.observacoes,

    d.condicao_disco, d.versao, d.codigo_catalogo,

    c.tipo_embalagem, c.condicao_capa, c.encarte_original, c.obi,

    e.edicao_limitada, e.numero_edicao, e.prensagem, e.assinado

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
.grid-card {
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease;
    border-radius: 10px;
}
.grid-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}
.card-img-top {
    height: 180px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
}
.modal-img {
    width: 200px;
    border-radius: 10px;
}

.editbtn{
   background: #ADD8E6;
}
</style>

<div class="container mt-4">
    <div class="row g-4">

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card grid-card" 
                     onclick="abrirDetalhes(<?= $row['produto_id'] ?>)"
                     data-bs-toggle="modal"
                     data-bs-target="#modalDetalhes">

                    <img src="../assets/uploads/<?= $row['imagem_capa'] ?>" 
                         class="card-img-top" 
                         alt="Capa">

                    <div class="card-body text-center">
                        <h6 class="mb-1 fw-bold"><?= $row['titulo_album'] ?></h6>
                        <p class="text-muted small mb-0"><?= $row['artista_banda'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Guarda os dados para o JS -->
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

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalhes do Item</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4 text-center">
                        <img id="modal_img" class="modal-img mb-3">
                    </div>

                    <div class="col-md-8">
                        <div id="modal_info"></div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                <button class="btn editbtn">Editar</button>
                <button class="btn btn-danger">Excluir</button>
            </div>

        </div>
    </div>
</div>

<script>
function abrirDetalhes(id) {
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
        <p><b>Prensagem:</b> ${data.prensagem}</p>
        <p><b>Assinado:</b> ${data.assinado}</p>
        <hr>
        <p><b>Observações:</b><br> ${data.observacoes ?? "<i>Sem observações</i>"}</p>
    `;
}
</script>
