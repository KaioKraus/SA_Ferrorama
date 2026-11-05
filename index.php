<?php
$conexao = @mysqli_connect('127.0.0.1', 'root', '', 'celestial_steel_db', 3309);
if ($conexao) {
    $conexao->set_charset('utf8mb4');
}

$dadosLanding = [
    'usuarios' => 0,
    'trens' => 0,
    'rotas' => 0,
    'sensores' => 0,
    'estacoes' => 0,
    'falhas' => 0,
];

$trensEmOperacao = [
    ['nome' => 'Trem Expresso Norte', 'velocidade' => '112 km/h'],
    ['nome' => 'Trem Noturno Central', 'velocidade' => '94 km/h'],
    ['nome' => 'Trem Regional Sul', 'velocidade' => '78 km/h'],
];

$rotasAtivas = [
    ['nome' => 'Rota Norte Expressa', 'trecho' => 'Estação Central → Estação Norte'],
    ['nome' => 'Rota Sul Regional', 'trecho' => 'Estação Central → Estação Sul'],
    ['nome' => 'Rota Transversal', 'trecho' => 'Estação Leste → Estação Oeste'],
];

$sensoresAtivos = [
    'Sensor de Temperatura 01',
    'Sensor de Peso 01',
    'Sensor de Proximidade 01',
    'Sensor de Temperatura 04',
];

const DADOS_FALLBACK = [
    'usuarios' => 40,
    'trens' => 7,
    'rotas' => 6,
    'sensores' => 10,
    'estacoes' => 9,
    'falhas' => 10,
];

if ($conexao && !mysqli_connect_errno()) {
    $consultas = [
        'usuarios' => 'SELECT COUNT(*) AS total FROM usuarios',
        'trens' => 'SELECT COUNT(*) AS total FROM trens',
        'rotas' => 'SELECT COUNT(*) AS total FROM rotas',
        'sensores' => 'SELECT COUNT(*) AS total FROM sensores',
        'estacoes' => 'SELECT COUNT(*) AS total FROM estacoes',
        'falhas' => 'SELECT COUNT(*) AS total FROM falha',
    ];

    foreach ($consultas as $chave => $sql) {
        $resultado = mysqli_query($conexao, $sql);
        if ($resultado) {
            $linha = mysqli_fetch_assoc($resultado);
            $dadosLanding[$chave] = (int) ($linha['total'] ?? 0);
        }
    }

    $resultadoTrens = mysqli_query($conexao, 'SELECT nome_trem, velocidade, temperatura, consumo FROM trens ORDER BY trem_id LIMIT 3');
    if ($resultadoTrens) {
        $trensEmOperacao = [];
        while ($linha = mysqli_fetch_assoc($resultadoTrens)) {
            $trensEmOperacao[] = [
                'nome' => $linha['nome_trem'],
                'velocidade' => number_format((float) $linha['velocidade'], 0, ',', '.') . ' km/h',
            ];
        }
    }

    $resultadoRotas = mysqli_query($conexao, 'SELECT nome, ponto_partida, ponto_chegada FROM rotas ORDER BY rota_id LIMIT 3');
    if ($resultadoRotas) {
        $rotasAtivas = [];
        while ($linha = mysqli_fetch_assoc($resultadoRotas)) {
            $rotasAtivas[] = [
                'nome' => $linha['nome'],
                'trecho' => $linha['ponto_partida'] . ' → ' . $linha['ponto_chegada'],
            ];
        }
    }

    $resultadoSensores = mysqli_query($conexao, 'SELECT nome_sensor FROM sensores ORDER BY sensor_id LIMIT 4');
    if ($resultadoSensores) {
        $sensoresAtivos = [];
        while ($linha = mysqli_fetch_assoc($resultadoSensores)) {
            $sensoresAtivos[] = $linha['nome_sensor'];
        }
    }
} else {
    $dadosLanding = DADOS_FALLBACK;
}

$rotasAtivas = array_slice($rotasAtivas, 0, 3);
$trensEmOperacao = array_slice($trensEmOperacao, 0, 3);
$sensoresAtivos = array_slice($sensoresAtivos, 0, 4);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celestial Steel | Monitoramento ferroviário</title>
    <meta name="description" content="Celestial Steel é a solução tecnológica para monitoramento ferroviário, sensores e acompanhamento operacional em tempo real.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.4/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body class="landing-body">
    <header class="landing-header">
        <div class="container">
            <a class="landing-brand" href="#top" aria-label="Ferrorama home">
                <img src="assets/img/logo.svg" alt="Logo do Ferrorama">
                <span>Celestial Steel</span>
            </a>

            <nav class="landing-nav" aria-label="Menu principal">
                <a href="#problema">Problema</a>
                <a href="#funcionalidades">Funcionalidades</a>
                <a href="#beneficios">Benefícios</a>
            </nav>

            <a href="public/tela_login.php" class="landing-btn">Entrar</a>
        </div>
    </header>

    <main id="top">
        <section class="landing-section landing-hero">
            <div class="landing-hero-grid">
                <div>
                    <span class="landing-kicker"><i class="bi bi-diagram-3-fill"></i> Monitoramento ferroviário</span>
                    <h1 class="landing-title">Celestial Steel: inteligência e <strong>segurança</strong> para a operação ferroviária.</h1>
                    <p class="landing-subtitle">
                        A Celestial Steel reúne monitoramento de trens, sensores, rotas e indicadores operacionais em uma solução tecnológica centralizada,
                        ajudando equipes a acompanhar a malha ferroviária com mais clareza, previsibilidade e agilidade.
                    </p>

                    <ul class="landing-checks">
                        <li><i class="bi bi-check-circle-fill"></i> Acompanhamento de trens e rotas em operação</li>
                        <li><i class="bi bi-check-circle-fill"></i> Visibilidade de sensores e falhas operacionais</li>
                        <li><i class="bi bi-check-circle-fill"></i> Apoio à decisão com dados organizados e confiáveis</li>
                    </ul>
                </div>

                <aside class="landing-panel" aria-label="Dados operacionais em tempo real">
                    <div class="landing-metrics">
                        <div class="landing-stat">
                            <strong><?php echo htmlspecialchars((string) $dadosLanding['trens']); ?></strong>
                        </div>
                        <div class="landing-stat">
                            <strong><?php echo htmlspecialchars((string) $dadosLanding['rotas']); ?></strong>
                        </div>
                        <div class="landing-stat">
                            <strong><?php echo htmlspecialchars((string) $dadosLanding['sensores']); ?></strong>
                        </div>
                        <div class="landing-stat">
                            <strong><?php echo htmlspecialchars((string) $dadosLanding['estacoes']); ?></strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section id="problema" class="landing-section section-layout-stack">
            <div class="section-heading">
                <h2>O problema que a Celestial Steel busca resolver</h2>
                <p>
                    A operação ferroviária depende de rapidez, confiabilidade e visibilidade. Quando trens, sensores e rotas não são monitorados de forma integrada,
                    falhas podem passar despercebidas e afetar segurança, tempo de resposta e eficiência operacional.
                </p>
            </div>

            <div class="problem-grid">
                <article class="problem-card">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h3>Falta de visibilidade</h3>
                    <p>Operadores precisam acompanhar múltiplas informações sem perder contexto sobre eventos críticos, condições de rota e status dos ativos.</p>
                </article>

                <article class="problem-card">
                    <i class="bi bi-graph-up-arrow"></i>
                    <h3>Risco à eficiência</h3>
                    <p>Sem um painel centralizado, a análise de dados e a tomada de decisão ficam mais lentas, impactando planejamento e execução das operações.</p>
                </article>

                <article class="problem-card">
                    <i class="bi bi-shield-check"></i>
                    <h3>Impacto na segurança</h3>
                    <p>O monitoramento adequado dos sensores e das condições do trem evita falhas não tratadas e fortalece a resposta a ocorrências operacionais.</p>
                </article>
            </div>
        </section>

        <section id="funcionalidades" class="landing-section section-layout-stack">
            <div class="section-heading">
                <h2>Principais funcionalidades</h2>
                <p>
                    O sistema foi pensado para reunir as informações mais relevantes da operação e entregar uma visão objetiva do desempenho da frota,
                    das rotas e dos sensores cadastrados no banco de dados.
                </p>
            </div>

            <div class="feature-grid">
                <article class="feature-card">
                    <i class="bi bi-train-front-fill"></i>
                    <h3>Monitoramento ferroviário</h3>
                    <p>O sistema organiza a operação com foco em trens, rotas e status das atividades, permitindo acompanhamento funcional da malha ferroviária.</p>
                    <ul class="feature-list">
                        <li>Controle do status operacional</li>
                        <li>Visualização de velocidade e desempenho</li>
                        <li>Indicadores de presença e atividade</li>
                    </ul>
                </article>

                <article class="feature-card">
                    <i class="bi bi-geo-alt-fill"></i>
                    <h3>Rotas e trajetos</h3>
                    <p>As rotas cadastradas conectam pontos de partida e chegada, auxiliando no acompanhamento de trechos, trajetos e linhas operacionais.</p>
                    <ul class="feature-list">
                        <li>Mapeamento da malha ferroviária</li>
                        <li>Consulta por trechos e destinos</li>
                        <li>Gestão do fluxo operacional</li>
                    </ul>
                </article>

                <article class="feature-card">
                    <i class="bi bi-activity"></i>
                    <h3>Sensores e informações operacionais</h3>
                    <p>Os sensores capturam dados de temperatura, peso, proximidade e outros indicadores relevantes para a supervisão técnica e a operação.</p>
                    <ul class="feature-list">
                        <li>Coleta de dados em tempo real</li>
                        <li>Monitoramento de condições críticas</li>
                        <li>Suporte à análise e resposta rápida</li>
                    </ul>
                </article>
            </div>
        </section>

        <section id="beneficios" class="landing-section section-layout-stack">
            <div class="section-heading">
                <h2>Benefícios da Celestial Steel</h2>
                <p>
                    A solução foi desenhada para otimizar o acompanhamento operacional, reduzir riscos e facilitar a atuação das equipes em tarefas diárias do monitoramento.
                </p>
            </div>

            <div class="benefit-grid">
                <article class="benefit-card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h3>Segurança</h3>
                    <p>O monitoramento dos trens e dos sensores permite detectar alertas e agir com mais rapidez antes que pequenas anomalias se transformem em falhas maiores.</p>
                </article>

                <article class="benefit-card">
                    <i class="bi bi-speedometer2"></i>
                    <h3>Eficiência</h3>
                    <p>A centralização das informações melhora o planejamento, reduz retrabalho e ajuda a manter a operação mais estável e previsível.</p>
                </article>

                <article class="benefit-card">
                    <i class="bi bi-broadcast-pin"></i>
                    <h3>Acompanhamento operacional</h3>
                    <p>Com uma visão clara do status dos ativos, das rotas e dos indicadores, a operação fica mais transparente para quem toma decisões e acompanha o cotidiano.</p>
                </article>
            </div>
        </section>

        <section class="landing-section landing-cta">
            <div class="landing-cta-box">
                <div class="landing-cta-copy">
                    <h2>Pronto para controlar a operação com mais clareza?</h2>
                    <p>
                        Acesse o sistema para acompanhar trens, sensores, rotas e indicadores em uma visão centralizada da operação ferroviária.
                    </p>
                </div>

                <div class="landing-actions">
                    <a href="#funcionalidades" class="landing-btn-secondary">Conhecer funcionalidades</a>
                    <a href="public/tela_login.php" class="landing-btn">Acessar o sistema</a>
                </div>
            </div>
        </section>

    </main>

    <footer class="landing-footer">
        Celestial Steel · Monitoramento ferroviário com dados da operação e visão integrada do sistema.
    </footer>
</body>

</html>

