<?php include '../components/auth.php'; ?>

<link rel="stylesheet" href="../assets/css/appbar.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg px-3 custom-navbar fixed-top" data-bs-theme="dark">
  <div class="container-fluid">

    <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
      <i class="bi bi-list"></i>
    </button>

    <span class="navbar-brand fw-bold">
      Bem-vindo, <?= htmlspecialchars($_SESSION['user']); ?> 👋
    </span>

    <div class="d-flex align-items-center ms-auto gap-3">

      <form class="d-flex" role="search" style="max-width: 250px;">
        <input id="pesquisa" class="form-control" type="search" placeholder="Pesquisar...">
      </form>

      <a href="../pages/ItemRegister.php" class="btn btn-outline-light d-flex align-items-center gap-2" id="addItemBtn">
        <i class="bi bi-plus-circle"></i> Adicionar
      </a>

      <select id="filtro" class="form-select w-auto">
        <option value="">Filtrar por...</option>
        <option value="LP">LP</option>
        <option value="CD">CD</option>
        <option value="Set Box LP">Set Box LP</option>
        <option value="Set Box CD">Set Box CD</option>
      </select>

      <button id="themeToggle" class="btn btn-outline-light">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
      </button>

    </div>

  </div>
</nav>


<div class="offcanvas offcanvas-start custom-offcanvas" id="sidebarMenu" data-bs-theme="dark">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title fw-bold textefont">VinilVerse</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column gap-2">

    <a href="../pages/userAccount.php" class="btn btn-outline-light w-100 text-start">
      <i class="bi bi-person-fill me-2"></i> Minha Conta
    </a>

    <a href="../pages/pageConfig.php" class="btn btn-outline-light w-100 text-start">
      <i class="bi bi-gear-fill me-2"></i> Configurações
    </a>

    <a href="../pages/mercado.php" class="btn btn-outline-light w-100 text-start">
      <i class="bi bi-shop me-2"></i> Mercado
    </a>

    <button class="btn btn-danger w-100 text-start mt-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
      <i class="bi bi-box-arrow-right me-2"></i> Sair
    </button>

  </div>
</div>
