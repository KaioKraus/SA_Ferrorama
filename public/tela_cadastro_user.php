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

                <li class="lista" id="li1"><a href="tela_cadastro_user.html" class="lista">Usuários</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_rotas.html" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.html" class="lista1">Sensores</a></li><hr class="hr1 opacity-100">

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
                    <div>
                        <input type="text" class="input_buscar" placeholder="Buscar por ID ou Nome">
                        <button class="btn_buscar">Buscar</button>
                    </div>
                    <button class="btn_add" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>CPF</th>
                            <th>Permissão</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nome do usuário</td>
                            <td>usuario@gmail.com</td>
                            <td>999.999.999-99</td>
                            <td><span class="badge_admin">Administrador</span></td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Nome do usuário</td>
                            <td>usuario@gmail.com</td>
                            <td>999.999.999-99</td>
                            <td><span class="badge_funcionario">Funcionário</span></td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Nome do usuário</td>
                            <td>usuario@gmail.com</td>
                            <td>999.999.999-99</td>
                            <td><span class="badge_funcionario">Funcionário</span></td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Nome do usuário</td>
                            <td>usuario@gmail.com</td>
                            <td>999.999.999-99</td>
                            <td><span class="badge_funcionario">Funcionário</span></td>
                            <td>
                                <button class="btn_edit"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="modal fade" id="modal_cadastro" tabindex="-1" aria-labelledby="label_modal_cadastro" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center text-dark" id="label_modal_cadastro">Cadastro de Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="position: absolute; right: 20px; top: 20px;"></button>
                    </div>
                    <div class="modal-body px-5 pt-4 pb-4">
                        <form class="w-100" id="form_cadastro_user">
                            <div class="row mb-4">
                                <div class="col pe-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_nome" class="form-control" />
                                        <label for="input_nome">Nome</label>
                                    </div>
                                </div>
                                <div class="col ps-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_cpf" class="form-control" />
                                        <label for="input_cpf">CPF</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col pe-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="email" id="input_email" class="form-control" />
                                        <label for="input_email">Email</label>
                                    </div>
                                </div>
                                <div class="col ps-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_matricula" class="form-control" />
                                        <label for="input_matricula">Matrícula</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col pe-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_telefone" class="form-control" />
                                        <label for="input_telefone">Telefone</label>
                                    </div>
                                </div>
                                <div class="col ps-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="password" id="input_senha" class="form-control pe-5" minlength="8" />
                                        <label for="input_senha">Senha</label>
                                        <button type="button" id="toggleSenhaCadastro" class="btn position-absolute top-50 end-0 translate-middle-y border-0" style="background: transparent; color: #585858; z-index: 5;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mb-5 mt-4">
                                <div class="toggle-container">
                                    <input type="radio" name="role" id="admin" checked>
                                    <label for="admin">Administrador</label>

                                    <input type="radio" name="role" id="funcionario">
                                    <label for="funcionario">Funcionário</label>

                                    <div class="slider"></div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn_adicionar px-5">Adicionar</button>
                                <div id="mensagem_cadastro" class="mt-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="../script/validacao.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>