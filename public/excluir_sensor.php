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
    // Desabilita temporariamente a checagem de foreign keys
    mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 0');
    
    // Primeiro, deletar registros de valor_sensor relacionados
    $stmtValor = mysqli_prepare($conexao, 'DELETE FROM valor_sensor WHERE sensor_id = ?');
    if ($stmtValor) {
        mysqli_stmt_bind_param($stmtValor, 'i', $sensorId);
        mysqli_stmt_execute($stmtValor);
        mysqli_stmt_close($stmtValor);
    }
    
    // Depois, deletar registros de falha relacionados
    $stmtFalha = mysqli_prepare($conexao, 'DELETE FROM falha WHERE sensor_id = ?');
    if ($stmtFalha) {
        mysqli_stmt_bind_param($stmtFalha, 'i', $sensorId);
        mysqli_stmt_execute($stmtFalha);
        mysqli_stmt_close($stmtFalha);
    }
    
    // Por fim, excluir o sensor
    $stmtExcluir = mysqli_prepare($conexao, 'DELETE FROM sensores WHERE sensor_id = ? LIMIT 1');
    if (!$stmtExcluir) {
        mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 1');
        throw new RuntimeException('Erro ao preparar: ' . mysqli_error($conexao));
    }
    mysqli_stmt_bind_param($stmtExcluir, 'i', $sensorId);
    if (!mysqli_stmt_execute($stmtExcluir)) {
        mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 1');
        throw new RuntimeException('Erro ao executar: ' . mysqli_stmt_error($stmtExcluir));
    }
    $linhasAfetadas = mysqli_stmt_affected_rows($stmtExcluir);
    mysqli_stmt_close($stmtExcluir);
    
    // Reabilita a checagem de foreign keys
    mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 1');

    if ($linhasAfetadas < 1) {
        $_SESSION['mensagem_erro'] = 'O sensor selecionado não foi encontrado.';
        voltarParaSensores();
    }

    $_SESSION['mensagem_sucesso'] = 'Sensor excluído com sucesso.';
} catch (Throwable $erro) {
    mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 1');
    error_log('Erro ao excluir sensor: ' . $erro->getMessage());
    $_SESSION['mensagem_erro'] = 'Erro: ' . $erro->getMessage();
}

voltarParaSensores();
