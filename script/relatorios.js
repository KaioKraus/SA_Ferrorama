document.addEventListener('click', function (e) {
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
