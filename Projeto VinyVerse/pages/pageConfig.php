<?php include '../components/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações - VinilVerse</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="p-3" data-bs-theme="light">

<div class="container mt-4" style="max-width: 700px;">

    <!-- TÍTULO + BOTÃO VOLTAR -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-gear-fill me-2"></i>Configurações
        </h2>

        <a href="../pages/home.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- BOTÃO MODO ESCURO -->
    <button id="themeToggle" class="btn btn-dark mb-4">
        <i class="bi bi-moon-fill" id="themeIcon"></i> Alternar Modo Escuro
    </button>

    <!-- CARD PRINCIPAL -->
    <div class="card shadow-sm p-4">

        <h4 class="fw-bold mb-3"><i class="bi bi-lightning-fill text-warning"></i> Atalhos</h4>

        <p class="text-muted mb-4">
            Gerencie os atalhos rápidos usados no sistema.
        </p>

        <!-- LISTA DE ATALHOS -->
        <ul class="list-group">

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span>✨ Abrir / Fechar Menu</span><br>
                    <small class="text-muted">Atalho atual:
                        <strong class="shortcutText" data-key="toggleSidebar"></strong>
                    </small>
                </div>
                <button class="btn btn-outline-primary editShortcut" data-key="toggleSidebar">Editar</button>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span>🔎 Focar Pesquisa</span><br>
                    <small class="text-muted">Atalho atual:
                        <strong class="shortcutText" data-key="focusSearch"></strong>
                    </small>
                </div>
                <button class="btn btn-outline-primary editShortcut" data-key="focusSearch">Editar</button>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span>➕ Adicionar Item</span><br>
                    <small class="text-muted">Atalho atual:
                        <strong class="shortcutText" data-key="addItem"></strong>
                    </small>
                </div>
                <button class="btn btn-outline-primary editShortcut" data-key="addItem">Editar</button>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span>🌙 Alternar Modo Escuro</span><br>
                    <small class="text-muted">Atalho atual:
                        <strong class="shortcutText" data-key="toggleTheme"></strong>
                    </small>
                </div>
                <button class="btn btn-outline-primary editShortcut" data-key="toggleTheme">Editar</button>
            </li>

        </ul>

        <button class="btn btn-danger w-100 mt-3" id="resetShortcuts">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurar Atalhos Padrão
        </button>

    </div>

</div>

<!-- MODAL PARA EDITAR ATALHO -->
<div class="modal fade" id="editShortcutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Atalho</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-2">Pressione a nova combinação de teclas...</p>
                <div class="border rounded p-3 fs-4 bg-light" id="shortcutPreview">
                    —
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script de Tema -->
<script>
const body = document.body;
const icon = document.getElementById("themeIcon");
const savedTheme = localStorage.getItem("theme") || "light";

body.setAttribute("data-bs-theme", savedTheme);
icon.className = savedTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-fill";

document.getElementById("themeToggle").addEventListener("click", () => {
    const newTheme = body.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
    body.setAttribute("data-bs-theme", newTheme);
    localStorage.setItem("theme", newTheme);
    icon.className = newTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-fill";
});
</script>

<!-- SCRIPT DE ATALHOS -->
<script>
// Atalhos padrão
const defaultShortcuts = {
    toggleSidebar: "Escape",
    focusSearch: "Control+f",
    addItem: "Shift+a",
    toggleTheme: "Control+d"
};

let shortcuts = JSON.parse(localStorage.getItem("vinylverseShortcuts")) || defaultShortcuts;

let currentKey = null;
let modal = new bootstrap.Modal(document.getElementById("editShortcutModal"));

// Preencher visualmente os atalhos na tela
document.querySelectorAll(".shortcutText").forEach(el => {
    const key = el.dataset.key;
    el.textContent = shortcuts[key] ? shortcuts[key].toUpperCase() : "—";
});

// Abrir modal de edição
document.querySelectorAll(".editShortcut").forEach(btn => {
    btn.addEventListener("click", () => {
        currentKey = btn.dataset.key;
        document.getElementById("shortcutPreview").textContent =
            shortcuts[currentKey] || "—";
        modal.show();
    });
});

// Captura do combo de teclas
document.addEventListener("keydown", function(e) {
    if (!currentKey) return;

    e.preventDefault();

    let combo = "";
    if (e.ctrlKey) combo += "Control+";
    if (e.shiftKey) combo += "Shift+";
    if (e.altKey) combo += "Alt+";

    combo += e.key;

    shortcuts[currentKey] = combo;
    localStorage.setItem("vinylverseShortcuts", JSON.stringify(shortcuts));

    document.querySelector(`strong[data-key="${currentKey}"]`).textContent = combo.toUpperCase();
    document.getElementById("shortcutPreview").textContent = combo;
});

// Resetar atalhos
document.getElementById("resetShortcuts").addEventListener("click", () => {
    shortcuts = { ...defaultShortcuts };
    localStorage.setItem("vinylverseShortcuts", JSON.stringify(shortcuts));

    document.querySelectorAll(".shortcutText").forEach(el => {
        const key = el.dataset.key;
        el.textContent = shortcuts[key].toUpperCase();
    });

    alert("Atalhos restaurados para o padrão!");
});
</script>

</body>
</html>
