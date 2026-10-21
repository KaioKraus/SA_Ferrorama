document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('grafico_velocidade');
    var status = document.getElementById('status-grafico-velocidade');
    var mensagem = document.getElementById('mensagem-grafico-velocidade');

    if (!canvas || !status || !mensagem) return;

    if (typeof Chart === 'undefined') {
        status.textContent = 'Gráfico indisponível';
        mensagem.textContent = 'Não foi possível carregar a biblioteca do gráfico.';
        mensagem.hidden = false;
        return;
    }

    var quantidadePontos = 12;
    var cores = [
        '#0A3D3D', '#599967', '#D97706', '#2563EB', '#9333EA',
        '#DC2626', '#0891B2', '#4D7C0F', '#C026D3', '#475569'
    ];
    var grafico = new Chart(canvas, {
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
                    position: 'bottom'
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
                    title: {
                        display: true,
                        text: 'Horário da atualização'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Velocidade (km/h)'
                    }
                }
            }
        }
    });

    var atualizando = false;

    function atualizarVelocidades() {
        if (atualizando) return;
        atualizando = true;

        fetch('dados_velocidade.php', {
            cache: 'no-store',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function (response) {
            return response.json().then(function (data) {
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Falha ao consultar os dados de velocidade.');
                }
                return data;
            });
        })
        .then(function (data) {
            var rotuloHorario = data.timestamp;
            var trensAtualizados = new Map(data.trens.map(function (trem) {
                return [String(trem.id), trem];
            }));

            grafico.data.datasets = grafico.data.datasets.filter(function (serie) {
                return trensAtualizados.has(serie.tremId);
            });
            grafico.data.labels.push(rotuloHorario);
            data.trens.forEach(function (trem) {
                var id = String(trem.id);
                var serie = grafico.data.datasets.find(function (dataset) {
                    return dataset.tremId === id;
                });

                if (!serie) {
                    var indiceCor = grafico.data.datasets.length % cores.length;
                    serie = {
                        tremId: id,
                        label: trem.nome,
                        data: Array(grafico.data.labels.length - 1).fill(null),
                        borderColor: cores[indiceCor],
                        backgroundColor: cores[indiceCor],
                        tension: 0.25,
                        spanGaps: false
                    };
                    grafico.data.datasets.push(serie);
                }

                serie.label = trem.nome;
            });

            grafico.data.datasets.forEach(function (serie) {
                var trem = trensAtualizados.get(serie.tremId);
                serie.data.push(trem ? trem.velocidade : null);
                if (serie.data.length > quantidadePontos) {
                    serie.data.shift();
                }
            });

            if (grafico.data.labels.length > quantidadePontos) {
                grafico.data.labels.shift();
            }

            grafico.update('none');
            status.textContent = 'Atualizado às ' + rotuloHorario;
            mensagem.hidden = data.trens.length > 0;
            mensagem.textContent = data.trens.length > 0 ? '' : 'Nenhum trem cadastrado para exibir.';
        })
        .catch(function (error) {
            console.error('Erro ao atualizar gráfico de velocidade:', error);
            status.textContent = 'Falha na atualização';
            mensagem.textContent = error.message || 'Não foi possível atualizar as velocidades.';
            mensagem.hidden = false;
        })
        .finally(function () {
            atualizando = false;
        });
    }

    atualizarVelocidades();
    window.setInterval(atualizarVelocidades, 5000);
});
