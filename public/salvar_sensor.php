<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

function responderSalvamentoSensor(int $statusHttp, bool $sucesso, string $mensagem): void
{
    http_response_code($statusHttp);
    echo json_encode(
        ['success' => $sucesso, 'message' => $mensagem],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderSalvamentoSensor(405, false, 'Método não permitido.');
}

if (!$usuarioEhAdministrador) {
    responderSalvamentoSensor(403, false, 'Somente administradores podem cadastrar ou editar sensores.');
}

$sensorId = null;
// Se veio um número de sensor, é uma edição. Sem número, é um cadastro novo.
if (isset($_POST['sensor_id']) && $_POST['sensor_id'] !== '') {
    $sensorId = filter_var($_POST['sensor_id'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($sensorId === false) {
        responderSalvamentoSensor(400, false, 'O sensor selecionado é inválido.');
    }
}

// Pega e confere o que foi preenchido no formulário.
$nomeSensor = trim((string) ($_POST['nome_sensor'] ?? ''));
$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
$tipoDado = trim((string) ($_POST['tipo_dado'] ?? ''));
$statusSensor = (string) ($_POST['status_sensor'] ?? '');

$quantidadeCaracteres = preg_match_all('/./us', $nomeSensor);
if (
    $nomeSensor === '' ||
    $quantidadeCaracteres === false ||
    $quantidadeCaracteres < 3 ||
    $quantidadeCaracteres > 155 ||
    $localizacao === '' ||
    $tipoDado === '' ||
    strlen($localizacao) > 155 ||
    strlen($tipoDado) > 155 ||
    !in_array($statusSensor, ['Ativo', 'Inativo'], true)
) {
    responderSalvamentoSensor(400, false, 'Preencha os campos corretamente e selecione um status válido.');
}

if ($sensorId !== null) {
    // Ao editar, permite manter a localização antiga do sensor.
    $stmtLocalizacaoAtual = mysqli_prepare($conexao, 'SELECT localizacao FROM sensores WHERE sensor_id = ? LIMIT 1');
    if ($stmtLocalizacaoAtual === false) {
        error_log('Erro ao preparar validação da localização do sensor: ' . mysqli_error($conexao));
        responderSalvamentoSensor(500, false, 'Não foi possível validar a localização do sensor.');
    }
    mysqli_stmt_bind_param($stmtLocalizacaoAtual, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtLocalizacaoAtual)) {
        error_log('Erro ao consultar localização atual do sensor: ' . mysqli_stmt_error($stmtLocalizacaoAtual));
        mysqli_stmt_close($stmtLocalizacaoAtual);
        responderSalvamentoSensor(500, false, 'Não foi possível validar a localização do sensor.');
    }
    $resultadoLocalizacaoAtual = mysqli_stmt_get_result($stmtLocalizacaoAtual);
    $localizacaoAtual = $resultadoLocalizacaoAtual ? mysqli_fetch_assoc($resultadoLocalizacaoAtual) : null;
    if ($resultadoLocalizacaoAtual) {
        mysqli_free_result($resultadoLocalizacaoAtual);
    }
    mysqli_stmt_close($stmtLocalizacaoAtual);
    if (!$localizacaoAtual) {
        responderSalvamentoSensor(404, false, 'O sensor selecionado não foi encontrado.');
    }
    $localizacaoLegadaMantida = $localizacaoAtual['localizacao'] === $localizacao;
} else {
    $localizacaoLegadaMantida = false;
}

$localizacaoPermitida = $localizacaoLegadaMantida;
if (!$localizacaoPermitida) {
    // Para um cadastro novo, aceita apenas trens e rotas que existem.
    $resultadoTrens = mysqli_query($conexao, 'SELECT trem_id, nome_trem FROM trens');
    $resultadoRotas = mysqli_query($conexao, 'SELECT rota_id, ponto_partida, ponto_chegada FROM rotas');
    if (!$resultadoTrens || !$resultadoRotas) {
        error_log('Erro ao validar localização do sensor: ' . mysqli_error($conexao));
        responderSalvamentoSensor(500, false, 'Não foi possível validar a localização selecionada.');
    }
    while ($trem = mysqli_fetch_assoc($resultadoTrens)) {
        $localizacaoPermitida = $localizacaoPermitida
            || $localizacao === 'Trem - ID ' . (int) $trem['trem_id'] . ' (' . $trem['nome_trem'] . ')';
    }
    while ($rota = mysqli_fetch_assoc($resultadoRotas)) {
        $localizacaoPermitida = $localizacaoPermitida
            || $localizacao === 'Rota ' . (int) $rota['rota_id'] . ': ' . $rota['ponto_partida'] . ' → ' . $rota['ponto_chegada'];
    }
    mysqli_free_result($resultadoTrens);
    mysqli_free_result($resultadoRotas);
}
if (!$localizacaoPermitida) {
    responderSalvamentoSensor(400, false, 'Selecione um trem ou uma rota existente.');
}

// Salva o sensor novo ou as mudanças do sensor escolhido.
if ($sensorId !== null) {
    $sql = 'UPDATE sensores SET nome_sensor = ?, localizacao = ?, tipo_dado = ?, status = ? WHERE sensor_id = ? LIMIT 1';
} else {
    $sql = 'INSERT INTO sensores (nome_sensor, localizacao, tipo_dado, status) VALUES (?, ?, ?, ?)';
}

$stmt = mysqli_prepare($conexao, $sql);
if ($stmt === false) {
    error_log('Erro ao preparar salvamento do sensor: ' . mysqli_error($conexao));
    responderSalvamentoSensor(500, false, 'Não foi possível preparar o salvamento do sensor.');
}

if ($sensorId !== null) {
    mysqli_stmt_bind_param($stmt, 'ssssi', $nomeSensor, $localizacao, $tipoDado, $statusSensor, $sensorId);
} else {
    mysqli_stmt_bind_param($stmt, 'ssss', $nomeSensor, $localizacao, $tipoDado, $statusSensor);
}

if (!mysqli_stmt_execute($stmt)) {
    error_log('Erro ao salvar sensor: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    responderSalvamentoSensor(500, false, 'Não foi possível salvar o sensor no banco de dados.');
}

$sensorEncontrado = true;
if ($sensorId !== null && mysqli_stmt_affected_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    $stmtVerificar = mysqli_prepare($conexao, 'SELECT sensor_id FROM sensores WHERE sensor_id = ? LIMIT 1');
    if ($stmtVerificar === false) {
        error_log('Erro ao verificar sensor atualizado: ' . mysqli_error($conexao));
        responderSalvamentoSensor(500, false, 'Não foi possível confirmar a atualização do sensor.');
    }

    mysqli_stmt_bind_param($stmtVerificar, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtVerificar)) {
        error_log('Erro ao consultar sensor atualizado: ' . mysqli_stmt_error($stmtVerificar));
        mysqli_stmt_close($stmtVerificar);
        responderSalvamentoSensor(500, false, 'Não foi possível confirmar a atualização do sensor.');
    }
    mysqli_stmt_store_result($stmtVerificar);
    $sensorEncontrado = mysqli_stmt_num_rows($stmtVerificar) > 0;
    mysqli_stmt_close($stmtVerificar);
} else {
    mysqli_stmt_close($stmt);
}

if (!$sensorEncontrado) {
    responderSalvamentoSensor(404, false, 'O sensor selecionado não foi encontrado.');
}

$_SESSION['mensagem_sucesso'] = $sensorId !== null
    ? 'Sensor atualizado com sucesso.'
    : 'Sensor cadastrado com sucesso.';

responderSalvamentoSensor(200, true, $_SESSION['mensagem_sucesso']);
