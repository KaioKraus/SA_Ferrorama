// Configurações e constantes
const LIMITES = {
    TEMPERATURA_MAX: 85, // °C - temperatura máxima segura
};

const INTERVALO_ATUALIZACAO = 5000; // 5 segundos

// Elementos do DOM
const metricaTemperatura = document.getElementById('metrica_temperatura');
const metricaConsumo = document.getElementById('metrica_consumo');
const medidorTemperatura = document.getElementById('medidor_temperatura');
const barraTemperatura = document.getElementById('barra_temperatura');
const mensagemConsumo = document.getElementById('mensagem-grafico-consumo');
const avisoConsumoDemo = document.getElementById('aviso-consumo-demo');
const statusIcone = document.getElementById('status_icone');
const statusTexto = document.getElementById('status_texto');
const cardStatus = statusIcone?.closest('.dashboard-card-status');

// Gráficos
let miniGraficoConsumo;
let graficoVelocidade;
let quantidadePontos = 12;
let cores = [
    '#0A3D3D', '#599967', '#D97706', '#2563EB', '#9333EA',
    '#DC2626', '#0891B2', '#4D7C0F', '#C026D3', '#475569'
];

// Função para criar mini gráfico
function criarMiniGrafico(canvasId, cor) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                data: [],
                borderColor: cor,
                backgroundColor: 'rgba(89, 153, 103, 0.22)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            animation: { duration: 0 }
        }
    });
}

// Função para atualizar mini gráfico
function atualizarMiniGrafico(grafico, novoValor, rotuloHorario) {
    if (!grafico) return;

    grafico.data.labels.push(rotuloHorario);
    grafico.data.datasets[0].data.push(novoValor);
    if (grafico.data.labels.length > quantidadePontos) {
        grafico.data.labels.shift();
        grafico.data.datasets[0].data.shift();
    }
    grafico.update('none');
}

// Função para determinar status do sistema
function determinarStatus(temperatura) {
    return temperatura > LIMITES.TEMPERATURA_MAX ? 'ALERTA' : 'OPERACIONAL';
}

// Função para atualizar indicadores visuais
function atualizarIndicadores(temperatura, consumo, rotuloHorario, historicoConsumo) {
    if (metricaTemperatura) {
        metricaTemperatura.textContent = temperatura.toFixed(0);
    }
    if (metricaConsumo) {
        metricaConsumo.textContent = consumo.toFixed(0);
    }
    if (medidorTemperatura && barraTemperatura) {
        const temperaturaLimitada = Math.max(0, Math.min(temperatura, LIMITES.TEMPERATURA_MAX));
        medidorTemperatura.setAttribute('aria-valuenow', temperaturaLimitada.toFixed(0));
        barraTemperatura.style.width = (temperaturaLimitada / LIMITES.TEMPERATURA_MAX * 100) + '%';
    }

    if (miniGraficoConsumo && Array.isArray(historicoConsumo) && historicoConsumo.length > 0) {
        miniGraficoConsumo.data.labels = historicoConsumo.map(function (ponto) {
            return ponto.horario;
        });
        miniGraficoConsumo.data.datasets[0].data = historicoConsumo.map(function (ponto) {
            return ponto.valor;
        });
        miniGraficoConsumo.update('none');
    } else {
        atualizarMiniGrafico(miniGraficoConsumo, consumo, rotuloHorario);
    }
    
    const status = determinarStatus(temperatura);
    const eAlerta = status === 'ALERTA';
    
    if (statusTexto) {
        statusTexto.textContent = status;
    }
    
    if (cardStatus) {
        if (eAlerta) {
            cardStatus.classList.add('alerta');
        } else {
            cardStatus.classList.remove('alerta');
        }
    }
    
    if (statusIcone) {
        if (eAlerta) {
            statusIcone.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
        } else {
            statusIcone.innerHTML = '<i class="bi bi-check-lg"></i>';
        }
    }
}

// Função para atualizar gráfico de velocidade
let atualizando = false;
function atualizarVelocidades() {
    if (atualizando) return;
    atualizando = true;

    const status = document.getElementById('status-grafico-velocidade');
    const mensagem = document.getElementById('mensagem-grafico-velocidade');

    fetch('dados_velocidade.php', {
        cache: 'no-store',
        headers: { 'Accept': 'application/json' }
    })
    .then(function (response) {
        return response.json().then(function (data) {
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Falha ao consultar os dados.');
            }
            return data;
        });
    })
    .then(function (data) {
        const rotuloHorario = data.timestamp;
        const trensAtualizados = new Map(data.trens.map(function (trem) {
            return [String(trem.id), trem];
        }));

        graficoVelocidade.data.datasets = graficoVelocidade.data.datasets.filter(function (serie) {
            return trensAtualizados.has(serie.tremId);
        });
        graficoVelocidade.data.labels.push(rotuloHorario);
        
        let somaTemp = 0;
        let somaConsumo = 0;
        let count = 0;
        
        data.trens.forEach(function (trem) {
            const id = String(trem.id);
            let serie = graficoVelocidade.data.datasets.find(function (dataset) {
                return dataset.tremId === id;
            });

            if (!serie) {
                const indiceCor = graficoVelocidade.data.datasets.length % cores.length;
                serie = {
                    tremId: id,
                    label: trem.nome,
                    data: Array(graficoVelocidade.data.labels.length - 1).fill(null),
                    borderColor: cores[indiceCor],
                    backgroundColor: cores[indiceCor],
                    tension: 0.25,
                    spanGaps: false
                };
                graficoVelocidade.data.datasets.push(serie);
            }

            serie.label = trem.nome;
            somaTemp += parseFloat(trem.temperatura) || 0;
            somaConsumo += parseFloat(trem.consumo) || 0;
            count++;
        });

        graficoVelocidade.data.datasets.forEach(function (serie) {
            const trem = trensAtualizados.get(serie.tremId);
            serie.data.push(trem ? trem.velocidade : null);
            if (serie.data.length > quantidadePontos) {
                serie.data.shift();
            }
        });

        if (graficoVelocidade.data.labels.length > quantidadePontos) {
            graficoVelocidade.data.labels.shift();
        }

        graficoVelocidade.update('none');
        
        if (count > 0) {
            const tempMedia = somaTemp / count;
            const consumoMedio = somaConsumo / count;
            atualizarIndicadores(tempMedia, consumoMedio, rotuloHorario, data.historico_consumo);
            if (mensagemConsumo) {
                mensagemConsumo.hidden = true;
                mensagemConsumo.textContent = '';
            }
        } else if (mensagemConsumo) {
            mensagemConsumo.textContent = 'Nenhum trem cadastrado.';
            mensagemConsumo.hidden = false;
        }
        if (avisoConsumoDemo) {
            avisoConsumoDemo.hidden = !data.consumo_simulado;
        }
        
        if (mensagem) {
            mensagem.hidden = data.trens.length > 0;
            mensagem.textContent = data.trens.length > 0 ? '' : 'Nenhum trem cadastrado.';
        }
    })
    .catch(function (error) {
        console.error('Erro ao atualizar:', error);
        if (status) status.textContent = 'Falha na atualização';
        if (mensagemConsumo) {
            mensagemConsumo.textContent = error.message || 'Não foi possível atualizar o consumo.';
            mensagemConsumo.hidden = false;
        }
        if (mensagem) {
            mensagem.textContent = error.message || 'Não foi possível atualizar.';
            mensagem.hidden = false;
        }
    })
    .finally(function () {
        atualizando = false;
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js não está carregado');
        return;
    }
    
    // Criar gráfico principal de velocidade SEM legenda
    const canvas = document.getElementById('grafico_velocidade');
    if (canvas) {
        graficoVelocidade = new Chart(canvas, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false  // LEGENDA DESABILITADA
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' km/h';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: false  // REMOVE HORÁRIOS DO EIXO X
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Criar mini gráficos
    miniGraficoConsumo = criarMiniGrafico('mini_grafico_consumo', '#111111');
    
    // Buscar dados inicial
    atualizarVelocidades();
    
    // Atualizar periodicamente
    setInterval(atualizarVelocidades, INTERVALO_ATUALIZACAO);
});
