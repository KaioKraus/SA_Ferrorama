<?php
// Cadastra uma rota ou atualiza uma rota existente de acordo com o ID enviado.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

header('Content-Type: application/json; charset=utf-8');

function erroRota(int $status, string $mensagem): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

// Converte nomes antigos dos pontos no mapa para os nomes de estações aceitos no banco.
function normalizarEstacao(string $nome): string
{
    $estacoesAntigas = [
        'Agulha Norte' => 'Estação Norte',
        'Agulha Central' => 'Estação Central',
        'Agulha Sul' => 'Estação Sul',
        'Agulha Oeste' => 'Estação Oeste',
        'Para-choque' => 'Estação Leste',
    ];
    $nome = trim($nome);
    return $estacoesAntigas[$nome] ?? $nome;
}

// Exige POST e restringe o salvamento a administradores.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    erroRota(405, 'Método não permitido.');
}
if (!$usuarioEhAdministrador) {
    erroRota(403, 'Acesso negado. Somente administradores podem cadastrar rotas.');
}

// Lê o trem, os pontos da rota e os demais campos do formulário.
$tremId = filter_var($_POST['trem_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$rotaId = $_POST['rota_id'] ?? '';
$rotaId = $rotaId === '' ? null : filter_var($rotaId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$partida = normalizarEstacao((string) ($_POST['ponto_partida'] ?? ''));
$chegada = normalizarEstacao((string) ($_POST['ponto_chegada'] ?? ''));
$nome = trim((string) ($_POST['nome'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));
$status = trim((string) ($_POST['status'] ?? 'Ativo'));

// Valida os campos obrigatórios e exige pontos de partida e chegada distintos.
if ($rotaId === false) {
    erroRota(400, 'Rota inválida.');
}
if ($partida === '' || $chegada === '' || $nome === '' || $tremId === false) {
    erroRota(400, 'Preencha todos os campos obrigatórios.');
}
if ($partida === $chegada) {
    erroRota(400, 'Ponto de partida e chegada não podem ser iguais.');
}
if (mb_strlen($nome) < 3 || mb_strlen($nome) > 155 || mb_strlen($partida) > 155 || mb_strlen($chegada) > 155) {
    erroRota(400, 'Confira o tamanho do nome e dos pontos da rota.');
}

// Confirma as estações e o trem no banco antes de gravar a rota.
try {
    foreach ([$partida, $chegada] as $ponto) {
        $consulta = $conexao->execute_query('SELECT estacao_id FROM estacoes WHERE nome_estacao = ? LIMIT 1', [$ponto]);
        if (!$consulta->fetch_assoc()) {
            erroRota(400, 'Selecione estações cadastradas no sistema.');
        }
    }

    $consulta = $conexao->execute_query('SELECT trem_id FROM trens WHERE trem_id = ?', [$tremId]);
    if (!$consulta->fetch_assoc()) {
        erroRota(400, 'Trem selecionado não existe.');
    }
    if (!in_array($status, ['Ativo', 'Manutenção', 'Inativo'], true)) {
        erroRota(400, 'O status selecionado é inválido.');
    }

    // Insere uma nova rota sem ID ou atualiza a rota indicada pelo formulário.
    $dados = [$partida, $chegada, $nome, $tremId, $descricao, $status];
    if ($rotaId === null) {
        $sql = 'INSERT INTO rotas (ponto_partida, ponto_chegada, nome, trem_id, descricao, status)
                VALUES (?, ?, ?, ?, ?, ?)';
        $conexao->execute_query($sql, $dados);
        $_SESSION['mensagem_sucesso'] = 'Rota cadastrada com sucesso!';
    } else {
        $sql = 'UPDATE rotas SET ponto_partida = ?, ponto_chegada = ?, nome = ?, trem_id = ?, descricao = ?, status = ?
                WHERE rota_id = ?';
        $dados[] = $rotaId;
        $conexao->execute_query($sql, $dados);
        // Uma edição idêntica pode afetar zero linhas. A consulta seguinte distingue esse caso de um ID inexistente.
        if ($conexao->affected_rows === 0) {
            $consulta = $conexao->execute_query('SELECT rota_id FROM rotas WHERE rota_id = ?', [$rotaId]);
            if (!$consulta->fetch_assoc()) {
                erroRota(404, 'A rota selecionada não foi encontrada.');
            }
        }
        $_SESSION['mensagem_sucesso'] = 'Rota atualizada com sucesso!';
    }
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao salvar rota: ' . $erro->getMessage());
    erroRota(500, $rotaId === null ? 'Erro ao cadastrar rota.' : 'Erro ao atualizar rota.');
}

echo json_encode(['success' => true, 'reload' => true], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
