<?php
include '../config/db.php';
include '../components/auth.php';

$usuario_id = $_SESSION['user_id'];

// Buscar artistas únicos para o appbar
$sqlArtistas = "
    SELECT DISTINCT artista_banda 
    FROM produtos 
    WHERE usuario_id = ?
    ORDER BY artista_banda ASC
";

$stmtArtistas = $conn->prepare($sqlArtistas);
$stmtArtistas->bind_param("i", $usuario_id);
$stmtArtistas->execute();
$resultArtistas = $stmtArtistas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VinilVerse - Home</title>

  <style>
    body {padding-top: 72px;}
  </style>


  <link rel="stylesheet" href="../assets/css/gridView.css">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Ícones Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="light">

  <!-- Appbar + Menu lateral -->
  <?php include '../components/appbar.php'; ?>

  <!-- Conteúdo principal -->
  <?php include '../components/gridView.php'; ?>

  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Tema escuro -->
  
  <script>
  const body = document.body;
  const toggleBtn = document.getElementById("themeToggle");
  const icon = document.getElementById("themeIcon");

  // Carrega tema salvo
  const savedTheme = localStorage.getItem("theme") || "light";
  body.setAttribute("data-bs-theme", savedTheme);
  icon.className = savedTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-fill";

  // Alternância
  toggleBtn.addEventListener("click", () => {
    const current = body.getAttribute("data-bs-theme");
    const next = current === "light" ? "dark" : "light";

    body.setAttribute("data-bs-theme", next);
    localStorage.setItem("theme", next);

    icon.className = next === "dark" ? "bi bi-sun-fill" : "bi bi-moon-fill";
  });

  document.getElementById("logoutBtn").addEventListener("click", function(e) {
    e.preventDefault();
    if (confirm("Tem certeza que deseja sair?")) {
        window.location.href = this.href;
    }
});
</script>


<!-- Modal de confirmação de logout (FORA do offcanvas) -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirmar saída</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Tem certeza que deseja encerrar sua sessão?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="../assets/auth/logout.php" class="btn btn-danger">Sair</a>
      </div>

    </div>
  </div>
</div>

<?php include '../components/keyboardShortcuts.php'; ?>

</body>
</html>
