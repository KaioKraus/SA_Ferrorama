<?php
// Recebe a confirmação de exclusão de uma conta pelo administrador.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    exit('Acesso negado. Somente administradores podem excluir usuários.');
}

// Valida o ID e impede que o administrador exclua a própria conta.
$destino = 'tela_cadastro_user.php';
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!isset($_POST['id'])) {
    $destino .= '?error=missing_id';
} elseif ($id === false) {
    $destino .= '?error=invalid_id';
} elseif ($id === (int) $_SESSION['usuario_id']) {
    $_SESSION['mensagem_erro'] = 'Você não pode excluir sua própria conta de administrador.';
} else {
    // Exclui a conta indicada e informa se havia um registro correspondente.
    try {
        $conexao->execute_query('DELETE FROM usuarios WHERE usuario_id = ?', [$id]);
        if ($conexao->affected_rows > 0) {
            $_SESSION['mensagem_sucesso'] = 'Usuário excluído com sucesso.';
        } else {
            $destino .= '?deleted=0';
        }
    } catch (mysqli_sql_exception $erro) {
        // O código 1451 indica que uma chave estrangeira ainda referencia a conta.
        if ($erro->getCode() === 1451) {
            $_SESSION['mensagem_erro'] = 'Não é possível excluir! Este usuário está vinculado a um ou mais trens. Para excluí-lo, primeiro remova ou reatribua os trens associados.';
        } else {
            error_log('Erro ao excluir usuário: ' . $erro->getMessage());
            $destino .= '?error=db_error';
        }
    }
}

header('Location: ' . $destino);
exit;
