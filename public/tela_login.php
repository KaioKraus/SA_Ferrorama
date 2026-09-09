<?php
session_start();
include "../infra/conexao.php";

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = trim($_POST['senha'] ?? '');

    if (!$email || strlen($senha) < 8) {
        $error = 'Email ou senha inválidos.';
    } else {
        // Tenta buscar pelo usuário na tabela `usuarios`
        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if ($user) {
                // Se existir coluna 'senha', tenta verificar (com hash ou texto simples)
                if (array_key_exists('senha', $user)) {
                    $hash = $user['senha'];
                    if (password_verify($senha, $hash) || $senha === $hash) {
                        $_SESSION['usuario_id'] = $user['usuario_id'];
                        $_SESSION['usuario_nome'] = $user['nome'];
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $error = 'Credenciais incorretas.';
                    }
                } else {
                    // Fallback: se não existe senha na tabela, aceita credencial de teste (mantém compatibilidade com validação.js)
                    if ($email === 'admin@teste.com' && $senha === 'adm1n123') {
                        $_SESSION['usuario_id'] = 0;
                        $_SESSION['usuario_nome'] = 'Administrador';
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $error = 'Usuário sem senha cadastrada no banco. Contate o administrador.';
                    }
                }
            } else {
                $error = 'Usuário não encontrado.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = 'Erro na autenticação (falha na consulta).';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.4/font/bootstrap-icons.css" rel="stylesheet">

    <title>Login</title>
</head>

<body class="body_login">

    <form class="form1" method="post" action="">

        <h1 class="h1_login">Login</h1>

        <div class="mb-3">

            <label for="inputEmail" class="form-label">Email:</label>

            <input name="email" type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp"
                placeholder="Digite seu email" required>
        </div>

        <div class="mb-3">

            <label for="inputSenha" class="form-label">Senha:</label>

            <div class="input-group">
                <input name="senha" type="password" class="form-control border-end-0" id="inputSenha" placeholder="Digite sua senha" minlength="8" required>
                <button class="border border-start-0 bg-white px-2 rounded-end" type="button" id="toggleSenha">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="btn1">

            <div id="mensagem">
                <?php if (!empty($error)) echo '<div class="text-danger fw-bold">'.htmlspecialchars($error).'</div>'; ?>
            </div>

            <button type="submit" class="button" id="button1">Entrar</button>
        </div>
    </form>

    <script src="../script/validacao.js"></script>
</body>

</html>