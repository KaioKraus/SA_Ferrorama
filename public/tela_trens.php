<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <title>Rafael Onibus</title>
</head>
<body>
    
    <!--Main-->
    <main>

        <nav class="nav1">

            <img src="../assets/img/logo.svg" alt="logo" class="logo">

            <ul class="ul1">

                <li class="lista1" id="li1"><a href="dashboard.html" class="lista1">Dashboard</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_relatorios.html" class="lista1">Relatórios</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_cadastro_user.html" class="lista1">Usuários</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_rotas.html" class="lista1">Rotas</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.html" class="lista1">Sensores</a></li><hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_trens.html" class="lista">Trens</a></li>
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
                    <li><a class="dropdown-item" href="tela_login.html">Sair</a></li>
                </ul>
            </div>
        </nav>

        <section class="trens-conteudo">
            <div class="trens-painel">
                <div class="trens-topo d-flex align-items-center justify-content-between">
                    <div class="input-group trens-busca">
                        <input type="text" class="form-control trens-input" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button class="btn trens-btn-busca" type="button" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                </div>

                <div class="trens-lista">
                    <div class="row trens-grid">
                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                    <button class="trens-card-acao"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal_monitoramento"
                                        aria-label="Detalhes do trem">

                                    <i class="bi bi-info-lg"></i>
                                    </button>   
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                    <button class="trens-card-acao"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal_monitoramento"
                                        aria-label="Detalhes do trem">

                                        <i class="bi bi-info-lg"></i>
                                    </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <strong>ID: 5823</strong>
                                        <button class="trens-card-acao"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_monitoramento"
                                                aria-label="Detalhes do trem">

                                            <i class="bi bi-info-lg"></i>
                                        </button>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                                <p>Nome:Nome do Trem</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modal_cadastro" tabindex="-1" aria-labelledby="label_modal_cadastro" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-ferroviario">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center fw-bold text-dark" id="label_modal_cadastro">Cadastro Ferroviário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-5 pt-4 pb-4">
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Nome">
                            </div>

                            <div class="row mb-3">
                                <div class="col pe-2">
                                    <input type="text" class="form-control" placeholder="Local de Partida">
                                </div>
                                <div class="col ps-2">
                                    <input type="text" class="form-control" placeholder="Local de Chegada">
                                </div>
                            </div>

                            <div class="d-flex justify-content-center gap-2 mb-4 mt-4">
                                <input type="checkbox" class="btn-check" id="btn-d" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-d">D</label>

                                <input type="checkbox" class="btn-check" id="btn-s1" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-s1">S</label>

                                <input type="checkbox" class="btn-check" id="btn-t" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-t">T</label>

                                <input type="checkbox" class="btn-check" id="btn-q1" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-q1">Q</label>

                                <input type="checkbox" class="btn-check" id="btn-q2" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-q2">Q</label>

                                <input type="checkbox" class="btn-check" id="btn-s2" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-s2">S</label>

                                <input type="checkbox" class="btn-check" id="btn-s3" autocomplete="off">
                                <label class="btn btn-outline-secondary rounded-circle btn_dia" for="btn-s3">S</label>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn_adicionar">Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="modal_monitoramento" tabindex="-1" aria-labelledby="label_modal_monitoramento" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">

            <!-- HEADER -->
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h4 class="modal-title w-100 text-center fw-bold">
                    Monitoramento em Tempo Real
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

                <!-- INFORMAÇÕES -->
                <div class="row text-center align-items-center">

                    <!-- Velocidade -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <span class="fw-bold fs-5">Velocidade:</span>
                        <span class="fs-5"> 999km/h</span>
                    </div>

                    <!-- Temperatura -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <span class="fw-bold fs-5">Temperatura:</span>
                        <span class="fs-5"> 99,99C°</span>
                    </div>

                    <!-- Status -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <span class="fw-bold fs-5">Status:</span>

                        <span class="badge rounded-pill bg-success fs-6 px-3 py-2">
                            Normal
                        </span>
                    </div>

                    <!-- Consumo -->
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
