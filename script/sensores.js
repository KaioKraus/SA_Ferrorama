document.addEventListener('DOMContentLoaded', function () {
    const inputBuscaSensor = document.getElementById('input_busca_sensor');
    const btnBuscaSensor = document.getElementById('btn_busca_sensor');
    const tbodySensores = document.getElementById('tbody_sensores');

    if (inputBuscaSensor && tbodySensores) {
        const linhasSensores = Array.from(tbodySensores.querySelectorAll('tr'));

        function filtrarSensores() {
            const termo = inputBuscaSensor.value.trim().toLowerCase();
            let algumVisivel = false;

            linhasSensores.forEach(linha => {
                const idSensor = (linha.querySelector('td') || {}).textContent.trim().toLowerCase();
                const nomeSensor = (linha.children[1] || {}).textContent.trim().toLowerCase();
                const corresponde = termo === '' || idSensor.includes(termo) || nomeSensor.includes(termo);

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
