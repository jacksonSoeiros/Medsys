<?php
$stats = $stats ?? [];
$chart = $chart ?? ['labels' => [], 'datasets' => []];
$chartLabels = $chart['labels'] ?? [];
$chartDatasets = $chart['datasets'] ?? [];
$chartPointCount = max(1, count($chartLabels));
$chartMax = 1;

foreach ($chartDatasets as $dataset) {
    foreach ($dataset['values'] ?? [] as $value) {
        $chartMax = max($chartMax, (int) $value);
    }
}

$svgWidth = 760;
$svgHeight = 280;
$paddingX = 36;
$paddingY = 24;
$plotWidth = $svgWidth - ($paddingX * 2);
$plotHeight = $svgHeight - ($paddingY * 2);

$buildPoints = static function (array $values) use ($chartPointCount, $chartMax, $paddingX, $paddingY, $plotWidth, $plotHeight): string {
    $points = [];
    $steps = max(1, $chartPointCount - 1);

    foreach ($values as $index => $value) {
        $x = $paddingX + ($plotWidth / $steps) * $index;
        $y = $paddingY + $plotHeight - (($plotHeight * (int) $value) / max(1, $chartMax));
        $points[] = round($x, 2) . ',' . round($y, 2);
    }

    return implode(' ', $points);
};
?>
<div class="page-shell">
    <section class="page-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="page-eyebrow">Visão geral</div>
                <h1 class="page-title">Bem-vindo, <?= e($usuario_nome) ?>.</h1>
                <p class="page-subtitle">
                    Seu ambiente clínico foi reorganizado para destacar indicadores, acelerar decisões e deixar a navegação mais elegante.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="surface-card h-100">
                    <div class="card-body">
                        <div class="subtle-note mb-2">Perfil ativo</div>
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <h2 class="h4 mb-1"><?= roleLabel($usuario_papel) ?></h2>
                                <p class="subtle-note mb-0">Painel preparado para sua rotina diária.</p>
                            </div>
                            <span class="badge text-bg-light border px-3 py-2">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="metric-label">Pacientes</div>
                    <div class="metric-value"><?= $stats['pacientes_total'] ?? 0 ?></div>
                    <div class="metric-trace"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="metric-label">Novos em 30 dias</div>
                    <div class="metric-value"><?= $stats['pacientes_30_dias'] ?? 0 ?></div>
                    <div class="metric-trace"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="metric-label">Evoluções em 30 dias</div>
                    <div class="metric-value"><?= $stats['evolucoes_30_dias'] ?? 0 ?></div>
                    <div class="metric-trace"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="metric-label"><?= e($stats['meu_rotulo'] ?? 'Minha utilização') ?></div>
                    <div class="metric-value"><?= $stats['meu_total'] ?? 0 ?></div>
                    <div class="metric-trace"></div>
                </div>
            </div>
        </div>
        <?php if (in_array($usuario_papel, ['administrador', 'chefe_equipe'])): ?>
            <div class="col-6 col-xl-2">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Funcionários</div>
                        <div class="metric-value"><?= $stats['funcionarios_total'] ?? 0 ?></div>
                        <div class="metric-trace"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($usuario_papel === 'administrador'): ?>
            <div class="col-6 col-xl-2">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Médicos</div>
                        <div class="metric-value"><?= $stats['medicos_total'] ?? 0 ?></div>
                        <div class="metric-trace"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card content-card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Linha de produção clínica</h5>
                    <p class="subtle-note mb-0">Pacientes alterados por dia, novos pacientes e evoluções.</p>
                </div>
                <div class="card-body">
                    <div class="dashboard-line-chart">
                        <div class="chart-legend">
                            <?php foreach ($chartDatasets as $dataset): ?>
                                <span><i style="background: <?= e($dataset['color']) ?>"></i><?= e($dataset['label']) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <svg viewBox="0 0 <?= $svgWidth ?> <?= $svgHeight ?>" preserveAspectRatio="none" aria-label="Gráfico em linhas da dashboard">
                            <?php for ($grid = 0; $grid <= 4; $grid++): ?>
                                <?php $y = $paddingY + ($plotHeight / 4) * $grid; ?>
                                <line x1="<?= $paddingX ?>" y1="<?= $y ?>" x2="<?= $svgWidth - $paddingX ?>" y2="<?= $y ?>" class="chart-grid-line"></line>
                            <?php endfor; ?>
                            <?php foreach ($chartDatasets as $dataset): ?>
                                <?php $points = $buildPoints($dataset['values'] ?? []); ?>
                                <polyline fill="none" stroke="<?= e($dataset['color']) ?>" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" points="<?= e($points) ?>"></polyline>
                                <?php foreach (($dataset['values'] ?? []) as $index => $value): ?>
                                    <?php
                                    $steps = max(1, $chartPointCount - 1);
                                    $cx = $paddingX + ($plotWidth / $steps) * $index;
                                    $cy = $paddingY + $plotHeight - (($plotHeight * (int) $value) / max(1, $chartMax));
                                    ?>
                                    <circle cx="<?= round($cx, 2) ?>" cy="<?= round($cy, 2) ?>" r="4.5" fill="<?= e($dataset['color']) ?>"></circle>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </svg>
                        <div class="chart-axis-labels">
                            <?php foreach ($chartLabels as $label): ?>
                                <span><?= e($label) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card content-card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Acessos rápidos</h5>
                    <p class="subtle-note mb-0">Atalhos centrais para sua operação.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (in_array($usuario_papel, ['administrador', 'funcionario', 'consultador', 'chefe_equipe'])): ?>
                            <div class="col-12 col-sm-6">
                                <a href="<?= url('pacientes') ?>" class="quick-link">
                                    <small>Gestão</small>
                                    <strong>Pacientes</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (in_array($usuario_papel, ['administrador', 'chefe_equipe'])): ?>
                            <div class="col-12 col-sm-6">
                                <a href="<?= url('funcionarios') ?>" class="quick-link">
                                    <small>Equipe</small>
                                    <strong>Funcionários</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($usuario_papel === 'administrador'): ?>
                            <div class="col-12 col-sm-6">
                                <a href="<?= url('medicos') ?>" class="quick-link">
                                    <small>Clínico</small>
                                    <strong>Médicos</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($usuario_papel === 'medico'): ?>
                            <div class="col-12 col-sm-6">
                                <a href="<?= url('pacientes') ?>" class="quick-link">
                                    <small>Atendimento</small>
                                    <strong>Prontuários</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
