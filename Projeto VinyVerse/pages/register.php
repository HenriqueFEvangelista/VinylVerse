<?php
include '../config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: register.php?success=cadastro");
        exit;
    } else {
        $error = "Erro: este e-mail já está cadastrado.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta - VinyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">VinylVerse</h2>
            <p class="text-muted">Crie sua conta</p>
        </div>

        <!-- Mensagens de erro ou sucesso -->
        <?php if ($error): ?>
            <div class="alert alert-danger text-center py-2"><?= $error ?></div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] === 'cadastro'): ?>
            <div class="alert alert-success text-center py-2">Cadastro realizado com sucesso!</div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nome de usuário</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Digite seu nome" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu e-mail" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-0">Já tem conta? 
                <a href="../index.php" class="text-decoration-none">Entrar</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<canvas id="fireworksCanvas"></canvas>
<script src="../assets/js/fireworks.js"></script>
</body>
</html>
