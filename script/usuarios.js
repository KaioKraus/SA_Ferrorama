document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modal_confirm_delete');
    if (!modalEl || typeof bootstrap === 'undefined') return;

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
});
