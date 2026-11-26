<?php
// Cadastra um trem com o nome e o responsável escolhidos pelo administrador.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

header('Content-Type: application/json; charset=utf-8');

function erroTrem(int $status, string $mensagem): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

// Exige uma solicitação POST de uma conta com permissão de cadastro.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    erroTrem(405, 'Método não permitido.');
}
if (!$usuarioEhAdministrador) {
    erroTrem(403, 'Acesso negado. Somente administradores podem cadastrar trens.');
}

// Lê o formulário e verifica o tamanho do nome e o ID do responsável.
$nome = is_string($_POST['nome_trem'] ?? null) ? trim($_POST['nome_trem']) : '';
$responsavelId = filter_var($_POST['responsavel_trem'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($nome === '' || $responsavelId === false) {
    erroTrem(400, 'Informe o nome do trem e um responsável.');
}
$tamanhoNome = preg_match_all('/./us', $nome);
if ($tamanhoNome === false || $tamanhoNome > 155) {
    erroTrem(400, 'O nome do trem deve ter no máximo 155 caracteres.');
}

// Confirma a existência do responsável antes de inserir o trem.
try {
    $consulta = $conexao->execute_query('SELECT usuario_id FROM usuarios WHERE usuario_id = ?', [$responsavelId]);
    if (!$consulta->fetch_assoc()) {
        erroTrem(400, 'O responsável selecionado não existe.');
    }

    $conexao->execute_query('INSERT INTO trens (nome_trem, responsavel_id) VALUES (?, ?)', [$nome, $responsavelId]);
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao cadastrar trem: ' . $erro->getMessage());
    erroTrem(500, 'Não foi possível cadastrar o trem.');
}

$_SESSION['mensagem_sucesso'] = 'Trem cadastrado com sucesso!';
echo json_encode(['success' => true, 'reload' => true], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
