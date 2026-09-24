<?php
session_start();
include __DIR__ . '/validar_acesso.php';
include __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$userId = (int)($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

$referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';

if ($userId !== (int)($_SESSION['usuario_id'] ?? 0)) {
    $_SESSION['mensagem_acesso'] = 'ID de usuário inválido.';
    header('Location: ' . $referer);
    exit;
}

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['mensagem_acesso'] = 'Nome ou email inválidos.';
    header('Location: ' . $referer);
    exit;
}

// Atualizar nome e email
if ($senha !== '') {
    if (strlen($senha) < 8) {
        $_SESSION['mensagem_acesso'] = 'A senha deve ter pelo menos 8 caracteres.';
        header('Location: ' . $referer);
        exit;
    }
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = 'UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE usuario_id = ? LIMIT 1';
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssi', $nome, $email, $hash, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $ok = false;
    }
} else {
    $sql = 'UPDATE usuarios SET nome = ?, email = ? WHERE usuario_id = ? LIMIT 1';
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ssi', $nome, $email, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $ok = false;
    }
}

if ($ok) {
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_email'] = strtolower($email);
    $_SESSION['mensagem_acesso'] = 'Perfil atualizado com sucesso.';
} else {
    $_SESSION['mensagem_acesso'] = 'Erro ao atualizar o perfil.';
}

header('Location: ' . $referer);
exit;
