<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

function responderCadastroRota(int $status, bool $sucesso, string $mensagem): void
{
    http_response_code($status);
    echo json_encode(
        ['success' => $sucesso, 'message' => $mensagem],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function normalizarEstacao($valor): string
{
    $mapeamento = [
        'Agulha Norte' => 'Estação Norte',
        'Agulha Central' => 'Estação Central',
        'Agulha Sul' => 'Estação Sul',
        'Agulha Oeste' => 'Estação Oeste',
        'Para-choque' => 'Estação Leste',
    ];

    $valor = trim((string) $valor);
    if ($valor === '') {
        return '';
    }

    return $mapeamento[$valor] ?? $valor;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderCadastroRota(405, false, 'Método não permitido.');
}

if (!isset($_SESSION['usuario_id']) || !$usuarioEhAdministrador) {
    responderCadastroRota(403, false, 'Acesso negado. Somente administradores podem cadastrar rotas.');
}

$pontoPartida = normalizarEstacao($_POST['ponto_partida'] ?? '');
$pontoChegada = normalizarEstacao($_POST['ponto_chegada'] ?? '');
$nome = trim((string) ($_POST['nome'] ?? ''));
$tremId = filter_var($_POST['trem_id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$descricao = trim((string) ($_POST['descricao'] ?? ''));
$status = trim((string) ($_POST['status'] ?? 'Ativo'));
$rotaId = null;

if (isset($_POST['rota_id']) && $_POST['rota_id'] !== '') {
    $rotaId = filter_var($_POST['rota_id'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($rotaId === false) {
        responderCadastroRota(400, false, 'Rota inválida.');
    }
}

if ($pontoPartida === '' || $pontoChegada === '' || $nome === '' || $tremId === false) {
    responderCadastroRota(400, false, 'Preencha todos os campos obrigatórios.');
}

if ($pontoPartida === $pontoChegada) {
    responderCadastroRota(400, false, 'Ponto de partida e chegada não podem ser iguais.');
}

if (mb_strlen($nome) < 3) {
    responderCadastroRota(400, false, 'Nome da rota deve ter no mínimo 3 caracteres.');
}

$sqlCheckTrem = 'SELECT trem_id FROM trens WHERE trem_id = ? LIMIT 1';
$stmtCheck = mysqli_prepare($conexao, $sqlCheckTrem);
if ($stmtCheck === false) {
    error_log('Erro ao preparar validação do trem da rota: ' . mysqli_error($conexao));
    responderCadastroRota(500, false, 'Não foi possível validar o trem selecionado.');
}

mysqli_stmt_bind_param($stmtCheck, 'i', $tremId);
if (!mysqli_stmt_execute($stmtCheck)) {
    error_log('Erro ao validar trem da rota: ' . mysqli_stmt_error($stmtCheck));
    mysqli_stmt_close($stmtCheck);
    responderCadastroRota(500, false, 'Não foi possível validar o trem selecionado.');
}

mysqli_stmt_store_result($stmtCheck);
if (mysqli_stmt_num_rows($stmtCheck) === 0) {
    mysqli_stmt_close($stmtCheck);
    responderCadastroRota(400, false, 'Trem selecionado não existe.');
}
mysqli_stmt_close($stmtCheck);

$statusValidos = ['Ativo', 'Manutenção', 'Inativo'];
if (!in_array($status, $statusValidos, true)) {
    $status = 'Ativo';
}

if ($rotaId !== null) {
    $sql = 'UPDATE rotas SET ponto_partida = ?, ponto_chegada = ?, nome = ?, trem_id = ?, descricao = ?, status = ? WHERE rota_id = ? LIMIT 1';
    $stmt = mysqli_prepare($conexao, $sql);
    if ($stmt === false) {
        error_log('Erro ao preparar atualização da rota: ' . mysqli_error($conexao));
        responderCadastroRota(500, false, 'Erro ao preparar gravação da rota.');
    }

    mysqli_stmt_bind_param($stmt, 'sssissi', $pontoPartida, $pontoChegada, $nome, $tremId, $descricao, $status, $rotaId);
} else {
    $sql = 'INSERT INTO rotas (ponto_partida, ponto_chegada, nome, trem_id, descricao, status) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($conexao, $sql);
    if ($stmt === false) {
        error_log('Erro ao preparar cadastro da rota: ' . mysqli_error($conexao));
        responderCadastroRota(500, false, 'Erro ao preparar gravação da rota.');
    }

    mysqli_stmt_bind_param($stmt, 'sssiss', $pontoPartida, $pontoChegada, $nome, $tremId, $descricao, $status);
}

if (!mysqli_stmt_execute($stmt)) {
    error_log('Erro ao salvar rota: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    responderCadastroRota(500, false, $rotaId !== null ? 'Erro ao atualizar rota.' : 'Erro ao cadastrar rota.');
}

mysqli_stmt_close($stmt);
$_SESSION['mensagem_sucesso'] = $rotaId !== null ? 'Rota atualizada com sucesso!' : 'Rota cadastrada com sucesso!';
http_response_code(200);
echo json_encode(['success' => true, 'reload' => true], JSON_UNESCAPED_UNICODE);
exit;
