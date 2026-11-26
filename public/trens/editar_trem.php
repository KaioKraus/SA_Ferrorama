<?php
// Atualiza o nome e o responsável de um trem existente.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

header('Content-Type: application/json; charset=utf-8');

function erroTrem(int $status, string $mensagem): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

// Aceita somente POST de administradores.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    erroTrem(405, 'Método não permitido.');
}
if (!$usuarioEhAdministrador) {
    erroTrem(403, 'Acesso negado.');
}

// Valida os identificadores e o nome enviados pelo formulário.
$tremId = filter_var($_POST['trem_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$responsavelId = filter_var($_POST['responsavel_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$nome = is_string($_POST['nome_trem'] ?? null) ? trim($_POST['nome_trem']) : '';
if ($tremId === false || $responsavelId === false || $nome === '') {
    erroTrem(400, 'Dados inválidos para atualizar o trem.');
}

// Verifica o responsável e aplica as alterações ao trem indicado.
try {
    $consulta = $conexao->execute_query('SELECT usuario_id FROM usuarios WHERE usuario_id = ?', [$responsavelId]);
    if (!$consulta->fetch_assoc()) {
        erroTrem(400, 'Responsável informado não existe.');
    }

    $sql = 'UPDATE trens SET nome_trem = ?, responsavel_id = ? WHERE trem_id = ?';
    $conexao->execute_query($sql, [$nome, $responsavelId, $tremId]);
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao atualizar trem: ' . $erro->getMessage());
    erroTrem(500, 'Erro ao atualizar os dados do trem.');
}

echo json_encode(['success' => true, 'message' => 'Dados do trem atualizados com sucesso.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
