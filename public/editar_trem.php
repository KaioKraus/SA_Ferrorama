<?php
session_start();
include __DIR__ . '/validar_acesso.php';
include __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$tremId = (int)($_POST['trem_id'] ?? 0);
$nomeTrem = trim((string)($_POST['nome_trem'] ?? ''));
$responsavelId = (int)($_POST['responsavel_id'] ?? 0);

if ($tremId <= 0 || $nomeTrem === '' || $responsavelId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos para atualizar o trem.']);
    exit;
}

$checkUsuario = mysqli_prepare($conexao, 'SELECT usuario_id FROM usuarios WHERE usuario_id = ? LIMIT 1');
if ($checkUsuario) {
    mysqli_stmt_bind_param($checkUsuario, 'i', $responsavelId);
    mysqli_stmt_execute($checkUsuario);
    $resultadoUsuario = mysqli_stmt_get_result($checkUsuario);
    $usuarioExiste = mysqli_num_rows($resultadoUsuario) > 0;
    mysqli_stmt_close($checkUsuario);
} else {
    $usuarioExiste = false;
}

if (!$usuarioExiste) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Responsável informado não existe.']);
    exit;
}

$sql = 'UPDATE trens SET nome_trem = ?, responsavel_id = ? WHERE trem_id = ? LIMIT 1';
$stmt = mysqli_prepare($conexao, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar atualização do trem.']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'sii', $nomeTrem, $responsavelId, $tremId);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Dados do trem atualizados com sucesso.']);
    exit;
}

http_response_code(500);
echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados do trem.']);
