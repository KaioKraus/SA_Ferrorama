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

$stmtRotasAtivas = mysqli_prepare($conexao, "SELECT COUNT(*) AS quantidade FROM rotas WHERE trem_id = ? AND status = 'Ativo'");
if (!$stmtRotasAtivas) {
    error_log('Erro ao verificar rotas ativas do trem: ' . mysqli_error($conexao));
    mysqli_rollback($conexao);
    $_SESSION['mensagem_erro'] = 'Erro ao verificar rotas do trem. Tente novamente.';
    header('Location: tela_trens.php');
    exit;
}

mysqli_stmt_bind_param($stmtRotasAtivas, 'i', $id);
if (!mysqli_stmt_execute($stmtRotasAtivas)) {
    error_log('Erro ao consultar rotas ativas do trem: ' . mysqli_stmt_error($stmtRotasAtivas));
    mysqli_stmt_close($stmtRotasAtivas);
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
$resultadoRotasAtivas = mysqli_stmt_get_result($stmtRotasAtivas);
if (!$resultadoRotasAtivas) {
    error_log('Erro ao obter rotas ativas do trem: ' . mysqli_stmt_error($stmtRotasAtivas));
    mysqli_stmt_close($stmtRotasAtivas);
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
$rotasAtivas = mysqli_fetch_assoc($resultadoRotasAtivas);
mysqli_stmt_close($stmtRotasAtivas);

if ((int) ($rotasAtivas['quantidade'] ?? 0) > 0) {
    mysqli_rollback($conexao);
    $_SESSION['mensagem_erro'] = 'Não é possível excluir! Este trem possui rotas ativas. Para excluí-lo, primeiro finalize ou remova as rotas associadas.';
    header('Location: tela_trens.php');
    exit;
}

$stmtRelatorios = mysqli_prepare($conexao, 'DELETE r FROM relatorio r INNER JOIN falha f ON f.falha_id = r.falha_id WHERE f.trem_id = ?');
if (!$stmtRelatorios) {
    error_log('Erro ao preparar exclusão de relatórios do trem: ' . mysqli_error($conexao));
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
mysqli_stmt_bind_param($stmtRelatorios, 'i', $id);
$ok = mysqli_stmt_execute($stmtRelatorios);
if (!$ok) {
    error_log('Erro ao excluir relatórios do trem: ' . mysqli_stmt_error($stmtRelatorios));
}
mysqli_stmt_close($stmtRelatorios);
if (!$ok) {
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}

$stmtFalhas = mysqli_prepare($conexao, 'DELETE FROM falha WHERE trem_id = ?');
if (!$stmtFalhas) {
    error_log('Erro ao preparar exclusão de falhas do trem: ' . mysqli_error($conexao));
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
mysqli_stmt_bind_param($stmtFalhas, 'i', $id);
$ok = mysqli_stmt_execute($stmtFalhas);
if (!$ok) {
    error_log('Erro ao excluir falhas do trem: ' . mysqli_stmt_error($stmtFalhas));
}
mysqli_stmt_close($stmtFalhas);
if (!$ok) {
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}

$stmtRotas = mysqli_prepare($conexao, 'DELETE FROM rotas WHERE trem_id = ?');
if (!$stmtRotas) {
    error_log('Erro ao preparar exclusão de rotas do trem: ' . mysqli_error($conexao));
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
mysqli_stmt_bind_param($stmtRotas, 'i', $id);
$ok = mysqli_stmt_execute($stmtRotas);
$rotasExcluidas = $ok ? mysqli_stmt_affected_rows($stmtRotas) : 0;
if (!$ok) {
    error_log('Erro ao excluir rotas do trem: ' . mysqli_stmt_error($stmtRotas));
}
mysqli_stmt_close($stmtRotas);
if (!$ok) {
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}

$stmtTrem = mysqli_prepare($conexao, 'DELETE FROM trens WHERE trem_id = ? LIMIT 1');
if (!$stmtTrem) {
    error_log('Erro ao preparar exclusão do trem: ' . mysqli_error($conexao));
    mysqli_rollback($conexao);
    header('Location: tela_trens.php?deleted=0');
    exit;
}
mysqli_stmt_bind_param($stmtTrem, 'i', $id);
$ok = mysqli_stmt_execute($stmtTrem);
$affected = mysqli_stmt_affected_rows($stmtTrem);
if (!$ok) {
    error_log('Erro ao excluir trem: ' . mysqli_stmt_error($stmtTrem));
}
mysqli_stmt_close($stmtTrem);

if ($ok && $affected > 0) {
    mysqli_commit($conexao);
    $_SESSION['mensagem_sucesso'] = 'Trem excluído com sucesso.';
    header('Location: tela_trens.php');
    exit;
}

mysqli_rollback($conexao);
$_SESSION['mensagem_erro'] = 'Erro ao excluir o trem. Tente novamente.';
header('Location: tela_trens.php');
exit;
