document.addEventListener('DOMContentLoaded', function () {
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
