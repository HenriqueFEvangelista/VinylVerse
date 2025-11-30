<script>
// ========================
// ATALHOS PADRÃO
// ========================
const defaultShortcuts = {
    toggleSidebar: "Escape",
    focusSearch: "Control+f",
    addItem: "Shift+a",
    toggleTheme: "Control+d"
};

// Carregar atalhos do localStorage ou defaults
const shortcuts = JSON.parse(localStorage.getItem("vinylverseShortcuts")) || defaultShortcuts;


// ========================
// FUNÇÃO: Checar se tecla bate com o atalho salvo
// ========================
function matchesShortcut(event, shortcut) {
    const parts = shortcut.toLowerCase().split("+");

    const ctrl = parts.includes("control");
    const shift = parts.includes("shift");
    const alt = parts.includes("alt");
    const key = parts[parts.length - 1];

    return (
        ctrl === event.ctrlKey &&
        shift === event.shiftKey &&
        alt === event.altKey &&
        event.key.toLowerCase() === key
    );
}


// ========================
// AÇÕES DO ATALHO
// ========================
const sidebarEl = document.getElementById("sidebarMenu");
const sidebar = new bootstrap.Offcanvas(sidebarEl);

// Listener global
document.addEventListener("keydown", function(e) {

    // Abrir/Fechar menu lateral
    if (matchesShortcut(e, shortcuts.toggleSidebar)) {
        const isOpen = sidebarEl.classList.contains("show");
        isOpen ? sidebar.hide() : sidebar.show();
    }

    // Focar no campo de pesquisa
    if (matchesShortcut(e, shortcuts.focusSearch)) {
        e.preventDefault();
        document.getElementById("pesquisa")?.focus();
    }

    // Abrir página adicionar item
    if (matchesShortcut(e, shortcuts.addItem)) {
        window.location.href = "../pages/ItemRegister.php";
    }

    // Alternar modo escuro
    if (matchesShortcut(e, shortcuts.toggleTheme)) {
        e.preventDefault();
        document.getElementById("themeToggle")?.click();
    }
});
</script>
