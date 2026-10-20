<?php
session_start();
include __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: text/html; charset=UTF-8');

$termoBusca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$trens = [];

if ($termoBusca === '') {
    $sql = 'SELECT trem_id, nome_trem, responsavel_id FROM trens ORDER BY trem_id ASC';
    $resultado = mysqli_query($conexao, $sql);
} else {
    $sql = 'SELECT trem_id, nome_trem, responsavel_id FROM trens WHERE CAST(trem_id AS CHAR) LIKE ? OR nome_trem LIKE ? ORDER BY trem_id ASC';
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

if (empty($trens)) {
    echo '<div class="col-12"><p class="text-center py-4 mb-0">Nenhum trem encontrado.</p></div>';
    exit;
}

foreach ($trens as $trem):
    $tremId = (int) $trem['trem_id'];
    $nomeTrem = htmlspecialchars((string) $trem['nome_trem'], ENT_QUOTES, 'UTF-8');
    $responsavelId = (int) $trem['responsavel_id'];
    $statusTrem = 'Inativo';
    ?>
    <div class="col">
        <article class="trens-card-trem" data-id="<?= $tremId; ?>" data-nome="<?= $nomeTrem; ?>" data-responsavel="<?= $responsavelId; ?>" data-status="<?= $statusTrem; ?>">
            <div class="trens-card-cabecalho">
                <div class="trens-card-header-row">
                    <span class="trens-card-status inativo"><?= $statusTrem; ?></span>
                    <div class="trens-card-acoes">
                        <?php if ($usuarioEhAdministrador): ?>
                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn_delete" type="button" data-id="<?= $tremId; ?>" aria-label="Excluir trem">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                        <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
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
