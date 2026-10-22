<?php
session_start();
include __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: text/html; charset=UTF-8');

$termoBusca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$statusParametro = $_GET['status'] ?? '';
$statusFiltro = is_string($statusParametro) ? trim($statusParametro) : '';
$statusPermitidos = ['Ativo', 'Manutenção', 'Inativo'];
if (!in_array($statusFiltro, $statusPermitidos, true)) {
    $statusFiltro = '';
}

$trens = [];

if ($termoBusca === '') {
    $sql = 'SELECT 
                t.trem_id,
                t.nome_trem,
                t.responsavel_id,
                COALESCE(t.velocidade, 0) AS velocidade,
                COALESCE(t.temperatura, 0) AS temperatura,
                COALESCE(t.consumo, 0) AS consumo,
                COALESCE((SELECT r.nome FROM rotas r WHERE r.trem_id = t.trem_id ORDER BY r.rota_id LIMIT 1), "Sem rota") AS rota_nome,
                (SELECT COUNT(*) FROM rotas r WHERE r.trem_id = t.trem_id) AS quantidade_rotas,
                CASE
                    WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN "Ativo"
                    WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Manutenção") THEN "Manutenção"
                    ELSE "Inativo"
                END AS status_trem,
                CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN 1 ELSE 0 END AS rota_ativa
            FROM trens t
            ORDER BY t.trem_id ASC';
    $resultado = mysqli_query($conexao, $sql);
} else {
    $sql = 'SELECT 
                t.trem_id,
                t.nome_trem,
                t.responsavel_id,
                COALESCE(t.velocidade, 0) AS velocidade,
                COALESCE(t.temperatura, 0) AS temperatura,
                COALESCE(t.consumo, 0) AS consumo,
                COALESCE((SELECT r.nome FROM rotas r WHERE r.trem_id = t.trem_id ORDER BY r.rota_id LIMIT 1), "Sem rota") AS rota_nome,
                (SELECT COUNT(*) FROM rotas r WHERE r.trem_id = t.trem_id) AS quantidade_rotas,
                CASE
                    WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN "Ativo"
                    WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Manutenção") THEN "Manutenção"
                    ELSE "Inativo"
                END AS status_trem,
                CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN 1 ELSE 0 END AS rota_ativa
            FROM trens t
            WHERE CAST(t.trem_id AS CHAR) LIKE ? OR t.nome_trem LIKE ?
            ORDER BY t.trem_id ASC';
    $stmt = mysqli_prepare($conexao, $sql);
    if ($stmt === false) {
        http_response_code(500);
        echo '<div class="col-12"><p class="text-center py-4 mb-0">Erro ao consultar os trens.</p></div>';
        exit;
    }

    $filtro = '%' . $termoBusca . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $filtro, $filtro);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
}

if ($resultado === false) {
    http_response_code(500);
    echo '<div class="col-12"><p class="text-center py-4 mb-0">Erro ao consultar os trens.</p></div>';
    exit;
}

while ($trem = mysqli_fetch_assoc($resultado)) {
    $trens[] = $trem;
}

if ($statusFiltro !== '') {
    $trens = array_values(array_filter($trens, static function ($trem) use ($statusFiltro) {
        $statusTrem = (string) ($trem['status_trem'] ?? 'Inativo');
        return $statusTrem === $statusFiltro;
    }));
}

if (empty($trens)) {
    echo '<div class="col-12"><p class="text-center py-4 mb-0">Nenhum trem encontrado.</p></div>';
    exit;
}

foreach ($trens as $trem):
    $tremId = (int) $trem['trem_id'];
    $nomeTrem = htmlspecialchars((string) $trem['nome_trem'], ENT_QUOTES, 'UTF-8');
    $responsavelId = (int) $trem['responsavel_id'];
    $statusTrem = (string) ($trem['status_trem'] ?? 'Inativo');
    $statusClasseTrem = strtolower(trim($statusTrem));
    if ($statusClasseTrem === 'manutenção' || $statusClasseTrem === 'manutencao') {
        $statusClasseTrem = 'manutencao';
    } elseif ($statusClasseTrem === 'ativo') {
        $statusClasseTrem = 'ativo';
    } else {
        $statusClasseTrem = 'inativo';
    }
    $rotaAtiva = !empty($trem['rota_ativa']) && (int) $trem['rota_ativa'] === 1;
    $velocidadeTrem = (float) ($trem['velocidade'] ?? 0);
    $temperaturaTrem = (float) ($trem['temperatura'] ?? 0);
    $consumoTrem = (float) ($trem['consumo'] ?? 0);
    $rotaNome = (string) ($trem['rota_nome'] ?? 'Sem rota');
    $quantidadeRotas = (int) ($trem['quantidade_rotas'] ?? 0);
    $mostrarBotaoExcluir = !($statusTrem === 'Ativo' && $rotaAtiva);
    ?>
    <div class="col">
        <article class="trens-card-trem" data-id="<?= $tremId; ?>" data-nome="<?= $nomeTrem; ?>" data-responsavel="<?= $responsavelId; ?>" data-status="<?= htmlspecialchars($statusTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota="<?= htmlspecialchars($rotaNome, ENT_QUOTES, 'UTF-8'); ?>" data-quantidade-rotas="<?= $quantidadeRotas; ?>" data-velocidade="<?= htmlspecialchars((string) $velocidadeTrem, ENT_QUOTES, 'UTF-8'); ?>" data-temperatura="<?= htmlspecialchars((string) $temperaturaTrem, ENT_QUOTES, 'UTF-8'); ?>" data-consumo="<?= htmlspecialchars((string) $consumoTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota-ativa="<?= $rotaAtiva ? '1' : '0'; ?>">
            <div class="trens-card-cabecalho">
                <div class="trens-card-header-row">
                    <a class="trens-card-status <?= htmlspecialchars($statusClasseTrem, ENT_QUOTES, 'UTF-8'); ?>"
                       href="tela_trens.php?status=<?= rawurlencode((string) $statusTrem); ?>"
                       aria-label="Filtrar trens com status <?= htmlspecialchars((string) $statusTrem, ENT_QUOTES, 'UTF-8'); ?>"><?= $statusTrem; ?></a>
                    <div class="trens-card-acoes">
                        <?php if ($usuarioEhAdministrador): ?>
                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <?php if ($mostrarBotaoExcluir): ?>
                                <button class="btn_delete" type="button" data-id="<?= $tremId; ?>" aria-label="Excluir trem">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_visualizar_trem" aria-label="Detalhes do trem">
                            <i class="bi bi-info-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="trens-card-id">
                    <strong>ID: <?= $tremId; ?></strong>
                </div>
                <p class="trens-card-nome">Nome: <?= $nomeTrem; ?></p>
            </div>
            <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
        </article>
    </div>
<?php endforeach;
