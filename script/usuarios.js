// Usuários: filtros, consulta externa de CEP, cadastro, edição e exclusão.
// Este arquivo separa a API externa de CEP da comunicação interna com o PHP.

// Aguarda o carregamento completo da página antes de acessar os elementos HTML.
document.addEventListener('DOMContentLoaded', function () {
    // Referências aos elementos usados pelo formulário e pela tabela de usuários.
    const inputBuscaUsuario = document.getElementById('input_busca_usuario');
    const btnBuscaUsuario = document.getElementById('btn_busca_usuario');
    const filtroCargoUsuario = document.getElementById('filtro_cargo_usuario');
    const tbodyUsuarios = document.getElementById('tbody_usuarios');
    const inputCep = document.getElementById('input_cep');
    const inputLogradouro = document.getElementById('input_logradouro');
    const inputBairro = document.getElementById('input_bairro');
    const inputCidade = document.getElementById('input_cidade');
    const inputEstado = document.getElementById('input_estado');
    const mensagemCep = document.getElementById('mensagem_cep');

    // Atualiza a indicação visual de campos preenchidos no formulário.
    function atualizarLabelFlutuante(campo) {
        if (!campo) return;
        const valor = campo.value ?? '';
        campo.classList.toggle('has-value', String(valor).trim() !== '');
    }

    // Remove caracteres não numéricos e aplica a máscara visual do CEP.
    function formatarCep(valor) {
        const apenasDigitos = String(valor || '').replace(/\D/g, '').slice(0, 8);
        if (apenasDigitos.length <= 5) {
            return apenasDigitos;
        }
        // Adiciona o traço visual no formato 00000-000.
        return `${apenasDigitos.slice(0, 5)}-${apenasDigitos.slice(5)}`;
    }

    // Define a cor e o conteúdo da mensagem exibida para o CEP.
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

    // Limpa os campos de endereço e atualiza suas labels flutuantes.
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

    // Consulta o CEP na BrasilAPI e preenche os dados de endereço retornados.
    function consultarCep() {
        if (!inputCep) return;

        // Retira a máscara do CEP, deixando apenas os oito números enviados à API.
        const cep = String(inputCep.value || '').replace(/\D/g, '');
        if (!cep) {
            limparCamposEndereco();
            atualizarStatusCep('', 'idle');
            return;
        }

        // Uma consulta só é feita após o usuário informar os oito dígitos completos.
        if (cep.length !== 8) {
            limparCamposEndereco();
            return;
        }

        // API externa: faz uma requisição GET para buscar os dados relacionados ao CEP.
        // Não há alteração no banco nesta etapa; a consulta apenas preenche o formulário.
        fetch(`https://brasilapi.com.br/api/cep/v1/${cep}`)
            .then(async response => {
                // Lê o corpo da resposta como texto para permitir uma validação personalizada do JSON.
                const texto = await response.text();

                // Trata erros HTTP, como CEP inexistente ou serviço indisponível.
                if (!response.ok) {
                    let mensagem = 'CEP não encontrado ou indisponível no momento.';
                    try {
                        // Converte o JSON de erro recebido da BrasilAPI em objeto JavaScript.
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
                    // Converte o JSON de sucesso recebido da API em um objeto JavaScript.
                    dados = JSON.parse(texto);
                } catch (erro) {
                    throw new Error('Não foi possível processar a resposta da BrasilAPI.');
                }

                // Copia cada propriedade do objeto recebido para o campo correspondente do formulário.
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
                // Executado quando ocorre erro na API, na conversão JSON ou na comunicação.
                limparCamposEndereco();
                atualizarStatusCep(error && error.message ? error.message : 'Não foi possível consultar o CEP.', 'erro');
            });
    }

    // Monitora o campo de CEP: formata durante a digitação e consulta ao perder o foco.
    if (inputCep) {
        inputCep.addEventListener('input', function () {
            this.value = formatarCep(this.value);
            if (this.value.length < 9) {
                atualizarStatusCep('', 'idle');
            }
        });
        inputCep.addEventListener('blur', consultarCep);
    }

    // Filtra a tabela de usuários pelo termo informado no campo de busca.
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

        if (filtroCargoUsuario && filtroCargoUsuario.form) {
            filtroCargoUsuario.addEventListener('change', function () {
                filtroCargoUsuario.form.submit();
            });
        }

        filtrarUsuarios();
    }

    // Configura a confirmação visual antes da exclusão de um usuário.
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

        if (permissaoText.includes('admin')) {
            if (radioAdmin) radioAdmin.checked = true;
        } else {
            if (radioFunc) radioFunc.checked = true;
        }

        const id = (btn.dataset && btn.dataset.id) || (row.dataset && row.dataset.id) || '';

        // Impede que o administrador desmarque seu próprio cargo administrativo.
        const formUsuario = document.getElementById('form_cadastro_user');
        const usuarioAtual = formUsuario ? Number(formUsuario.dataset.usuarioAtual) : 0;
        const editandoProprioPerfil = usuarioAtual > 0 && Number(id) === usuarioAtual;

        if (radioFunc) {
            radioFunc.disabled = editandoProprioPerfil;
        }
        if (editandoProprioPerfil && radioAdmin) {
            radioAdmin.checked = true;
        }

        if (inputId) inputId.value = id;

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
            const radioFunc = document.getElementById('funcionario');
            if (radioFunc) radioFunc.disabled = false;
            if (mensagemCep) {
                mensagemCep.textContent = '';
                mensagemCep.className = 'small mt-n2 mb-3 text-muted';
            }
            document.querySelectorAll('#form_cadastro_user .input-field input, #form_cadastro_user .input-field select, #form_cadastro_user .input-field textarea').forEach(campo => atualizarLabelFlutuante(campo));
            
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

    // Intercepta o envio do formulário para realizar o cadastro sem recarregar a página.
    const formCadastro = document.getElementById('form_cadastro_user');
    if (formCadastro) {
        formCadastro.addEventListener('submit', function (e) {
            // Impede o comportamento padrão do formulário, que recarregaria a página.
            e.preventDefault();

            const mensagemDiv = document.getElementById('mensagem_cadastro');
            const camposObrigatorios = formCadastro.querySelectorAll('input[required], textarea[required], select[required]');
            const campoVazio = Array.from(camposObrigatorios).find(campo => String(campo.value).trim() === '');

            // Valida campos vazios antes de enviar os dados ao PHP.
            if (campoVazio) {
                mensagemDiv.innerHTML = '';
                campoVazio.focus();
                campoVazio.reportValidity();
                return;
            }

            // Mostra o indicador de carregamento enquanto o backend processa o cadastro.
            mensagemDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

            // API interna: envia os dados do formulário ao arquivo PHP indicado no atributo action.
            // FormData mantém os nomes dos campos iguais aos usados pelo PHP com $_POST.
            fetch(formCadastro.action, {
                method: 'POST',
                body: new FormData(formCadastro)
            })
            .then(async response => {
                // Obtém a resposta bruta produzida pelo PHP.
                const text = await response.text();
                try {
                    // Converte o JSON interno do PHP em um objeto JavaScript.
                    // Exemplo: { success: true, reload: true }.
                    const data = JSON.parse(text);
                    if (data.success) {
                        if (data.reload) {
                            // Fecha o modal e recarrega a tabela após o cadastro ou edição.
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
                        // Exibe a mensagem de erro enviada pelo PHP no campo success false.
                        mensagemDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                } catch (err) {
                    // Executado quando o PHP não devolve um JSON válido.
                    console.error('Resposta inválida do servidor:', text);
                    mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro do servidor. Verifique os dados e tente novamente.</div>';
                }
            })
            .catch(error => {
                // Executado quando a requisição fetch não consegue alcançar o servidor.
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="alert alert-danger">Erro ao processar requisição</div>';
            });
        });
    }
});
