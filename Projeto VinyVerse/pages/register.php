<?php
include '../config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitização básica
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $senhaRaw = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $senhaRaw === '') {
        $error = "Preencha todos os campos.";
    } else {
        $password = password_hash($senhaRaw, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Erro no prepare (pouco comum, mas tratável)
            $error = "Erro interno. Tente novamente mais tarde.";
        } else {
            // Bind dos parâmetros
            $stmt->bind_param("sss", $username, $email, $password);

            try {
                // Execute pode lançar mysqli_sql_exception no seu ambiente
                $stmt->execute();

                // Se chegou aqui, foi inserido com sucesso
                header("Location: register.php?success=cadastro");
                exit;

            } catch (mysqli_sql_exception $e) {
                // Código 1062 = duplicate entry (unique key)
                if ($e->getCode() == 1062) {
                    $error = "Este e-mail já está cadastrado.";
                } else {
                    // Mensagem genérica + log (se quiser)
                    $error = "Erro ao cadastrar usuário. Código: " . $e->getCode();
                    // Opcional: registrar $e->getMessage() em log do servidor
                }
            } finally {
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta - VinylVerse</title>
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

        <!-- Mensagens -->
        <?php if ($error): ?>
            <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] === 'cadastro'): ?>
            <div class="alert alert-success text-center py-2">Cadastro realizado com sucesso!</div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label" for="username">Nome de usuário</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Senha</label>
                <input type="password" name="password" id="password" class="form-control" required>
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
