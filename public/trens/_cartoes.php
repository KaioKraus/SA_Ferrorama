<?php if (empty($trens)): ?>
    <div class="col-12">
        <p class="text-center py-4 mb-0"><?= $mensagemSemTrens ?? 'Nenhum trem encontrado.'; ?></p>
    </div>
<?php else: ?>
    <?php foreach ($trens as $trem): ?>
        <?php
        // Prepara os dados e as classes usados no cartão e nos modais.
        $tremId = (int) $trem['trem_id'];
        $nomeTrem = htmlspecialchars((string) $trem['nome_trem'], ENT_QUOTES, 'UTF-8');
        $responsavelId = (int) $trem['responsavel_id'];
        $statusTrem = (string) ($trem['status_trem'] ?? 'Inativo');
        $statusClasseTrem = $statusTrem === 'Manutenção' ? 'manutencao' : strtolower($statusTrem);
        $rotaAtiva = (int) $trem['rota_ativa'] === 1;
        $velocidadeTrem = (float) ($trem['velocidade'] ?? 0);
        $temperaturaTrem = (float) ($trem['temperatura'] ?? 0);
        $consumoTrem = (float) ($trem['consumo'] ?? 0);
        $rotaNome = (string) ($trem['rota_nome'] ?? 'Sem rota');
        $quantidadeRotas = (int) ($trem['quantidade_rotas'] ?? 0);
        $mostrarBotaoExcluir = !($statusTrem === 'Ativo' && $rotaAtiva);
        ?>
        <div class="col">
            <article class="trens-card-trem" data-id="<?= $tremId; ?>" data-nome="<?= $nomeTrem; ?>" data-responsavel="<?= $responsavelId; ?>" data-status="<?= htmlspecialchars($statusTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota="<?= htmlspecialchars($rotaNome, ENT_QUOTES, 'UTF-8'); ?>" data-quantidade-rotas="<?= $quantidadeRotas; ?>" data-velocidade="<?= htmlspecialchars((string) $velocidadeTrem, ENT_QUOTES, 'UTF-8'); ?>" data-temperatura="<?= htmlspecialchars((string) $temperaturaTrem, ENT_QUOTES, 'UTF-8'); ?>" data-consumo="<?= htmlspecialchars((string) $consumoTrem, ENT_QUOTES, 'UTF-8'); ?>" data-rota-ativa="<?= $rotaAtiva ? '1' : '0'; ?>">
                <div class="trens-card-cabecalho">
                    <div class="trens-card-header-row">
                        <span class="trens-card-status <?= htmlspecialchars($statusClasseTrem, ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($statusTrem, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <div class="trens-card-acoes">
                            <?php if ($usuarioEhAdministrador): ?>
                            <button class="btn_edit" type="button" data-bs-toggle="modal" data-bs-target="#modal_monitoramento" aria-label="Editar trem">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <?php if ($mostrarBotaoExcluir): ?>
                                    <button class="btn_delete" type="button" data-id="<?= $tremId; ?>" aria-label="Excluir trem">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <button class="trens-card-acao" type="button" data-bs-toggle="modal" data-bs-target="#modal_visualizar_trem" aria-label="Detalhes do trem">
                                <i class="bi bi-info-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="trens-card-id">
                        <strong>ID: <?= $tremId; ?></strong>
                    </div>
                    <p class="trens-card-nome">Nome: <?= $nomeTrem; ?></p>
                </div>
                <img src="../../assets/img/trem_card.png" alt="Trem" class="trens-card-img">
            </article>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
