<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuario - <?= e($paciente['nome_completo'] ?? '') ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { margin: 0 0 10px; }
        .muted { color: #666; }
        .section { margin-bottom: 24px; }
        .card { border: 1px solid #d9d9d9; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
        .meta { margin-bottom: 12px; }
        .meta strong { display: inline-block; min-width: 150px; }
        .anexo { page-break-inside: avoid; margin-bottom: 14px; }
        .anexo img { width: 180px; height: auto; border: 1px solid #ccc; margin-top: 6px; }
        .separator { border-top: 1px solid #ddd; margin: 18px 0; }
    </style>
</head>
<body>
    <div class="section">
        <h1>Prontuario do Paciente</h1>
        <p class="muted">Gerado em <?= date('d/m/Y H:i') ?></p>
    </div>

    <div class="section card">
        <div class="meta"><strong>Paciente:</strong> <?= e($paciente['nome_completo'] ?? '') ?></div>
        <div class="meta"><strong>CPF:</strong> <?= formatCpf($paciente['cpf'] ?? '') ?></div>
        <div class="meta"><strong>Data de nascimento:</strong> <?= !empty($paciente['data_nascimento']) ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : '' ?></div>
        <div class="meta"><strong>Telefone:</strong> <?= formatPhone($paciente['telefone'] ?? '') ?></div>
        <div class="meta"><strong>Endereco:</strong> <?= e(trim(($paciente['endereco_logradouro'] ?? '') . ', ' . ($paciente['endereco_numero'] ?? '') . ' - ' . ($paciente['endereco_bairro'] ?? '') . ' - ' . ($paciente['endereco_cidade'] ?? '') . '/' . ($paciente['endereco_uf'] ?? ''))) ?></div>
        <div class="meta"><strong>CEP:</strong> <?= formatCep($paciente['endereco_cep'] ?? '') ?></div>
    </div>

    <div class="section">
        <h2>Evolucoes</h2>
        <?php if (empty($evolucoes)): ?>
            <div class="card">Nenhuma evolucao registrada.</div>
        <?php else: ?>
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="card">
                    <div><strong>Medico:</strong> <?= e($evolucao['medico_nome']) ?></div>
                    <div><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evolucao['registrado_em'])) ?></div>
                    <div class="separator"></div>
                    <div><?= nl2br(e($evolucao['texto_evolucao'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Anexos de Imagem</h2>
        <?php if (empty($anexos)): ?>
            <div class="card">Nenhuma imagem anexada.</div>
        <?php else: ?>
            <?php foreach ($anexos as $anexo): ?>
                <div class="card anexo">
                    <div><strong>Arquivo:</strong> <?= e($anexo['nome_original']) ?></div>
                    <div><strong>Adicionado por:</strong> Dr(a). <?= e($anexo['medico_nome']) ?></div>
                    <div><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($anexo['registrado_em'])) ?></div>
                    <?php if (!empty($anexo['pdf_src'])): ?>
                        <img src="<?= $anexo['pdf_src'] ?>" alt="<?= e($anexo['nome_original']) ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
