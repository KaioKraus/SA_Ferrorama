document.addEventListener('DOMContentLoaded', function () {
    const rotasDb = Array.isArray(window.rotasDb) ? window.rotasDb : [];
    const avisoExclusaoRota = document.getElementById('aviso_exclusao_rota');

    if (avisoExclusaoRota) {
        window.setTimeout(function () {
            avisoExclusaoRota.classList.remove('show');
            window.setTimeout(function () {
                avisoExclusaoRota.remove();
            }, 150);
        }, 5000);
    }

    function preencherModalRota(rota) {
        const rotaIdEl = document.getElementById('modal_rota_id');
        const rotaTremEl = document.getElementById('modal_rota_trem');
        const rotaStatusEl = document.getElementById('modal_rota_status');
        const rotaPartidaEl = document.getElementById('modal_rota_partida');
        const rotaDestinoEl = document.getElementById('modal_rota_destino');
        const rotaDistanciaEl = document.getElementById('modal_rota_distancia');
        const rotaDuracaoEl = document.getElementById('modal_rota_duracao');
        const rotaDataEl = document.getElementById('modal_rota_data');

        const rotaAtual = rota || {};

        if (rotaIdEl) rotaIdEl.textContent = rotaAtual.rota_id ?? '';
        if (rotaTremEl) rotaTremEl.textContent = rotaAtual.nome_trem ?? rotaAtual.trem_id ?? '';
        if (rotaStatusEl) rotaStatusEl.textContent = rotaAtual.status ?? '';
        if (rotaPartidaEl) rotaPartidaEl.textContent = rotaAtual.ponto_partida ?? '';
        if (rotaDestinoEl) rotaDestinoEl.textContent = rotaAtual.ponto_chegada ?? '';
        if (rotaDistanciaEl) rotaDistanciaEl.textContent = rotaAtual.distancia ?? '';
        if (rotaDuracaoEl) rotaDuracaoEl.textContent = rotaAtual.duracao ?? '';
        if (rotaDataEl) rotaDataEl.textContent = rotaAtual.data_criacao ?? '';
    }

    const modalMonitoramento = document.getElementById('modal_monitoramento');
    if (modalMonitoramento) {
        modalMonitoramento.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const rotaId = botao && botao.dataset ? botao.dataset.rotaId : null;

            let rotaSelecionada = null;

            if (rotaId) {
                rotaSelecionada = rotasDb.find(function (rota) {
                    return String(rota.rota_id) === String(rotaId);
                }) || null;
            }

            preencherModalRota(rotaSelecionada);
        });
    }

    const filtroPartidaRota = document.getElementById('filtro_partida_rota');
    const filtroDestinoRota = document.getElementById('filtro_destino_rota');
    const linhasRotas = Array.from(document.querySelectorAll('.table tbody tr[data-rota-id]'));
    const tbodyRotas = document.querySelector('.table tbody');
    const linhaRotasVaziaInicial = tbodyRotas ? tbodyRotas.querySelector('[data-empty-routes]') : null;

    function normalizarPontoRota(ponto) {
        return String(ponto || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .replace(/\s+/g, ' ')
            .toLocaleLowerCase('pt-BR');
    }

    function aplicarFiltroRotas() {
        const partidaSelecionada = normalizarPontoRota(filtroPartidaRota ? filtroPartidaRota.value : '');
        const destinoSelecionado = normalizarPontoRota(filtroDestinoRota ? filtroDestinoRota.value : '');

        let algumVisivel = false;

        linhasRotas.forEach(function (linha) {
            const pontoPartida = normalizarPontoRota(linha.dataset.pontoPartida);
            const destinoRota = normalizarPontoRota(linha.dataset.pontoChegada);

            const correspondePartida = partidaSelecionada === '' || pontoPartida === partidaSelecionada;
            const correspondeDestino = destinoSelecionado === '' || destinoRota === destinoSelecionado;
            const corresponde = correspondePartida && correspondeDestino;

            linha.style.display = corresponde ? '' : 'none';
            if (corresponde) algumVisivel = true;
        });

        const linhaVazia = tbodyRotas ? tbodyRotas.querySelector('#linha_rota_vazia') : null;
        if (linhaRotasVaziaInicial) {
            linhaRotasVaziaInicial.hidden = linhasRotas.length > 0;
        }

        if (linhasRotas.length > 0 && !algumVisivel) {
            if (tbodyRotas && !linhaVazia) {
                const linha = document.createElement('tr');
                linha.id = 'linha_rota_vazia';
                linha.innerHTML = '<td colspan="7" class="text-center text-muted">Nenhuma rota encontrada.</td>';
                tbodyRotas.appendChild(linha);
            }
        } else if (linhaVazia) {
            linhaVazia.remove();
        }
    }

    if (filtroPartidaRota) {
        filtroPartidaRota.addEventListener('change', aplicarFiltroRotas);
    }

    if (filtroDestinoRota) {
        filtroDestinoRota.addEventListener('change', aplicarFiltroRotas);
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


    // Formulário de cadastro de rota
    const formCadastroRota = document.getElementById('form_cadastro_rota');
    if (formCadastroRota) {
        formCadastroRota.addEventListener('submit', function (e) {
            e.preventDefault();

            const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
            mensagemDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';

            // Coleta os dados do formulário
            const pontoPartida = document.querySelector('select[name="ponto_partida"]').value.trim();
            const pontoChegada = document.querySelector('select[name="ponto_chegada"]').value.trim();
            const nome = document.querySelector('input[name="nome"]').value.trim();
            const tremId = document.querySelector('select[name="trem_id"]').value.trim();
            const descricao = document.querySelector('textarea[name="descricao"]').value.trim();
            
            // Verifica qual status está selecionado
            let status = 'Ativo';
            if (document.getElementById('status_manutencao') && document.getElementById('status_manutencao').checked) {
                status = 'Manutenção';
            } else if (document.getElementById('status_inativo') && document.getElementById('status_inativo').checked) {
                status = 'Inativo';
            }

            // Cria o FormData
            const formData = new FormData();
            formData.append('ponto_partida', pontoPartida);
            formData.append('ponto_chegada', pontoChegada);
            formData.append('nome', nome);
            formData.append('trem_id', tremId);
            formData.append('descricao', descricao);
            formData.append('status', status);

            // Envia via fetch
            fetch('cadastrar_rota.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        mensagemDiv.innerHTML = '<div class="alert alert-success" style="background-color: #d1e7dd; border-radius: 8px; border: none;">' + data.message + '</div>';
                        formCadastroRota.reset();
                        // Fecha o modal após 2 segundos e recarrega a página
                        setTimeout(() => {
                            const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modalAddRota'));
                            if (modalCadastro) {
                                modalCadastro.hide();
                            }
                            location.reload();
                        }, 2000);
                    } else {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">' + data.message + '</div>';
                    }
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro do servidor. Verifique os dados e tente novamente.</div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro ao processar requisição</div>';
            });
        });
    }

    // Limpar mensagem ao abrir o modal novamente
    const modalAddRota = document.getElementById('modalAddRota');
    if (modalAddRota) {
        modalAddRota.addEventListener('show.bs.modal', function () {
            const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
            if (mensagemDiv) {
                mensagemDiv.innerHTML = '';
            }
            const form = document.getElementById('form_cadastro_rota');
            if (form) {
                form.reset();
            }
        });
    }
});
