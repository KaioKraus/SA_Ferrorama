<?php
session_start();

require_once __DIR__ . '/validar_acesso.php';

function voltarParaSensores(): void
{
    header('Location: tela_sensores.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    voltarParaSensores();
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    $_SESSION['mensagem_erro'] = 'Somente administradores podem excluir sensores.';
    voltarParaSensores();
}

$sensorId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($sensorId === false) {
    $_SESSION['mensagem_erro'] = 'O sensor selecionado é inválido.';
    voltarParaSensores();
}

try {
    if (!mysqli_begin_transaction($conexao)) {
        throw new RuntimeException('Não foi possível iniciar a transação: ' . mysqli_error($conexao));
    }
    $transacaoIniciada = true;

    $stmtSensor = mysqli_prepare($conexao, 'SELECT sensor_id FROM sensores WHERE sensor_id = ? FOR UPDATE');
    if (!$stmtSensor) {
        throw new RuntimeException('Não foi possível preparar a consulta do sensor: ' . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmtSensor, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtSensor)) {
        throw new RuntimeException('Não foi possível consultar o sensor: ' . mysqli_stmt_error($stmtSensor));
    }
    $resultadoSensor = mysqli_stmt_get_result($stmtSensor);
    if (!$resultadoSensor) {
        throw new RuntimeException('Não foi possível obter o sensor: ' . mysqli_stmt_error($stmtSensor));
    }
    $sensor = $resultadoSensor ? mysqli_fetch_assoc($resultadoSensor) : null;
    mysqli_free_result($resultadoSensor);
    mysqli_stmt_close($stmtSensor);

    if (!$sensor) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem_erro'] = 'O sensor selecionado não foi encontrado.';
        voltarParaSensores();
    }

    $stmtTelemetria = mysqli_prepare($conexao, 'SELECT COUNT(*) AS quantidade FROM valor_sensor WHERE sensor_id = ?');
    if (!$stmtTelemetria) {
        throw new RuntimeException('Não foi possível preparar a verificação da telemetria: ' . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmtTelemetria, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtTelemetria)) {
        throw new RuntimeException('Não foi possível consultar os registros de telemetria: ' . mysqli_stmt_error($stmtTelemetria));
    }
    $resultadoTelemetria = mysqli_stmt_get_result($stmtTelemetria);
    if (!$resultadoTelemetria) {
        throw new RuntimeException('Não foi possível obter os registros de telemetria: ' . mysqli_stmt_error($stmtTelemetria));
    }
    $telemetria = $resultadoTelemetria ? mysqli_fetch_assoc($resultadoTelemetria) : null;
    mysqli_free_result($resultadoTelemetria);
    mysqli_stmt_close($stmtTelemetria);

    if ((int) ($telemetria['quantidade'] ?? 0) > 0) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem_erro'] = 'Não é possível excluir este sensor porque ele possui registros vinculados na tabela de telemetria.';
        voltarParaSensores();
    }

    $stmtExcluir = mysqli_prepare($conexao, 'DELETE FROM sensores WHERE sensor_id = ? LIMIT 1');
    if (!$stmtExcluir) {
        throw new RuntimeException('Não foi possível preparar a exclusão do sensor: ' . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmtExcluir, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtExcluir)) {
        throw new RuntimeException('Não foi possível excluir o sensor: ' . mysqli_stmt_error($stmtExcluir));
    }
    $linhasAfetadas = mysqli_stmt_affected_rows($stmtExcluir);
    mysqli_stmt_close($stmtExcluir);

    if ($linhasAfetadas < 1) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem_erro'] = 'O sensor selecionado não foi encontrado.';
        voltarParaSensores();
    }

    if (!mysqli_commit($conexao)) {
        throw new RuntimeException('Não foi possível confirmar a exclusão: ' . mysqli_error($conexao));
    }
    $transacaoIniciada = false;
    $_SESSION['mensagem_sucesso'] = 'Sensor excluído com sucesso.';
} catch (Throwable $erro) {
    if (!empty($transacaoIniciada)) {
        mysqli_rollback($conexao);
    }
    error_log('Erro ao excluir sensor: ' . $erro->getMessage());
    $_SESSION['mensagem_erro'] = 'Não foi possível excluir o sensor. Verifique se existem outros registros vinculados e tente novamente.';
}

voltarParaSensores();
