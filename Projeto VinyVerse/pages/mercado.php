<?php
$DISCOGS_TOKEN = "kZXiXudVfKXheEYLlnDQeMRdWWBZIluRwJjABFsY";

include '../components/KeyBoardESC.php';

// Query inicial
$q = isset($_GET['q']) ? urlencode($_GET['q']) : "rock";

// Página inicial sempre 1
$page = 1;

// Monta URL
$url = "https://api.discogs.com/database/search?q=$q&type=release&per_page=20&page=$page";

$headers = [
    "User-Agent: MeuAppVinil/1.0",
    "Authorization: Discogs token=$DISCOGS_TOKEN"
];

function httpGET($u, $h) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $u);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, $h);
    $r = curl_exec($c);
    curl_close($c);
    return $r;
}

$json = httpGET($url, $headers);
$data = json_decode($json, true);
$results = $data["results"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Catálogo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.card:hover { transform: scale(1.05); transition:.2s; cursor:pointer; }

/* Modal corrigido */
.modal-bg {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(3px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-box {
    width: 650px;
    background: #fff;
    border-radius: 12px;
    animation: fadeIn 0.3s ease;
    position: relative;
    padding: 0;
}
.modal-content-scroll {
    max-height: 80vh;
    overflow-y: auto;
    padding: 20px;
}

/* Botões do carrossel SEMPRE clicáveis */
.carousel-control-prev,
.carousel-control-next {
    z-index: 9999 !important;
    pointer-events: auto !important;
}

.close {
    cursor: pointer;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 15px;
    top: 10px;
    z-index: 9999;
}
</style>
</head>

<body class="bg-light">
<a href="home.php" id= "btnSair" class="btn btn-danger position-fixed top-0 start-0 m-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <div class="container mt-5 pt-4">

        <h1 class="text-center mb-4 fw-bold">Catálogo de Discos</h1>

        <!-- Busca -->
        <form class="row justify-content-center mb-4" method="GET">
            <div class="col-md-6 d-flex">
                <input type="text" name="q" class="form-control form-control-lg" placeholder="Buscar..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button class="btn btn-primary btn-lg ms-2">Buscar</button>
            </div>
        </form>

        <!-- Cards -->
        <div class="row g-4" id="cards-container">
            <?php foreach ($results as $item): ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                
                <div class="card shadow-sm border-0 h-100">
                    <img src="<?= $item['cover_image'] ?>" class="card-img-top" onclick="abrirDetalhes(<?= $item['id'] ?>)">
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= $item['title'] ?></h5>
                        <p class="text-muted"><?= $item['year'] ?? '' ?></p>
                    </div>

                    <button
                        class="btn btn-outline-primary w-100 mb-2"
                        onclick="abrirDetalhes(<?= $item['id'] ?>); event.stopPropagation();"
                    >
                        Ver detalhes
                    </button>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- Load more -->
        <div class="text-center my-4">
            <button id="loadMoreBtn" 
                class="btn btn-dark btn-lg" 
                data-page="1" 
                data-q="<?= htmlspecialchars($_GET['q'] ?? 'rock') ?>">
                Carregar mais
            </button>
        </div>

    </div>


<!-- Modal -->
<div id="modalBg" class="modal-bg">
    <div class="modal-box">
        <span id="closeModal" class="close">&times;</span>
        <div class="modal-content-scroll" id="modalContent">Carregando...</div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ----- MODAL -----
function abrirDetalhes(id) {
    document.getElementById("modalBg").style.display = "flex";
    document.getElementById("modalContent").innerHTML = "Carregando...";

    fetch("../actions/detalhes_release.php?id=" + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById("modalContent").innerHTML = html;

            // Inicializa carrossel após inserir HTML
            document.querySelectorAll('.carousel').forEach(c => {
                new bootstrap.Carousel(c, {
                    interval: false
                });
            });
        });
}

document.getElementById("closeModal").onclick = () => {
    document.getElementById("modalBg").style.display = "none";
};

window.onclick = e => {
    if (e.target.id === "modalBg")
        document.getElementById("modalBg").style.display = "none";
};
</script>

<script>
// ----- LOAD MORE -----
document.getElementById("loadMoreBtn").addEventListener("click", function() {
    let btn = this;
    let page = parseInt(btn.getAttribute("data-page")) + 1;
    let q = btn.getAttribute("data-q");

    btn.innerHTML = "Carregando...";

    fetch(`../actions/load_more.php?page=${page}&q=${q}`)
        .then(res => res.text())
        .then(html => {
            if (html.trim() === "") {
                btn.innerHTML = "Não há mais resultados";
                btn.disabled = true;
                return;
            }

            document.getElementById("cards-container")
                .insertAdjacentHTML("beforeend", html);

            btn.setAttribute("data-page", page);
            btn.innerHTML = "Carregar mais";
        });
});
</script>

</body>
</html>
