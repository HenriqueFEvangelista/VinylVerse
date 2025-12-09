<?php
$DISCOGS_TOKEN = "kZXiXudVfKXheEYLlnDQeMRdWWBZIluRwJjABFsY";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo "<p>Erro: ID inválido.</p>";
    exit;
}

$url = "https://api.discogs.com/releases/$id";
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

if (!$data) {
    echo "<p>Erro ao carregar detalhes.</p>";
    exit;
}

$title = $data['title'] ?? 'Sem título';
$year = $data['year'] ?? '—';
$country = $data['country'] ?? '—';
$genres = isset($data['genres']) ? implode(', ', $data['genres']) : '—';
$styles = isset($data['styles']) ? implode(', ', $data['styles']) : '—';
$labels = isset($data['labels']) ? array_map(fn($l)=>$l['name']." (".$l['catno'].")", $data['labels']) : [];
$tracklist = $data['tracklist'] ?? [];
$images = $data['images'] ?? [];
$extraArtists = $data['extraartists'] ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">

    <!-- Título e informações principais -->
    <h2 class="fw-bold text-center mb-3"><?= htmlspecialchars($title) ?></h2>

    <div class="row g-4">

        <!-- Coluna ESQUERDA: Carrossel de Imagens -->
        <div class="col-md-6">
            <?php if ($images): ?>
                <div id="carouselCapas" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= $img['uri'] ?>" class="d-block w-100 rounded shadow-sm" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselCapas" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselCapas" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coluna DIREITA: Informações -->
        <div class="col-md-6">
            <div class="p-3 border rounded bg-light">
                <p><strong>Ano:</strong> <?= $year ?></p>
                <p><strong>País:</strong> <?= htmlspecialchars($country) ?></p>
                <p><strong>Gêneros:</strong> <?= htmlspecialchars($genres) ?></p>
                <p><strong>Estilos:</strong> <?= htmlspecialchars($styles) ?></p>
                <p><strong>Gravadoras:</strong> <?= htmlspecialchars(implode(', ', $labels)) ?></p>
            </div>
        </div>
    </div>

    <!-- Tracklist com slider horizontal -->
    <?php if ($tracklist): ?>
        <h4 class="mt-4">Tracklist</h4>
        <div class="d-flex overflow-auto gap-3 p-2" style="white-space: nowrap; max-width: 100%;">
            <?php foreach ($tracklist as $t): ?>
                <div class="border rounded p-3 shadow-sm track-item" style="min-width: 220px; display:inline-block;">
                    <strong><?= htmlspecialchars($t['position'] ?? '') ?></strong><br>
                    <?= htmlspecialchars($t['title'] ?? '') ?><br>
                    <?php if (!empty($t['duration'])): ?>
                        <span class="text-muted">(<?= $t['duration'] ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Créditos -->
    <?php if ($extraArtists): ?>
        <h4 class="mt-4">Créditos</h4>
        <ul class="list-group">
            <?php foreach ($extraArtists as $a): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($a['name']) ?></strong> —
                    <?= htmlspecialchars($a['role']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
