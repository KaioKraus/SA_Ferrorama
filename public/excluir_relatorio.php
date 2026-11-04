<?php
session_start();

require_once __DIR__ . '/validar_acesso.php';

function voltarParaRelatorios(): void
{
    header('Location: tela_relatorios.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    voltarParaRelatorios();
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    $_SESSION['mensagem_erro'] = 'Somente administradores podem excluir relatórios.';
    voltarParaRelatorios();
}

$relatorioId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($relatorioId === false) {
    $_SESSION['mensagem_erro'] = 'O relatório selecionado é inválido.';
    voltarParaRelatorios();
}

try {
    $stmtExcluir = mysqli_prepare($conexao, 'DELETE FROM relatorio WHERE relatorio_id = ? LIMIT 1');
    if (!$stmtExcluir) {
        throw new RuntimeException('Erro ao preparar exclusão: ' . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmtExcluir, 'i', $relatorioId);
    if (!mysqli_stmt_execute($stmtExcluir)) {
        throw new RuntimeException('Erro ao executar exclusão: ' . mysqli_stmt_error($stmtExcluir));
    }
    $linhasAfetadas = mysqli_stmt_affected_rows($stmtExcluir);
    mysqli_stmt_close($stmtExcluir);

    if ($linhasAfetadas < 1) {
        $_SESSION['mensagem_erro'] = 'O relatório selecionado não foi encontrado.';
        voltarParaRelatorios();
    }

    $_SESSION['mensagem_sucesso'] = 'Relatório excluído com sucesso.';
} catch (Throwable $erro) {
    error_log('Erro ao excluir relatório: ' . $erro->getMessage());
    $_SESSION['mensagem_erro'] = 'Erro: ' . $erro->getMessage();
}

voltarParaRelatorios();
