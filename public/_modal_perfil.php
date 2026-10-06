<?php
// Modal de edição de perfil reutilizável
session_start();
$usuarioNome = $_SESSION['usuario_nome'] ?? '';
$usuarioEmail = $_SESSION['usuario_email'] ?? '';
$usuarioId = $_SESSION['usuario_id'] ?? 0;
?>


<!-- Modal: Editar Perfil (incluir em todas as páginas) -->
<div class="modal fade" id="modal_editar_perfil" tabindex="-1" aria-labelledby="modal_editar_perfil_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <form method="POST" action="editar_usuario.php" id="form_editar_perfil">
                <div class="perfil_modal_body">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioId, ENT_QUOTES) ?>">
                    <input type="hidden" name="nome" value="<?= htmlspecialchars($usuarioNome ?: 'Administrador', ENT_QUOTES) ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($usuarioEmail ?: 'admin@teste.com', ENT_QUOTES) ?>">

                    <div class="perfil_header_row">
                        <div class="perfil_card">
                            <h5 class="perfil_card_title" id="modal_editar_perfil_label">Perfil</h5>

                            <div class="perfil_main">
                                <div class="perfil_avatar">FOTO</div>

                                <div class="perfil_data">
                                    <div class="perfil_row"><span class="perfil_label">Nome:</span> <?= htmlspecialchars($usuarioNome ?: 'Administrador', ENT_QUOTES) ?></div>
                                    <div class="perfil_row"><span class="perfil_label">Email:</span> <?= htmlspecialchars($usuarioEmail ?: 'admin@teste.com', ENT_QUOTES) ?></div>
                                    <div class="perfil_row"><span class="perfil_label">Permissão:</span> administrador</div>
                                    <div class="perfil_row"><span class="perfil_label">Telefone:</span> (99) 99999-9999</div>
                                    <div class="perfil_row"><span class="perfil_label">CPF:</span> 999.999.999-99</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="perfil_editar">
                        <div class="perfil_editar_title">Editar Senha</div>

                        <div class="perfil_input_wrap">
                            <input type="password" class="perfil_input" id="senha" name="senha" placeholder="Nova Senha">
                        </div>

                        <div class="perfil_input_wrap">
                            <input type="password" class="perfil_input" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha">
                        </div>
                    </div>

                    <div class="perfil_actions">
                        <button type="button" class="perfil_btn perfil_btn_cancelar" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="perfil_btn perfil_btn_salvar">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form_editar_perfil');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var senha = document.getElementById('senha').value || '';
        var confirmar = document.getElementById('confirmar_senha').value || '';

        if (senha || confirmar) {
            if (senha.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres.');
                return;
            }
            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem.');
                return;
            }

            var confirmarSenha = confirm('Deseja realmente alterar sua senha?');
            if (!confirmarSenha) {
                e.preventDefault();
                return;
            }
        }
    });
});
</script>

<?php if (!empty($_SESSION['mensagem_acesso'])): ?>
    <!-- Modal pequeno de notificação (topo central) -->
    <div class="modal fade" id="modalFlash" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" style="transform: translateY(-40%);">
            <div class="modal-content text-center">
                <div class="modal-body py-2">
                    <?= htmlspecialchars($_SESSION['mensagem_acesso']) ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var flashModalEl = document.getElementById('modalFlash');
        if (!flashModalEl) return;
        var modal = new bootstrap.Modal(flashModalEl, {keyboard: false, backdrop: false});
        modal.show();
        setTimeout(function () { modal.hide(); }, 2500);
    });
    </script>
    <?php unset($_SESSION['mensagem_acesso']); ?>
<?php endif; ?>
