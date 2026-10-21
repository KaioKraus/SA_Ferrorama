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
    const modalAddRota = document.getElementById('modalAddRota');
    const tituloModalRota = document.getElementById('titulo_modal_rota');
    const botaoSalvarRota = document.getElementById('btn_salvar_rota');
    const campoRotaId = formCadastroRota ? formCadastroRota.querySelector('[name="rota_id"]') : null;

    function definirModoCadastroRota() {
        if (formCadastroRota) formCadastroRota.reset();
        if (campoRotaId) campoRotaId.value = '';
        if (tituloModalRota) tituloModalRota.textContent = 'Cadastro de Rota';
        if (botaoSalvarRota) botaoSalvarRota.textContent = 'Adicionar';
    }

    const botaoAdicionarRota = document.querySelector('[data-bs-target="#modalAddRota"]');
    if (botaoAdicionarRota) {
        botaoAdicionarRota.addEventListener('click', definirModoCadastroRota);
    }

    document.addEventListener('click', function (event) {
        const botaoEditar = event.target.closest && event.target.closest('.btn_edit');
        if (!botaoEditar || !modalAddRota || !formCadastroRota || !campoRotaId) return;

        const linha = botaoEditar.closest('tr[data-rota-id]');
        const rota = linha && rotasDb.find(function (item) {
            return String(item.rota_id) === String(linha.dataset.rotaId);
        });
        if (!rota) return;

        formCadastroRota.elements.ponto_partida.value = rota.ponto_partida || '';
        formCadastroRota.elements.ponto_chegada.value = rota.ponto_chegada || '';
        formCadastroRota.elements.nome.value = rota.nome || '';
        formCadastroRota.elements.trem_id.value = rota.trem_id || '';
        formCadastroRota.elements.descricao.value = rota.descricao || '';
        campoRotaId.value = rota.rota_id;

        const status = Array.from(formCadastroRota.querySelectorAll('[name="status"]')).find(function (opcao) {
            return opcao.value === rota.status;
        });
        if (status) status.checked = true;
        if (tituloModalRota) tituloModalRota.textContent = 'Editar Rota';
        if (botaoSalvarRota) botaoSalvarRota.textContent = 'Salvar';
        bootstrap.Modal.getOrCreateInstance(modalAddRota).show();
    });

    if (formCadastroRota) {
        formCadastroRota.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(formCadastroRota.action, {
                method: 'POST',
                body: new FormData(formCadastroRota)
            })
            .then(async response => {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        if (data.reload) {
                            const modalCadastro = bootstrap.Modal.getInstance(modalAddRota);
                            if (modalCadastro) {
                                modalCadastro.hide();
                            }
                            location.reload();
                        } else {
                            const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                            if (mensagemDiv) {
                                mensagemDiv.innerHTML = '<div class="alert alert-success" style="background-color: #d1e7dd; border-radius: 8px; border: none;">' + data.message + '</div>';
                            }
                            setTimeout(() => {
                                const modalCadastro = bootstrap.Modal.getInstance(modalAddRota);
                                if (modalCadastro) {
                                    modalCadastro.hide();
                                }
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                        if (mensagemDiv) {
                            mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">' + data.message + '</div>';
                        }
                    }
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                    if (mensagemDiv) {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro do servidor. Verifique os dados e tente novamente.</div>';
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                if (mensagemDiv) {
                    mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro ao processar requisição</div>';
                }
            });
        });
    }

    // Limpar mensagem ao abrir o modal novamente
    if (modalAddRota) {
        modalAddRota.addEventListener('show.bs.modal', function () {
            const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
            if (mensagemDiv) {
                mensagemDiv.innerHTML = '';
            }
        });
    }
});
