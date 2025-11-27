<?php
$DISCOGS_TOKEN = "kZXiXudVfKXheEYLlnDQeMRdWWBZIluRwJjABFsY";

$releaseId = $_GET['id'] ?? null;
if (!$releaseId) die("<p class='text-danger'>ID não informado.</p>");

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

// Release completo
$releaseUrl = "https://api.discogs.com/releases/$releaseId";
$release = json_decode(httpGET($releaseUrl, $headers), true);

$title   = $release["title"] ?? "Sem título";
$artists = isset($release["artists"]) ? implode(", ", array_column($release["artists"], "name")) : "Artista não informado";
$year    = $release["year"] ?? "—";
$cover   = $release["images"][0]["uri"] ?? "";

// Marketplace
$marketUrl = "https://api.discogs.com/marketplace/search?release_id=$releaseId&per_page=10";
$market = json_decode(httpGET($marketUrl, $headers), true);
$prices = $market["listings"] ?? [];
?>

<style>
.modal-album-cover {
    width: 100%;
    border-radius: 8px;
}
.track-item {
    padding: 6px 0;
    border-bottom: 1px solid #ddd;
}
.price-box {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 10px;
    background: #fafafa;
}
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-5">
            <img src="<?= $cover ?>" class="modal-album-cover shadow-sm mb-3">
        </div>

        <div class="col-md-7">
            <h3 class="fw-bold"><?= $artists ?></h3>
            <h5 class="text-muted"><?= $title ?> (<?= $year ?>)</h5>

            <hr>

            <h5 class="fw-semibold">Faixas</h5>
            <ul class="list-group mb-3">
                <?php foreach ($release["tracklist"] as $t): ?>
                    <li class="list-group-item">
                        <b><?= $t["position"] ?></b> — <?= $t["title"] ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5 class="fw-semibold">Preços no Marketplace</h5>

            <?php if ($prices): ?>
                <?php foreach ($prices as $p): ?>
                    <div class="price-box">
                        <b><?= $p["price"]["value"] ?> <?= $p["price"]["currency"] ?></b><br>
                        Condição: <?= $p["condition"] ?><br>
                        Vendedor: <?= $p["seller"]["username"] ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nenhum preço encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
