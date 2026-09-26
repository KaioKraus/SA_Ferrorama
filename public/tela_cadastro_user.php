<?php
require_once "../infra/conexao.php";

$sql = "SELECT 
            u.usuario_id,
            u.nome,
            u.email,
            u.cpf,
            c.nome AS cargo
        FROM usuarios u
        LEFT JOIN Cargos c ON u.cargo_id = c.id
        ORDER BY u.usuario_id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar usuários: " . mysqli_error($conexao));
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
                    <li class="lista" id="li1"><a href="tela_cadastro_user.php" class="lista">Usuários</a></li>
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
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">Editar</button></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo">
            <div class="card_usuarios">
                <div class="barra_busca">
                    <div class="input-group dashboard-busca">
                        <input type="text" id="input_busca_usuario" class="form-control dashboard-input" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button class="btn dashboard-btn-busca" id="btn_busca_usuario" type="button" aria-label="Buscar funcionário">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <?php if ($usuarioEhAdministrador): ?>
                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                    <?php endif; ?>
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
<tbody id="tbody_usuarios">

    <?php if (mysqli_num_rows($resultado) > 0): ?>

        <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>

            <?php
            $cargo = $usuario['cargo'] ?? 'Sem permissão';

            if (stripos($cargo, 'admin') !== false) {
                $classe_cargo = 'badge_admin';
            } else {
                $classe_cargo = 'badge_funcionario';
            }

            $cpf = $usuario['cpf'];

            if (strlen($cpf) === 11) {
                $cpf = substr($cpf, 0, 3) . '.' .
                       substr($cpf, 3, 3) . '.' .
                       substr($cpf, 6, 3) . '-' .
                       substr($cpf, 9, 2);
            }
            ?>

            <tr data-user-row data-id="<?= htmlspecialchars($usuario['usuario_id'], ENT_QUOTES, 'UTF-8') ?>">

                <td>
                    <?= htmlspecialchars($usuario['nome']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($usuario['email']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($cpf) ?>
                </td>

                <td>
                    <span class="<?= $classe_cargo ?>">
                        <?= htmlspecialchars($cargo) ?>
                    </span>
                </td>

                <td>
                    <button class="btn_edit">
                        <i class="bi bi-pencil-square"></i>
                        Edit
                    </button>

                    <button class="btn_delete ms-4">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>

            </tr>

        <?php endwhile; ?>

    <?php else: ?>

        <tr>
            <td colspan="5" class="text-center">
                Nenhum usuário cadastrado.
            </td>
        </tr>

    <?php endif; ?>

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
                            <input type="hidden" id="input_user_id" name="id" value="">
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
                                        <input required="" autocomplete="off" type="password" id="input_senha" class="form-control pe-5" minlength="8" />
                                        <label for="input_senha">Senha</label>
                                        <button type="button" id="toggleSenhaCadastro" class="btn position-absolute top-50 end-0 translate-middle-y border-0" style="background: transparent; color: #585858; z-index: 5;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col ps-2">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="text" id="input_telefone" class="form-control" />
                                        <label for="input_telefone">Telefone</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col">
                                    <div class="input-field">
                                        <input required="" autocomplete="off" type="email" id="input_email" class="form-control" />
                                        <label for="input_email">Email</label>
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

    <div class="modal fade" id="modal_confirm_delete" tabindex="-1" aria-labelledby="label_modal_confirm_delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label_modal_confirm_delete">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal_confirm_text">Deseja realmente excluir este usuário?</p>
                </div>
                <div class="modal-footer">
                    <form id="form_delete_user" method="post" action="excluir_usuario.php">
                        <input type="hidden" name="id" id="delete_user_id" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../script/validacao.js"></script>
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
    <script src="../script/nav.js"></script>
    <script src="../script/usuarios.js"></script>
</body>

</html>