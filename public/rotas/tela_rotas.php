<?php
// Prepara a lista, os filtros e o formulário de gerenciamento de rotas.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
// Usa os parâmetros da URL para restaurar os filtros após recarregar a tela.
$statusFiltro = is_string($_GET['status'] ?? null) ? trim($_GET['status']) : '';
$statusPermitidos = ['Ativo', 'Manutenção', 'Inativo'];
if (!in_array($statusFiltro, $statusPermitidos, true)) {
    $statusFiltro = '';
}
$partidaFiltro = is_string($_GET['ponto_partida'] ?? null) ? trim($_GET['ponto_partida']) : '';
$destinoFiltro = is_string($_GET['ponto_chegada'] ?? null) ? trim($_GET['ponto_chegada']) : '';
$termoBusca = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';

// Consulta as rotas com os nomes dos trens e formata a data de criação.
$sql = "SELECT r.rota_id, r.nome, r.ponto_partida, r.ponto_chegada, COALESCE(r.descricao, '') AS descricao,
               r.status, r.trem_id, COALESCE(t.nome_trem, '') AS nome_trem,
               DATE_FORMAT(r.data_criacao, '%d/%m/%Y %H:%i') AS data_criacao
        FROM rotas r
        LEFT JOIN trens t ON t.trem_id = r.trem_id
        ORDER BY r.rota_id";

// Carrega as estações, os trens disponíveis e as rotas já cadastradas.
try {
    $estacoes = $conexao->query('SELECT nome_estacao FROM estacoes ORDER BY nome_estacao')->fetch_all(MYSQLI_ASSOC);
    $trens = $conexao->query('SELECT trem_id, nome_trem FROM trens ORDER BY nome_trem')->fetch_all(MYSQLI_ASSOC);
    $rotasBanco = $conexao->query($sql)->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao carregar rotas: ' . $erro->getMessage());
    http_response_code(500);
    exit('Não foi possível carregar as rotas.');
}

// Combina as estações cadastradas com os pontos de rotas antigas para montar as opções.
$estacoesMapa = array_column($estacoes, 'nome_estacao');
$pontos = array_unique(array_merge(array_column($rotasBanco, 'ponto_partida'), array_column($rotasBanco, 'ponto_chegada')));
sort($pontos, SORT_NATURAL | SORT_FLAG_CASE);
$opcoesEstacoes = array_unique(array_merge($estacoesMapa, $pontos));
$pontosFiltro = array_unique(array_map(static fn($ponto) => trim((string) $ponto), $pontos));
$pontosFiltro = array_filter($pontosFiltro, static fn($ponto) => $ponto !== '');
sort($pontosFiltro, SORT_NATURAL | SORT_FLAG_CASE);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/style/style.css">
    <title>Celestial Steel - Rotas</title>
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

                <li class="lista" id="li1"><a href="tela_rotas.php" class="lista">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="../sensores/tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="../trens/tela_trens.php" class="lista1">Trens</a></li>
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


        <section class="conteudo">
            <div class="card_usuarios rotas-page">
                <div class="barra_busca">
                    <div class="barra_busca_filtro">
                        <form method="GET" action="tela_rotas.php" class="trens-filtros rotas-filtros" id="form_busca_rota">
                            <div class="input-group dashboard-busca trens-busca">
                                <input id="input_busca_rota" name="q" type="text" class="form-control dashboard-input trens-input" value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar por nome ou estação" aria-label="Buscar por nome ou estação">
                                <button id="btn_busca_rota" class="btn dashboard-btn-busca trens-btn-busca" type="button" aria-label="Buscar rota">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>

                            <select id="filtro_partida_rota" name="ponto_partida" class="form-select trens-filtro-responsavel" aria-label="Filtrar por ponto de partida">
                                <option value="">Todos os pontos de partida</option>
                                <?php foreach ($pontosFiltro as $ponto): ?>
                                    <option value="<?= htmlspecialchars($ponto, ENT_QUOTES, 'UTF-8'); ?>" <?= $partidaFiltro === $ponto ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($ponto, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="filtro_destino_rota" name="ponto_chegada" class="form-select trens-filtro-responsavel" aria-label="Filtrar por ponto de chegada">
                                <option value="">Todos os pontos de chegada</option>
                                <?php foreach ($pontosFiltro as $ponto): ?>
                                    <option value="<?= htmlspecialchars($ponto, ENT_QUOTES, 'UTF-8'); ?>" <?= $destinoFiltro === $ponto ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($ponto, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="filtro_status_rota" name="status" class="form-select trens-filtro-responsavel" aria-label="Filtrar rotas por status">
                                <option value="">Todos os status</option>
                                <?php foreach ($statusPermitidos as $status): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>" <?= $statusFiltro === $status ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <?php if ($usuarioEhAdministrador): ?>
                    <div>
                        <button class="btn trens-btn-add" data-bs-toggle="modal" data-bs-target="#modalAddRota">Adicionar</button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['deleted'])): ?>
                    <div id="aviso_exclusao_rota" class="alert alert-success alert-dismissible fade show rota-delete-toast" role="status" aria-live="polite">
                        Rota excluída com sucesso.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div id="aviso_exclusao_rota" class="alert alert-danger alert-dismissible fade show rota-delete-toast" role="alert" aria-live="assertive">
                        <?= $_GET['error'] === 'not_found' ? 'A rota não foi encontrada.' : 'Não foi possível excluir a rota. Verifique suas permissões e tente novamente.'; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endif; ?>

                <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ponto de partida</th>
                            <th>Destino</th>
                            <th>Trem</th>
                            <th>Status</th>
                            <?php if ($usuarioEhAdministrador): ?>
                                <th>Ações</th>
                            <?php else: ?>
                                <th>Visualizar</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rotasBanco)): ?>
                            <?php foreach ($rotasBanco as $rota): ?>
                                <?php
                                $statusClasse = 'ativo';
                                if (isset($rota['status'])) {
                                    $statusNormalizado = strtolower(trim((string) $rota['status']));
                                    if ($statusNormalizado === 'inativo') {
                                        $statusClasse = 'inativo';
                                        } elseif (in_array($statusNormalizado, ['manutenção', 'manutencao'], true)) {
                                        $statusClasse = 'manutencao';
                                    }
                                }
                                $nomeTrem = $rota['nome_trem'] ?? 'Trem não informado';
                                ?>
                                <tr
                                    data-rota-id="<?= (int) ($rota['rota_id'] ?? 0); ?>"
                                    data-ponto-partida="<?= htmlspecialchars((string) ($rota['ponto_partida'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-ponto-chegada="<?= htmlspecialchars((string) ($rota['ponto_chegada'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-status="<?= htmlspecialchars((string) ($rota['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <td><?= htmlspecialchars((string) ($rota['rota_id'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['nome'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['ponto_partida'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['ponto_chegada'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) $nomeTrem) ?></td>
                                    <td>
                                        <span class="trens-card-status rota-status-filtro <?= htmlspecialchars($statusClasse); ?>">
                                            <?= htmlspecialchars((string) ($rota['status'] ?? '')) ?: 'Sem status' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" data-bs-toggle="modal" data-bs-target="#modalAddRota" aria-label="Editar rota"><i class="bi bi-pencil-square"></i></button>
                                            <button class="btn_delete mx-2" type="button" data-bs-toggle="modal" data-bs-target="#modal_confirm_delete" data-id="<?= (int) ($rota['rota_id'] ?? 0); ?>" aria-label="Excluir rota"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                        <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" data-rota-id="<?= (int) ($rota['rota_id'] ?? 0); ?>" aria-label="Informações da rota"><i class="bi bi-info-lg"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" data-empty-routes class="text-center text-muted"><?= $statusFiltro !== '' ? 'Nenhuma rota encontrada com esse status.' : 'Nenhuma rota cadastrada.'; ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>

                <div class="modal fade" id="modalAddRota" tabindex="-1" aria-labelledby="titulo_modal_rota" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered route-modal-dialog">
                        <div class="modal-content" style="border-radius: 18px; border: none;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title w-100 text-center text-dark" id="titulo_modal_rota">Cadastro de Rota</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; right: 20px; top: 20px;"></button>
                            </div>
                            <div class="modal-body px-5 pt-4 pb-4">
                                <form method="POST" action="cadastrar_rota.php" id="form_cadastro_rota">
                                    <input type="hidden" name="rota_id" value="">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="input-field">
                                                <select required name="ponto_partida" id="select_ponto_partida" aria-label="Ponto de partida">
                                                    <option value="" selected disabled></option>
                                                    <?php foreach ($opcoesEstacoes as $ponto): ?>
                                                        <option value="<?= htmlspecialchars((string) $ponto) ?>"><?= htmlspecialchars((string) $ponto) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="select_ponto_partida">Ponto de partida</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-field">
                                                <select required name="ponto_chegada" id="select_ponto_chegada" aria-label="Ponto de chegada">
                                                    <option value="" selected disabled></option>
                                                    <?php foreach ($opcoesEstacoes as $ponto): ?>
                                                        <option value="<?= htmlspecialchars((string) $ponto) ?>"><?= htmlspecialchars((string) $ponto) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="select_ponto_chegada">Ponto de chegada</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="input-field">
                                                <input required type="text" name="nome" id="input_nome_rota" autocomplete="off" minlength="3" placeholder=" " />
                                                <label for="input_nome_rota">Nome</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="input-field">
                                                <select required name="trem_id" id="input_trem_rota" aria-label="Trem">
                                                    <option value="" selected disabled></option>
                                                    <?php foreach ($trens as $trem): ?>
                                                        <option value="<?= $trem['trem_id'] ?>"><?= htmlspecialchars($trem['nome_trem']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="input_trem_rota">Trem</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col">
                                            <div class="input-field">
                                                <textarea name="descricao" id="input_descricao_rota" rows="1" style="resize: none;" placeholder=" "></textarea>
                                                <label for="input_descricao_rota">Descrição</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mb-3 mt-3">
                                        <div class="toggle-container-status">
                                            <input type="radio" name="status" id="status_ativo" value="Ativo" checked>
                                            <label for="status_ativo">Ativo</label>
                                            <input type="radio" name="status" id="status_manutencao" value="Manutenção">
                                            <label for="status_manutencao">Manutenção</label>
                                            <input type="radio" name="status" id="status_inativo" value="Inativo">
                                            <label for="status_inativo">Inativo</label>
                                            <div class="slider-status"></div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn_adicionar px-5 mt-3" id="btn_salvar_rota">Adicionar</button>
                                        <div id="mensagem_cadastro_rota" class="mt-3"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modal_monitoramento" tabindex="-1" aria-labelledby="label_modal_monitoramento" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 1050px;">
                <div class="modal-content border-0 rounded-4 shadow">

                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h4 class="modal-title w-100 text-center fw-bold">
                            Rotas
                        </h4>

                        <button type="button"
                            class="btn-close position-absolute end-0 top-0 m-4"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        </button>

                    </div>

                    <div class="modal-body route-modal-body">
                        <div class="route-modal-layout">
                            <div class="route-visual" aria-label="Visualização da rota">
                                <img src="../../assets/img/trilho.svg" alt="Imagem de trilho" class="route-track-image">
                            </div>

                            <div class="route-info-panel">
                                <div class="route-info-row"><span>ID:</span> <strong id="modal_rota_id"></strong></div>
                                <div class="route-info-row"><span>Trem:</span> <strong id="modal_rota_trem"></strong></div>
                                <div class="route-info-row"><span>Status:</span> <strong id="modal_rota_status"></strong></div>
                                <div class="route-info-row"><span>Partida:</span> <strong id="modal_rota_partida"></strong></div>
                                <div class="route-info-row"><span>Destino:</span> <strong id="modal_rota_destino"></strong></div>
                                <div class="route-info-row"><span>Descrição:</span> <strong id="modal_rota_descricao"></strong></div>
                                <div class="route-info-row"><span>Data de criação:</span> <strong id="modal_rota_data"></strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

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
                        <li class="mb-2"><a href="tela_rotas.php" class="text-decoration-none">Rotas</a></li>
                        <li class="mb-2"><a href="../sensores/tela_sensores.php" class="text-decoration-none">Sensores</a></li>
                        <li class="mb-2"><a href="../acesso/tela_login.php" class="text-decoration-none">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_confirm_delete" tabindex="-1" aria-labelledby="label_modal_confirm_delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label_modal_confirm_delete">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal_confirm_text">Deseja realmente excluir esta rota?</p>
                </div>
                <div class="modal-footer">
                    <form id="form_delete_route" method="post" action="excluir_rota.php">
                        <input type="hidden" name="id" id="delete_route_id" value="">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFiltro, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <?php if ($usuarioEhAdministrador): ?>
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../compartilhado/_modal_perfil.php'; ?>
    <script>
        window.rotasDb = <?php echo json_encode($rotasBanco, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../script/validacao.js"></script>
    <script src="../../script/nav.js"></script>
    <script src="../../script/rotas.js?v=<?php echo filemtime(__DIR__ . '/../../script/rotas.js'); ?>"></script>
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
