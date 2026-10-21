<?php
session_start();
include __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$usuarios = [];
$resultadoUsuarios = mysqli_query($conexao, "SELECT usuario_id, nome FROM usuarios ORDER BY nome ASC");
if ($resultadoUsuarios) {
    while ($usuario = mysqli_fetch_assoc($resultadoUsuarios)) {
        $usuarios[] = $usuario;
    }
}

$termoBusca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$trens = [];

if ($termoBusca === '') {
    $resultadoTrens = mysqli_query($conexao, "
        SELECT 
            t.trem_id,
            t.nome_trem,
            t.responsavel_id,
            COALESCE(t.velocidade, 0) AS velocidade,
            COALESCE(t.temperatura, 0) AS temperatura,
            COALESCE(t.consumo, 0) AS consumo,
            COALESCE((SELECT r.nome FROM rotas r WHERE r.trem_id = t.trem_id ORDER BY r.rota_id LIMIT 1), 'Sem rota') AS rota_nome,
            CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = 'Ativo') THEN 'Ativo' ELSE 'Inativo' END AS status_trem,
            CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = 'Ativo') THEN 1 ELSE 0 END AS rota_ativa
        FROM trens t
        ORDER BY t.trem_id ASC
    ");
} else {
    $sqlBusca = 'SELECT 
            t.trem_id,
            t.nome_trem,
            t.responsavel_id,
            COALESCE(t.velocidade, 0) AS velocidade,
            COALESCE(t.temperatura, 0) AS temperatura,
            COALESCE(t.consumo, 0) AS consumo,
            COALESCE((SELECT r.nome FROM rotas r WHERE r.trem_id = t.trem_id ORDER BY r.rota_id LIMIT 1), "Sem rota") AS rota_nome,
            CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN "Ativo" ELSE "Inativo" END AS status_trem,
            CASE WHEN EXISTS (SELECT 1 FROM rotas r WHERE r.trem_id = t.trem_id AND r.status = "Ativo") THEN 1 ELSE 0 END AS rota_ativa
        FROM trens t
        WHERE CAST(t.trem_id AS CHAR) LIKE ? OR t.nome_trem LIKE ?
        ORDER BY t.trem_id ASC';
    $stmt = mysqli_prepare($conexao, $sqlBusca);
    if ($stmt === false) {
        error_log('Erro ao preparar busca de trens: ' . mysqli_error($conexao));
        http_response_code(500);
        die('Não foi possível buscar os trens.');
    }

    $filtro = '%' . $termoBusca . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $filtro, $filtro);
    mysqli_stmt_execute($stmt);
    $resultadoTrens = mysqli_stmt_get_result($stmt);
}

if (!$resultadoTrens) {
    error_log('Erro ao consultar trens: ' . mysqli_error($conexao));
    http_response_code(500);
    die('Não foi possível carregar os trens cadastrados.');
}
while ($trem = mysqli_fetch_assoc($resultadoTrens)) {
    $trens[] = $trem;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <title>Celestial Steel</title>
</head>

<body>

<?php if (empty($usuarioEhAdministrador) || !$usuarioEhAdministrador): ?>
<style>
    .btn_delete { display: none !important; }
</style>
<?php endif; ?>

    <!--Main-->
    <main>

        <nav class="nav1_mobile">
            <button class="hamburger-btn" data-bs-toggle="modal" data-bs-target="#navModal" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>

            <img src="../assets/img/logo.svg" alt="logo" class="logo-mobile">
        </nav>


        <nav class="nav1">

            <img src="../assets/img/logo.svg" alt="logo" class="logo">

            <ul class="ul1">

                <li class="lista1" id="li1"><a href="dashboard.php" class="lista1">Dashboard</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_relatorios.php" class="lista1">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <?php if ($usuarioEhAdministrador): ?>
                    <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li>
                    <hr class="hr1 opacity-100">
                <?php endif; ?>

                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_trens.php" class="lista">Trens</a></li>
            </ul>

            <div class="dropdown">
                <div class="box_usuario dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <div class="img_usuario">
                        <img src="../assets/img/usuario.png" alt="usuario" class="img-fluid">
                    </div>
                    <div class="h3">
                        <h3 class="h3_1">Seja Bem Vindo!</h3>
                        <h3 class="h3_2"><?php echo htmlspecialchars($usuarioNome); ?></h3>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal_editar_perfil">Editar</button></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo trens-conteudo">
            <div class="trens-painel">
                <div class="trens-topo d-flex align-items-center justify-content-between">
                    <form method="GET" action="tela_trens.php" class="input-group trens-busca" autocomplete="off">
                        <input id="input_busca_trem" name="q" type="text" class="form-control trens-input" value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button id="btn_busca_trem" class="btn trens-btn-busca" type="submit" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <?php if ($usuarioEhAdministrador): ?>
                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                    <?php endif; ?>
                </div>

                <div class="trens-lista">
                    <div class="row trens-grid">
                        <?php if (empty($trens)): ?>
                            <div class="col-12">
                                <p class="text-center py-4 mb-0">Nenhum trem cadastrado.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($trens as $trem): ?>
                                <?php
                                $tremId = (int) $trem['trem_id'];
                                $nomeTrem = htmlspecialchars((string) $trem['nome_trem'], ENT_QUOTES, 'UTF-8');
                                $responsavelId = (int) $trem['responsavel_id'];
                                $statusTrem = (string) ($trem['status_trem'] ?? 'Inativo');
                                $rotaAtiva = !empty($trem['rota_ativa']) && (int) $trem['rota_ativa'] === 1;
                                $velocidadeTrem = (float) ($trem['velocidade'] ?? 0);
                                $temperaturaTrem = (float) ($trem['temperatura'] ?? 0);
                                $consumoTrem = (float) ($trem['consumo'] ?? 0);
                                $rotaNome = (string) ($trem['rota_nome'] ?? 'Sem rota');
                                $mostrarBotaoExcluir = !($statusTrem === 'Ativo' && $rotaAtiva);
                                ?>
                                <div class="col">
                                    <article class="trens-card-trem" data-id="<?= $tremId; ?>" data-nome="<?= $nomeTrem; ?>" data-responsavel="<?= $responsavelId; ?>" data-status="<?= htmlspecialchars($statusTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota="<?= htmlspecialchars($rotaNome, ENT_QUOTES, 'UTF-8'); ?>" data-velocidade="<?= htmlspecialchars((string) $velocidadeTrem, ENT_QUOTES, 'UTF-8'); ?>" data-temperatura="<?= htmlspecialchars((string) $temperaturaTrem, ENT_QUOTES, 'UTF-8'); ?>" data-consumo="<?= htmlspecialchars((string) $consumoTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota-ativa="<?= $rotaAtiva ? '1' : '0'; ?>">
                                        <div class="trens-card-cabecalho">
                                            <div class="trens-card-header-row">
                                                <span class="trens-card-status <?= strtolower($statusTrem) === 'ativo' ? 'ativo' : 'inativo'; ?>"><?= $statusTrem; ?></span>
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
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modal_cadastro" tabindex="-1" aria-labelledby="label_modal_cadastro" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center text-dark" id="label_modal_cadastro">Cadastro Ferroviário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="position: absolute; right: 20px; top: 20px;"></button>
                    </div>
                    <div class="modal-body px-5 pt-4 pb-4">
                        <form class="w-100" id="form_cadastro_trem" method="POST" action="cadastrar_trem.php">
                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_nome_trem" name="nome_trem" class="form-control" maxlength="155" />
                                        <label for="input_nome_trem">Nome</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <select required="" id="input_responsavel_trem" name="responsavel_trem" class="form-select">
                                            <option value="" selected disabled></option>
                                            <?php foreach ($usuarios as $usuario): ?>
                                                <option value="<?= (int) $usuario['usuario_id']; ?>"><?= htmlspecialchars($usuario['nome']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="input_responsavel_trem">Responsável</label>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn_adicionar px-5">Adicionar</button>
                                <div id="mensagem_cadastro_trem" class="mt-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_monitoramento" tabindex="-1" aria-labelledby="label_modal_monitoramento" aria-hidden="true" data-is-admin="<?= $usuarioEhAdministrador ? '1' : '0'; ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center text-dark" id="label_modal_monitoramento">Dados do Trem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="position: absolute; right: 20px; top: 20px;"></button>
                    </div>
                    <div class="modal-body px-5 pt-4 pb-4">
                        <form class="w-100" id="form_editar_trem" method="POST" action="editar_trem.php">
                            <input type="hidden" name="trem_id" id="modal_trem_id_hidden" value="">

                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="modal_trem_id" readonly />
                                        <label for="modal_trem_id">ID do Trem</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="modal_trem_nome" name="nome_trem" <?= $usuarioEhAdministrador ? '' : 'readonly'; ?> />
                                        <label for="modal_trem_nome">Nome</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <select required="" id="modal_trem_responsavel" name="responsavel_id" class="form-select" <?= $usuarioEhAdministrador ? '' : 'disabled'; ?> aria-disabled="<?= $usuarioEhAdministrador ? 'false' : 'true'; ?>">
                                            <option value="" selected disabled></option>
                                            <?php foreach ($usuarios as $usuario): ?>
                                                <option value="<?= (int) $usuario['usuario_id']; ?>"><?= htmlspecialchars($usuario['nome']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="modal_trem_responsavel">Responsável</label>
                                    </div>
                                </div>
                            </div>

                            <?php if ($usuarioEhAdministrador): ?>
                                <div class="text-center mt-4">
                                    <button type="submit" id="btn_salvar_trem" class="btn btn_adicionar px-5">Salvar alterações</button>
                                </div>
                            <?php endif; ?>

                            <div id="mensagem_editar_trem" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_visualizar_trem" tabindex="-1" aria-labelledby="label_modal_visualizar_trem" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 1050px;">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h4 class="modal-title w-100 text-center fw-bold" id="label_modal_visualizar_trem">Dados do Trem</h4>
                        <button type="button" class="btn-close position-absolute end-0 top-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body route-modal-body">
                        <div class="route-modal-layout">
                            <div class="route-visual" aria-label="Visualização do trem">
                                <img src="../assets/img/trilho.png" alt="Imagem de trilho" class="route-track-image">
                            </div>

                            <div class="route-info-panel">
                                <div class="route-info-row"><span>ID:</span> <strong id="modal_trem_visualizar_id">0</strong></div>
                                <div class="route-info-row"><span>Nome:</span> <strong id="modal_trem_visualizar_nome">Nome do trem</strong></div>
                                <div class="route-info-row"><span>Status:</span> <strong id="modal_trem_visualizar_status">Inativo</strong></div>
                                <div class="route-info-row"><span>Rota:</span> <strong id="modal_trem_visualizar_rota">Sem rota</strong></div>
                                <div class="route-info-row"><span>Responsável:</span> <strong id="modal_trem_visualizar_responsavel">Sem responsável</strong></div>
                                <div class="route-info-row"><span>Velocidade:</span> <strong id="modal_trem_visualizar_velocidade">0</strong></div>
                                <div class="route-info-row"><span>Temperatura:</span> <strong id="modal_trem_visualizar_temperatura">0°C</strong></div>
                                <div class="route-info-row"><span>Consumo:</span> <strong id="modal_trem_visualizar_consumo">0kWh</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <div class="modal fade" id="modal_confirm_delete" tabindex="-1" aria-labelledby="label_modal_confirm_delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label_modal_confirm_delete">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal_confirm_text">Deseja realmente excluir este trem?</p>
                </div>
                <div class="modal-footer">
                    <form id="form_delete_train" method="post" action="excluir_trem.php">
                        <input type="hidden" name="id" id="delete_train_id" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Navegação (para telas pequenas) -->
    <div class="modal fade" id="navModal" tabindex="-1" aria-labelledby="navModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="navModalLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="mb-2"><a href="tela_relatorios.php" class="text-decoration-none">Relatórios</a></li>
                        <?php if ($usuarioEhAdministrador): ?>
                            <li class="mb-2"><a href="tela_cadastro_user.php" class="text-decoration-none">Usuários</a></li>
                        <?php endif; ?>
                        <li class="mb-2"><a href="tela_rotas.php" class="text-decoration-none">Rotas</a></li>
                        <li class="mb-2"><a href="tela_sensores.php" class="text-decoration-none">Sensores</a></li>
                        <li class="mb-2"><a href="tela_login.php" class="text-decoration-none">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/_modal_perfil.php'; ?>
    <script>
        window.tremUsuarios = <?= json_encode($usuarios, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/nav.js"></script>
    <script src="../script/trens.js"></script>
</body>

</html>