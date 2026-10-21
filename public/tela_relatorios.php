<?php
session_start();
include __DIR__ . '/validar_acesso.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$resultadoTrens = mysqli_query($conexao, 'SELECT trem_id, nome_trem FROM trens ORDER BY nome_trem ASC');
if (!$resultadoTrens) {
    error_log('Erro ao consultar trens para filtros de relatório: ' . mysqli_error($conexao));
    http_response_code(500);
    die('Não foi possível carregar os filtros de relatórios.');
}
$trensRelatorio = mysqli_fetch_all($resultadoTrens, MYSQLI_ASSOC);
mysqli_free_result($resultadoTrens);

$resultadoTiposSensor = mysqli_query($conexao, 'SELECT DISTINCT tipo_dado FROM sensores WHERE tipo_dado <> "" ORDER BY tipo_dado ASC');
if (!$resultadoTiposSensor) {
    error_log('Erro ao consultar tipos de sensores para filtros de relatório: ' . mysqli_error($conexao));
    http_response_code(500);
    die('Não foi possível carregar os filtros de relatórios.');
}
$tiposSensorRelatorio = mysqli_fetch_all($resultadoTiposSensor, MYSQLI_ASSOC);
mysqli_free_result($resultadoTiposSensor);
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

                <li class="lista" id="li1"><a href="tela_relatorios.php" class="lista">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <?php if ($usuarioEhAdministrador): ?>
                    <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li>
                    <hr class="hr1 opacity-100">
                <?php endif; ?>

                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li>
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
            <div class="card_relatorios">
                <div class="barra_busca">
                    <div class="barra_busca_filtro">
                        <div class="input-group dashboard-busca">
                            <input id="input_busca_relatorio" type="text" class="form-control dashboard-input" placeholder="Buscar por ID, Data, Trem ou Falha" aria-label="Buscar por ID, Data, Trem ou Falha">
                            <button id="btn_busca_relatorio" class="btn dashboard-btn-busca" type="button" aria-label="Buscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="filtro_busca">
                            <select id="filtro_tipo_relatorio" class="form-select filtro_busca_select" aria-label="Filtrar por tipo de sensor">
                                <option value="todos" selected>Todos os tipos</option>
                                <?php foreach ($tiposSensorRelatorio as $tipoSensor): ?>
                                    <option value="<?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                </div>
                <div class="table-responsive relatorios-tabela-container">
                    <table class="table relatorios-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Data</th>
                                <th scope="col">Trem</th>
                                <th scope="col">Sensor</th>
                                <th scope="col">Falha</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_relatorios">
                            <tr><td colspan="5" class="text-center text-muted">Carregando relatórios...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card_gerar_relatorios">
                <h5 class="card-title">Gerar Relatório</h5>
                <p class="card-text">Selecione o trem, o sensor e o período desejado.</p>

                <form id="form_relatorio">
                    <div class="form-relatorio-row">
                        <div class="form-relatorio-group">
                            <label for="relatorio_trem">Trem</label>
                            <select id="relatorio_trem" class="form-select input-relatorio" required>
                                <option value="todos" selected>Todos os trens</option>
                                <?php foreach ($trensRelatorio as $trem): ?>
                                    <option value="<?= (int) $trem['trem_id']; ?>">
                                        <?= htmlspecialchars($trem['nome_trem'], ENT_QUOTES, 'UTF-8'); ?> (ID <?= (int) $trem['trem_id']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-relatorio-group">
                            <label for="relatorio_sensor">Sensor</label>
                            <select id="relatorio_sensor" class="form-select input-relatorio" required>
                                <option value="todos" selected>Todos os sensores</option>
                                <?php foreach ($tiposSensorRelatorio as $tipoSensor): ?>
                                    <option value="<?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-relatorio-row">
                        <div class="form-relatorio-group">
                            <label for="relatorio_data_inicio">Data início</label>
                            <input type="date" id="relatorio_data_inicio" name="relatorio_data_inicio" class="form-control input-relatorio" required>
                        </div>

                        <div class="form-relatorio-group">
                            <label for="relatorio_data_fim">Data fim</label>
                            <input type="date" id="relatorio_data_fim" name="relatorio_data_fim" class="form-control input-relatorio" required>
                        </div>
                    </div>

                    <div class="form-relatorio-actions">
                        <button type="submit" class="btn btn_adicionar">Enviar</button>
                    </div>
                    <div id="mensagem_relatorio" class="mt-3 text-center"></div>
                </form>
            </div>

        </section>
    </main>

    <div class="modal fade" id="modal_confirm_delete" tabindex="-1" aria-labelledby="label_modal_confirm_delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label_modal_confirm_delete">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal_confirm_text">Deseja realmente excluir este sensor?</p>
                </div>
                <div class="modal-footer">
                    <form id="form_delete_sensor" method="post" action="excluir_sensor.php">
                        <input type="hidden" name="id" id="delete_sensor_id" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_relatorio" tabindex="-1" aria-labelledby="label_modal_relatorio" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-ferroviario">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title w-100 text-center fw-bold text-dark" id="label_modal_relatorio">Gerar relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5 pt-4 pb-4">
                    <form id="form_relatorio_modal">
                        <div class="mb-3">
                            <label for="relatorio_modal_trem" class="form-label fw-bold text-dark">Trem</label>
                            <select id="relatorio_modal_trem" class="form-select" required>
                                <option value="todos" selected>Todos os trens</option>
                                <?php foreach ($trensRelatorio as $trem): ?>
                                    <option value="<?= (int) $trem['trem_id']; ?>">
                                        <?= htmlspecialchars($trem['nome_trem'], ENT_QUOTES, 'UTF-8'); ?> (ID <?= (int) $trem['trem_id']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="relatorio_modal_sensor" class="form-label fw-bold text-dark">Sensor</label>
                            <select id="relatorio_modal_sensor" class="form-select" required>
                                <option value="todos" selected>Todos os sensores</option>
                                <?php foreach ($tiposSensorRelatorio as $tipoSensor): ?>
                                    <option value="<?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars($tipoSensor['tipo_dado'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="relatorio_modal_data_inicio" class="form-label fw-bold text-dark">Data início</label>
                                <input type="date" id="relatorio_modal_data_inicio" class="form-control input-relatorio" required>
                            </div>
                            <div class="col-md-6">
                                <label for="relatorio_modal_data_fim" class="form-label fw-bold text-dark">Data fim</label>
                                <input type="date" id="relatorio_modal_data_fim" class="form-control input-relatorio" required>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn_adicionar px-5">Gerar relatório</button>
                            <div id="mensagem_relatorio_modal" class="mt-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../script/validacao.js?v=20261006"></script>
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
    <!-- Modal: Detalhes do Relatório -->
    <div class="modal fade" id="modal_detalhes_relatorio" tabindex="-1" aria-labelledby="label_modal_detalhes_relatorio" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label_modal_detalhes_relatorio">Detalhes do Relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> <span id="det-id"></span></li>
                        <li><strong>Trem:</strong> <span id="det-trem"></span></li>
                        <li><strong>Sensor:</strong> <span id="det-sensor"></span></li>
                        <li><strong>Data:</strong> <span id="det-data"></span></li>
                        <li><strong>Falha:</strong> <span id="det-falha"></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/_modal_perfil.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/sensores.js"></script>
    <script src="../script/nav.js"></script>
    <script src="../script/relatorios.js"></script>
</body>

</html>