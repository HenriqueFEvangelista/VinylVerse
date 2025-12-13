<div class="card shadow-sm p-3 mt-4">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-disc"></i> Discos por Artista
    </h5>

    <canvas id="graficoArtistas" height="200"></canvas>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById("graficoArtistas");

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: <?= json_encode($labelsArtistas) ?>,
            datasets: [{
                data: <?= json_encode($dadosArtistas) ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });

});
</script>

