<?php
// Salva os dados enviados pelo modal de edição do perfil.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/dashboard.php');
    exit;
}

// Usa a conta autenticada quando o formulário não informa um ID.
$usuarioAtual = (int) $_SESSION['usuario_id'];
$usuarioId = (int) ($_POST['id'] ?? $usuarioAtual);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Permite a edição do próprio perfil e reserva a edição de outras contas a administradores.
if ($usuarioId <= 0 || (!$usuarioEhAdministrador && $usuarioId !== $usuarioAtual)) {
    $mensagem = 'ID de usuário inválido.';
} elseif ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mensagem = 'Nome ou email inválidos.';
} elseif ($senha !== '' && strlen($senha) < 8) {
    $mensagem = 'A senha deve ter pelo menos 8 caracteres.';
} else {
    // Gera um hash somente quando o formulário inclui uma nova senha.
    $hash = $senha === '' ? null : password_hash($senha, PASSWORD_DEFAULT);
    try {
        $sql = 'UPDATE usuarios SET nome = ?, email = ?, senha = COALESCE(?, senha) WHERE usuario_id = ?';
        $conexao->execute_query($sql, [$nome, $email, $hash, $usuarioId]);
        if ($usuarioId === $usuarioAtual) {
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = strtolower($email);
        }
        $mensagem = 'Perfil atualizado com sucesso.';
    } catch (mysqli_sql_exception $erro) {
        error_log('Erro ao atualizar perfil: ' . $erro->getMessage());
        $mensagem = 'Erro ao atualizar o perfil.';
    }
}

// Guarda o resultado para mostrar no retorno à tela de origem.
$_SESSION['mensagem_acesso'] = $mensagem;
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../dashboard/dashboard.php'));
exit;
