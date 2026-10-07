document.addEventListener('DOMContentLoaded', function () {
    var deleteModalEl = document.getElementById('modal_confirm_delete');
    var bsModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(deleteModalEl) : null;

    document.querySelectorAll('.trens-card-cabecalho').forEach(function (header) {
        if (header.querySelector('.btn_delete')) return;
        var strong = header.querySelector('strong');
        var idText = strong ? strong.textContent.replace('ID:', '').trim() : '';
        var del = document.createElement('button');
        del.type = 'button';
        del.className = 'btn_delete ms-2';
        del.setAttribute('data-id', idText);
        del.setAttribute('aria-label', 'Excluir trem');
        del.innerHTML = '<i class="bi bi-trash"></i>';
        header.appendChild(del);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('.btn_delete');
        if (!btn) return;
        var id = btn.getAttribute('data-id') || '';
        var input = document.getElementById('delete_train_id');
        if (input) input.value = id;
        var textEl = document.getElementById('modal_confirm_text');
        if (textEl) textEl.textContent = 'Deseja realmente excluir o trem de ID ' + id + '?';
        if (bsModal) bsModal.show();
    });

    var formCadastroTrem = document.getElementById('form_cadastro_trem');
    if (formCadastroTrem) {
        formCadastroTrem.addEventListener('submit', function (event) {
            event.preventDefault();

            var mensagem = document.getElementById('mensagem_cadastro_trem');
            if (mensagem) {
                mensagem.innerHTML = '<div class="alert alert-info py-2 mb-0">Enviando cadastro...</div>';
            }

            var formData = new FormData(formCadastroTrem);

            fetch('cadastrar_trem.php', {
                method: 'POST',
                body: formData
            })
            .then(async function (response) {
                var text = await response.text();
                try {
                    var data = JSON.parse(text);
                    if (data.success) {
                        if (mensagem) {
                            mensagem.innerHTML = '<div class="alert alert-success py-2 mb-0">' + data.message + '</div>';
                        }
                        formCadastroTrem.reset();
                        setTimeout(function () {
                            var modalCadastro = document.getElementById('modal_cadastro');
                            if (modalCadastro && typeof bootstrap !== 'undefined') {
                                var modalInstance = bootstrap.Modal.getInstance(modalCadastro);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }
                            }
                            location.reload();
                        }, 1200);
                        return;
                    }
                    if (mensagem) {
                        mensagem.innerHTML = '<div class="alert alert-danger py-2 mb-0">' + (data.message || 'Erro ao cadastrar trem.') + '</div>';
                    }
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    if (mensagem) {
                        mensagem.innerHTML = '<div class="alert alert-danger py-2 mb-0">Erro do servidor. Verifique os dados e tente novamente.</div>';
                    }
                }
            })
            .catch(function (error) {
                console.error('Erro no cadastro do trem:', error);
                if (mensagem) {
                    mensagem.innerHTML = '<div class="alert alert-danger py-2 mb-0">Erro ao processar requisição.</div>';
                }
            });
        });
    }
});
