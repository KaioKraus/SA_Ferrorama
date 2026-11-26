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
        throw new RuntimeException('Não foi possível iniciar a exclusão.');
    }

    $stmtHistorico = mysqli_prepare(
        $conexao,
        'SELECT sensor_id FROM valor_sensor WHERE sensor_id = ?
         UNION ALL
         SELECT sensor_id FROM falha WHERE sensor_id = ?
         LIMIT 1'
    );
    if (!$stmtHistorico) {
        throw new RuntimeException('Não foi possível verificar o histórico do sensor.');
    }
    mysqli_stmt_bind_param($stmtHistorico, 'ii', $sensorId, $sensorId);
    if (!mysqli_stmt_execute($stmtHistorico)) {
        throw new RuntimeException('Não foi possível verificar o histórico do sensor.');
    }
    mysqli_stmt_store_result($stmtHistorico);
    $possuiHistorico = mysqli_stmt_num_rows($stmtHistorico) > 0;
    mysqli_stmt_close($stmtHistorico);

    if ($possuiHistorico) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem_erro'] = 'Não é possível excluir um sensor que já possui medições ou falhas registradas.';
        voltarParaSensores();
    }

    $stmtExcluir = mysqli_prepare($conexao, 'DELETE FROM sensores WHERE sensor_id = ? LIMIT 1');
    if (!$stmtExcluir) {
        throw new RuntimeException('Não foi possível preparar a exclusão do sensor.');
    }
    mysqli_stmt_bind_param($stmtExcluir, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtExcluir)) {
        throw new RuntimeException('Não foi possível excluir o sensor.');
    }
    $linhasAfetadas = mysqli_stmt_affected_rows($stmtExcluir);
    mysqli_stmt_close($stmtExcluir);

    if ($linhasAfetadas < 1) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem_erro'] = 'O sensor selecionado não foi encontrado.';
        voltarParaSensores();
    }

    if (!mysqli_commit($conexao)) {
        throw new RuntimeException('Não foi possível concluir a exclusão do sensor.');
    }
    $_SESSION['mensagem_sucesso'] = 'Sensor excluído com sucesso.';
} catch (Throwable $erro) {
    mysqli_rollback($conexao);
    error_log('Erro ao excluir sensor: ' . $erro->getMessage());
    $_SESSION['mensagem_erro'] = 'Não foi possível excluir o sensor. Tente novamente.';
}

voltarParaSensores();
