<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

// Definir timezone para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['success' => false, 'message' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$colunasTrens = mysqli_query($conexao, 'SHOW COLUMNS FROM trens');
if ($colunasTrens === false) {
    error_log('Erro ao consultar as colunas dos trens: ' . mysqli_error($conexao));
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Não foi possível verificar os dados de velocidade.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$temVelocidade = false;
$temTemperatura = false;
$temConsumo = false;
while ($coluna = mysqli_fetch_assoc($colunasTrens)) {
    if ($coluna['Field'] === 'velocidade') {
        $temVelocidade = true;
    }
    if ($coluna['Field'] === 'temperatura') {
        $temTemperatura = true;
    }
    if ($coluna['Field'] === 'consumo') {
        $temConsumo = true;
    }
}
mysqli_free_result($colunasTrens);
$campoVelocidade = $temVelocidade ? 'velocidade' : '0 AS velocidade';
$campoTemperatura = $temTemperatura ? 'temperatura' : '0 AS temperatura';
$campoConsumo = $temConsumo ? 'consumo' : '0 AS consumo';
$sql = 'SELECT trem_id, nome_trem, ' . $campoVelocidade . ', ' . $campoTemperatura . ', ' . $campoConsumo . ' FROM trens ORDER BY trem_id ASC';
$resultado = mysqli_query($conexao, $sql);
if ($resultado === false) {
    error_log('Erro ao consultar dados dos trens: ' . mysqli_error($conexao));
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Não foi possível atualizar os dados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$trens = [];
while ($trem = mysqli_fetch_assoc($resultado)) {
    $trens[] = [
        'id' => (int) $trem['trem_id'],
        'nome' => (string) $trem['nome_trem'],
        'velocidade' => (float) $trem['velocidade'],
        'temperatura' => (float) $trem['temperatura'],
        'consumo' => (float) $trem['consumo'],
    ];
}
mysqli_free_result($resultado);

echo json_encode([
    'success' => true,
    'timestamp' => date('H:i:s'),
    'trens' => $trens,
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
