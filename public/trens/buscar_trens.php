<?php
// Recebe os filtros e devolve apenas o fragmento HTML com os cartões de trens.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';
require_once __DIR__ . '/_consulta.php';

header('Content-Type: text/html; charset=UTF-8');

// Normaliza o termo e aceita apenas os status previstos pela tela.
$busca = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$status = is_string($_GET['status'] ?? null) ? trim($_GET['status']) : '';
if (!in_array($status, ['Ativo', 'Manutenção', 'Inativo'], true)) {
    $status = '';
}

// Consulta os trens antes de montar o fragmento compartilhado.
try {
    $trens = listarTrens($conexao, $busca, $status);
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao consultar os trens: ' . $erro->getMessage());
    http_response_code(500);
    exit('<div class="col-12"><p class="text-center py-4 mb-0">Erro ao consultar os trens.</p></div>');
}

$mensagemSemTrens = 'Nenhum trem encontrado.';
require __DIR__ . '/_cartoes.php';
