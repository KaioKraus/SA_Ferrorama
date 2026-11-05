<?php
session_start();
include __DIR__ . '/validar_acesso.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';

$relatorioId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$relatorio = null;

if ($relatorioId !== false && $relatorioId !== null) {
    $sqlRelatorio = "
        SELECT r.relatorio_id, r.data_relatorio, r.data_inicio, r.data_fim,
               f.falha_id, f.trem_id, f.sensor_id,
               t.nome_trem, s.nome_sensor, s.tipo_dado, s.localizacao,
               s.status AS status_sensor
        FROM relatorio r
        LEFT JOIN falha f ON f.falha_id = r.falha_id
        LEFT JOIN trens t ON t.trem_id = f.trem_id
        LEFT JOIN sensores s ON s.sensor_id = f.sensor_id
        WHERE r.relatorio_id = ?
        LIMIT 1
    ";

    $stmtRelatorio = mysqli_prepare($conexao, $sqlRelatorio);
    if ($stmtRelatorio) {
        mysqli_stmt_bind_param($stmtRelatorio, 'i', $relatorioId);
        if (mysqli_stmt_execute($stmtRelatorio)) {
            $resultadoRelatorio = mysqli_stmt_get_result($stmtRelatorio);
            $relatorio = $resultadoRelatorio ? mysqli_fetch_assoc($resultadoRelatorio) : null;
            if ($resultadoRelatorio) {
                mysqli_free_result($resultadoRelatorio);
            }
        } else {
            error_log('Erro ao consultar relatório detalhado: ' . mysqli_stmt_error($stmtRelatorio));
        }
        mysqli_stmt_close($stmtRelatorio);
    }
}

function formatarDataRelatorio(string $valor): string
{
    if ($valor === '' || $valor === null) {
        return '—';
    }

    $data = DateTime::createFromFormat('Y-m-d', $valor);
    if (!$data) {
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }

    return $data->format('d/m/Y');
}

function formatarPeriodoRelatorio(?string $inicio, ?string $fim): string
{
    $inicioFormatado = $inicio ? formatarDataRelatorio($inicio) : '—';
    $fimFormatado = $fim ? formatarDataRelatorio($fim) : '—';

    if ($inicioFormatado === '—' && $fimFormatado === '—') {
        return '—';
    }

    if ($inicioFormatado === '—') {
        return $fimFormatado;
    }

    if ($fimFormatado === '—') {
        return $inicioFormatado;
    }

    return $inicioFormatado . ' - ' . $fimFormatado;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <title>Detalhes do Relatório</title>
</head>
<body>
    <main>
        <nav class="nav1">
            <img src="../assets/img/logo.svg" alt="logo" class="logo">
            <ul class="ul1">
                <li class="lista1" id="li1"><a href="dashboard.php" class="lista1">Dashboard</a></li>
                <hr class="hr1 opacity-100">
                <li class="lista" id="li1"><a href="tela_relatorios.php" class="lista">Relatórios</a></li>
                <hr class="hr1 opacity-100">
                <?php if ($usuarioEhAdministrador): ?>
                    <li class="lista1" id="li1"><a href="tela_cadastro_user.php" class="lista1">Usuários</a></li>
                    <hr class="hr1 opacity-100">
                <?php endif; ?>
                <li class="lista1" id="li1"><a href="tela_rotas.php" class="lista1">Rotas</a></li>
                <hr class="hr1 opacity-100">
                <li class="lista1" id="li1"><a href="tela_sensores.php" class="lista1">Sensores</a></li>
                <hr class="hr1 opacity-100">
                <li class="lista1" id="li1"><a href="tela_trens.php" class="lista1">Trens</a></li>
            </ul>

            <div class="dropdown">
                <div class="box_usuario dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <div class="img_usuario">
                        <img src="../assets/img/usuario.png" alt="usuario" class="img-fluid">
                    </div>
                    <div class="h3">
                        <h3 class="h3_1">Seja Bem Vindo!</h3>
                        <h3 class="h3_2"><?php echo htmlspecialchars($usuarioNome); ?></h3>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal_editar_perfil">Editar</button></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo conteudo-relatorios relatorio-preview-page">
            <div class="relatorio-preview-card">
                <div class="relatorio-preview-header">
                    <div class="relatorio-preview-topbar-left">
                        <a href="tela_relatorios.php" class="relatorio-back-link">
                            <i class="bi bi-chevron-left"></i> Voltar para Relatórios
                        </a>
                        <h1>Relatório de Falhas</h1>
                    </div>

                    <?php if ($relatorioId !== false && $relatorioId !== null): ?>
                        <a href="gerar_relatorio.php?id=<?= (int) $relatorioId; ?>&formato=pdf" class="relatorio-download-btn" download>
                            <i class="bi bi-download"></i>
                            Baixar relatório (PDF)
                        </a>
                    <?php else: ?>
                        <button class="relatorio-download-btn" type="button" disabled aria-disabled="true">
                            <i class="bi bi-download"></i>
                            Baixar relatório (PDF)
                        </button>
                    <?php endif; ?>
                </div>

                <div class="relatorio-preview-brandrow">
                    <div class="relatorio-brand">
                        <span class="brand-name">CELESTIAL STEEL</span>
                    </div>
                    <div class="relatorio-meta">
                        <span>Gerado em 06/10/2026 às 14:32</span>
                    </div>
                </div>

                <div class="relatorio-content-panel">
                    <div class="relatorio-summary-row">
                        <div class="relatorio-metric">
                            <span class="metric-label">Período</span>
                            <span class="metric-value">01/09/2026 a 30/09/2026</span>
                        </div>
                        <div class="relatorio-metric">
                            <span class="metric-label">Tipo de falha</span>
                            <span class="metric-value">Superaquecimento</span>
                        </div>
                        <div class="relatorio-metric">
                            <span class="metric-label">Ocorrências encontradas</span>
                            <span class="metric-value">5</span>
                        </div>
                        <div class="relatorio-metric">
                            <span class="metric-label">Emitido por</span>
                            <span class="metric-value">Fulano de Tal (Administrador)</span>
                        </div>
                    </div>

                    <div class="relatorio-resumo">
                        <h3>Resumo das ocorrências</h3>
                        <div class="resumo-grid">
                            <div class="resumo-box">
                                <span class="resumo-label">Ocorrências no período</span>
                                <span class="resumo-value">5</span>
                            </div>
                            <div class="resumo-box">
                                <span class="resumo-label">Resolvidas</span>
                                <span class="resumo-value text-success">3</span>
                            </div>
                            <div class="resumo-box">
                                <span class="resumo-label">Em aberto (em análise ou pendentes)</span>
                                <span class="resumo-value text-warning">2</span>
                            </div>
                            <div class="resumo-box">
                                <span class="resumo-label">Trens afetados</span>
                                <span class="resumo-value text-success">3</span>
                            </div>
                        </div>
                    </div>

                    <div class="relatorio-graficos">
                        <div class="grafico-box">
                            <h4>Ocorrências por semana</h4>
                            <div class="barras-semana">
                                <div class="barra-coluna">
                                    <span class="barra-valor">1</span>
                                    <div class="barra" style="height: 55%;"></div>
                                    <span class="barra-label">01 a 07/09</span>
                                </div>
                                <div class="barra-coluna">
                                    <span class="barra-valor">1</span>
                                    <div class="barra" style="height: 55%;"></div>
                                    <span class="barra-label">08 a 14/09</span>
                                </div>
                                <div class="barra-coluna">
                                    <span class="barra-valor">1</span>
                                    <div class="barra" style="height: 60%;"></div>
                                    <span class="barra-label">15 a 21/09</span>
                                </div>
                                <div class="barra-coluna">
                                    <span class="barra-valor">2</span>
                                    <div class="barra" style="height: 90%;"></div>
                                    <span class="barra-label">22 a 30/09</span>
                                </div>
                            </div>
                            <div class="grafico-legend">
                                <span><i class="bi bi-square-fill"></i> Ocorrências de superaquecimento</span>
                            </div>
                        </div>

                        <div class="grafico-box grafico-linhas-box">
                            <h4>Ocorrências por trem <span>5 no total</span></h4>
                            <div class="lista-trens-barra">
                                <div class="linha-trens">
                                    <span class="nome-trens">Aurora (5823)</span>
                                    <div class="trilha"><span style="width: 40%;"></span></div>
                                    <span class="numero-trens">2</span>
                                </div>
                                <div class="linha-trens">
                                    <span class="nome-trens">Horizonte (5827)</span>
                                    <div class="trilha"><span style="width: 40%;"></span></div>
                                    <span class="numero-trens">2</span>
                                </div>
                                <div class="linha-trens">
                                    <span class="nome-trens">Meridian (5824)</span>
                                    <div class="trilha"><span style="width: 20%;"></span></div>
                                    <span class="numero-trens">1</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relatorio-table-section">
                        <h3>Ocorrências registradas</h3>
                        <div class="table-wrap">
                            <table class="table relatorio-final-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Tipo de falha</th>
                                        <th>Trem</th>
                                        <th>Sensor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>00412</td>
                                        <td>24/09/2026</td>
                                        <td>Superaquecimento</td>
                                        <td>Aurora (5823)</td>
                                        <td>Temp. eixo 2</td>
                                        <td><span class="status-pill status-ok">Resolvido</span></td>
                                    </tr>
                                    <tr>
                                        <td>00412</td>
                                        <td>24/09/2026</td>
                                        <td>Superaquecimento</td>
                                        <td>Aurora (5823)</td>
                                        <td>Temp. eixo 2</td>
                                        <td><span class="status-pill status-ok">Resolvido</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/_modal_perfil.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/nav.js"></script>
</body>
</html>
