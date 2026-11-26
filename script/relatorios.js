// Relatórios: filtros, consulta ao backend, criação, exibição e exportação.
// As chamadas à API interna recebem ou devolvem dados em formato JSON.

const tabelaRelatorios = document.getElementById('tbody_relatorios');
const buscaRelatorios = document.getElementById('input_busca_relatorio');
const botaoBuscaRelatorios = document.getElementById('btn_busca_relatorio');
const filtroTipoRelatorio = document.getElementById('filtro_tipo_relatorio');
const isAdmin = document.body && document.body.dataset.isAdmin === '1';
let temporizadorNotificacaoRelatorio;

// Exibe um feedback visual com mensagem, tipo e fechamento automático opcional.
function mostrarMensagemRelatorio(elemento, mensagem, tipo, autoFechar = true) {
    if (!elemento) return;
    window.clearTimeout(temporizadorNotificacaoRelatorio);

    const alertaAnterior = document.querySelector('.relatorio-feedback .alert');
    if (alertaAnterior) alertaAnterior.remove();
    elemento.replaceChildren();

    const titulos = {
        success: 'Sucesso',
        warning: 'Atenção',
        danger: 'Erro',
        secondary: 'Relatórios'
    };
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show mb-0 relatorio-feedback-alert`;
    alerta.setAttribute('role', tipo === 'danger' || tipo === 'warning' ? 'alert' : 'status');
    elemento.dataset.mensagem = mensagem;
    elemento.dataset.tipoMensagem = tipo;

    const titulo = document.createElement('strong');
    titulo.textContent = `${titulos[tipo] || 'Aviso'}: `;
    alerta.appendChild(titulo);
    alerta.appendChild(document.createTextNode(mensagem));

    const botaoFechar = document.createElement('button');
    botaoFechar.type = 'button';
    botaoFechar.className = 'btn-close';
    botaoFechar.setAttribute('aria-label', 'Fechar aviso');
    botaoFechar.addEventListener('click', function () {
        window.clearTimeout(temporizadorNotificacaoRelatorio);
        elemento.replaceChildren();
    });
    alerta.appendChild(botaoFechar);
    elemento.appendChild(alerta);

    if (autoFechar) {
        temporizadorNotificacaoRelatorio = window.setTimeout(function () {
            if (alerta.parentElement === elemento) {
                elemento.replaceChildren();
            }
        }, 5000);
    }
}

// Filtra as linhas da tabela pelos campos de busca e tipo.
function filtrarLinhasRelatorio() {
    if (!tabelaRelatorios || !buscaRelatorios) return;

    const termo = buscaRelatorios.value.trim().toLocaleLowerCase();
    const tipo = filtroTipoRelatorio ? filtroTipoRelatorio.value : 'todos';
    tabelaRelatorios.querySelectorAll('tr[data-relatorio]').forEach((linha) => {
        const textoBusca = `${linha.dataset.relatorioId || ''} ${linha.dataset.relatorioFalhaId || ''} ${linha.dataset.relatorioTipo || linha.dataset.tipo || ''}`.toLocaleLowerCase();
        const correspondeBusca = termo === '' || textoBusca.includes(termo);
        const correspondeTipo = tipo === 'todos' || (linha.dataset.relatorioTipo || linha.dataset.tipo) === tipo;
        linha.hidden = !correspondeBusca || !correspondeTipo;
    });
}

// Converte uma data no formato YYYY-MM-DD para o formato brasileiro.
function formatarData(data) {
    if (!data) return '';
    const [ano, mes, dia] = String(data).split('-');
    if (!ano || !mes || !dia) return data;
    return `${dia}/${mes}/${ano}`;
}

// Une as datas inicial e final em uma representação legível.
function formatarPeriodo(dataInicio, dataFim) {
    const inicio = formatarData(dataInicio);
    const fim = formatarData(dataFim);
    if (!inicio && !fim) return '';
    if (!inicio) return fim;
    if (!fim) return inicio;
    return `${inicio} - ${fim}`;
}

// Reconstrói a tabela usando os objetos de relatório recebidos da API.
function renderizarRelatorios(relatorios) {
    if (!tabelaRelatorios) return;

    tabelaRelatorios.replaceChildren();
    if (relatorios.length === 0) {
        const linha = document.createElement('tr');
        const celula = document.createElement('td');
        celula.colSpan = 5;
        celula.className = 'text-center text-muted';
        celula.textContent = 'Nenhum relatório encontrado para os filtros selecionados.';
        linha.appendChild(celula);
        tabelaRelatorios.appendChild(linha);
        return;
    }

    relatorios.forEach((relatorio) => {
        const linha = document.createElement('tr');
        linha.dataset.relatorio = 'true';
        linha.dataset.relatorioId = String(relatorio.relatorio_id ?? '');
        linha.dataset.relatorioFalhaId = String(relatorio.falha_id ?? '');
        linha.dataset.relatorioTipo = relatorio.tipo_dado || 'tipo';
        linha.dataset.tipo = relatorio.tipo_dado || 'tipo';
        linha.dataset.dataInicio = relatorio.data_inicio || '';
        linha.dataset.dataFim = relatorio.data_fim || '';

        const colunas = [
            String(relatorio.relatorio_id ?? ''),
            formatarData(relatorio.data_relatorio),
            relatorio.tipo_dado || 'Tipo de falha',
            formatarPeriodo(relatorio.data_inicio, relatorio.data_fim),
            ''
        ];

        colunas.forEach((valor, indice) => {
            const celula = document.createElement('td');
            if (indice === 4) {
                celula.className = 'acoes-relatorio';

                if (isAdmin) {
                    const botaoExcluir = document.createElement('button');
                    botaoExcluir.type = 'button';
                    botaoExcluir.className = 'btn_delete';
                    botaoExcluir.setAttribute('aria-label', 'Excluir relatório');
                    botaoExcluir.dataset.relatorioExcluir = String(relatorio.relatorio_id ?? '');
                    botaoExcluir.innerHTML = '<i class="bi bi-trash"></i>';
                    celula.appendChild(botaoExcluir);
                }

                const botaoDetalhes = document.createElement('a');
                botaoDetalhes.href = `detalhes_relatorio.php?id=${encodeURIComponent(String(relatorio.relatorio_id ?? ''))}`;
                botaoDetalhes.className = 'trens-card-acao';
                botaoDetalhes.setAttribute('aria-label', 'Detalhes do relatório');
                botaoDetalhes.title = 'Detalhes do relatório';
                botaoDetalhes.innerHTML = '<i class="bi bi-info-lg"></i>';

                celula.appendChild(botaoDetalhes);
            } else {
                celula.textContent = String(valor ?? '');
            }
            linha.appendChild(celula);
        });

        tabelaRelatorios.appendChild(linha);
    });

    filtrarLinhasRelatorio();
}

// API interna de consulta: envia os filtros pela URL e transforma o JSON em linhas da tabela.
// Consulta relatórios no PHP conforme os filtros enviados pela URL.
async function consultarRelatorios(filtros, mensagemElemento) {
    if (!tabelaRelatorios) return false;

    const parametros = new URLSearchParams(filtros);
    const botao = mensagemElemento?.closest('form')?.querySelector('[type="submit"]');
    if (botao) botao.disabled = true;
    tabelaRelatorios.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Consultando relatórios...</td></tr>';
    mostrarMensagemRelatorio(mensagemElemento, 'Consultando dados no banco...', 'secondary', false);

    try {
        const resposta = await fetch(`gerar_relatorio.php?${parametros.toString()}`, {
            headers: { Accept: 'application/json' },
            cache: 'no-store'
        });
        const resultado = await resposta.json();
        if (!resposta.ok || !resultado.success) {
            throw new Error(resultado.message || 'Não foi possível consultar os relatórios.');
        }

        renderizarRelatorios(resultado.data);
        const quantidade = resultado.data.length;
        mostrarMensagemRelatorio(
            mensagemElemento,
            quantidade
                ? `${quantidade} registro(s) encontrado(s).`
                : 'Nenhum registro encontrado para os filtros selecionados.',
            quantidade ? 'success' : 'warning'
        );
        return true;
    } catch (erro) {
        tabelaRelatorios.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Falha ao carregar os relatórios.</td></tr>';
        mostrarMensagemRelatorio(mensagemElemento, erro.message, 'danger');
        return false;
    } finally {
        if (botao) botao.disabled = false;
    }
}

// API interna de criação: envia os filtros por POST e interpreta a resposta JSON do PHP.
// Cria relatórios no PHP e atualiza a tabela com o resultado JSON.
async function criarRelatorios(filtros, mensagemElemento) {
    const formulario = mensagemElemento?.closest('form');
    const botao = formulario?.querySelector('[type="submit"]');
    if (botao) botao.disabled = true;
    mostrarMensagemRelatorio(mensagemElemento, 'Criando relatório...', 'secondary', false);

    try {
        const resposta = await fetch('gerar_relatorio.php', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams(filtros),
            cache: 'no-store'
        });
        const resultado = await resposta.json();
        if (!resposta.ok || !resultado.success) {
            throw new Error(resultado.message || 'Não foi possível criar o relatório.');
        }

        const existentes = Array.from(tabelaRelatorios.querySelectorAll('tr[data-relatorio]'), (linha) => ({
            relatorio_id: linha.dataset.relatorioId,
            data_relatorio: linha.dataset.relatorioData,
            falha_id: linha.dataset.relatorioFalhaId,
            tipo_dado: linha.dataset.relatorioTipo,
            data_inicio: linha.dataset.relatorioInicio,
            data_fim: linha.dataset.relatorioFim
        }));
        renderizarRelatorios([...resultado.data, ...existentes]);
        mostrarMensagemRelatorio(
            mensagemElemento,
            `${resultado.message} ${resultado.data.length} registro(s) criado(s).`,
            resultado.message.includes('não permite salvar o período') ? 'warning' : 'success'
        );
        return true;
    } catch (erro) {
        mostrarMensagemRelatorio(mensagemElemento, erro.message, 'danger');
        return false;
    } finally {
        if (botao) botao.disabled = false;
    }
}

// Liga um formulário de relatório às ações de criação e validação.
function configurarFormularioRelatorio(formulario, prefixo, mensagemId) {
    if (!formulario) return;

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        const mensagem = document.getElementById(mensagemId);
        const dataInicio = document.getElementById(`${prefixo}data_inicio`).value;
        const dataFim = document.getElementById(`${prefixo}data_fim`).value;

        if (dataInicio > dataFim) {
            mostrarMensagemRelatorio(mensagem, 'A data inicial não pode ser maior que a final.', 'danger');
            return;
        }

        const criado = await criarRelatorios({
            trem: document.getElementById(`${prefixo}trem`).value,
            tipo_dado: document.getElementById(`${prefixo}tipo_dado`).value,
            data_inicio: dataInicio,
            data_fim: dataFim
        }, mensagem);

        if (criado && formulario.id === 'form_relatorio_modal') {
            const modalElemento = document.getElementById('modal_relatorio');
            if (typeof bootstrap !== 'undefined' && modalElemento) {
                bootstrap.Modal.getOrCreateInstance(modalElemento).hide();
            }
            const mensagemPrincipal = document.getElementById('mensagem_relatorio');
            mostrarMensagemRelatorio(
                mensagemPrincipal,
                mensagem.dataset.mensagem || 'Relatório criado com sucesso.',
                mensagem.dataset.tipoMensagem || 'success'
            );
            mensagem.replaceChildren();
        }
    });
}

configurarFormularioRelatorio(
    document.getElementById('form_relatorio'),
    'relatorio_',
    'mensagem_relatorio'
);
configurarFormularioRelatorio(
    document.getElementById('form_relatorio_modal'),
    'relatorio_modal_',
    'mensagem_relatorio_modal'
);

// Exportação: usa a mesma API interna, mas recebe o arquivo como blob em vez de JSON.
const botaoExportarRelatorio = document.getElementById('btn_exportar_relatorio');
const modalFormatoRelatorio = document.getElementById('modal_formato_relatorio');
if (botaoExportarRelatorio && modalFormatoRelatorio) {
    modalFormatoRelatorio.querySelectorAll('[data-formato-relatorio]').forEach((botaoFormato) => {
        botaoFormato.addEventListener('click', async () => {
            const formato = botaoFormato.dataset.formatoRelatorio;
            const mensagem = document.getElementById('mensagem_relatorio');
            const dataInicio = document.getElementById('relatorio_data_inicio').value;
            const dataFim = document.getElementById('relatorio_data_fim').value;

            if (Boolean(dataInicio) !== Boolean(dataFim)) {
                bootstrap.Modal.getOrCreateInstance(modalFormatoRelatorio).hide();
                mostrarMensagemRelatorio(mensagem, 'Informe as duas datas do período.', 'danger');
                return;
            }
            if (dataInicio && dataInicio > dataFim) {
                bootstrap.Modal.getOrCreateInstance(modalFormatoRelatorio).hide();
                mostrarMensagemRelatorio(mensagem, 'A data inicial não pode ser maior que a final.', 'danger');
                return;
            }

            const parametros = new URLSearchParams({
                trem: document.getElementById('relatorio_trem').value,
                tipo_dado: document.getElementById('relatorio_tipo_dado').value,
                data_inicio: dataInicio,
                data_fim: dataFim,
                formato
            });

            bootstrap.Modal.getOrCreateInstance(modalFormatoRelatorio).hide();
            botaoFormato.disabled = true;
            mostrarMensagemRelatorio(mensagem, `Preparando o arquivo ${formato.toUpperCase()}...`, 'secondary', false);

            try {
                const resposta = await fetch(`gerar_relatorio.php?${parametros.toString()}`, {
                    headers: { Accept: formato === 'pdf' ? 'application/pdf' : 'text/csv' },
                    cache: 'no-store'
                });
                if (!resposta.ok) {
                    const resultado = await resposta.json().catch(() => null);
                    throw new Error(resultado?.message || 'Não foi possível baixar o relatório.');
                }

                const arquivo = await resposta.blob();
                const url = URL.createObjectURL(arquivo);
                const link = document.createElement('a');
                const disposicao = resposta.headers.get('Content-Disposition') || '';
                const nomeArquivo = disposicao.match(/filename="?([^";]+)"?/i)?.[1] || `relatorio.${formato}`;
                link.href = url;
                link.download = nomeArquivo;
                document.body.appendChild(link);
                link.click();
                link.remove();
                setTimeout(() => URL.revokeObjectURL(url), 1000);
                mostrarMensagemRelatorio(mensagem, `Download do ${formato.toUpperCase()} iniciado.`, 'success');
            } catch (erro) {
                mostrarMensagemRelatorio(mensagem, erro.message, 'danger');
            } finally {
                botaoFormato.disabled = false;
            }
        });
    });
}

if (botaoBuscaRelatorios) {
    botaoBuscaRelatorios.addEventListener('click', (evento) => {
        evento.preventDefault();
        if (buscaRelatorios && buscaRelatorios.form) {
            buscaRelatorios.form.submit();
        }
    });
}

const modalConfirmDelete = document.getElementById('modal_confirm_delete');
if (modalConfirmDelete && typeof bootstrap !== 'undefined') {
    const bsModal = new bootstrap.Modal(modalConfirmDelete);
    
    document.addEventListener('click', function (e) {
        const btn = e.target.closest && e.target.closest('[data-relatorio-excluir]');
        if (!btn) return;
        
        const id = btn.getAttribute('data-relatorio-excluir') || '';
        if (!id) return;
        
        const input = document.getElementById('delete_relatorio_id');
        if (input) input.value = id;
        
        const textEl = document.getElementById('modal_confirm_text');
        if (textEl) textEl.textContent = 'Deseja realmente excluir o relatório de ID ' + id + '?';
        
        bsModal.show();
    });
}
