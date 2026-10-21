document.addEventListener('DOMContentLoaded', function () {
    const inputBuscaSensor = document.getElementById('input_busca_sensor');
    const btnBuscaSensor = document.getElementById('btn_busca_sensor');
    const tbodySensores = document.getElementById('tbody_sensores');
    const modalCadastroSensor = document.getElementById('modal_cadastro_sensor');
    const formCadastroSensor = document.getElementById('form_cadastro_sensor');

    function prepararCadastroSensor() {
        if (!formCadastroSensor) return;

        formCadastroSensor.reset();
        const titulo = document.getElementById('label_modal_cadastro_sensor');
        const botaoSalvar = document.getElementById('btn_salvar_sensor');
        const mensagem = document.getElementById('mensagem_cadastro_sensor');
        if (titulo) titulo.textContent = 'Cadastro de Sensor';
        if (botaoSalvar) botaoSalvar.textContent = 'Adicionar';
        if (mensagem) mensagem.textContent = '';

        const radioTrem = document.getElementById('vinculo_trem');
        if (radioTrem) {
            radioTrem.checked = true;
            radioTrem.dispatchEvent(new Event('change'));
        }
        const status = document.getElementById('input_status_sensor');
        if (status) status.value = 'Ativo';
    }

    document.querySelectorAll('[data-bs-target="#modal_cadastro_sensor"]').forEach(function (botao) {
        botao.addEventListener('click', prepararCadastroSensor);
    });

    if (tbodySensores && modalCadastroSensor && typeof bootstrap !== 'undefined') {
        tbodySensores.addEventListener('click', function (event) {
            const botaoEditar = event.target.closest('.btn_edit');
            if (!botaoEditar) return;

            const linha = botaoEditar.closest('tr[data-sensor-id]');
            if (!linha || !formCadastroSensor) return;

            const titulo = document.getElementById('label_modal_cadastro_sensor');
            const botaoSalvar = document.getElementById('btn_salvar_sensor');
            const mensagem = document.getElementById('mensagem_cadastro_sensor');
            const radioTrem = document.getElementById('vinculo_trem');
            const radioRota = document.getElementById('vinculo_rota');
            const vinculo = linha.dataset.vinculo;
            const radioVinculo = vinculo === 'rota' ? radioRota : radioTrem;

            document.getElementById('input_nome_sensor').value = linha.dataset.nome || '';
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

            document.getElementById('input_tipo_sensor').value = linha.dataset.tipo || '';
            document.getElementById('input_status_sensor').value = linha.dataset.status || 'Ativo';
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

        inputBuscaSensor.addEventListener('input', filtrarSensores);
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
