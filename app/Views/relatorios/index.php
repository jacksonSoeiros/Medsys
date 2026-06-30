<?php
$periodo = $periodo ?? ['inicio' => date('Y-m-d', strtotime('-29 days')), 'fim' => date('Y-m-d')];
$summary = $summary ?? [];
$daily = $daily ?? [];
$recentPatients = $recentPatients ?? [];
$recentEvolutions = $recentEvolutions ?? [];
?>
<div class="page-shell">
    <section class="page-hero mb-4">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <div class="page-eyebrow">Gestão e auditoria</div>
                <h1 class="page-title">Relatórios do sistema</h1>
                <p class="page-subtitle">Visualize movimentações do período e imprima o consolidado diretamente no navegador.</p>
            </div>
            <a href="<?= url('relatorios/imprimir') . '?inicio=' . urlencode($periodo['inicio']) . '&fim=' . urlencode($periodo['fim']) ?>" target="_blank" rel="noopener" class="btn btn-primary">Visualizar para impressão</a>
        </div>
    </section>

    <div class="card toolbar-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('relatorios') ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="inicio" class="form-label">Data inicial</label>
                    <input type="date" class="form-control" id="inicio" name="inicio" value="<?= e($periodo['inicio']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="fim" class="form-label">Data final</label>
                    <input type="date" class="form-control" id="fim" name="fim" value="<?= e($periodo['fim']) ?>">
                </div>
                <div class="col-md-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-success">Atualizar relatório</button>
                    <a href="<?= url('relatorios') ?>" class="btn btn-secondary">Limpar filtro</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100"><div class="card-body"><div class="metric-label">Pacientes novos</div><div class="metric-value"><?= (int) ($summary['pacientes_novos'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100"><div class="card-body"><div class="metric-label">Pacientes alterados</div><div class="metric-value"><?= (int) ($summary['pacientes_atualizados'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100"><div class="card-body"><div class="metric-label">Evoluções</div><div class="metric-value"><?= (int) ($summary['evolucoes'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100"><div class="card-body"><div class="metric-label">Anexos</div><div class="metric-value"><?= (int) ($summary['anexos'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100"><div class="card-body"><div class="metric-label">Usuários ativos</div><div class="metric-value"><?= (int) ($summary['usuarios_ativos'] ?? 0) ?></div></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card content-card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Movimento diário</h5>
                    <p class="subtle-note mb-0">Resumo do período filtrado.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Novos</th>
                                    <th>Alterados</th>
                                    <th>Evoluções</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daily as $item): ?>
                                    <tr>
                                        <td><?= e($item['label']) ?></td>
                                        <td><?= (int) $item['pacientes_novos'] ?></td>
                                        <td><?= (int) $item['pacientes_atualizados'] ?></td>
                                        <td><?= (int) $item['evolucoes'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card content-card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Pacientes alterados no período</h5>
                    <p class="subtle-note mb-0">Últimos 10 registros com atualização.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Cidade</th>
                                    <th>Atualizado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentPatients)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Nenhuma alteração encontrada no período.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($recentPatients as $patient): ?>
                                    <tr>
                                        <td><?= e(formatPatientCode($patient['codigo_paciente'] ?? null)) ?></td>
                                        <td><?= e($patient['nome_completo']) ?></td>
                                        <td><?= e($patient['endereco_cidade'] ?? '') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($patient['atualizado_em'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card content-card">
                <div class="card-header">
                    <h5 class="mb-1">Últimas evoluções médicas</h5>
                    <p class="subtle-note mb-0">Trechos recentes para conferência antes da impressão.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Evolução</th>
                                    <th>Registrado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentEvolutions)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Nenhuma evolução encontrada no período.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($recentEvolutions as $evolution): ?>
                                    <tr>
                                        <td><?= e($evolution['paciente_nome']) ?></td>
                                        <td><?= e($evolution['medico_nome']) ?></td>
                                        <td class="text-break"><?= e(mb_strimwidth($evolution['texto_evolucao'], 0, 120, '...')) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($evolution['registrado_em'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
