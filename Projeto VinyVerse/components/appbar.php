<?php include '../components/auth.php'; ?>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/appbar.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark px-3 custom-navbar">
  <div class="container-fluid">
    
    <!-- Botão para abrir o menu lateral -->
    <button class="btn btn-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
      <i class="bi bi-list"></i>
    </button>

    <!-- Texto de boas-vindas -->
    <span class="navbar-brand fw-bold">
      Bem-vindo, <?= htmlspecialchars($_SESSION['user']); ?> 👋
    </span>

    <!-- Campo de pesquisa -->
    <form class="d-flex ms-auto gap5" role="search" style="max-width: 250px;">
      <input id="pesquisa" class="form-control me-2" type="search" placeholder="Pesquisar..." aria-label="Search">
    </form>


    <!-- Botão Adicionar-->
    <a href="../pages/ItemRegister.php" class="btn btn-light d-flex align-items-center me-3 gap-2" id="addItemBtn">
      <i class="bi bi-plus-circle me-2"></i> Adicionar
    </a>



    <!-- Filtro -->
    <select id="filtro" class="form-select w-auto gap5">
      <option value="">Filtrar por...</option>
      <option value="LP">LP</option>
      <option value="CD">CD</option>
      <option value="Set Box LP">Set Box LP</option>
      <option value="Set Box CD">Set Box CD</option>
  </select>
  </div>
</nav>

<!-- Menu lateral (Offcanvas Bootstrap) -->
<div class="offcanvas offcanvas-start custom-offcanvas" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarLabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title fw-bold textefont" id="sidebarLabel">VinilVerse</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
  </div>
  <div class="offcanvas-body ">
    <ul class="list-group list-group-flush custom-slidemenu">
      <li class="list-group-item"><a href="../pages/userAccount.php" class="text-decoration-none text-dark">Minha Conta</a></li>
      <li class="list-group-item"><a href="../pages/pageConfig.php" class="text-decoration-none text-dark">Configurações</a></li>
      <li class="list-group-item"><a href="../pages/mercado.php" class="text-decoration-none text-dark">Mercado</a></li>
      <li class="list-group-item">
        <a href="../assets/auth/logout.php" class="btn btn-danger w-100">Sair</a>
      </li>
    </ul>
  </div>
</div>

<!-- Ícones Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
