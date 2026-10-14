document.addEventListener('DOMContentLoaded', function () {
    var deleteModalEl = document.getElementById('modal_confirm_delete');
    var bsModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(deleteModalEl) : null;

    function preencherModalTrem(card) {
        if (!card) return;

        var idEl = document.getElementById('modal_trem_id');
        var nomeEl = document.getElementById('modal_trem_nome');
        var partidaEl = document.getElementById('modal_trem_partida');
        var chegadaEl = document.getElementById('modal_trem_chegada');
        var horaPartidaEl = document.getElementById('modal_trem_hora_partida');
        var horaChegadaEl = document.getElementById('modal_trem_hora_chegada');
        var diasEl = document.getElementById('modal_trem_dias');
        var statusEl = document.getElementById('modal_trem_status');
        var responsavelEl = document.getElementById('modal_trem_responsavel');

        var id = (card.querySelector('strong') && card.querySelector('strong').textContent.replace('ID:', '').trim()) || card.dataset.id || '';
        var nome = (card.querySelector('.trens-card-nome') && card.querySelector('.trens-card-nome').textContent.replace(/^Nome\s*:\s*/i, '').trim()) || card.dataset.nome || 'Nome do Trem';
        var status = (card.querySelector('.trens-card-status') && card.querySelector('.trens-card-status').textContent.trim()) || card.dataset.status || 'Ativo';
        var partida = card.dataset.partida || 'Estação Norte';
        var chegada = card.dataset.chegada || 'Estação Sul';
        var horaPartida = card.dataset.horaPartida || '06:00';
        var horaChegada = card.dataset.horaChegada || '08:00';
        var dias = card.dataset.dias || 'Segunda, Quarta, Sexta';
        var responsavel = card.dataset.responsavel || '';

        if (idEl) idEl.value = id;
        if (nomeEl) nomeEl.value = nome;
        if (partidaEl) partidaEl.value = partida;
        if (chegadaEl) chegadaEl.value = chegada;
        if (horaPartidaEl) horaPartidaEl.value = horaPartida;
        if (horaChegadaEl) horaChegadaEl.value = horaChegada;
        if (diasEl) diasEl.value = dias;
        if (statusEl) statusEl.value = status;

        if (responsavelEl) {
            if (responsavel) {
                responsavelEl.value = responsavel;
            } else {
                responsavelEl.selectedIndex = 0;
            }
        }
    }

    var modalMonitoramento = document.getElementById('modal_monitoramento');
    if (modalMonitoramento) {
        modalMonitoramento.addEventListener('show.bs.modal', function (event) {
            var botao = event.relatedTarget;
            var card = botao ? botao.closest('.trens-card-trem') : null;
            preencherModalTrem(card);
        });
    }

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

            var nomeVisivel = document.getElementById('input_nome_trem');
            var nomeBackend = formCadastroTrem.querySelector('input[name="nome_trem"]');
            if (nomeVisivel && nomeBackend) {
                nomeBackend.value = nomeVisivel.value.trim() || 'Trem sem nome';
            }

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
