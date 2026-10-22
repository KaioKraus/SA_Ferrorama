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

function protegerCelulaCsv(string $valor): string
{
    if (preg_match('/^[\t\r\n ]*[=+\-@]/', $valor) === 1) {
        return "'" . $valor;
    }

    return $valor;
}

function textoPdf(string $texto): string
{
    $textoWindows = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $texto);
    if ($textoWindows === false) {
        throw new RuntimeException('Não foi possível converter o texto do relatório para PDF.');
    }

    return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $textoWindows);
}

function gerarPdfRelatorio(array $relatorios): string
{
    $linhas = [];
    foreach ($relatorios as $relatorio) {
        $linhas[] = sprintf(
            '%-6s %-10s %-5s %-20s %-20s %-22s %-6s',
            $relatorio['relatorio_id'],
            date('d/m/Y', strtotime($relatorio['data_relatorio'])),
            $relatorio['trem_id'],
            substr((string) $relatorio['nome_trem'], 0, 20),
            substr((string) $relatorio['nome_sensor'], 0, 20),
            substr((string) $relatorio['tipo_dado'], 0, 22),
            $relatorio['falha_id']
        );
    }
    if (!$linhas) {
        $linhas[] = 'Nenhum registro encontrado para os filtros selecionados.';
    }

    $paginas = array_chunk($linhas, 32);
    $objetos = [
        1 => '<< /Type /Catalog /Pages 2 0 R >>',
        3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>'
    ];
    $idsPaginas = [];

    foreach ($paginas as $indice => $linhasPagina) {
        $idPagina = 4 + ($indice * 2);
        $idConteudo = $idPagina + 1;
        $idsPaginas[] = $idPagina . ' 0 R';

        $conteudo = "BT\n/F1 16 Tf\n40 555 Td\n(" . textoPdf('Relatório de falhas') . ") Tj\n";
        $conteudo .= "/F1 8 Tf\n0 -24 Td\n(" . textoPdf('Gerado em: ' . date('d/m/Y H:i')) . ") Tj\n";
        $conteudo .= "0 -18 Td\n(" . textoPdf(sprintf('%-6s %-10s %-5s %-20s %-20s %-22s %-6s', 'ID', 'DATA', 'TREM', 'NOME DO TREM', 'SENSOR', 'TIPO DE DADO', 'FALHA')) . ") Tj\n";
        $conteudo .= "0 -14 Td\n(" . str_repeat('-', 96) . ") Tj\n";
        foreach ($linhasPagina as $linha) {
            $conteudo .= "0 -13 Td\n(" . textoPdf($linha) . ") Tj\n";
        }
        $conteudo .= "ET";

        $objetos[$idPagina] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $idConteudo . ' 0 R >>';
        $objetos[$idConteudo] = '<< /Length ' . strlen($conteudo) . " >>\nstream\n" . $conteudo . "\nendstream";
    }

    $objetos[2] = '<< /Type /Pages /Kids [' . implode(' ', $idsPaginas) . '] /Count ' . count($idsPaginas) . ' >>';
    ksort($objetos);

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objetos as $id => $objeto) {
        $offsets[$id] = strlen($pdf);
        $pdf .= $id . " 0 obj\n" . $objeto . "\nendobj\n";
    }

    $inicioXref = strlen($pdf);
    $quantidadeObjetos = max(array_keys($objetos)) + 1;
    $pdf .= "xref\n0 " . $quantidadeObjetos . "\n0000000000 65535 f \n";
    for ($id = 1; $id < $quantidadeObjetos; $id++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
    }
    $pdf .= "trailer\n<< /Size " . $quantidadeObjetos . " /Root 1 0 R >>\nstartxref\n" . $inicioXref . "\n%%EOF";

    return $pdf;
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
    $formato = (string) ($_GET['formato'] ?? '');

    if ($formato !== '' && !in_array($formato, ['csv', 'pdf'], true)) {
        responderJson(400, ['success' => false, 'message' => 'O formato de arquivo selecionado é inválido.']);
    }

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

    if ($formato === 'pdf') {
        $arquivoPdf = gerarPdfRelatorio($relatorios);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="relatorio-' . date('Y-m-d') . '.pdf"');
        header('Content-Length: ' . strlen($arquivoPdf));
        echo $arquivoPdf;
        exit;
    }

    if ($formato === 'csv') {
        $arquivo = fopen('php://output', 'w');
        if (!$arquivo) {
            throw new RuntimeException('Não foi possível criar o arquivo CSV.');
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio-' . date('Y-m-d') . '.csv"');
        fwrite($arquivo, "\xEF\xBB\xBF");
        fputcsv($arquivo, [
            'ID do relatório',
            'Data',
            'ID do trem',
            'Trem',
            'ID do sensor',
            'Sensor',
            'Tipo de dado',
            'ID da falha'
        ], ';', '"', '');

        foreach ($relatorios as $relatorio) {
            fputcsv($arquivo, [
                $relatorio['relatorio_id'],
                $relatorio['data_relatorio'],
                $relatorio['trem_id'],
                protegerCelulaCsv((string) $relatorio['nome_trem']),
                $relatorio['sensor_id'],
                protegerCelulaCsv((string) $relatorio['nome_sensor']),
                protegerCelulaCsv((string) $relatorio['tipo_dado']),
                $relatorio['falha_id']
            ], ';', '"', '');
        }

        fclose($arquivo);
        exit;
    }

    responderJson(200, ['success' => true, 'data' => $relatorios]);
} catch (Throwable $erro) {
    error_log('Erro ao gerar relatório: ' . $erro->getMessage());
    responderJson(500, ['success' => false, 'message' => 'Não foi possível consultar os relatórios. Tente novamente.']);
}
