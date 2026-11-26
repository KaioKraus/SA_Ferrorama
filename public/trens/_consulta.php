<?php
// Compartilha a consulta de trens entre a tela completa e a busca de cartões.

function listarTrens(mysqli $conexao, string $busca, string $status, int $responsavel = 0): array
{
    // Reúne as rotas de cada trem e calcula seu status, dando prioridade às rotas ativas.
    $sql = <<<SQL
        SELECT t.*,
               COALESCE(r.nome, 'Sem rota') AS rota_nome,
               COALESCE(rotas.quantidade, 0) AS quantidade_rotas,
               COALESCE(rotas.ativa, 0) AS rota_ativa,
               CASE
                   WHEN rotas.ativa = 1 THEN 'Ativo'
                   WHEN rotas.em_manutencao = 1 THEN 'Manutenção'
                   ELSE 'Inativo'
               END AS status_trem
        FROM trens t
        LEFT JOIN (
            SELECT trem_id, MIN(rota_id) AS primeira_rota, COUNT(*) AS quantidade,
                   MAX(status = 'Ativo') AS ativa, MAX(status = 'Manutenção') AS em_manutencao
            FROM rotas
            GROUP BY trem_id
        ) rotas ON rotas.trem_id = t.trem_id
        LEFT JOIN rotas r ON r.rota_id = rotas.primeira_rota
        WHERE (CAST(t.trem_id AS CHAR) LIKE ? OR t.nome_trem LIKE ?)
          AND (? = 0 OR t.responsavel_id = ?)
        ORDER BY t.trem_id
        SQL;

    // Busca por ID ou nome e cruza o resultado com o responsável e o status selecionados.
    $filtro = '%' . $busca . '%';
    $consulta = $conexao->execute_query($sql, [$filtro, $filtro, $responsavel, $responsavel]);
    $trens = $consulta->fetch_all(MYSQLI_ASSOC);

    if ($status !== '') {
        $trens = array_filter($trens, static fn($trem) => $trem['status_trem'] === $status);
    }

    return $trens;
}
