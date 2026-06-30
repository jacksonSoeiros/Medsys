<?php
$paciente = $paciente ?? [];
$prontuario = $prontuario ?? [];
$evolucoes = $evolucoes ?? [];
$anexos = $anexos ?? [];
?>
<div class="document-header">
    <div class="document-brand">
        <img src="<?= asset('assets/img/prefeitura-paragominas-2025.png') ?>" alt="Prefeitura de Paragominas">
        <div class="document-title">
            <h1>Prontuário de <?= e($paciente['nome_completo'] ?? '') ?></h1>
            <p>Código do paciente: <strong><?= e(formatPatientCode($paciente['codigo_paciente'] ?? null)) ?></strong></p>
        </div>
    </div>
    <div class="document-meta">
        <div>Impresso em <?= date('d/m/Y H:i') ?></div>
        <div>Prontuário #<?= (int) ($prontuario['id'] ?? 0) ?></div>
    </div>
</div>

<section class="document-section">
    <h2>Identificação do paciente</h2>
    <div class="document-grid">
        <div class="document-grid-item">
            <p class="document-line"><strong>Paciente:</strong> <?= e($paciente['nome_completo'] ?? '') ?></p>
            <p class="document-line"><strong>Código:</strong> <?= e(formatPatientCode($paciente['codigo_paciente'] ?? null)) ?></p>
            <p class="document-line"><strong>CPF:</strong> <?= e(formatCpf($paciente['cpf'] ?? '')) ?></p>
        </div>
        <div class="document-grid-item">
            <p class="document-line"><strong>Nascimento:</strong> <?= !empty($paciente['data_nascimento']) ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : '' ?></p>
            <p class="document-line"><strong>Telefone:</strong> <?= e(formatPhone($paciente['telefone'] ?? '')) ?></p>
            <p class="document-line"><strong>CEP:</strong> <?= e(formatCep($paciente['endereco_cep'] ?? '')) ?></p>
        </div>
    </div>
    <div class="document-card" style="margin-top: 1rem;">
        <p class="document-line"><strong>Endereço:</strong> <?= e(trim(($paciente['endereco_logradouro'] ?? '') . ', ' . ($paciente['endereco_numero'] ?? '') . ' - ' . ($paciente['endereco_bairro'] ?? '') . ' - ' . ($paciente['endereco_cidade'] ?? '') . '/' . ($paciente['endereco_uf'] ?? ''))) ?></p>
        <?php if (!empty($paciente['endereco_complemento'])): ?>
            <p class="document-line"><strong>Complemento:</strong> <?= e($paciente['endereco_complemento']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="document-section">
    <h2>Evoluções</h2>
    <div class="document-list">
        <?php if (empty($evolucoes)): ?>
            <div class="document-card">Nenhuma evolução registrada.</div>
        <?php else: ?>
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="document-card">
                    <p class="document-line"><strong>Médico:</strong> Dr(a). <?= e($evolucao['medico_nome']) ?></p>
                    <p class="document-line"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evolucao['registrado_em'])) ?></p>
                    <div class="document-paragraph"><?= e($evolucao['texto_evolucao']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="document-section">
    <h2>Anexos do prontuário</h2>
    <div class="document-list">
        <?php if (empty($anexos)): ?>
            <div class="document-card">Nenhuma imagem anexada.</div>
        <?php else: ?>
            <?php foreach ($anexos as $anexo): ?>
                <div class="document-card">
                    <p class="document-line"><strong>Arquivo:</strong> <?= e($anexo['nome_original']) ?></p>
                    <p class="document-line"><strong>Registrado por:</strong> Dr(a). <?= e($anexo['medico_nome']) ?></p>
                    <p class="document-line"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($anexo['registrado_em'])) ?></p>
                    <div class="document-photo-grid" style="margin-top: 0.8rem;">
                        <img src="<?= e($anexo['view_url'] ?? '') ?>" alt="<?= e($anexo['nome_original']) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
