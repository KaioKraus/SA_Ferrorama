<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
                    </div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddRota">
                        <i class="bi bi-plus-lg"></i> Adicionar
                    </button>
                </div>
                <!-- Modal: Adicionar Rota -->
                <div class="modal fade" id="modalAddRota" tabindex="-1" aria-labelledby="modalAddRotaLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="tela_rotas.php" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddRotaLabel">Adicionar Rota</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Ponto de Partida</label>
                                        <input type="text" name="ponto_partida" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Destino</label>
                                        <input type="text" name="destino" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nome da Rota</label>
                                        <input type="text" name="nome_rota" class="form-control" required>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Horário de Início</label>
                                            <input type="time" name="horario_inicio" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Horário de Término</label>
                                            <input type="time" name="horario_termino" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
    
    <!--Main-->
    <main>

        <nav class="nav1">

            <img src="../assets/img/logo.svg" alt="logo" class="logo">

            <ul class="ul1">

                <li class="lista1" id="li1"><a href="dashboard.php" class="lista1">Dashboard</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_relatorios.php" class="lista1">Relatórios</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li><hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_rotas.php" class="lista">Rotas</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_trens.php" class="lista1">Trens</a></li>
            </ul>

            <div class="dropdown">
                <div class="box_usuario dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <img src="" alt="" class="box_img_usuario">
                    <div class="h3">
                        <h3 class="h3_1">Fulano de Tal</h3>
                        <h3 class="h3_2">Matricula:</h3>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="">Editar</a></li>
                    <li><a class="dropdown-item" href="tela_login.php">Sair</a></li>
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
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddRota">
                        <i class="bi bi-plus-lg"></i> Adicionar
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
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
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
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                                <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                    <i class="bi bi-info-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                        style="max-height: 350px; background: #f2f2f2;"
                    >
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>