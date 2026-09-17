document.addEventListener('click', function (e) {
    const deleteBtn = e.target.closest && e.target.closest('.btn_delete');
    if (deleteBtn) {
        const modalEl = document.getElementById('modal_confirm_delete');
        if (!modalEl || typeof bootstrap === 'undefined') return;

        const row = deleteBtn.closest('tr');
        const id = (deleteBtn.dataset && deleteBtn.dataset.id) || (row && row.dataset && row.dataset.id) ||
            (row && row.querySelector('td') ? row.querySelector('td').textContent.trim() : '');

        const input = document.getElementById('delete_sensor_id');
        if (input) input.value = id;

        const textEl = document.getElementById('modal_confirm_text');
        if (textEl) {
            textEl.textContent = 'Deseja realmente excluir o relatório de ID ' + id + '?';
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        return;
    }

    if (!e.target.closest('.btn-detalhes-relatorio')) return;
    const btn = e.target.closest('.btn-detalhes-relatorio');
    const card = btn.closest('.relatorio-card');
    if (!card) return;

    const id = card.dataset.id || '';
    const trem = card.dataset.trem || '';
    const sensor = card.dataset.sensor || '';
    const data = card.dataset.data || '';
    const falha = card.dataset.falha || '';

    const modalEl = document.getElementById('modal_detalhes_relatorio');
    if (!modalEl) return;

    const setText = (idSel, value) => {
        const el = modalEl.querySelector(idSel);
        if (el) el.textContent = value;
    };

    setText('#det-id', id);
    setText('#det-trem', trem);
    setText('#det-sensor', sensor);
    setText('#det-data', data);
    setText('#det-falha', falha);

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
});
