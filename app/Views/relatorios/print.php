<?php
$periodo = $periodo ?? ['inicio' => date('Y-m-d', strtotime('-29 days')), 'fim' => date('Y-m-d')];
$summary = $summary ?? [];
$daily = $daily ?? [];
$recentPatients = $recentPatients ?? [];
$recentEvolutions = $recentEvolutions ?? [];
?>
<div class="document-header">
    <div class="document-brand">
        <img src="<?= asset('assets/img/prefeitura-paragominas-2025.png') ?>" alt="Prefeitura de Paragominas">
        <div class="document-title">
            <h1>Relatório do sistema Med-Sys</h1>
            <p>Período de <?= date('d/m/Y', strtotime($periodo['inicio'])) ?> até <?= date('d/m/Y', strtotime($periodo['fim'])) ?></p>
        </div>
    </div>
    <div class="document-meta">
        <div>Impresso em <?= date('d/m/Y H:i') ?></div>
        <div>Perfil autorizado: Admin / Chefe de Equipe</div>
    </div>
</div>

<section class="document-section">
    <h2>Resumo executivo</h2>
    <div class="document-grid">
        <div class="document-grid-item">
            <p class="document-line"><strong>Pacientes novos:</strong> <?= (int) ($summary['pacientes_novos'] ?? 0) ?></p>
            <p class="document-line"><strong>Pacientes alterados:</strong> <?= (int) ($summary['pacientes_atualizados'] ?? 0) ?></p>
            <p class="document-line"><strong>Evoluções:</strong> <?= (int) ($summary['evolucoes'] ?? 0) ?></p>
        </div>
        <div class="document-grid-item">
            <p class="document-line"><strong>Anexos:</strong> <?= (int) ($summary['anexos'] ?? 0) ?></p>
            <p class="document-line"><strong>Usuários ativos:</strong> <?= (int) ($summary['usuarios_ativos'] ?? 0) ?></p>
            <p class="document-line"><strong>Relatório:</strong> Consolidado operacional</p>
        </div>
    </div>
</section>

<section class="document-section">
    <h2>Movimento diário</h2>
    <div class="document-list">
        <?php foreach ($daily as $item): ?>
            <div class="document-card">
                <p class="document-line"><strong>Dia:</strong> <?= e($item['label']) ?></p>
                <p class="document-line"><strong>Novos pacientes:</strong> <?= (int) $item['pacientes_novos'] ?></p>
                <p class="document-line"><strong>Pacientes alterados:</strong> <?= (int) $item['pacientes_atualizados'] ?></p>
                <p class="document-line"><strong>Evoluções:</strong> <?= (int) $item['evolucoes'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="document-section">
    <h2>Pacientes alterados</h2>
    <div class="document-list">
        <?php if (empty($recentPatients)): ?>
            <div class="document-card">Nenhum paciente alterado no período.</div>
        <?php else: ?>
            <?php foreach ($recentPatients as $patient): ?>
                <div class="document-card">
                    <p class="document-line"><strong>Código:</strong> <?= e(formatPatientCode($patient['codigo_paciente'] ?? null)) ?></p>
                    <p class="document-line"><strong>Paciente:</strong> <?= e($patient['nome_completo']) ?></p>
                    <p class="document-line"><strong>Cidade:</strong> <?= e($patient['endereco_cidade'] ?? '') ?></p>
                    <p class="document-line"><strong>Atualizado em:</strong> <?= date('d/m/Y H:i', strtotime($patient['atualizado_em'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="document-section">
    <h2>Últimas evoluções</h2>
    <div class="document-list">
        <?php if (empty($recentEvolutions)): ?>
            <div class="document-card">Nenhuma evolução registrada no período.</div>
        <?php else: ?>
            <?php foreach ($recentEvolutions as $evolution): ?>
                <div class="document-card">
                    <p class="document-line"><strong>Paciente:</strong> <?= e($evolution['paciente_nome']) ?></p>
                    <p class="document-line"><strong>Médico:</strong> <?= e($evolution['medico_nome']) ?></p>
                    <p class="document-line"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evolution['registrado_em'])) ?></p>
                    <div class="document-paragraph"><?= e($evolution['texto_evolucao']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
