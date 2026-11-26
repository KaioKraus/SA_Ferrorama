<?php
// Prepara os dados para listar, consultar e gerenciar os trens cadastrados.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';
require_once __DIR__ . '/_consulta.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Lê os filtros por ID ou nome, responsável e status.
$termoBusca = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$responsavelSelecionado = filter_var($_GET['responsavel'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]) ?: 0;
$statusFiltro = is_string($_GET['status'] ?? null) ? trim($_GET['status']) : '';
$statusPermitidos = ['Ativo', 'Manutenção', 'Inativo'];
if (!in_array($statusFiltro, $statusPermitidos, true)) {
    $statusFiltro = '';
}

// Carrega a lista de responsáveis e aplica os filtros à consulta de trens.
try {
    $usuarios = $conexao->query('SELECT usuario_id, nome FROM usuarios ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
    $trens = listarTrens($conexao, $termoBusca, $statusFiltro, $responsavelSelecionado);
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao consultar trens: ' . $erro->getMessage());
    http_response_code(500);
    exit('Não foi possível carregar os trens cadastrados.');
}

$mensagemSemTrens = 'Nenhum trem cadastrado.';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/style/style.css">
    <title>Celestial Steel</title>
</head>

<body>

    <?php
    // Mostra mensagens de operações anteriores e as consome da sessão.
    if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999; max-width: 600px;" role="alert">
            <strong>Atenção!</strong> <?php echo htmlspecialchars($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999; max-width: 600px;" role="alert">
            <strong>Sucesso!</strong> <?php echo htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>

<?php if (empty($usuarioEhAdministrador) || !$usuarioEhAdministrador): ?>
<style>
    .btn_delete { display: none !important; }
</style>
<?php endif; ?>

    <main>

        <nav class="nav1_mobile">
            <button class="hamburger-btn" data-bs-toggle="modal" data-bs-target="#navModal" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>

            <img src="../../assets/img/logo.svg" alt="logo" class="logo-mobile">
        </nav>


        <nav class="nav1">

            <img src="../../assets/img/logo.svg" alt="logo" class="logo">

            <ul class="ul1">

                <li class="lista1" id="li1"><a href="../dashboard/dashboard.php" class="lista1">Dashboard</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="../relatorios/tela_relatorios.php" class="lista1">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <?php if ($usuarioEhAdministrador): ?>
                    <li class="lista1" id="li1"><a href="../usuarios/tela_cadastro_user.php" class="lista1">Usuários</a></li>
                    <hr class="hr1 opacity-100">
                <?php endif; ?>

                <li class="lista1" id="li1"><a href="../rotas/tela_rotas.php" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="../sensores/tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_trens.php" class="lista">Trens</a></li>
            </ul>

            <div class="dropdown">
                <div class="box_usuario dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <div class="h3">
                        <h3 class="h3_2"><?php echo htmlspecialchars($usuarioNome); ?></h3>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../acesso/logout.php">Sair</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal_editar_perfil">Editar</button></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo trens-conteudo">
            <div class="trens-painel">

                <div class="trens-topo d-flex align-items-center justify-content-between">
                    <form method="GET" action="tela_trens.php" class="trens-filtros" autocomplete="off">
                        <div class="input-group dashboard-busca trens-busca">
                            <input id="input_busca_trem" name="q" type="text" class="form-control dashboard-input trens-input" value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                            <button id="btn_busca_trem" class="btn dashboard-btn-busca trens-btn-busca" type="button" aria-label="Buscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <select id="filtro_responsavel_trem" name="responsavel" class="form-select trens-filtro-responsavel" aria-label="Filtrar trens por responsável">
                            <option value="0">Todos os responsáveis</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= (int) $usuario['usuario_id']; ?>" <?= (int) $responsavelSelecionado === (int) $usuario['usuario_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filtro_status_trem" name="status" class="form-select trens-filtro-responsavel" aria-label="Filtrar trens por status">
                            <option value="">Todos os status</option>
                            <?php foreach ($statusPermitidos as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>" <?= $statusFiltro === $status ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <?php if ($usuarioEhAdministrador): ?>
                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                    <?php endif; ?>
                </div>

                <div class="trens-lista">
                    <div class="row trens-grid">
                        <?php require __DIR__ . '/_cartoes.php'; ?>
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
                                        <input required="" autocomplete="off" type="text" id="modal_trem_id" class="form-control" readonly aria-readonly="true" tabindex="-1" />
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
                                <img src="../../assets/img/trilho.svg" alt="Imagem de trilho" class="route-track-image">
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
                    <div id="modal_confirm_text" class="alert alert-warning mb-0" role="alert">Deseja realmente excluir este trem?</div>
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

    <div class="modal fade" id="navModal" tabindex="-1" aria-labelledby="navModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="navModalLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="../dashboard/dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="mb-2"><a href="../relatorios/tela_relatorios.php" class="text-decoration-none">Relatórios</a></li>
                        <?php if ($usuarioEhAdministrador): ?>
                            <li class="mb-2"><a href="../usuarios/tela_cadastro_user.php" class="text-decoration-none">Usuários</a></li>
                        <?php endif; ?>
                        <li class="mb-2"><a href="../rotas/tela_rotas.php" class="text-decoration-none">Rotas</a></li>
                        <li class="mb-2"><a href="../sensores/tela_sensores.php" class="text-decoration-none">Sensores</a></li>
                        <li class="mb-2"><a href="../acesso/tela_login.php" class="text-decoration-none">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../compartilhado/_modal_perfil.php'; ?>
    <script>
        window.tremUsuarios = <?= json_encode($usuarios, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../script/nav.js"></script>
    <script src="../../script/trens.js?v=<?php echo filemtime(__DIR__ . '/../../script/trens.js'); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertWarning = document.querySelector('.alert-warning');
            const alertSuccess = document.querySelector('.alert-success');
            
            if (alertWarning) {
                setTimeout(function() {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertWarning);
                    bsAlert.close();
                }, 7000);
            }
            
            if (alertSuccess) {
                setTimeout(function() {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertSuccess);
                    bsAlert.close();
                }, 4000);
            }
        });
    </script>
</body>

</html>