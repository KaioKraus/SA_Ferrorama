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

                <li class="lista1" id="li1"><a href="dashboard.php" class="lista1">Dashboard</a></li><hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_relatorios.php" class="lista">Relatórios</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li><hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li><hr class="hr1 opacity-100">

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
            <div class="card_relatorios">
                <div class="barra_busca">
                    <div>
                        <input type="text" class="input_buscar" placeholder="Buscar por ID ou Tipo de falha">
                        <button class="btn_buscar">Buscar</button>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Tipo de falha</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ID</td>
                            <td>00/00/0000</td>
                            <td>Tipo de falha</td>
                            <td>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>00/00/0000</td>
                            <td>Tipo de falha</td>
                            <td>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>00/00/0000</td>
                            <td>Tipo de falha</td>
                            <td>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>00/00/0000</td>
                            <td>Tipo de falha</td>
                            <td>
                                <button class="btn_delete ms-4"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card_gerar_relatorios">
                <h5 class="card-title">Gerar Relatório</h5>
                <p class="card-text">Selecione o período e o tipo de erro para gerar o relatório.</p>

                <div class="form-relatorio-row">
                    <div class="form-relatorio-group">
                        <label for="data_inicio">Data início</label>
                        <input type="date" id="data_inicio" name="data_inicio" class="form-control input-relatorio">
                    </div>

                    <div class="form-relatorio-group">
                        <label for="data_fim">Data fim</label>
                        <input type="date" id="data_fim" name="data_fim" class="form-control input-relatorio">
                    </div>
                </div>

                <div class="form-relatorio-group form-relatorio-group-full">
                    <label for="tipo_erro">Tipo de erro</label>
                    <select id="tipo_erro" name="tipo_erro" class="form-select input-relatorio">
                        <option value="">Selecione</option>
                        <option value="falha_sensores">Falha de sensores</option>
                        <option value="falha_trens">Falha de trens</option>
                        <option value="falha_rotas">Falha de rotas</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>

                <div class="form-relatorio-actions">
                    <button class="btn btn_adicionar">Enviar</button>
                </div>
            </div>

        </section>


    </main>

    <script src="../script/validacao.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>