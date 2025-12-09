<?php
if (!isset($conn)) {
    include '../config/db.php';
}

if (!isset($user_id) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Consulta para pegar quantidades
$sql = "
SELECT 
    COUNT(*) AS total,
    SUM(formato = 'CD') AS cd,
    SUM(formato = 'LP') AS lp,
    SUM(formato = 'Set Box CD') AS box_cd,
    SUM(formato = 'Set Box LP') AS box_lp
FROM produtos
WHERE usuario_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dados = $stmt->get_result()->fetch_assoc();
?>

<!-- IMPORTAÇÃO DO CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="card shadow-sm p-3 mt-3">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-bar-chart-line-fill"></i> Estatísticas da Coleção
    </h5>

    <canvas id="graficoColecao" height="150"></canvas>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("graficoColecao");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["TOTAL", "CD", "LP", "BoxSet CD", "BoxSet LP"],
            datasets: [{
                label: "Quantidade",
                data: [
                    <?= $dados['total'] ?>,
                    <?= $dados['cd'] ?>,
                    <?= $dados['lp'] ?>,
                    <?= $dados['box_cd'] ?>,
                    <?= $dados['box_lp'] ?>
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
