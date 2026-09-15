document.addEventListener('DOMContentLoaded', function(){
    var deleteModalEl = document.getElementById('modal_confirm_delete');
    var bsModal = new bootstrap.Modal(deleteModalEl);

    // Ao clicar no botão .btn_delete na tabela de sensores, pega o ID da primeira célula da linha
    document.addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.btn_delete');
        if(!btn) return;
        var id = btn.getAttribute('data-id') || '';
        if(!id){
            var tr = btn.closest('tr');
            if(!tr) return;
            id = (tr.querySelector('td') && tr.querySelector('td').textContent.trim()) || '';
        }

        var input = document.getElementById('delete_sensor_id');
        if(input) input.value = id;

        var textEl = document.getElementById('modal_confirm_text');
        if(textEl) textEl.textContent = 'Deseja realmente excluir o sensor de ID ' + id + '?';

        bsModal.show();
    });
});
