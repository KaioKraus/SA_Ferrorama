<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function responderJson(int $status, array $dados): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    responderJson(401, ['success' => false, 'message' => 'Sua sessão expirou. Entre novamente.']);
}

require_once __DIR__ . '/validar_acesso.php';

try {
    $trem = trim((string) ($_GET['trem'] ?? 'todos'));
    $sensor = trim((string) ($_GET['sensor'] ?? 'todos'));
    $dataInicio = trim((string) ($_GET['data_inicio'] ?? ''));
    $dataFim = trim((string) ($_GET['data_fim'] ?? ''));

    if ($trem !== 'todos' && (!ctype_digit($trem) || (int) $trem < 1)) {
        responderJson(400, ['success' => false, 'message' => 'O trem selecionado é inválido.']);
    }
    if ($sensor === '' || ($sensor !== 'todos' && strlen($sensor) > 155)) {
        responderJson(400, ['success' => false, 'message' => 'O sensor selecionado é inválido.']);
    }

    $temDataInicio = $dataInicio !== '';
    $temDataFim = $dataFim !== '';
    if ($temDataInicio !== $temDataFim) {
        responderJson(400, ['success' => false, 'message' => 'Informe as duas datas do período.']);
    }
    if ($temDataInicio) {
        $inicio = DateTime::createFromFormat('!Y-m-d', $dataInicio);
        $fim = DateTime::createFromFormat('!Y-m-d', $dataFim);
        if (!$inicio || $inicio->format('Y-m-d') !== $dataInicio || !$fim || $fim->format('Y-m-d') !== $dataFim) {
            responderJson(400, ['success' => false, 'message' => 'Informe datas válidas.']);
        }
        if ($dataInicio > $dataFim) {
            responderJson(400, ['success' => false, 'message' => 'A data inicial não pode ser maior que a final.']);
        }
    }

    $filtros = [];
    $tipos = '';
    $valores = [];

    if ($trem !== 'todos') {
        $filtros[] = 'f.trem_id = ?';
        $tipos .= 'i';
        $valores[] = (int) $trem;
    }
    if ($sensor !== 'todos') {
        $filtros[] = 's.tipo_dado = ?';
        $tipos .= 's';
        $valores[] = $sensor;
    }
    if ($temDataInicio) {
        $filtros[] = 'r.data_relatorio BETWEEN ? AND ?';
        $tipos .= 'ss';
        $valores[] = $dataInicio;
        $valores[] = $dataFim;
    }

    $sql = 'SELECT r.relatorio_id, r.data_relatorio, f.falha_id, f.trem_id,
                   t.nome_trem, f.sensor_id, s.nome_sensor, s.tipo_dado
            FROM relatorio r
            INNER JOIN falha f ON f.falha_id = r.falha_id
            INNER JOIN sensores s ON s.sensor_id = f.sensor_id
            INNER JOIN trens t ON t.trem_id = f.trem_id';
    if ($filtros) {
        $sql .= ' WHERE ' . implode(' AND ', $filtros);
    }
    $sql .= ' ORDER BY r.data_relatorio DESC, r.relatorio_id DESC';

    $stmt = mysqli_prepare($conexao, $sql);
    if (!$stmt) {
        throw new RuntimeException('Não foi possível preparar a consulta de relatórios: ' . mysqli_error($conexao));
    }
    if ($valores) {
        $parametros = [$stmt, $tipos];
        foreach (array_keys($valores) as $indice) {
            $parametros[] = &$valores[$indice];
        }
        if (!call_user_func_array('mysqli_stmt_bind_param', $parametros)) {
            throw new RuntimeException('Não foi possível vincular os filtros de relatórios: ' . mysqli_stmt_error($stmt));
        }
    }
    if (!mysqli_stmt_execute($stmt)) {
        throw new RuntimeException('Não foi possível consultar relatórios: ' . mysqli_stmt_error($stmt));
    }

    $resultado = mysqli_stmt_get_result($stmt);
    if (!$resultado) {
        throw new RuntimeException('Não foi possível obter os relatórios: ' . mysqli_stmt_error($stmt));
    }
    $relatorios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_free_result($resultado);
    mysqli_stmt_close($stmt);

    responderJson(200, ['success' => true, 'data' => $relatorios]);
} catch (Throwable $erro) {
    error_log('Erro ao gerar relatório: ' . $erro->getMessage());
    responderJson(500, ['success' => false, 'message' => 'Não foi possível consultar os relatórios. Tente novamente.']);
}
