document.addEventListener('DOMContentLoaded', function () {
    // Modal de confirmação de exclusão
    const modalEl = document.getElementById('modal_confirm_delete');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        document.addEventListener('click', function (event) {
            const btn = event.target.closest && event.target.closest('.btn_delete');
            if (!btn) return;

            const row = btn.closest('tr');
            const id = (btn.dataset && btn.dataset.id) || (row && row.dataset && row.dataset.id) || '';
            const name = (btn.dataset && btn.dataset.name) ||
                (row && row.dataset && row.dataset.name) ||
                (row && row.querySelector('td') ? row.querySelector('td').textContent.trim() : '');

            const input = document.getElementById('delete_user_id');
            if (input) input.value = id;

            const textEl = document.getElementById('modal_confirm_text');
            if (textEl) {
                if (name) {
                    textEl.textContent = 'Deseja realmente excluir o usuário ' + name + '?';
                } else {
                    textEl.textContent = 'Deseja realmente excluir este usuário?';
                }
            }

            modal.show();
        });
    }

    // Formulário de cadastro de usuário
    const formCadastro = document.getElementById('form_cadastro_user');
    if (formCadastro) {
        formCadastro.addEventListener('submit', function (e) {
            e.preventDefault();

            const mensagemDiv = document.getElementById('mensagem_cadastro');
            mensagemDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

            // Coleta os dados do formulário
            const nome = document.getElementById('input_nome').value.trim();
            const cpf = document.getElementById('input_cpf').value.trim();
            const email = document.getElementById('input_email').value.trim();
            const telefone = document.getElementById('input_telefone').value.trim();
            const senha = document.getElementById('input_senha').value.trim();
            
            // Verifica qual permissão está selecionada
            const permissao = document.getElementById('admin').checked ? 'admin' : 'funcionario';

            // Cria o FormData
            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('cpf', cpf);
            formData.append('email', email);
            formData.append('telefone', telefone);
            formData.append('senha', senha);
            formData.append('permissao', permissao);

            // Envia via fetch
            fetch('cadastrar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mensagemDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    formCadastro.reset();
                    
                    // Fecha o modal após 2 segundos e recarrega a página
                    setTimeout(() => {
                        const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cadastro'));
                        if (modalCadastro) {
                            modalCadastro.hide();
                        }
                        location.reload();
                    }, 2000);
                } else {
                    mensagemDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao processar requisição</div>';
            });
        });
    }
});
