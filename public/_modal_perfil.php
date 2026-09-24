<?php
// Modal de edição de perfil reutilizável
session_start();
$usuarioNome = $_SESSION['usuario_nome'] ?? '';
$usuarioEmail = $_SESSION['usuario_email'] ?? '';
$usuarioId = $_SESSION['usuario_id'] ?? 0;
?>

<!-- Modal: Editar Perfil (incluir em todas as páginas) -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="editar_usuario.php" id="formEditarPerfil">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPerfilLabel">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioId, ENT_QUOTES) ?>">

                    <div class="mb-3">
                        <label for="nome_display" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome_display" value="<?= htmlspecialchars($usuarioNome, ENT_QUOTES) ?>" disabled>
                        <input type="hidden" name="nome" value="<?= htmlspecialchars($usuarioNome, ENT_QUOTES) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email_display" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email_display" value="<?= htmlspecialchars($usuarioEmail, ENT_QUOTES) ?>" disabled>
                        <input type="hidden" name="email" value="<?= htmlspecialchars($usuarioEmail, ENT_QUOTES) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Nova senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite uma nova senha">
                    </div>

                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label">Confirmar senha</label>
                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('formEditarPerfil');
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
