<?php
// Gera as opções de vínculo com trens e rotas a partir dos registros do banco.

function listarLocaisSensores(mysqli $conexao): array
{
    $trens = $conexao->query('SELECT trem_id, nome_trem FROM trens ORDER BY nome_trem');
    $rotas = $conexao->query('SELECT rota_id, ponto_partida, ponto_chegada FROM rotas ORDER BY nome');

    // Monta as chaves e as descrições que o formulário e a validação de sensores compartilham.
    $locais = [];
    foreach ($trens as $trem) {
        $locais[] = [
            'chave' => 'trem_' . $trem['trem_id'],
            'vinculo' => 'trem',
            'nome' => 'Trem - ID ' . $trem['trem_id'] . ' (' . $trem['nome_trem'] . ')',
        ];
    }
    foreach ($rotas as $rota) {
        $locais[] = [
            'chave' => 'trecho_rota' . $rota['rota_id'],
            'vinculo' => 'rota',
            'nome' => 'Rota ' . $rota['rota_id'] . ': ' . $rota['ponto_partida'] . ' → ' . $rota['ponto_chegada'],
        ];
    }

    return $locais;
}
