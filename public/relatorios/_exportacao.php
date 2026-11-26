<?php
// Prepara o conteúdo dos relatórios e envia arquivos de download em PDF ou CSV.

// Faz a planilha tratar texto iniciado por operadores de fórmula como texto literal.
function protegerCelulaCsv(string $valor): string
{
    if (preg_match('/^[\t\r\n ]*[=+\-@]/', $valor) === 1) {
        return "'" . $valor;
    }

    return $valor;
}

// Converte a codificação e escapa caracteres reservados no texto do PDF.
function textoPdf(string $texto): string
{
    $textoWindows = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $texto);
    if ($textoWindows === false) {
        throw new RuntimeException('Não foi possível converter o texto do relatório para PDF.');
    }

    return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $textoWindows);
}

// Transforma os registros em linhas de texto com colunas de largura fixa.
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

    // Divide as linhas em páginas e cria os objetos que compõem o documento.
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

    // Serializa os objetos e registra a posição de cada um para a tabela de referências do PDF.
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

// Configura o nome e o tipo do download e envia o conteúdo no formato escolhido.
function baixarRelatorios(string $formato, array $relatorios, string $sufixo): void
{
    if ($formato === 'pdf') {
        $pdf = gerarPdfRelatorio($relatorios);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="relatorio-' . $sufixo . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    $arquivo = fopen('php://output', 'w');
    if (!$arquivo) {
        throw new RuntimeException('Não foi possível criar o arquivo CSV.');
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio-' . $sufixo . '.csv"');
    // O marcador UTF-8 ajuda leitores de planilha a reconhecer os acentos no CSV.
    fwrite($arquivo, "\xEF\xBB\xBF");
    fputcsv($arquivo, ['ID do relatório', 'Data', 'ID do trem', 'Trem', 'ID do sensor', 'Sensor', 'Tipo de dado', 'ID da falha'], ';', '"', '');
    foreach ($relatorios as $relatorio) {
        fputcsv($arquivo, [
            $relatorio['relatorio_id'],
            $relatorio['data_relatorio'],
            $relatorio['trem_id'],
            protegerCelulaCsv((string) $relatorio['nome_trem']),
            $relatorio['sensor_id'],
            protegerCelulaCsv((string) $relatorio['nome_sensor']),
            protegerCelulaCsv((string) $relatorio['tipo_dado']),
            $relatorio['falha_id'],
        ], ';', '"', '');
    }
    fclose($arquivo);
    exit;
}
