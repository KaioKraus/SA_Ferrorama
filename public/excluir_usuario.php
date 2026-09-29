<?php
session_start();

// Verifica acesso e permissões
include __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

// Só administradores devem chegar aqui (validar_acesso.php já redireciona quando necessário)

// Validação básica do POST
if (!isset($_POST['id'])) {
    header('Location: tela_cadastro_user.php?error=missing_id');
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) {
    header('Location: tela_cadastro_user.php?error=invalid_id');
    exit;
}

// Evita que o usuário exclua a si próprio
if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === (int)$id) {
    header('Location: tela_cadastro_user.php?error=cannot_delete_self');
    exit;
}

// Prepara e executa exclusão
$sql = 'DELETE FROM usuarios WHERE usuario_id = ? LIMIT 1';
if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected > 0) {
        header('Location: tela_cadastro_user.php?deleted=1');
        exit;
    } else {
        header('Location: tela_cadastro_user.php?deleted=0');
        exit;
    }
} else {
    header('Location: tela_cadastro_user.php?error=db_prepare');
    exit;
}

?>
