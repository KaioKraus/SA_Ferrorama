<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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

                <li class="lista1" id="li1"><a href="dashboard.html" class="lista1">Dashboard</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_relatorios.html" class="lista1">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_cadastro_user.html" class="lista1">Usuários</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_rotas.html" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.html" class="lista">Sensores</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_trens.html" class="lista1">Trens</a></li>
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


         <section class="conteudo">
            <div class="card_usuarios">
                <div class="barra_busca">
                    <div class="input-group dashboard-busca">
                        <input type="text" id="input_busca_sensor" class="form-control dashboard-input" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button class="btn dashboard-btn-busca" id="btn_busca_sensor" type="button" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro_sensor">Adicionar</button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Localização</th>
                            <th>Tipo de dado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_sensores">
                        <tr>
                            <td>10001</td>
                            <td>Sensor de Temperatura A1</td>
                            <td>Trem - ID 5823</td>
                            <td>Temperatura</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>10002</td>
                            <td>Sensor de Vibração B2</td>
                            <td>Rota 1 (Estação Norte → Estação Sul)</td>
                            <td>Vibração</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>10003</td>
                            <td>Sensor de Velocidade C3</td>
                            <td>Trem - ID 5823</td>
                            <td>Velocidade</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                           <td>10004</td>
                            <td>Sensor de Pressão D4</td>
                            <td>Rota 2 (Estação Norte → Estação Sul)</td>
                            <td>Pressão</td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

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

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn_adicionar px-5">Adicionar</button>
                                <div id="mensagem_cadastro_sensor" class="mt-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </main>

    <script src="../script/validacao.js"></script>
    <script src="../script/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>