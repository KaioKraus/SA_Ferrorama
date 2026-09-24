<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: tela_login.php');
    exit;
}
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

                <li class="lista" id="li1"><a href="dashboard.php" class="lista">Dashboard</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_relatorios.php" class="lista1">Relatórios</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista1" id="li1"><a href="tela_trens.php" class="lista1">Trens</a></li>
            </ul>

            <div class="dropdown">
                <div class="box_usuario dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <div class="h3">
                        <h3 class="h3_1">Bem vindo Fulano de Tal</h3>
                        <h3 class="h3_2">Matricula: 123456</h3>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo">
            <div class="dashboard-painel">
                <div class="dashboard-topo d-flex align-items-center justify-content-between">
                </div>

                <div>
                    <div class="dashboard-graficos d-flex justify-content-between">
                        <div class="grafico-velocidade">

                        </div>
                        <div class="grafico-temperatura">

                        </div>
                        <div class="grafico-consumo">

                        </div>
                        <div class="status">

                        </div>

                    </div>

                    <div class="trilho">
                        <img src="../assets/img/trilho2.png" alt="trilho" class="trilho-img img-fluid">
                    </div>
                </div>

            </div>
        </section>


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
                        <li class="mb-2"><a href="tela_cadastro_user.php" class="text-decoration-none">Usuários</a></li>
                        <li class="mb-2"><a href="tela_rotas.php" class="text-decoration-none">Rotas</a></li>
                        <li class="mb-2"><a href="tela_sensores.php" class="text-decoration-none">Sensores</a></li>
                        <li class="mb-2"><a href="tela_login.php" class="text-decoration-none">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/nav.js"></script>
</body>

</html>