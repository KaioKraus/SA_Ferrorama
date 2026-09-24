<?php
session_start();
include __DIR__ . '/validar_acesso.php';
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
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
                    <li><a class="dropdown-item" href="">Editar</a></li>
                </ul>
            </div>
        </nav>


        <section class="conteudo">
            <div class="card_usuarios">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group dashboard-busca" style="max-width:420px;">
                        <input type="text" class="form-control dashboard-input" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button class="btn dashboard-btn-busca" type="button" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <button class="btn trens-btn-add" data-bs-toggle="modal" data-bs-target="#modalAddRota"> Adicionar
                    </button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Ponto de Partida</th>
                            <th>Destino</th>
                            <th>Nome</th>
                            <th>Horario de Inicio</th>
                            <th>Horario de Término</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>99999</td>
                            <td>Estação Norte</td>
                            <td>Estação Sul</td>
                            <td>Rota 1</td>
                            <td>08:00</td>
                            <td>09:00</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn_delete mx-2"><i class="bi bi-trash"></i></button>
                                <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                    <i class="bi bi-info-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>99999</td>
                            <td>Estação Norte</td>
                            <td>Estação Sul</td>
                            <td>Rota 2</td>
                            <td>10:00</td>
                            <td>11:00</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn_delete mx-2"><i class="bi bi-trash"></i></button>
                                <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                    <i class="bi bi-info-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Modal: Adicionar Rota (igual a Usuários) -->
                <div class="modal fade" id="modalAddRota" tabindex="-1" aria-labelledby="modalAddRotaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 15px; border: none;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title w-100 text-center text-dark" id="modalAddRotaLabel">Adicionar Rota</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px; top: 20px;"></button>
                            </div>
                            <div class="modal-body px-5 pt-4 pb-4">
                                <form class="w-100" action="tela_rotas.php" method="post">
                                    <div class="row mb-4">
                                        <div class="col pe-2">
                                            <div class="input-field">
                                                <input required autocomplete="off" type="text" name="ponto_partida" class="form-control" />
                                                <label>Ponto de Partida</label>
                                            </div>
                                        </div>
                                        <div class="col ps-2">
                                            <div class="input-field">
                                                <input required autocomplete="off" type="text" name="destino" class="form-control" />
                                                <label>Destino</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="input-field">
                                                <input required autocomplete="off" type="text" name="nome_rota" class="form-control nome-rota-input" />
                                                <label>Nome da Rota</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col pe-2">
                                            <div class="input-field">
                                                <input required type="time" name="horario_inicio" class="form-control" />
                                                <label>Horário de Início</label>
                                            </div>
                                        </div>
                                        <div class="col ps-2">
                                            <div class="input-field">
                                                <input required type="time" name="horario_termino" class="form-control" />
                                                <label>Horário de Término</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn_adicionar">Adicionar</button>
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
                    <div class="modal-body px-5 pb-4">

                        <!-- IMAGEM -->
                        <div class="text-center mb-5">
                            <img
                                src="../assets/img/trilho.png" alt="" class="img-fluid"
                                alt="Mapa Ferroviário"
                                class="img-fluid rounded"
                                style="max-height: 350px; background: #f2f2f2;">
                        </div>

                        <div class="row text-center align-items-center">

                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="fw-bold fs-5">Velocidade:</span>
                                <span class="fs-5"> 999km/h</span>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="fw-bold fs-5">Temperatura:</span>
                                <span class="fs-5"> 99,99C°</span>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="fw-bold fs-5">Status:</span>

                                <span class="badge rounded-pill bg-success fs-6 px-3 py-2">
                                    Normal
                                </span>
                            </div>

                            <div class="col-md-3">
                                <span class="fw-bold fs-5">Consumo:</span>
                                <span class="fs-5"> 99,99kWh</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/nav.js"></script>
    <script src="../script/rotas.js"></script>
</body>

</html>