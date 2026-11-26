<?php
// Gera relatórios por POST e consulta ou exporta o histórico por GET.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Responde em JSON quando a sessão expira, para que a tela possa orientar o usuário.
if (!isset($_SESSION['usuario_id'])) {
    erroRelatorio(401, 'Sua sessão expirou. Entre novamente.');
}

require_once __DIR__ . '/../acesso/validar_acesso.php';
require_once __DIR__ . '/_exportacao.php';

function erroRelatorio(int $status, string $mensagem): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

$metodo = $_SERVER['REQUEST_METHOD'];
if (!in_array($metodo, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    erroRelatorio(405, 'Método não permitido.');
}

// Lê os parâmetros conforme o método e usa PDF como formato padrão na consulta por ID.
$entrada = $metodo === 'POST' ? $_POST : $_GET;
$relatorioId = $metodo === 'GET' ? filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) : null;
$porId = $relatorioId !== false && $relatorioId !== null;
$formato = is_string($entrada['formato'] ?? null) ? $entrada['formato'] : '';
if ($porId && $formato === '') {
    $formato = 'pdf';
}
if ($metodo === 'GET' && !in_array($formato, ['', 'csv', 'pdf'], true)) {
    erroRelatorio(400, 'O formato de arquivo selecionado é inválido.');
}

// Normaliza os filtros e aceita o parâmetro antigo de sensor como alternativa ao tipo de dado.
$trem = is_string($entrada['trem'] ?? null) ? trim($entrada['trem']) : 'todos';
$tipoDado = $entrada['tipo_dado'] ?? null;
if (!is_string($tipoDado)) {
    $tipoDado = is_string($entrada['sensor'] ?? null) ? $entrada['sensor'] : 'todos';
}
$tipoDado = trim($tipoDado);
$dataInicio = is_string($entrada['data_inicio'] ?? null) ? trim($entrada['data_inicio']) : '';
$dataFim = is_string($entrada['data_fim'] ?? null) ? trim($entrada['data_fim']) : '';
$temPeriodo = $dataInicio !== '' && $dataFim !== '';

$transacaoIniciada = false;
try {
    // Valida filtros e datas nas buscas e exige um período completo na geração.
    if (!$porId) {
        if ($trem !== 'todos' && (!ctype_digit($trem) || (int) $trem < 1)) {
            erroRelatorio(400, 'O trem selecionado é inválido.');
        }
        if ($tipoDado === '' || strlen($tipoDado) > 155) {
            erroRelatorio(400, 'O tipo de dado selecionado é inválido.');
        }
        if (!$temPeriodo && ($metodo === 'POST' || $dataInicio !== '' || $dataFim !== '')) {
            erroRelatorio(400, 'Informe as duas datas do período.');
        }
        if ($temPeriodo) {
            $inicio = DateTime::createFromFormat('!Y-m-d', $dataInicio);
            $fim = DateTime::createFromFormat('!Y-m-d', $dataFim);
            if (!$inicio || $inicio->format('Y-m-d') !== $dataInicio || !$fim || $fim->format('Y-m-d') !== $dataFim) {
                erroRelatorio(400, 'Informe datas válidas.');
            }
            if ($dataInicio > $dataFim) {
                erroRelatorio(400, 'A data inicial não pode ser maior que a final.');
            }
        }
    }

    $tremId = $trem === 'todos' ? 0 : (int) $trem;
    $tipo = $tipoDado === 'todos' ? '' : $tipoDado;
    // Reaproveita os critérios de trem e tipo de dado na geração e na consulta.
    $filtroFalhas = "WHERE (? = 0 OR f.trem_id = ?) AND (? = '' OR s.tipo_dado = ?)";
    // Localiza as falhas correspondentes para gerar um relatório por falha encontrada.
    if ($metodo === 'POST') {
        $sql = 'SELECT f.falha_id, f.trem_id, t.nome_trem, f.sensor_id, s.nome_sensor, s.tipo_dado
                FROM falha f
                INNER JOIN sensores s ON s.sensor_id = f.sensor_id
                INNER JOIN trens t ON t.trem_id = f.trem_id ' . $filtroFalhas;
        $falhas = $conexao->execute_query($sql, [$tremId, $tremId, $tipo, $tipo])->fetch_all(MYSQLI_ASSOC);
        if (!$falhas) {
            erroRelatorio(422, 'Não há falhas para os filtros selecionados. Nenhum relatório foi criado.');
        }

        // Verifica se esta instalação do banco possui as duas colunas necessárias para gravar o período.
        $colunas = $conexao->query("SHOW COLUMNS FROM relatorio WHERE Field IN ('data_inicio', 'data_fim')");
        $salvarPeriodo = $colunas->num_rows === 2;
        $sql = $salvarPeriodo
            ? 'INSERT INTO relatorio (data_relatorio, data_inicio, data_fim, falha_id) VALUES (CURDATE(), ?, ?, ?)'
            : 'INSERT INTO relatorio (data_relatorio, falha_id) VALUES (CURDATE(), ?)';

        // Grava o lote em uma transação para que uma falha interrompa o conjunto inteiro.
        if (!$conexao->begin_transaction()) {
            throw new mysqli_sql_exception($conexao->error, $conexao->errno);
        }
        $transacaoIniciada = true;
        $consulta = $conexao->prepare($sql);
        if (!$consulta) {
            throw new mysqli_sql_exception($conexao->error, $conexao->errno);
        }
        if ($salvarPeriodo) {
            $consulta->bind_param('ssi', $dataInicio, $dataFim, $falhaId);
        } else {
            $consulta->bind_param('i', $falhaId);
        }
        $relatoriosCriados = [];
        // Reusa a instrução preparada e reúne os dados dos registros para devolver ao formulário.
        foreach ($falhas as $falha) {
            $falhaId = (int) $falha['falha_id'];
            if (!$consulta->execute()) {
                throw new mysqli_sql_exception($consulta->error, $consulta->errno);
            }
            $relatoriosCriados[] = [
                'relatorio_id' => $consulta->insert_id,
                'data_relatorio' => date('Y-m-d'),
                'data_inicio' => $salvarPeriodo ? $dataInicio : null,
                'data_fim' => $salvarPeriodo ? $dataFim : null,
                'falha_id' => $falhaId,
                'trem_id' => (int) $falha['trem_id'],
                'nome_trem' => $falha['nome_trem'],
                'sensor_id' => (int) $falha['sensor_id'],
                'nome_sensor' => $falha['nome_sensor'],
                'tipo_dado' => $falha['tipo_dado'],
            ];
        }
        if (!$conexao->commit()) {
            throw new mysqli_sql_exception($conexao->error, $conexao->errno);
        }
        $transacaoIniciada = false;
        http_response_code(201);
        exit(json_encode([
            'success' => true,
            'message' => $salvarPeriodo
                ? 'Relatório criado com sucesso.'
                : 'Relatório criado. O banco atual não permite salvar o período selecionado.',
            'data' => $relatoriosCriados,
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
    }

    // Consulta o histórico pelo ID ou pelos filtros informados.
    $sql = 'SELECT r.relatorio_id, r.data_relatorio, f.falha_id, f.trem_id,
                   t.nome_trem, f.sensor_id, s.nome_sensor, s.tipo_dado
            FROM relatorio r
            INNER JOIN falha f ON f.falha_id = r.falha_id
            INNER JOIN sensores s ON s.sensor_id = f.sensor_id
            INNER JOIN trens t ON t.trem_id = f.trem_id';
    if ($porId) {
        $consulta = $conexao->execute_query($sql . ' WHERE r.relatorio_id = ?', [$relatorioId]);
    } else {
        $sql .= ' ' . $filtroFalhas;
        $valores = [$tremId, $tremId, $tipo, $tipo];
        // Na consulta do histórico, considera a data do relatório e inclui as duas datas do intervalo.
        if ($temPeriodo) {
            $sql .= ' AND r.data_relatorio BETWEEN ? AND ?';
            $valores[] = $dataInicio;
            $valores[] = $dataFim;
        }
        $sql .= ' ORDER BY r.data_relatorio DESC, r.relatorio_id DESC';
        $consulta = $conexao->execute_query($sql, $valores);
    }
    $relatorios = $consulta->fetch_all(MYSQLI_ASSOC);
    if ($porId && !$relatorios) {
        erroRelatorio(404, 'Relatório não encontrado.');
    }
    // Envia o download no formato solicitado ou devolve os registros em JSON.
    if ($formato !== '') {
        baixarRelatorios($formato, $relatorios, $porId ? (string) $relatorioId : date('Y-m-d'));
    }
    echo json_encode(['success' => true, 'data' => $relatorios], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $erro) {
    // Desfaz o lote se houver erro antes da confirmação da transação.
    if ($transacaoIniciada) {
        $conexao->rollback();
    }
    error_log('Erro ao gerar relatório: ' . $erro->getMessage());
    erroRelatorio(500, $metodo === 'POST'
        ? 'Não foi possível criar o relatório. Tente novamente.'
        : 'Não foi possível consultar os relatórios. Tente novamente.');
}
