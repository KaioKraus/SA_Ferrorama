<?php
session_start();
include __DIR__ . '/validar_acesso.php';
include __DIR__ . '/../infra/conexao.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$sqlSensores = 'SELECT sensor_id, nome_sensor, localizacao, tipo_dado, status FROM sensores ORDER BY sensor_id ASC';
$resultadoSensores = mysqli_query($conexao, $sqlSensores);
$sensores = [];
if ($resultadoSensores) {
    while ($sensor = mysqli_fetch_assoc($resultadoSensores)) {
        $sensores[] = $sensor;
    }
    mysqli_free_result($resultadoSensores);
}

function normalizarVinculoLocalizacao(string $localizacao): array {
    $localizacaoNormalizada = trim($localizacao);
    if (stripos($localizacaoNormalizada, 'rota') !== false) {
        return ['vinculo' => 'rota', 'localizacao' => 'trecho_rota1'];
    }

    return ['vinculo' => 'trem', 'localizacao' => 'trem_5823'];
}

function normalizarTipoDado(string $tipoDado): string {
    $tipo = strtolower(trim($tipoDado));

    if (stripos($tipo, 'temperatura') !== false) return 'temperatura';
    if (stripos($tipo, 'vibr') !== false) return 'vibracao';
    if (stripos($tipo, 'velocidade') !== false) return 'velocidade';
    if (stripos($tipo, 'press') !== false) return 'pressao';
    if (stripos($tipo, 'umidade') !== false) return 'umidade';
    if (stripos($tipo, 'energia') !== false) return 'energia';

    return 'outro';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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

                <li class="lista1" id="li1"><a href="tela_relatorios.php" class="lista1">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <?php if ($usuarioEhAdministrador): ?>
                    <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li>
                    <hr class="hr1 opacity-100">
                <?php endif; ?>

                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_sensores.php" class="lista">Sensores</a></li>
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
            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                    style="z-index: 9999; max-width: 600px;" role="alert">
                    <strong>Atenção!</strong> <?= htmlspecialchars($_SESSION['mensagem_erro'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem_erro']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                    style="z-index: 9999; max-width: 600px;" role="alert">
                    <strong>Sucesso!</strong> <?= htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            <?php endif; ?>
            <div class="card_usuarios">
                <div class="barra_busca">
                    <div class="input-group dashboard-busca">
                        <input type="text" id="input_busca_sensor" class="form-control dashboard-input" placeholder="Buscar por ID, nome, localização ou tipo de dado" aria-label="Buscar por ID, nome, localização ou tipo de dado">
                        <button class="btn dashboard-btn-busca" id="btn_busca_sensor" type="button" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <?php if ($usuarioEhAdministrador): ?>
                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro_sensor">Adicionar</button>
                    <?php endif; ?>
                </div>
                <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Localização</th>
                            <th>Tipo de dado</th>
                            <th>Status</th>
                            <?php if ($usuarioEhAdministrador): ?>
                                <th>Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_sensores">
                        <?php if (!empty($sensores)): ?>
                            <?php foreach ($sensores as $sensor): ?>
                                <?php
                                $sensorId = (int)($sensor['sensor_id'] ?? 0);
                                $nomeSensor = (string)($sensor['nome_sensor'] ?? '');
                                $localizacaoSensor = (string)($sensor['localizacao'] ?? '');
                                $tipoDadoSensor = (string)($sensor['tipo_dado'] ?? '');
                                $statusSensor = (string)($sensor['status'] ?? 'Ativo');
                                $vinculoSensor = normalizarVinculoLocalizacao($localizacaoSensor);
                                $statusClass = strtolower($statusSensor) === 'ativo' ? 'ativo' : 'inativo';
                                ?>
                                <tr data-sensor-id="<?= htmlspecialchars((string)$sensorId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-nome="<?= htmlspecialchars($nomeSensor, ENT_QUOTES, 'UTF-8') ?>"
                                    data-vinculo="<?= htmlspecialchars($vinculoSensor['vinculo'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-localizacao="<?= htmlspecialchars($vinculoSensor['localizacao'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-tipo="<?= htmlspecialchars(normalizarTipoDado($tipoDadoSensor), ENT_QUOTES, 'UTF-8') ?>"
                                    data-status="<?= htmlspecialchars($statusSensor, ENT_QUOTES, 'UTF-8') ?>">
                                    <td><?= htmlspecialchars((string)$sensorId, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($nomeSensor, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($localizacaoSensor, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($tipoDadoSensor, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="trens-card-status <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($statusSensor, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <?php if ($usuarioEhAdministrador): ?>
                                        <td>
                                            <button class="btn_edit" type="button" aria-label="Editar sensor <?= htmlspecialchars($nomeSensor, ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-pencil-square"></i></button>
                                            <button type="button" class="btn_delete ms-4" data-id="<?= htmlspecialchars((string)$sensorId, ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-trash"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $usuarioEhAdministrador ? 6 : 5; ?>" class="text-center text-muted">Nenhum sensor cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </section>

        <?php if ($usuarioEhAdministrador): ?>
        <div class="modal fade" id="modal_cadastro_sensor" tabindex="-1" aria-labelledby="label_modal_cadastro_sensor" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-ferroviario">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center fw-bold text-dark" id="label_modal_cadastro_sensor">Cadastro de Sensor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-5 pt-4 pb-4">
                        <form id="form_cadastro_sensor">
                            <div class="mb-3">
                                <input required autocomplete="off" type="text" id="input_nome_sensor" class="form-control" placeholder="Nome">
                            </div>

                            <div class="text-center mb-2">
                                <label for="input_localizacao_sensor" class="fw-bold">Localização</label>
                            </div>

                            <!-- RN03: Todo sensor deve estar vinculado a um trem ou a um trecho da ferrovia -->
                            <div class="mb-3">
                                <select required id="input_localizacao_sensor" class="form-select">
                                    <option value="" selected disabled>Trem</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-center mb-3">
                                <div class="toggle-container">
                                    <input type="radio" name="tipo_vinculo" id="vinculo_trem" checked>
                                    <label for="vinculo_trem">Trem</label>

                                    <input type="radio" name="tipo_vinculo" id="vinculo_rota">
                                    <label for="vinculo_rota">Rota</label>

                                    <div class="slider"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <select required id="input_tipo_sensor" class="form-select">
                                    <option value="" selected disabled>Tipo de dado</option>
                                    <option value="temperatura">Temperatura</option>
                                    <option value="vibracao">Vibração</option>
                                    <option value="velocidade">Velocidade</option>
                                    <option value="pressao">Pressão</option>
                                    <option value="umidade">Umidade</option>
                                    <option value="energia">Energia</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="input_status_sensor" class="fw-bold d-block text-center mb-2">Status da transmissão</label>
                                <select required id="input_status_sensor" class="form-select">
                                    <option value="Ativo" selected>Ativo</option>
                                    <option value="Inativo">Inativo</option>
                                </select>
                            </div>

                            <div class="text-center mt-4">
                                <button id="btn_salvar_sensor" type="submit" class="btn btn_adicionar px-5">Adicionar</button>
                                <div id="mensagem_cadastro_sensor" class="mt-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>


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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/nav.js?v=20261006"></script>
    <script src="../script/sensores.js?v=20261021"></script>

    <script src="../script/validacao.js?v=20261006"></script>
    <script src="../script/scripts.js?v=20261006"></script>
</body>

</html>