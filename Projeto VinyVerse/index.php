<?php
include 'config/db.php';
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['username'];
    $_SESSION['user_id'] = $user['id']; // 🔹 Adiciona o ID do usuário na sessão
    header("Location: pages/home.php");
    exit;
    }
else{
        $error = "E-mail ou senha incorretos.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - VinyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">VinylVerse</h2>
            <p class="text-muted">Entre na sua conta</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center py-2"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Digite seu e-mail" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-0">Não tem conta? 
                <a href="pages/register.php" class="text-decoration-none">Cadastre-se</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
