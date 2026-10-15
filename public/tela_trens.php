<?php
session_start();
include __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$usuarios = [];
$resultadoUsuarios = mysqli_query($conexao, "SELECT usuario_id, nome FROM usuarios ORDER BY nome ASC");
if ($resultadoUsuarios) {
    while ($usuario = mysqli_fetch_assoc($resultadoUsuarios)) {
        $usuarios[] = $usuario;
    }
}
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

<?php if (empty($usuarioEhAdministrador) || !$usuarioEhAdministrador): ?>
<style>
    .btn_delete { display: none !important; }
</style>
<?php endif; ?>

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

                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">

                <li class="lista" id="li1"><a href="tela_trens.php" class="lista">Trens</a></li>
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

        <section class="trens-conteudo">
            <div class="trens-painel">
                <div class="trens-topo d-flex align-items-center justify-content-between">
                    <div class="input-group trens-busca">
                        <input type="text" class="form-control trens-input" placeholder="Buscar por ID ou Nome" aria-label="Buscar por ID ou Nome">
                        <button class="btn trens-btn-busca" type="button" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <?php if ($usuarioEhAdministrador): ?>
                    <button class="btn trens-btn-add" type="button" data-bs-toggle="modal" data-bs-target="#modal_cadastro">Adicionar</button>
                    <?php endif; ?>
                </div>

                <div class="trens-lista">
                    <div class="row trens-grid">
                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5823" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5823</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status inativo">Inativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5824" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5824</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5825" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5825</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status inativo">Inativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5826" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5826</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5827" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5827</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status inativo">Inativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5828" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5828</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5829" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5829</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status inativo">Inativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5830" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5830</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5831" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5831</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status inativo">Inativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5832" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5832</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>

                        <div class="col">
                            <article class="trens-card-trem">
                                <div class="trens-card-cabecalho">
                                    <div class="trens-card-header-row">
                                        <span class="trens-card-status ativo">Ativo</span>
                                        <div class="trens-card-acoes">
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <?php if ($usuarioEhAdministrador): ?>
                                            <button class="btn_edit" type="button" aria-label="Editar trem">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn_delete" type="button" data-id="5833" aria-label="Excluir trem">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Detalhes do trem">
                                                <i class="bi bi-info-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="trens-card-id">
                                        <strong>ID: 5833</strong>
                                    </div>
                                    <p class="trens-card-nome">Nome:Nome do Trem</p>
                                </div>
                                <img src="../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modal_cadastro" tabindex="-1" aria-labelledby="label_modal_cadastro" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-cadastro-trem">
                <div class="modal-content modal-ferroviario modal-cadastro-ferroviario">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center fw-bold text-dark" id="label_modal_cadastro">Cadastro Ferroviário</h5>
                        <button type="button" class="btn-close modal-fechar" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-2 pb-4">
                        <form id="form_cadastro_trem" method="POST">
                            <input type="hidden" name="nome_trem" value="Trem sem nome">
                            <input type="hidden" name="partida" value="Estação Norte">
                            <input type="hidden" name="chegada" value="Estação Sul">
                            <input type="hidden" name="horario_partida" value="06:00">
                            <input type="hidden" name="horario_chegada" value="08:00">
                            <input type="hidden" name="dias_semana[]" value="Segunda">
                            <input type="hidden" name="dias_semana[]" value="Quarta">
                            <input type="hidden" name="dias_semana[]" value="Sexta">

                            <div class="modal-field-wrapper">
                                <label for="input_id_trem" class="modal-field-label">ID</label>
                                <input type="text" id="input_id_trem" name="id_trem" class="form-control modal-input" required>
                            </div>

                            <div class="modal-field-wrapper">
                                <label for="input_nome_trem" class="modal-field-label">Nome</label>
                                <input type="text" id="input_nome_trem" name="nome_trem_visivel" class="form-control modal-input" required>
                            </div>

                            <div class="modal-field-wrapper mb-4">
                                <label for="input_responsavel_trem" class="modal-field-label">Responsável</label>
                                <select id="input_responsavel_trem" name="responsavel_trem" class="form-select modal-input modal-select" required>
                                    <option value="" selected disabled></option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= (int) $usuario['usuario_id']; ?>"><?= htmlspecialchars($usuario['nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="text-center mt-2">
                                <button type="submit" class="btn btn_adicionar modal-btn-adicionar">Adicionar</button>
                            </div>
                            <div id="mensagem_cadastro_trem" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_monitoramento" tabindex="-1" aria-labelledby="label_modal_monitoramento" aria-hidden="true" data-is-admin="<?= $usuarioEhAdministrador ? '1' : '0'; ?>">
            <div class="modal-dialog modal-dialog-centered modal-dados-trem">
                <div class="modal-content modal-dados-trem-content">
                    <div class="modal-header modal-dados-trem-header">
                        <h4 class="modal-title w-100 text-center fw-bold" id="label_modal_monitoramento">
                            Dados do Trem
                        </h4>

                        <button type="button"
                            class="btn-close modal-dados-trem-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        </button>
                    </div>

                    <div class="modal-body modal-dados-trem-body">
                        <form id="form_editar_trem" method="POST" action="editar_trem.php">
                            <input type="hidden" name="trem_id" id="modal_trem_id_hidden" value="">

                            <div class="dados-trem-grid dados-trem-grid-3campos">
                                <div class="dados-trem-field dados-trem-field-full">
                                    <label>ID do Trem</label>
                                    <input id="modal_trem_id" type="text" readonly>
                                </div>

                                <div class="dados-trem-field dados-trem-field-full">
                                    <label>Nome</label>
                                    <input id="modal_trem_nome" name="nome_trem" type="text" <?= $usuarioEhAdministrador ? '' : 'readonly'; ?>>
                                </div>

                                <div class="dados-trem-field dados-trem-field-full">
                                    <label>Responsável</label>
                                    <select id="modal_trem_responsavel" name="responsavel_id" class="form-select" <?= $usuarioEhAdministrador ? '' : 'disabled'; ?> aria-disabled="<?= $usuarioEhAdministrador ? 'false' : 'true'; ?>">
                                        <option value="">Selecione o responsável</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= (int) $usuario['usuario_id']; ?>"><?= htmlspecialchars($usuario['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <?php if ($usuarioEhAdministrador): ?>
                                <div class="text-center mt-4">
                                    <button type="submit" id="btn_salvar_trem" class="btn btn_adicionar modal-btn-adicionar">Salvar alterações</button>
                                </div>
                            <?php endif; ?>

                            <div id="mensagem_editar_trem" class="mt-3"></div>
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
                    <p id="modal_confirm_text">Deseja realmente excluir este trem?</p>
                </div>
                <div class="modal-footer">
                    <form id="form_delete_train" method="post" action="excluir_trem.php">
                        <input type="hidden" name="id" id="delete_train_id" value="">
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
    <script src="../script/nav.js"></script>
    <script src="../script/trens.js"></script>
</body>

</html>