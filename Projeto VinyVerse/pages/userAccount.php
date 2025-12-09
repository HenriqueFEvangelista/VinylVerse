<?php 
include '../components/auth.php';
include '../config/db.php';
include '../components/KeyBoardESC.php';

// Buscar dados do usuário logado
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created);
$stmt->fetch();
$stmt->close();

// CONTAGENS DA COLEÇÃO
$sqlContagem = "
SELECT 
    COUNT(*) AS total_geral,
    SUM(CASE WHEN formato = 'CD' THEN 1 ELSE 0 END) AS total_cd,
    SUM(CASE WHEN formato = 'LP' THEN 1 ELSE 0 END) AS total_lp,
    SUM(CASE WHEN formato = 'Set Box CD' THEN 1 ELSE 0 END) AS total_box_cd,
    SUM(CASE WHEN formato = 'Set Box LP' THEN 1 ELSE 0 END) AS total_box_lp
FROM produtos
WHERE usuario_id = ?
";

$stmt2 = $conn->prepare($sqlContagem);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$contagens = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

// Caminho da foto
$profilePath = "../assets/uploads/profiles/user_$user_id.jpg";
$profileImg = file_exists($profilePath) ? $profilePath : "../assets/img/default_profile.png";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minha Conta - VinilVerse</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

</head>

<body class="p-3" data-bs-theme="light">

<div class="container mt-4" style="max-width: 750px;">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-person-circle me-2"></i> Minha Conta</h2>

        <div class="d-flex gap-2">
            <button id="themeToggle" class="btn btn-outline-dark">
                <i class="bi bi-moon-fill"></i>
            </button>

            <a href="../pages/home.php" id="btnSair" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="card shadow-sm p-4">

        <!-- FOTO DO PERFIL -->
        <div class="text-center mb-3">
            <img src="<?= $profileImg ?>" class="rounded-circle shadow" width="140" height="140" style="object-fit: cover">

            <br>

            <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#editPhotoModal">
                <i class="bi bi-camera-fill"></i> Alterar Foto
            </button>
        </div>

        <hr>

        <!-- INFORMAÇÕES -->
        <div class="mb-3">
            <h5 class="fw-bold">Informações da Conta</h5>

            <p><strong>Nome:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Conta criada em:</strong> <?= date("d/m/Y H:i", strtotime($created)) ?></p>

            <button class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                <i class="bi bi-pencil-fill"></i> Editar Informações
            </button>
        </div>

        <hr>

        <!--GRAFICO COLEÇÃO -->
        <?php include '../components/grafico.php'; ?>

        <hr>

        <!-- SENHA -->
        <div class="mb-3">
            <h5 class="fw-bold">Segurança</h5>

            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editPasswordModal">
                <i class="bi bi-shield-lock"></i> Alterar Senha
            </button>
        </div>

    </div>

</div>

<!-- ===== MODAIS ===== -->
<!-- EDITAR FOTO -->
<div class="modal fade" id="editPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="../actions/update_profile_image.php" method="POST" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">Alterar Foto de Perfil</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="text-center mb-3">
                        <img id="previewImage" 
                             src="<?= $profileImg ?>" 
                             class="rounded-circle shadow"
                             width="140" height="140"
                             style="object-fit: cover">
                    </div>

                    <input 
                        type="file" 
                        name="profile_image" 
                        id="inputPhoto" 
                        class="form-control" 
                        accept="image/*" 
                        required
                    >

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Salvar</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- EDITAR INFORMAÇÕES -->
<div class="modal fade" id="editInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="../actions/update_profile.php" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Informações</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label class="form-label fw-bold">Nome</label>
                    <input 
                        type="text" 
                        name="username" 
                        value="<?= htmlspecialchars($username) ?>" 
                        class="form-control" 
                        required
                    >

                    <label class="form-label fw-bold mt-3">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?= htmlspecialchars($email) ?>" 
                        class="form-control" 
                        required
                    >

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Salvar Alterações</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ALTERAR SENHA -->
<div class="modal fade" id="editPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="../actions/update_password.php" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">Alterar Senha</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label class="form-label fw-bold">Senha Atual</label>
                    <input type="password" name="current" class="form-control" required>

                    <label class="form-label fw-bold mt-3">Nova Senha</label>
                    <input type="password" name="newpass" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-warning">Alterar Senha</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("inputPhoto").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById("previewImage");

    if (file) {
        preview.src = URL.createObjectURL(file);
    }
});

// Tema
const toggleBtn = document.getElementById("themeToggle");
const html = document.body;
const savedTheme = localStorage.getItem("theme") || "light";
html.setAttribute("data-bs-theme", savedTheme);
updateIcon(savedTheme);

toggleBtn.addEventListener("click", () => {
    const current = html.getAttribute("data-bs-theme");
    const next = current === "light" ? "dark" : "light";

    html.setAttribute("data-bs-theme", next);
    localStorage.setItem("theme", next);
    updateIcon(next);
});

function updateIcon(theme) {
    if (theme === "dark") {
        toggleBtn.innerHTML = `<i class="bi bi-sun-fill"></i>`;
        toggleBtn.classList.replace("btn-outline-dark", "btn-outline-light");
    } else {
        toggleBtn.innerHTML = `<i class="bi bi-moon-fill"></i>`;
        toggleBtn.classList.replace("btn-outline-light", "btn-outline-dark");
    }
}
</script>

</body>
</html>
