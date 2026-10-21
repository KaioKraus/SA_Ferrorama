<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

function responderCadastroTrem(int $status, bool $sucesso, string $mensagem): void
{
    http_response_code($status);
    echo json_encode(
        ['success' => $sucesso, 'message' => $mensagem],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderCadastroTrem(405, false, 'Método não permitido.');
}

if (!isset($_SESSION['usuario_id']) || !$usuarioEhAdministrador) {
    responderCadastroTrem(403, false, 'Acesso negado. Somente administradores podem cadastrar trens.');
}

$nomeTrem = trim((string) ($_POST['nome_trem'] ?? ''));
$responsavelId = filter_var($_POST['responsavel_trem'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($nomeTrem === '' || $responsavelId === false) {
    responderCadastroTrem(400, false, 'Informe o nome do trem e um responsável.');
}

$quantidadeCaracteres = preg_match_all('/./us', $nomeTrem);
if ($quantidadeCaracteres === false || $quantidadeCaracteres > 155) {
    responderCadastroTrem(400, false, 'O nome do trem deve ter no máximo 155 caracteres.');
}

$sqlUsuario = 'SELECT usuario_id FROM usuarios WHERE usuario_id = ? LIMIT 1';
$stmtUsuario = mysqli_prepare($conexao, $sqlUsuario);
if ($stmtUsuario === false) {
    error_log('Erro ao preparar validação do responsável do trem: ' . mysqli_error($conexao));
    responderCadastroTrem(500, false, 'Não foi possível validar o responsável.');
}

mysqli_stmt_bind_param($stmtUsuario, 'i', $responsavelId);
if (!mysqli_stmt_execute($stmtUsuario)) {
    error_log('Erro ao validar responsável do trem: ' . mysqli_stmt_error($stmtUsuario));
    mysqli_stmt_close($stmtUsuario);
    responderCadastroTrem(500, false, 'Não foi possível validar o responsável.');
}

mysqli_stmt_store_result($stmtUsuario);
$responsavelExiste = mysqli_stmt_num_rows($stmtUsuario) > 0;
mysqli_stmt_close($stmtUsuario);

if (!$responsavelExiste) {
    responderCadastroTrem(400, false, 'O responsável selecionado não existe.');
}

$sql = 'INSERT INTO trens (nome_trem, responsavel_id) VALUES (?, ?)';
$stmt = mysqli_prepare($conexao, $sql);
if ($stmt === false) {
    error_log('Erro ao preparar cadastro de trem: ' . mysqli_error($conexao));
    responderCadastroTrem(500, false, 'Não foi possível preparar o cadastro do trem.');
}

mysqli_stmt_bind_param($stmt, 'si', $nomeTrem, $responsavelId);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Erro ao cadastrar trem: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    responderCadastroTrem(500, false, 'Não foi possível cadastrar o trem.');
}

$tremId = mysqli_stmt_insert_id($stmt);
mysqli_stmt_close($stmt);

$_SESSION['mensagem_sucesso'] = 'Trem cadastrado com sucesso!';
http_response_code(200);
echo json_encode(['success' => true, 'reload' => true], JSON_UNESCAPED_UNICODE);
exit;
