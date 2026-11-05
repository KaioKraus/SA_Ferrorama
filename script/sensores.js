document.addEventListener('DOMContentLoaded', function () {
    const inputBuscaSensor = document.getElementById('input_busca_sensor');
    const btnBuscaSensor = document.getElementById('btn_busca_sensor');
    const tbodySensores = document.getElementById('tbody_sensores');
    const modalCadastroSensor = document.getElementById('modal_cadastro_sensor');
    const formCadastroSensor = document.getElementById('form_cadastro_sensor');

    // Função idêntica à do modal de usuários
    function atualizarLabelFlutuante(campo) {
        if (!campo) return;
        const valor = campo.value ?? '';
        let temValor = String(valor).trim() !== '';
        
        // Para selects, também verificar se não é a opção disabled
        if (campo.tagName.toLowerCase() === 'select') {
            const opcaoSelecionada = campo.options[campo.selectedIndex];
            if (opcaoSelecionada && opcaoSelecionada.disabled) {
                temValor = false;
            }
        }
        
        campo.classList.toggle('has-value', temValor);
    }

    // Aplicar em todos os campos quando a página carregar
    document.querySelectorAll('.input-field input, .input-field select, .input-field textarea').forEach(campo => {
        atualizarLabelFlutuante(campo);
        campo.addEventListener('input', () => atualizarLabelFlutuante(campo));
        campo.addEventListener('change', () => atualizarLabelFlutuante(campo));
        // Para textarea, também escutar blur para garantir atualização
        if (campo.tagName.toLowerCase() === 'textarea') {
            campo.addEventListener('blur', () => atualizarLabelFlutuante(campo));
        }
    });

    function prepararCadastroSensor() {
        if (!formCadastroSensor) return;

        formCadastroSensor.reset();
        const titulo = document.getElementById('label_modal_cadastro_sensor');
        const botaoSalvar = document.getElementById('btn_salvar_sensor');
        const mensagem = document.getElementById('mensagem_cadastro_sensor');
        const sensorId = document.getElementById('input_sensor_id');
        if (titulo) titulo.textContent = 'Cadastro de Sensor';
        if (botaoSalvar) botaoSalvar.textContent = 'Adicionar';
        if (mensagem) mensagem.textContent = '';
        if (sensorId) sensorId.value = '';
        delete formCadastroSensor.dataset.localizacaoChave;
        delete formCadastroSensor.dataset.localizacaoOriginal;
        delete formCadastroSensor.dataset.tipoChave;
        delete formCadastroSensor.dataset.tipoOriginal;

        const radioTrem = document.getElementById('vinculo_trem');
        if (radioTrem) {
            radioTrem.checked = true;
            radioTrem.dispatchEvent(new Event('change'));
        }
        
        const statusAtivo = document.getElementById('status_sensor_ativo');
        if (statusAtivo) {
            statusAtivo.checked = true;
        }
        
        // Remover classe has-value de todos os campos ao resetar
        document.querySelectorAll('#form_cadastro_sensor .input-field input, #form_cadastro_sensor .input-field select, #form_cadastro_sensor .input-field textarea').forEach(campo => {
            campo.classList.remove('has-value');
        });
    }

    document.querySelectorAll('[data-bs-target="#modal_cadastro_sensor"]').forEach(function (botao) {
        botao.addEventListener('click', prepararCadastroSensor);
    });

    // Adicionar listeners para o toggle Trem/Rota mudar o label do select
    const radioTrem = document.getElementById('vinculo_trem');
    const radioRota = document.getElementById('vinculo_rota');
    const selectLocalizacao = document.getElementById('input_localizacao_sensor');
    const labelLocalizacao = selectLocalizacao ? selectLocalizacao.parentElement.querySelector('label[for="input_localizacao_sensor"]') : null;

    function atualizarLabelLocalizacao() {
        if (!labelLocalizacao) return;
        if (radioRota && radioRota.checked) {
            labelLocalizacao.textContent = 'Rota';
        } else {
            labelLocalizacao.textContent = 'Trem';
        }
    }

    if (radioTrem) {
        radioTrem.addEventListener('change', atualizarLabelLocalizacao);
    }
    if (radioRota) {
        radioRota.addEventListener('change', atualizarLabelLocalizacao);
    }

    // Inicializar o label correto
    atualizarLabelLocalizacao();

    if (tbodySensores && modalCadastroSensor && typeof bootstrap !== 'undefined') {
        tbodySensores.addEventListener('click', function (event) {
            const botaoEditar = event.target.closest('.btn_edit');
            if (!botaoEditar) return;

            const linha = botaoEditar.closest('tr[data-sensor-id]');
            if (!linha || !formCadastroSensor) return;

            const titulo = document.getElementById('label_modal_cadastro_sensor');
            const botaoSalvar = document.getElementById('btn_salvar_sensor');
            const mensagem = document.getElementById('mensagem_cadastro_sensor');
            const sensorId = document.getElementById('input_sensor_id');
            const radioTrem = document.getElementById('vinculo_trem');
            const radioRota = document.getElementById('vinculo_rota');
            const vinculo = linha.dataset.vinculo;
            const radioVinculo = vinculo === 'rota' ? radioRota : radioTrem;

            const inputNome = document.getElementById('input_nome_sensor');
            inputNome.value = linha.dataset.nome || '';
            if (sensorId) sensorId.value = linha.dataset.sensorId || '';
            formCadastroSensor.dataset.localizacaoChave = linha.dataset.localizacao || '';
            formCadastroSensor.dataset.localizacaoOriginal = linha.dataset.localizacaoOriginal || '';
            formCadastroSensor.dataset.tipoChave = linha.dataset.tipo || '';
            formCadastroSensor.dataset.tipoOriginal = linha.dataset.tipoOriginal || '';

            if (radioVinculo) {
                radioVinculo.checked = true;
                radioVinculo.dispatchEvent(new Event('change'));
            }

            const localizacao = document.getElementById('input_localizacao_sensor');
            if (localizacao) {
                const valorLocalizacao = linha.dataset.localizacao || '';
                if (!Array.from(localizacao.options).some(function (option) {
                    return option.value === valorLocalizacao;
                })) {
                    const opcaoAtual = document.createElement('option');
                    opcaoAtual.value = valorLocalizacao;
                    opcaoAtual.textContent = linha.cells[2].textContent.trim();
                    localizacao.appendChild(opcaoAtual);
                }
                localizacao.value = valorLocalizacao;
            }

            const tipoEl = document.getElementById('input_tipo_sensor');
            
            if (tipoEl) {
                tipoEl.value = linha.dataset.tipo || '';
            }
            
            // Definir status usando os radio buttons
            const statusValue = linha.dataset.status || 'Ativo';
            const statusAtivo = document.getElementById('status_sensor_ativo');
            const statusInativo = document.getElementById('status_sensor_inativo');
            if (statusValue === 'Inativo' && statusInativo) {
                statusInativo.checked = true;
            } else if (statusAtivo) {
                statusAtivo.checked = true;
            }
            
            // Atualizar labels após preencher os campos
            setTimeout(function() {
                if (inputNome) atualizarLabelFlutuante(inputNome);
                if (localizacao) atualizarLabelFlutuante(localizacao);
                if (tipoEl) atualizarLabelFlutuante(tipoEl);
            }, 50);
            
            if (titulo) titulo.textContent = 'Editar Sensor';
            if (botaoSalvar) botaoSalvar.textContent = 'Salvar alterações';
            if (mensagem) mensagem.textContent = '';

            bootstrap.Modal.getOrCreateInstance(modalCadastroSensor).show();
        });
    }

    if (inputBuscaSensor && tbodySensores) {
        const linhasSensores = Array.from(tbodySensores.querySelectorAll('tr'));

        function filtrarSensores() {
            const termo = inputBuscaSensor.value.trim().toLowerCase();
            let algumVisivel = false;

            linhasSensores.forEach(linha => {
                const idSensor = (linha.children[0] || {}).textContent.trim().toLowerCase();
                const nomeSensor = (linha.children[1] || {}).textContent.trim().toLowerCase();
                const localizacaoSensor = (linha.children[2] || {}).textContent.trim().toLowerCase();
                const tipoDadoSensor = (linha.children[3] || {}).textContent.trim().toLowerCase();

                const corresponde = termo === '' ||
                    idSensor.includes(termo) ||
                    nomeSensor.includes(termo) ||
                    localizacaoSensor.includes(termo) ||
                    tipoDadoSensor.includes(termo);

                linha.hidden = !corresponde;
                if (corresponde) algumVisivel = true;
            });

            let linhaVazia = tbodySensores.querySelector('#linha_sensor_vazia');

            if (linhasSensores.length > 0 && !algumVisivel) {
                if (!linhaVazia) {
                    linhaVazia = document.createElement('tr');
                    linhaVazia.id = 'linha_sensor_vazia';
                    linhaVazia.innerHTML = '<td colspan="6" class="text-center text-muted">Nenhum sensor encontrado.</td>';
                    tbodySensores.appendChild(linhaVazia);
                }
            } else if (linhaVazia) {
                linhaVazia.remove();
            }
        }

        if (btnBuscaSensor) {
            btnBuscaSensor.addEventListener('click', filtrarSensores);
        }
    }

    var deleteModalEl = document.getElementById('modal_confirm_delete');
    if (deleteModalEl && typeof bootstrap !== 'undefined') {
        var bsModal = new bootstrap.Modal(deleteModalEl);

        // Ao clicar no botão .btn_delete na tabela de sensores, pega o ID da primeira célula da linha
        document.addEventListener('click', function (e) {
            var btn = e.target.closest && e.target.closest('.btn_delete');
            if (!btn) return;
            var id = btn.getAttribute('data-id') || '';
            if (!id) {
                var tr = btn.closest('tr');
                if (!tr) return;
                id = (tr.querySelector('td') && tr.querySelector('td').textContent.trim()) || '';
            }

            var input = document.getElementById('delete_sensor_id');
            if (input) input.value = id;

            var textEl = document.getElementById('modal_confirm_text');
            if (textEl) textEl.textContent = 'Deseja realmente excluir o sensor de ID ' + id + '?';

            bsModal.show();
        });
    }
});
