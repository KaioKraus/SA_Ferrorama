<?php
session_start();
include __DIR__ . '/validar_acesso.php';
require_once "../infra/conexao.php";
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';

$sql_trens = "SELECT trem_id, nome_trem FROM trens ORDER BY nome_trem ASC";
$resultado_trens = mysqli_query($conexao, $sql_trens);

$sql_estacoes = "SELECT nome_estacao AS estacao FROM estacoes WHERE status = 'Ativo' ORDER BY nome_estacao ASC";
$resultado_estacoes = mysqli_query($conexao, $sql_estacoes);

$sql_rotas = "SELECT r.rota_id, r.nome, r.ponto_partida, r.ponto_chegada, r.descricao, r.status, r.trem_id, t.nome_trem
    FROM rotas r
    LEFT JOIN trens t ON t.trem_id = r.trem_id
    ORDER BY r.rota_id ASC";
$resultado_rotas = mysqli_query($conexao, $sql_rotas);

$estacoes = [];
if ($resultado_estacoes) {
    while ($row = mysqli_fetch_assoc($resultado_estacoes)) {
        $estacoes[] = $row['estacao'];
    }
}

$rotasBanco = [];
if ($resultado_rotas) {
    while ($row = mysqli_fetch_assoc($resultado_rotas)) {
        $rotasBanco[] = [
            'rota_id' => $row['rota_id'] ?? '',
            'nome' => $row['nome'] ?? '',
            'ponto_partida' => $row['ponto_partida'] ?? '',
            'ponto_chegada' => $row['ponto_chegada'] ?? '',
            'descricao' => $row['descricao'] ?? '',
            'status' => $row['status'] ?? '',
            'trem_id' => $row['trem_id'] ?? '',
            'nome_trem' => $row['nome_trem'] ?? '',
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <title>Celestial Steel - Rotas</title>
</head>
<body>
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

                <li class="lista" id="li1"><a href="tela_rotas.php" class="lista">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_trens.php" class="lista1">Trens</a></li>
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


        <section class="conteudo">
            <div class="card_usuarios">
                <div class="barra_busca mb-3">
                    <div class="barra_busca_filtro">
                        <div class="input-group dashboard-busca">
                            <input type="text" class="form-control dashboard-input" placeholder="Buscar por ID">
                            <button class="btn dashboard-btn-busca" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>

                        <div class="filtro_busca">
                            <select class="form-select filtro_busca_select">
                                <option selected>Ponto de Partida</option>
                                <option>Estação Norte</option>
                                <option>Estação Sul</option>
                                <option>Estação Leste</option>
                                <option>Estação Oeste</option>
                            </select>
                        </div>

                        <div class="filtro_busca">
                            <select class="form-select filtro_busca_select">
                                <option selected>Destino</option>
                                <option>Estação Norte</option>
                                <option>Estação Sul</option>
                                <option>Estação Leste</option>
                                <option>Estação Oeste</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($usuarioEhAdministrador): ?>
                    <div>
                        <button class="btn trens-btn-add" data-bs-toggle="modal" data-bs-target="#modalAddRota">Adicionar</button>
                    </div>
                    <?php endif; ?>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ponto de partida</th>
                            <th>Destino</th>
                            <th>Trem</th>
                            <th>Status</th>
                            <th>Ações</th>
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
                                    } elseif ($statusNormalizado === 'manutenção' || $statusNormalizado === 'manutencao' || $statusNormalizado === 'manutenção') {
                                        $statusClasse = 'manutencao';
                                    }
                                }
                                $nomeTrem = $rota['nome_trem'] ?? 'Trem não informado';
                                ?>
                                <tr data-rota-id="<?= (int) ($rota['rota_id'] ?? 0); ?>">
                                    <td><?= htmlspecialchars((string) ($rota['rota_id'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['nome'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['ponto_partida'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($rota['ponto_chegada'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) $nomeTrem) ?></td>
                                    <td><span class="trens-card-status <?= htmlspecialchars($statusClasse) ?>"><?= htmlspecialchars((string) ($rota['status'] ?? '')) ?: 'Sem status' ?></span></td>
                                    <td>
                                        <button class="btn_edit" type="button" aria-label="Editar rota"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn_delete mx-2" type="button" data-bs-toggle="modal" data-bs-target="#modal_confirm_delete" data-id="<?= (int) ($rota['rota_id'] ?? 0); ?>" aria-label="Excluir rota"><i class="bi bi-trash"></i></button>
                                        <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" data-rota-id="<?= (int) ($rota['rota_id'] ?? 0); ?>" aria-label="Informações da rota"><i class="bi bi-info-lg"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhuma rota cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="modal fade" id="modalAddRota" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 15px; border: none;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title w-100 text-center text-dark">Cadastro de Rota</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; right: 20px; top: 20px;"></button>
                            </div>
                            <div class="modal-body px-5 pt-4 pb-4">
                                <form method="POST" action="cadastrar_rota.php" id="form_cadastro_rota">
                                    <div class="row mb-4">
                                        <div class="col pe-2">
                                            <div class="input-field">
                                                <select required name="ponto_partida" class="form-select">
                                                    <option value=""></option>
                                                    <?php if (count($estacoes) > 0): ?>
                                                        <?php foreach($estacoes as $estacao): ?>
                                                            <option value="<?= htmlspecialchars($estacao) ?>"><?= htmlspecialchars($estacao) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <label>Ponto de partida</label>
                                            </div>
                                        </div>
                                        <div class="col ps-2">
                                            <div class="input-field">
                                                <select required name="ponto_chegada" class="form-select">
                                                    <option value=""></option>
                                                    <?php if (count($estacoes) > 0): ?>
                                                        <?php foreach($estacoes as $estacao): ?>
                                                            <option value="<?= htmlspecialchars($estacao) ?>"><?= htmlspecialchars($estacao) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <label>Ponto de chegada</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="input-field">
                                                <input required type="text" name="nome" class="form-control" minlength="3">
                                                <label>Nome</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="input-field">
                                                <select required name="trem_id" class="form-select">
                                                    <option value=""></option>
                                                    <?php 
                                                    if ($resultado_trens && mysqli_num_rows($resultado_trens) > 0):
                                                        while($trem = mysqli_fetch_assoc($resultado_trens)): 
                                                    ?>
                                                        <option value="<?= $trem['trem_id'] ?>"><?= htmlspecialchars($trem['nome_trem']) ?></option>
                                                    <?php 
                                                        endwhile;
                                                    endif; 
                                                    ?>
                                                </select>
                                                <label>Trem</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="input-field">
                                                <textarea name="descricao" class="form-control" rows="3" style="resize: none;" placeholder=" "></textarea>
                                                <label>Descrição</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mb-5 mt-4">
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
                                        <button type="submit" class="btn btn_adicionar px-5">Adicionar</button>
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
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow">

                    <!-- HEADER -->
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

                    <!-- BODY -->
                    <div class="modal-body route-modal-body">
                        <div class="route-modal-layout">
                            <div class="route-visual" aria-label="Visualização da rota">
                                <img src="../assets/img/trilho.png" alt="Imagem de trilho" class="route-track-image">
                            </div>

                            <div class="route-info-panel">
                                <div class="route-info-row"><span>ID:</span> <strong id="modal_rota_id"></strong></div>
                                <div class="route-info-row"><span>Trem:</span> <strong id="modal_rota_trem"></strong></div>
                                <div class="route-info-row"><span>Status:</span> <strong id="modal_rota_status"></strong></div>
                                <div class="route-info-row"><span>Partida:</span> <strong id="modal_rota_partida"></strong></div>
                                <div class="route-info-row"><span>Destino:</span> <strong id="modal_rota_destino"></strong></div>
                                <div class="route-info-row"><span>Distância:</span> <strong id="modal_rota_distancia"></strong></div>
                                <div class="route-info-row"><span>Duração:</span> <strong id="modal_rota_duracao"></strong></div>
                                <div class="route-info-row"><span>Data de criação:</span> <strong id="modal_rota_data"></strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/_modal_perfil.php'; ?>
    <script>
        window.rotasDb = <?php echo json_encode($rotasBanco, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/validacao.js"></script>
    <script src="../script/nav.js"></script>
    <script src="../script/rotas.js"></script>
</body>
</html>
