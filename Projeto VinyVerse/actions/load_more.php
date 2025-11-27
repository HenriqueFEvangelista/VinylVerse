<?php
$DISCOGS_TOKEN = "kZXiXudVfKXheEYLlnDQeMRdWWBZIluRwJjABFsY";

$page = $_GET['page'] ?? 1;
$q = isset($_GET['q']) ? urlencode($_GET['q']) : "rock";

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

foreach ($results as $item):
?>

<div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card shadow-sm border-0 h-100" onclick="abrirDetalhes(<?= $item['id'] ?>)">
        <img src="<?= $item['cover_image'] ?>" class="card-img-top">
        <div class="card-body">
            <h5 class="card-title"><?= $item['title'] ?></h5>
            <p class="text-muted"><?= $item['year'] ?? '' ?></p>
        </div>

        <button
            class="btn btn-outline-primary w-100 mt-auto"
            onclick="abrirDetalhes(<?= $item['id'] ?>); event.stopPropagation();"
        >
            Ver detalhes
        </button>
    </div>
</div>

<?php endforeach; ?>
