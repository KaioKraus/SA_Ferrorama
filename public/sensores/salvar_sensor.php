<?php
// Cadastra ou atualiza um sensor e responde ao formulário em JSON.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';
require_once __DIR__ . '/_locais.php';

header('Content-Type: application/json; charset=utf-8');

function erroSensor(int $status, string $mensagem): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

// Aceita somente POST e exige permissão administrativa.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    erroSensor(405, 'Método não permitido.');
}
if (!$usuarioEhAdministrador) {
    erroSensor(403, 'Somente administradores podem cadastrar ou editar sensores.');
}

// Lê os campos enviados e distingue cadastro de edição pela presença do ID.
$sensorId = $_POST['sensor_id'] ?? '';
$sensorId = $sensorId === '' ? null : filter_var($sensorId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$nome = trim((string) ($_POST['nome_sensor'] ?? ''));
$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
$tipoDado = trim((string) ($_POST['tipo_dado'] ?? ''));
$status = (string) ($_POST['status_sensor'] ?? '');

if ($sensorId === false) {
    erroSensor(400, 'O sensor selecionado é inválido.');
}

// Verifica o nome, a localização, o tipo de dado e o status antes de salvar.
$tamanhoNome = preg_match_all('/./us', $nome);
if ($tamanhoNome < 3 || $tamanhoNome > 155 || $localizacao === '' || strlen($localizacao) > 155
    || $tipoDado === '' || strlen($tipoDado) > 155 || !in_array($status, ['Ativo', 'Inativo'], true)) {
    erroSensor(400, 'Preencha os campos corretamente e selecione um status válido.');
}

// Busca a localização atual quando o formulário estiver editando um sensor.
try {
    $localizacaoAtual = null;
    if ($sensorId !== null) {
        $consulta = $conexao->execute_query('SELECT localizacao FROM sensores WHERE sensor_id = ?', [$sensorId]);
        $sensor = $consulta->fetch_assoc();
        if (!$sensor) {
            erroSensor(404, 'O sensor selecionado não foi encontrado.');
        }
        $localizacaoAtual = $sensor['localizacao'];
    }

    // Preserva uma localização antiga se ela não mudou. Novos vínculos devem corresponder a um trem ou a uma rota.
    if ($localizacao !== $localizacaoAtual && !in_array($localizacao, array_column(listarLocaisSensores($conexao), 'nome'), true)) {
        erroSensor(400, 'Selecione um trem ou uma rota existente.');
    }

    // Insere ou atualiza o registro conforme a presença do ID.
    if ($sensorId === null) {
        $sql = 'INSERT INTO sensores (nome_sensor, localizacao, tipo_dado, status) VALUES (?, ?, ?, ?)';
        $conexao->execute_query($sql, [$nome, $localizacao, $tipoDado, $status]);
        $mensagem = 'Sensor cadastrado com sucesso.';
    } else {
        $sql = 'UPDATE sensores SET nome_sensor = ?, localizacao = ?, tipo_dado = ?, status = ? WHERE sensor_id = ?';
        $conexao->execute_query($sql, [$nome, $localizacao, $tipoDado, $status, $sensorId]);
        // Confere a existência do registro quando o banco informa que nenhum campo mudou.
        if ($conexao->affected_rows === 0) {
            $consulta = $conexao->execute_query('SELECT sensor_id FROM sensores WHERE sensor_id = ?', [$sensorId]);
            if (!$consulta->fetch_assoc()) {
                erroSensor(404, 'O sensor selecionado não foi encontrado.');
            }
        }
        $mensagem = 'Sensor atualizado com sucesso.';
    }
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao salvar sensor: ' . $erro->getMessage());
    erroSensor(500, 'Não foi possível salvar o sensor no banco de dados.');
}

$_SESSION['mensagem_sucesso'] = $mensagem;
echo json_encode(['success' => true, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
