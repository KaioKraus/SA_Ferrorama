document.addEventListener('DOMContentLoaded', function () {
    const rotasDb = Array.isArray(window.rotasDb) ? window.rotasDb : [];
    const avisoExclusaoRota = document.getElementById('aviso_exclusao_rota');

    // Função para atualizar label flutuante - IGUAL AO usuarios.js
    function atualizarLabelFlutuante(campo) {
        if (!campo) return;

        const tag = campo.tagName.toLowerCase();
        let temValor = false;

        if (tag === 'select') {
            temValor = campo.value !== '' && campo.selectedIndex !== -1 && campo.value !== null;
        } else {
            temValor = String(campo.value ?? '').trim() !== '';
        }

        campo.classList.toggle('has-value', temValor);
    }

    // Aplicar em TODOS os campos .input-field - igual aos outros modais
    document.querySelectorAll('.input-field input, .input-field select, .input-field textarea').forEach(campo => {
        atualizarLabelFlutuante(campo);
        campo.addEventListener('input', function() {
            atualizarLabelFlutuante(this);
        });
        campo.addEventListener('change', function() {
            atualizarLabelFlutuante(this);
        });
        campo.addEventListener('blur', function() {
            atualizarLabelFlutuante(this);
        });
    });

    if (avisoExclusaoRota) {
        window.setTimeout(function () {
            avisoExclusaoRota.classList.remove('show');
            window.setTimeout(function () {
                avisoExclusaoRota.remove();
            }, 150);
        }, 5000);
    }

    // Configuração das estações baseada no projeto trem-visual-controle (circuito XP500)
    // Coordenadas extraídas do arquivo circuito.js com as posições das agulhas e para-choque
    const stations = [
        {
            id: 'agulha-norte',
            name: 'Agulha Norte',
            x: 430,
            y: 72,
            viewBox: { width: 1661, height: 947 }
        },
        {
            id: 'agulha-central',
            name: 'Agulha Central',
            x: 346,
            y: 560,
            viewBox: { width: 1661, height: 947 }
        },
        {
            id: 'agulha-sul',
            name: 'Agulha Sul',
            x: 1092,
            y: 862,
            viewBox: { width: 1661, height: 947 }
        },
        {
            id: 'para-choque',
            name: 'Para-choque',
            x: 1152.5,
            y: 676.5,
            viewBox: { width: 1661, height: 947 }
        }
    ];

    let selectedDeparture = null;
    let selectedArrival = null;

    function normalizarNomeEstacao(valor) {
        const mapaAliases = {
            'Agulha Norte': 'Estação Norte',
            'Agulha Central': 'Estação Central',
            'Agulha Sul': 'Estação Sul',
            'Para-choque': 'Estação Leste',
            'Agulha Oeste': 'Estação Oeste'
        };

        const nome = String(valor || '').trim();
        return mapaAliases[nome] || nome;
    }

    function initializeStationMarkers() {
        const mapContainer = document.querySelector('.route-selector-map');
        if (!mapContainer) return;

        const mapImage = mapContainer.querySelector('.route-selector-image');
        if (!mapImage) return;

        // Aguarda o carregamento da imagem
        const setupMarkers = function() {
            const imageRect = mapImage.getBoundingClientRect();
            const scaleX = imageRect.width / stations[0].viewBox.width;
            const scaleY = imageRect.height / stations[0].viewBox.height;

            // Remove marcadores antigos se existirem
            mapContainer.querySelectorAll('.station-marker').forEach(m => m.remove());

            stations.forEach(function(station) {
                const marker = document.createElement('div');
                marker.className = 'station-marker';
                marker.dataset.stationId = station.id;
                marker.dataset.stationName = station.name;
                
                const posX = (station.x * scaleX);
                const posY = (station.y * scaleY);
                
                marker.style.left = posX + 'px';
                marker.style.top = posY + 'px';

                const label = document.createElement('span');
                label.className = 'station-label';
                label.textContent = station.name;
                marker.appendChild(label);

                marker.addEventListener('click', function() {
                    handleStationClick(station.id, station.name, marker);
                });

                mapContainer.appendChild(marker);
            });
        };

        if (mapImage.complete) {
            setupMarkers();
        } else {
            mapImage.addEventListener('load', setupMarkers);
        }

        // Reposiciona marcadores ao redimensionar
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(setupMarkers, 250);
        });
    }

    function handleStationClick(stationId, stationName, markerElement) {
        const selectPartida = document.getElementById('select_ponto_partida');
        const selectChegada = document.getElementById('select_ponto_chegada');
        
        if (!selectPartida || !selectChegada) return;

        // Remove seleções visuais anteriores
        document.querySelectorAll('.station-marker').forEach(function(m) {
            m.classList.remove('selected-departure', 'selected-arrival');
        });

        // Lógica de seleção: primeiro clique = partida, segundo = chegada
        const nomePartida = normalizarNomeEstacao(stationName);

        if (selectedDeparture === null) {
            // Primeira seleção: Ponto de Partida
            selectedDeparture = nomePartida;
            selectPartida.value = nomePartida;
            markerElement.classList.add('selected-departure');
        } else if (selectedArrival === null && nomePartida !== selectedDeparture) {
            // Segunda seleção: Ponto de Chegada (deve ser diferente)
            selectedArrival = nomePartida;
            selectChegada.value = nomePartida;
            markerElement.classList.add('selected-arrival');
            
            // Marca novamente o ponto de partida
            document.querySelectorAll('.station-marker').forEach(function(m) {
                if (normalizarNomeEstacao(m.dataset.stationName) === selectedDeparture) {
                    m.classList.add('selected-departure');
                }
            });
        } else {
            // Reset: começar de novo
            selectedDeparture = nomePartida;
            selectedArrival = null;
            selectPartida.value = nomePartida;
            selectChegada.value = '';
            markerElement.classList.add('selected-departure');
        }
    }

    function resetStationSelection() {
        selectedDeparture = null;
        selectedArrival = null;
        document.querySelectorAll('.station-marker').forEach(function(m) {
            m.classList.remove('selected-departure', 'selected-arrival');
        });
        const selectPartida = document.getElementById('select_ponto_partida');
        const selectChegada = document.getElementById('select_ponto_chegada');
        if (selectPartida) selectPartida.value = '';
        if (selectChegada) selectChegada.value = '';
    }

    // Inicializar marcadores quando o modal for aberto
    const modalAddRota = document.getElementById('modalAddRota');
    if (modalAddRota) {
        modalAddRota.addEventListener('shown.bs.modal', function() {
            setTimeout(initializeStationMarkers, 100);
        });
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
    if (modalEl && typeof bootstrap !== 'undefined') {
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
    }


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
        resetStationSelection();
    }

    const botaoAdicionarRota = document.querySelector('[data-bs-target="#modalAddRota"]');
    if (botaoAdicionarRota) {
        botaoAdicionarRota.addEventListener('click', definirModoCadastroRota);
    }

    // Listener para quando o modal abrir
    if (modalAddRota) {
        modalAddRota.addEventListener('show.bs.modal', function (event) {
            const botaoEditar = event.relatedTarget;
            
            // Se não for botão de editar, não faz nada (deixa o modal limpo para cadastro)
            if (!botaoEditar || !botaoEditar.classList.contains('btn_edit')) {
                return;
            }

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
            
            // Atualizar labels após preencher - IGUAL AO trens.js
            const inputNome = document.getElementById('input_nome_rota');
            const selectTrem = document.getElementById('input_trem_rota');
            const textareaDescricao = document.getElementById('input_descricao_rota');
            
            // Usar setTimeout para garantir que o DOM foi atualizado
            setTimeout(function() {
                if (inputNome) atualizarLabelFlutuante(inputNome);
                if (selectTrem) atualizarLabelFlutuante(selectTrem);
                if (textareaDescricao) atualizarLabelFlutuante(textareaDescricao);
            }, 50);
            
            // Preencher seleção de estações no mapa
            selectedDeparture = normalizarNomeEstacao(rota.ponto_partida || null);
            selectedArrival = normalizarNomeEstacao(rota.ponto_chegada || null);
            
            // Atualizar marcadores visuais
            setTimeout(function() {
                document.querySelectorAll('.station-marker').forEach(function(marker) {
                    const stationName = normalizarNomeEstacao(marker.dataset.stationName);
                    marker.classList.remove('selected-departure', 'selected-arrival');
                    if (stationName === selectedDeparture) {
                        marker.classList.add('selected-departure');
                    }
                    if (stationName === selectedArrival) {
                        marker.classList.add('selected-arrival');
                    }
                });
            }, 150);
        });
    }

    if (formCadastroRota) {
        formCadastroRota.addEventListener('submit', function (e) {
            e.preventDefault();

            const botaoAdicionar = formCadastroRota.querySelector('button[type="submit"]');
            if (botaoAdicionar) {
                botaoAdicionar.disabled = true;
            }

            fetch(formCadastroRota.action, {
                method: 'POST',
                body: new FormData(formCadastroRota)
            })
            .then(async function (response) {
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const modalCadastro = modalAddRota && typeof bootstrap !== 'undefined'
                            ? bootstrap.Modal.getInstance(modalAddRota)
                            : null;
                        if (modalCadastro) {
                            modalCadastro.hide();
                        }
                        location.reload();
                        return;
                    }

                    const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                    if (mensagemDiv) {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">' + (data.message || 'Erro ao cadastrar rota.') + '</div>';
                    }
                    if (botaoAdicionar) {
                        botaoAdicionar.disabled = false;
                    }
                } catch (err) {
                    console.error('Resposta inválida do servidor:', text);
                    const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                    if (mensagemDiv) {
                        mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro do servidor. Verifique os dados e tente novamente.</div>';
                    }
                    if (botaoAdicionar) {
                        botaoAdicionar.disabled = false;
                    }
                }
            })
            .catch(function (error) {
                console.error('Erro no cadastro da rota:', error);
                const mensagemDiv = document.getElementById('mensagem_cadastro_rota');
                if (mensagemDiv) {
                    mensagemDiv.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Erro ao processar requisição.</div>';
                }
                if (botaoAdicionar) {
                    botaoAdicionar.disabled = false;
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
