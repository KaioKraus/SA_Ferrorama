<?php
// Protege as telas internas e atualiza o cargo da conta a partir do banco.

// Redireciona para o login quando a sessão não identifica uma conta.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../acesso/tela_login.php');
    exit;
}

require_once __DIR__ . '/../../infra/conexao.php';

// Mantém o cargo sem privilégios quando o usuário ou a permissão não é reconhecido.
$usuarioCargoId = 0;
try {
    $consulta = $conexao->execute_query('SELECT cargo_id FROM usuarios WHERE usuario_id = ?', [$_SESSION['usuario_id']]);
    $usuario = $consulta->fetch_assoc();
    $cargoId = (int) ($usuario['cargo_id'] ?? 0);
    if (in_array($cargoId, [1, 2], true)) {
        $usuarioCargoId = $cargoId;
    }
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao consultar permissão do usuário: ' . $erro->getMessage());
}

// Expõe as permissões que controlam as ações exibidas e aceitas no servidor.
$_SESSION['usuario_cargo_id'] = $usuarioCargoId;
$usuarioEhAdministrador = $usuarioCargoId === 1;
$usuarioEhFuncionario = $usuarioCargoId === 2;
