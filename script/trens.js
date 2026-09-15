document.addEventListener('DOMContentLoaded', function(){
    var deleteModalEl = document.getElementById('modal_confirm_delete');
    var bsModal = new bootstrap.Modal(deleteModalEl);

    document.querySelectorAll('.trens-card-cabecalho').forEach(function(header){
        if(header.querySelector('.btn_delete')) return;
        var strong = header.querySelector('strong');
        var idText = strong ? strong.textContent.replace('ID:','').trim() : '';
        var del = document.createElement('button');
        del.type = 'button';
        del.className = 'btn_delete ms-2';
        del.setAttribute('data-id', idText);
        del.setAttribute('aria-label','Excluir trem');
        del.innerHTML = '<i class="bi bi-trash"></i>';
        header.appendChild(del);
    });

    // Ao clicar no botão de delete abre o modal mostrando o ID
    document.addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.btn_delete');
        if(!btn) return;
        var id = btn.getAttribute('data-id') || '';
        var input = document.getElementById('delete_train_id');
        if(input) input.value = id;
        var textEl = document.getElementById('modal_confirm_text');
        if(textEl) textEl.textContent = 'Deseja realmente excluir o trem de ID ' + id + '?';
        bsModal.show();
    });
});
