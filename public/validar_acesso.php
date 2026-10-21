<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: tela_login.php');
    exit;
}

include_once __DIR__ . '/../infra/conexao.php';

$usuarioCargoId = 0;
$sqlCargo = 'SELECT cargo_id FROM usuarios WHERE usuario_id = ? LIMIT 1';
$stmtCargo = mysqli_prepare($conexao, $sqlCargo);
if ($stmtCargo) {
    mysqli_stmt_bind_param($stmtCargo, 'i', $_SESSION['usuario_id']);
    if (mysqli_stmt_execute($stmtCargo)) {
        $resultadoCargo = mysqli_stmt_get_result($stmtCargo);
        $usuarioCargo = $resultadoCargo ? mysqli_fetch_assoc($resultadoCargo) : null;
        $cargoId = filter_var($usuarioCargo['cargo_id'] ?? null, FILTER_VALIDATE_INT);
        if ($cargoId !== false && in_array($cargoId, [1, 2], true)) {
            $usuarioCargoId = $cargoId;
        }
    } else {
        error_log('Erro ao consultar permissão do usuário: ' . mysqli_stmt_error($stmtCargo));
    }
    mysqli_stmt_close($stmtCargo);
} else {
    error_log('Erro ao preparar consulta de permissão: ' . mysqli_error($conexao));
}

$_SESSION['usuario_cargo_id'] = $usuarioCargoId;

$usuarioEhAdministrador = ((int)$usuarioCargoId === 1);
$usuarioEhFuncionario = ((int)$usuarioCargoId === 2);

if (basename($_SERVER['PHP_SELF']) === 'tela_cadastro_user.php' && !$usuarioEhAdministrador) {
    header('Location: dashboard.php');
    exit;
}
?>
