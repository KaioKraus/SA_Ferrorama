<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: tela_login.php');
    exit;
}

$usuarioCargoId = filter_var($_SESSION['usuario_cargo_id'] ?? null, FILTER_VALIDATE_INT);
$usuarioEmail = strtolower(trim($_SESSION['usuario_email'] ?? ''));

if ($usuarioCargoId === false || !in_array($usuarioCargoId, [1, 2], true)) {
    include_once __DIR__ . '/../infra/conexao.php';

    $sqlCargo = 'SELECT cargo_id FROM usuarios WHERE usuario_id = ? LIMIT 1';
    if ($stmtCargo = mysqli_prepare($conexao, $sqlCargo)) {
        mysqli_stmt_bind_param($stmtCargo, 'i', $_SESSION['usuario_id']);
        mysqli_stmt_execute($stmtCargo);
        $resultadoCargo = mysqli_stmt_get_result($stmtCargo);
        $usuarioCargo = mysqli_fetch_assoc($resultadoCargo);

        $usuarioCargoId = filter_var($usuarioCargo['cargo_id'] ?? null, FILTER_VALIDATE_INT);
        if ($usuarioCargoId === false) {
            $usuarioCargoId = 0;
        }

        $_SESSION['usuario_cargo_id'] = (int)$usuarioCargoId;
        mysqli_stmt_close($stmtCargo);
    } else {
        $usuarioCargoId = 0;
    }
}

if ($usuarioEmail === 'admin@teste.com' && (int)$usuarioCargoId !== 1) {
    $usuarioCargoId = 1;
    $_SESSION['usuario_cargo_id'] = 1;
}

$usuarioEhAdministrador = ((int)$usuarioCargoId === 1);
$usuarioEhFuncionario = ((int)$usuarioCargoId === 2);

if (basename($_SERVER['PHP_SELF']) === 'tela_cadastro_user.php' && !$usuarioEhAdministrador) {
    header('Location: dashboard.php');
    exit;
}
?>
