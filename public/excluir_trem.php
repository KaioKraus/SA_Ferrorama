<?php
session_start();

require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_trens.php');
    exit;
}

if (!isset($_SESSION['usuario_id']) || !$usuarioEhAdministrador) {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_POST['id'])) {
    header('Location: tela_trens.php?error=missing_id');
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) {
    header('Location: tela_trens.php?error=invalid_id');
    exit;
}

$sql = 'DELETE FROM trens WHERE trem_id = ? LIMIT 1';
if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected > 0) {
        header('Location: tela_trens.php?deleted=1');
    } else {
        header('Location: tela_trens.php?deleted=0');
    }
    exit;
}

header('Location: tela_trens.php?error=db_prepare');
exit;
