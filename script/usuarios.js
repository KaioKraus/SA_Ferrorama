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

    // Editar usuário: abre modal de cadastro preenchido com dados da linha
    document.addEventListener('click', function (event) {
        const btn = event.target.closest && event.target.closest('.btn_edit');
        if (!btn) return;

        const row = btn.closest('tr');
        if (!row) return;

        const cells = row.querySelectorAll('td');
        const nome = cells[0] ? cells[0].textContent.trim() : '';
        const email = cells[1] ? cells[1].textContent.trim() : '';
        const cpf = cells[2] ? cells[2].textContent.trim() : '';
        const permissaoText = cells[3] ? cells[3].textContent.trim().toLowerCase() : '';

        // Preenche os inputs do modal
        const inputId = document.getElementById('input_user_id');
        const inputNome = document.getElementById('input_nome');
        const inputCpf = document.getElementById('input_cpf');
        const inputEmail = document.getElementById('input_email');
        const inputTelefone = document.getElementById('input_telefone');
        const inputSenha = document.getElementById('input_senha');
        const radioAdmin = document.getElementById('admin');
        const radioFunc = document.getElementById('funcionario');

        if (inputNome) inputNome.value = nome;
        if (inputCpf) inputCpf.value = cpf;
        if (inputEmail) inputEmail.value = email;
        if (inputTelefone) inputTelefone.value = '';
        if (inputSenha) inputSenha.value = '';

        // Tenta identificar permissão pelo texto da célula
        if (permissaoText.includes('admin')) {
            if (radioAdmin) radioAdmin.checked = true;
        } else {
            if (radioFunc) radioFunc.checked = true;
        }

        // Se o botão ou a linha tiver dataset.id, usa como id
        const id = (btn.dataset && btn.dataset.id) || (row.dataset && row.dataset.id) || '';
        if (inputId) inputId.value = id;

        // Ajusta título e botão do modal
        const modalEl = document.getElementById('modal_cadastro');
        const modalTitle = document.getElementById('label_modal_cadastro');
        const submitBtn = document.querySelector('#form_cadastro_user .btn_adicionar');
        if (modalTitle) modalTitle.textContent = 'Editar Usuário';
        if (submitBtn) submitBtn.textContent = 'Salvar';
        // senha não obrigatória em edição
        if (inputSenha) inputSenha.required = false;

        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });

    // Quando abrir o modal pelo botão "Adicionar", resetar para modo cadastro
    const addButtons = document.querySelectorAll('[data-bs-target="#modal_cadastro"]');
    addButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const modalTitle = document.getElementById('label_modal_cadastro');
            const submitBtn = document.querySelector('#form_cadastro_user .btn_adicionar');
            const form = document.getElementById('form_cadastro_user');
            if (modalTitle) modalTitle.textContent = 'Cadastro de Usuário';
            if (submitBtn) submitBtn.textContent = 'Adicionar';
            if (form) form.reset();
            // senha obrigatória no cadastro
            const senhaInput = document.getElementById('input_senha');
            if (senhaInput) senhaInput.required = true;
            const inputId = document.getElementById('input_user_id');
            if (inputId) inputId.value = '';
            const mensagemDiv = document.getElementById('mensagem_cadastro');
            if (mensagemDiv) mensagemDiv.innerHTML = '';
        });
    });

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
            // inclui id quando em modo edição (campo hidden)
            const userId = document.getElementById('input_user_id') ? document.getElementById('input_user_id').value : '';
            if (userId) formData.append('id', userId);
            formData.append('telefone', telefone);
            formData.append('senha', senha);
            formData.append('permissao', permissao);

            // Envia via fetch
            fetch('cadastrar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
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
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro do servidor: <pre style="white-space:pre-wrap">' + text + '</pre></div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao processar requisição</div>';
            });
        });
    }
});
