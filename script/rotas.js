document.addEventListener('DOMContentLoaded', function () {
    const filtroRotas = document.getElementById('filtroRotas');
    const campoFiltroRota = document.getElementById('campoFiltroRota');
    const linhasRotas = document.querySelectorAll('.table tbody tr');

    const aplicarFiltroRotas = function () {
        const termo = (filtroRotas ? filtroRotas.value.trim().toLowerCase() : '');
        const campo = campoFiltroRota ? campoFiltroRota.value : 'todos';

        linhasRotas.forEach(function (linha) {
            const pontoPartida = (linha.cells[1]?.textContent || '').toLowerCase();
            const destino = (linha.cells[2]?.textContent || '').toLowerCase();

            const corresponde = !termo || (
                (campo === 'todos' && (pontoPartida.includes(termo) || destino.includes(termo))) ||
                (campo === 'ponto_partida' && pontoPartida.includes(termo)) ||
                (campo === 'destino' && destino.includes(termo))
            );

            linha.style.display = corresponde ? '' : 'none';
        });
    };

    if (filtroRotas) {
        filtroRotas.addEventListener('input', aplicarFiltroRotas);
    }

    if (campoFiltroRota) {
        campoFiltroRota.addEventListener('change', aplicarFiltroRotas);
    }

    const modalEl = document.getElementById('modal_confirm_delete');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.addEventListener('click', function (event) {
        const btn = event.target.closest && event.target.closest('.btn_delete');
        if (!btn) return;

        const row = btn.closest('tr');
        const id = (btn.dataset && btn.dataset.id) || (row && row.dataset && row.dataset.id) ||
            (row && row.querySelector('td') ? row.querySelector('td').textContent.trim() : '');

        const input = document.getElementById('delete_route_id');
        if (input) input.value = id;

        const textEl = document.getElementById('modal_confirm_text');
        if (textEl) {
            textEl.textContent = 'Deseja realmente excluir a rota de ID ' + id + '?';
        }

        modal.show();
    });
});
