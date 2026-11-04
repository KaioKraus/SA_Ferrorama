document.addEventListener('DOMContentLoaded', function () {
    function atualizarLabelFlutuante(campo) {
        if (!campo) return;
        const valor = campo.value ?? '';
        campo.classList.toggle('has-value', String(valor).trim() !== '');
    }

    document.querySelectorAll('.input-field input, .input-field select, .input-field textarea').forEach(campo => {
        atualizarLabelFlutuante(campo);
        campo.addEventListener('input', () => atualizarLabelFlutuante(campo));
        campo.addEventListener('change', () => atualizarLabelFlutuante(campo));
    });

    const inputBuscaUsuario = document.getElementById('input_busca_usuario');
    const btnBuscaUsuario = document.getElementById('btn_busca_usuario');
    const tbodyUsuarios = document.getElementById('tbody_usuarios');
    const inputCep = document.getElementById('input_cep');
    const inputLogradouro = document.getElementById('input_logradouro');
    const inputBairro = document.getElementById('input_bairro');
    const inputCidade = document.getElementById('input_cidade');
    const inputEstado = document.getElementById('input_estado');
    const mensagemCep = document.getElementById('mensagem_cep');

    function formatarCep(valor) {
        const apenasDigitos = String(valor || '').replace(/\D/g, '').slice(0, 8);
        if (apenasDigitos.length <= 5) {
            return apenasDigitos;
        }
        return `${apenasDigitos.slice(0, 5)}-${apenasDigitos.slice(5)}`;
    }

    function atualizarStatusCep(texto, tipo) {
        if (!mensagemCep) return;
        mensagemCep.textContent = texto;
        mensagemCep.className = 'small mt-n2 mb-3';

        if (tipo === 'erro') {
            mensagemCep.classList.add('text-danger');
        } else if (tipo === 'sucesso') {
            mensagemCep.classList.add('text-success');
        } else if (tipo === 'info') {
            mensagemCep.classList.add('text-info');
        } else {
            mensagemCep.classList.add('text-muted');
        }
    }

    function limparCamposEndereco() {
        if (inputLogradouro) inputLogradouro.value = '';
        if (inputBairro) inputBairro.value = '';
        if (inputCidade) inputCidade.value = '';
        if (inputEstado) inputEstado.value = '';
        atualizarLabelFlutuante(inputLogradouro);
        atualizarLabelFlutuante(inputBairro);
        atualizarLabelFlutuante(inputCidade);
        atualizarLabelFlutuante(inputEstado);
    }

    function consultarCep() {
        if (!inputCep) return;

        const cep = String(inputCep.value || '').replace(/\D/g, '');
        if (!cep) {
            limparCamposEndereco();
            atualizarStatusCep('', 'idle');
            return;
        }

        if (cep.length !== 8) {
            limparCamposEndereco();
            return;
        }

        

        fetch(`https://brasilapi.com.br/api/cep/v1/${cep}`)
            .then(async response => {
                const texto = await response.text();
                if (!response.ok) {
                    let mensagem = 'CEP não encontrado ou indisponível no momento.';
                    try {
                        const dados = JSON.parse(texto);
                        if (dados && dados.message) {
                            mensagem = dados.message;
                        }
                    } catch (erro) {
                        console.warn('Resposta inesperada da BrasilAPI:', texto);
                    }
                    throw new Error(mensagem);
                }

                let dados;
                try {
                    dados = JSON.parse(texto);
                } catch (erro) {
                    throw new Error('Não foi possível processar a resposta da BrasilAPI.');
                }

                if (inputLogradouro) {
                    inputLogradouro.value = dados.street || '';
                    atualizarLabelFlutuante(inputLogradouro);
                }
                if (inputBairro) {
                    inputBairro.value = dados.neighborhood || '';
                    atualizarLabelFlutuante(inputBairro);
                }
                if (inputCidade) {
                    inputCidade.value = dados.city || '';
                    atualizarLabelFlutuante(inputCidade);
                }
                if (inputEstado) {
                    inputEstado.value = (dados.state || '').toUpperCase();
                    atualizarLabelFlutuante(inputEstado);
                }

                
            })
            .catch(error => {
                limparCamposEndereco();
                atualizarStatusCep(error && error.message ? error.message : 'Não foi possível consultar o CEP.', 'erro');
            });
    }

    if (inputCep) {
        inputCep.addEventListener('input', function () {
            this.value = formatarCep(this.value);
            if (this.value.length < 9) {
                atualizarStatusCep('', 'idle');
            }
        });
        inputCep.addEventListener('blur', consultarCep);
    }

    if (inputBuscaUsuario && tbodyUsuarios) {
        const linhasUsuarios = Array.from(tbodyUsuarios.querySelectorAll('tr[data-user-row]'));

        function filtrarUsuarios() {
            const termo = inputBuscaUsuario.value.trim().toLowerCase();
            let algumVisivel = false;

            linhasUsuarios.forEach(linha => {
                const idUsuario = (linha.dataset.id || '').toLowerCase();
                const nomeUsuario = linha.children[0].textContent.trim().toLowerCase();
                const corresponde = termo === '' || idUsuario.includes(termo) || nomeUsuario.includes(termo);

                linha.hidden = !corresponde;
                if (corresponde) algumVisivel = true;
            });

            let linhaVazia = tbodyUsuarios.querySelector('#linha_usuario_vazia');

            if (linhasUsuarios.length > 0 && !algumVisivel) {
                if (!linhaVazia) {
                    linhaVazia = document.createElement('tr');
                    linhaVazia.id = 'linha_usuario_vazia';
                    linhaVazia.innerHTML = '<td colspan="5" class="text-center text-muted">Nenhum funcionário encontrado.</td>';
                    tbodyUsuarios.appendChild(linhaVazia);
                }
            } else if (linhaVazia) {
                linhaVazia.remove();
            }
        }

        if (btnBuscaUsuario) {
            btnBuscaUsuario.addEventListener('click', filtrarUsuarios);
        }
    }

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
        const telefone = cells[3] ? cells[3].textContent.trim() : '';
        const permissaoText = cells[4] ? cells[4].textContent.trim().toLowerCase() : '';

        // Preenche os inputs do modal
        const inputId = document.getElementById('input_user_id');
        const inputCpf = document.getElementById('input_cpf');
        const inputEmail = document.getElementById('input_email');
        const inputTelefone = document.getElementById('input_telefone');
        const inputNome = document.getElementById('input_nome');
        const inputSenha = document.getElementById('input_senha');
        const inputCep = document.getElementById('input_cep');
        const inputLogradouro = document.getElementById('input_logradouro');
        const inputBairro = document.getElementById('input_bairro');
        const inputCidade = document.getElementById('input_cidade');
        const inputEstado = document.getElementById('input_estado');
        const radioAdmin = document.getElementById('admin');
        const radioFunc = document.getElementById('funcionario');

        if (inputCpf) {
            inputCpf.value = cpf;
            atualizarLabelFlutuante(inputCpf);
        }
        if (inputEmail) {
            inputEmail.value = email;
            atualizarLabelFlutuante(inputEmail);
        }
        if (inputTelefone) {
            inputTelefone.value = telefone;
            atualizarLabelFlutuante(inputTelefone);
        }
        if (inputNome) {
            inputNome.value = nome;
            atualizarLabelFlutuante(inputNome);
        }
        if (inputCep) {
            const cep = (row.dataset.cep || '').trim();
            inputCep.value = cep ? formatarCep(cep) : '';
            atualizarLabelFlutuante(inputCep);
        }
        if (inputLogradouro) {
            const logradouro = (row.dataset.logradouro || '').trim();
            inputLogradouro.value = logradouro;
            atualizarLabelFlutuante(inputLogradouro);
        }
        if (inputBairro) {
            const bairro = (row.dataset.bairro || '').trim();
            inputBairro.value = bairro;
            atualizarLabelFlutuante(inputBairro);
        }
        if (inputCidade) {
            const cidade = (row.dataset.cidade || '').trim();
            inputCidade.value = cidade;
            atualizarLabelFlutuante(inputCidade);
        }
        if (inputEstado) {
            const estado = (row.dataset.estado || '').trim();
            inputEstado.value = estado.toUpperCase();
            atualizarLabelFlutuante(inputEstado);
        }
        if (inputSenha) {
            inputSenha.value = '';
            inputSenha.required = false;
            atualizarLabelFlutuante(inputSenha);
        }

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
        if (modalTitle) modalTitle.textContent = 'Edição de Usuário';
        if (submitBtn) submitBtn.textContent = 'Salvar';

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
            limparCamposEndereco();
            if (mensagemCep) {
                mensagemCep.textContent = '';
                mensagemCep.className = 'small mt-n2 mb-3 text-muted';
            }
            document.querySelectorAll('#form_cadastro_user .input-field input, #form_cadastro_user .input-field select, #form_cadastro_user .input-field textarea').forEach(campo => atualizarLabelFlutuante(campo));
            
            // Senha obrigatória no cadastro
            const senhaInput = document.getElementById('input_senha');
            if (senhaInput) {
                senhaInput.value = '';
                senhaInput.required = true;
                atualizarLabelFlutuante(senhaInput);
            }
            
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
            const camposObrigatorios = formCadastro.querySelectorAll('input[required], textarea[required], select[required]');
            const campoVazio = Array.from(camposObrigatorios).find(campo => String(campo.value).trim() === '');

            if (campoVazio) {
                mensagemDiv.innerHTML = '';
                campoVazio.focus();
                campoVazio.reportValidity();
                return;
            }

            mensagemDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

            const nome = document.getElementById('input_nome').value.trim();
            const cpf = document.getElementById('input_cpf').value.trim();
            const email = document.getElementById('input_email').value.trim();
            const telefone = document.getElementById('input_telefone').value.trim();
            const cep = document.getElementById('input_cep') ? document.getElementById('input_cep').value.trim() : '';
            const logradouro = document.getElementById('input_logradouro') ? document.getElementById('input_logradouro').value.trim() : '';
            const bairro = document.getElementById('input_bairro') ? document.getElementById('input_bairro').value.trim() : '';
            const cidade = document.getElementById('input_cidade') ? document.getElementById('input_cidade').value.trim() : '';
            const estado = document.getElementById('input_estado') ? document.getElementById('input_estado').value.trim() : '';
            const senha = document.getElementById('input_senha').value.trim();
            const permissao = document.getElementById('admin').checked ? 'admin' : 'funcionario';

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('cpf', cpf);
            formData.append('email', email);
            formData.append('telefone', telefone);
            formData.append('cep', cep);
            formData.append('logradouro', logradouro);
            formData.append('bairro', bairro);
            formData.append('cidade', cidade);
            formData.append('estado', estado);
            formData.append('permissao', permissao);
            
            const userId = document.getElementById('input_user_id').value;
            if (userId) {
                formData.append('id', userId);
                if (senha) {
                    formData.append('senha', senha);
                } else {
                    formData.append('senha', '');
                }
            } else {
                formData.append('senha', senha);
            }

            fetch('cadastrar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        if (data.reload) {
                            // Fecha o modal e recarrega a página
                            const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cadastro'));
                            if (modalCadastro) {
                                modalCadastro.hide();
                            }
                            location.reload();
                        } else {
                            mensagemDiv.innerHTML = '<div class="alert alert-success">' + (data.message || 'Operação realizada com sucesso') + '</div>';
                            formCadastro.reset();
                            setTimeout(() => {
                                const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cadastro'));
                                if (modalCadastro) {
                                    modalCadastro.hide();
                                }
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro do servidor. Verifique os dados e tente novamente.</div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao processar requisição</div>';
            });
        });
    }
});
