<?php
// Exclui uma rota escolhida na listagem e retorna à tela de rotas.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

// Inclui a busca, o status e o resultado da exclusão na URL de retorno.
function redirecionarRotas(string $resultado): void
{
    $parametros = [];
    $termoBusca = $_POST['q'] ?? '';
    $status = $_POST['status'] ?? '';

    if (is_string($termoBusca) && trim($termoBusca) !== '') {
        $parametros['q'] = trim($termoBusca);
    }

    if (is_string($status) && in_array($status, ['Ativo', 'Manutenção', 'Inativo'], true)) {
        $parametros['status'] = $status;
    }

    $parametros[$resultado === 'deleted' ? 'deleted' : 'error'] = $resultado === 'deleted' ? '1' : $resultado;
    header('Location: tela_rotas.php?' . http_build_query($parametros));
    exit;
}

// Confere o método, o cargo e o ID recebido antes de alterar a tabela.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    redirecionarRotas('invalid_request');
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    redirecionarRotas('forbidden');
}

$rotaId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($rotaId === false) {
    redirecionarRotas('invalid_id');
}

// Exclui o ID informado e distingue uma rota inexistente de uma exclusão concluída.
$stmt = mysqli_prepare($conexao, 'DELETE FROM rotas WHERE rota_id = ? LIMIT 1');
if ($stmt === false) {
    error_log('Erro ao preparar exclusão de rota: ' . mysqli_error($conexao));
    redirecionarRotas('database_error');
}

mysqli_stmt_bind_param($stmt, 'i', $rotaId);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Erro ao excluir rota: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    redirecionarRotas('database_error');
}

$linhasAfetadas = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($linhasAfetadas < 1) {
    redirecionarRotas('not_found');
}

redirecionarRotas('deleted');
