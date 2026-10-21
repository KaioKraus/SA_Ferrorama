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

mysqli_begin_transaction($conexao);

$faltaDependencia = false;

$stmtRelatorios = mysqli_prepare($conexao, 'DELETE r FROM relatorio r INNER JOIN falha f ON f.falha_id = r.falha_id WHERE f.trem_id = ?');
if ($stmtRelatorios) {
    mysqli_stmt_bind_param($stmtRelatorios, 'i', $id);
    mysqli_stmt_execute($stmtRelatorios);
    mysqli_stmt_close($stmtRelatorios);
}

$stmtFalhas = mysqli_prepare($conexao, 'DELETE FROM falha WHERE trem_id = ?');
if ($stmtFalhas) {
    mysqli_stmt_bind_param($stmtFalhas, 'i', $id);
    mysqli_stmt_execute($stmtFalhas);
    mysqli_stmt_close($stmtFalhas);
}

$stmtRotas = mysqli_prepare($conexao, 'DELETE FROM rotas WHERE trem_id = ?');
if ($stmtRotas) {
    mysqli_stmt_bind_param($stmtRotas, 'i', $id);
    mysqli_stmt_execute($stmtRotas);
    mysqli_stmt_close($stmtRotas);
}

$stmtTrem = mysqli_prepare($conexao, 'DELETE FROM trens WHERE trem_id = ? LIMIT 1');
if ($stmtTrem) {
    mysqli_stmt_bind_param($stmtTrem, 'i', $id);
    mysqli_stmt_execute($stmtTrem);
    $affected = mysqli_stmt_affected_rows($stmtTrem);
    mysqli_stmt_close($stmtTrem);

    if ($affected > 0) {
        mysqli_commit($conexao);
        header('Location: tela_trens.php?deleted=1');
        exit;
    }
}

mysqli_rollback($conexao);
header('Location: tela_trens.php?deleted=0');
exit;
