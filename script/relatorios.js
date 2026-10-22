const tabelaRelatorios = document.getElementById('tbody_relatorios');
const buscaRelatorios = document.getElementById('input_busca_relatorio');
const botaoBuscaRelatorios = document.getElementById('btn_busca_relatorio');
const filtroTipoRelatorio = document.getElementById('filtro_tipo_relatorio');

function mostrarMensagemRelatorio(elemento, mensagem, tipo) {
    if (!elemento) return;
    elemento.textContent = mensagem;
    elemento.className = `mt-3 text-center fw-bold text-${tipo}`;
}

function filtrarLinhasRelatorio() {
    if (!tabelaRelatorios || !buscaRelatorios) return;

    const termo = buscaRelatorios.value.trim().toLocaleLowerCase();
    const tipo = filtroTipoRelatorio ? filtroTipoRelatorio.value : 'todos';
    tabelaRelatorios.querySelectorAll('tr[data-relatorio]').forEach((linha) => {
        const correspondeBusca = termo === '' || linha.textContent.toLocaleLowerCase().includes(termo);
        const correspondeTipo = tipo === 'todos' || linha.dataset.tipo === tipo;
        linha.hidden = !correspondeBusca || !correspondeTipo;
    });
}

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
        linha.dataset.tipo = relatorio.tipo_dado;
        const data = String(relatorio.data_relatorio || '').split('-').reverse().join('/');
        const valores = [
            relatorio.relatorio_id,
            data,
            `${relatorio.nome_trem} (ID ${relatorio.trem_id})`,
            `${relatorio.nome_sensor} — ${relatorio.tipo_dado}`,
            `Falha #${relatorio.falha_id}`
        ];

        valores.forEach((valor) => {
            const celula = document.createElement('td');
            celula.textContent = String(valor ?? '');
            linha.appendChild(celula);
        });
        tabelaRelatorios.appendChild(linha);
    });

    filtrarLinhasRelatorio();
}

async function consultarRelatorios(filtros, mensagemElemento) {
    if (!tabelaRelatorios) return false;

    const parametros = new URLSearchParams(filtros);
    const botao = mensagemElemento?.closest('form')?.querySelector('[type="submit"]');
    if (botao) botao.disabled = true;
    tabelaRelatorios.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Consultando relatórios...</td></tr>';
    mostrarMensagemRelatorio(mensagemElemento, 'Consultando dados no banco...', 'secondary');

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

        const consultou = await consultarRelatorios({
            trem: document.getElementById(`${prefixo}trem`).value,
            sensor: document.getElementById(`${prefixo}sensor`).value,
            data_inicio: dataInicio,
            data_fim: dataFim
        }, mensagem);

        if (consultou && formulario.id === 'form_relatorio_modal') {
            const modalElemento = document.getElementById('modal_relatorio');
            if (typeof bootstrap !== 'undefined' && modalElemento) {
                bootstrap.Modal.getOrCreateInstance(modalElemento).hide();
            }
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
                sensor: document.getElementById('relatorio_sensor').value,
                data_inicio: dataInicio,
                data_fim: dataFim,
                formato
            });

            bootstrap.Modal.getOrCreateInstance(modalFormatoRelatorio).hide();
            botaoFormato.disabled = true;
            mostrarMensagemRelatorio(mensagem, `Preparando o arquivo ${formato.toUpperCase()}...`, 'secondary');

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
    botaoBuscaRelatorios.addEventListener('click', filtrarLinhasRelatorio);
}
if (buscaRelatorios) {
    buscaRelatorios.addEventListener('keydown', (evento) => {
        if (evento.key === 'Enter') {
            evento.preventDefault();
            filtrarLinhasRelatorio();
        }
    });
}
if (filtroTipoRelatorio) {
    filtroTipoRelatorio.addEventListener('change', filtrarLinhasRelatorio);
}

consultarRelatorios({ trem: 'todos', sensor: 'todos' }, null);
