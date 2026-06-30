<?php
$paciente = $paciente ?? [];
$prontuario = $prontuario ?? [];
$evolucoes = $evolucoes ?? [];
$anexos = $anexos ?? [];
?>

<div class="page-shell">
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">Prontuário médico</div>
            <h1 class="page-title">Prontuário de <?= e($paciente['nome_completo']) ?></h1>
            <p class="page-subtitle">Código para pesquisa: <strong><?= e(formatPatientCode($paciente['codigo_paciente'] ?? null)) ?></strong></p>
        </div>
        <div class="page-actions">
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar à Lista de Pacientes</a>
            <a href="<?= url("pacientes/{$paciente['id']}") ?>" class="btn btn-primary">Dados do Paciente</a>
            <a href="<?= url("prontuarios/{$paciente['id']}/imprimir") ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">Imprimir prontuário</a>
        </div>
    </div>

    <?php if (\App\Helpers\Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= \App\Helpers\Session::flash('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (\App\Helpers\Session::hasFlash('errors')): ?>
        <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Nova Evolução</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url("prontuarios/{$paciente['id']}/evolucoes") ?>">
                <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
                
                <div class="mb-3">
                    <label for="texto_evolucao" class="form-label">Descrição da Evolução</label>
                    <textarea class="form-control" id="texto_evolucao" name="texto_evolucao" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Adicionar Evolução</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Anexar Imagens</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url("prontuarios/{$paciente['id']}/anexos") ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
                <div class="mb-3">
                    <label for="anexos" class="form-label">Imagens do prontuário</label>
                    <input type="file" class="form-control" id="anexos" name="anexos[]" accept="image/*" multiple required>
                    <div class="form-text">As imagens ficam registradas com data, hora e médico responsável.</div>
                </div>
                <button type="submit" class="btn btn-outline-success">Enviar imagens</button>
            </form>
        </div>
    </div>

    <h3 class="mb-3">Histórico de Evoluções</h3>

    <?php if (empty($evolucoes)): ?>
        <div class="alert alert-info">
            Nenhuma evolução registrada.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-subtitle text-muted mb-0">
                                    Dr(a). <?= $evolucao['medico_nome'] ?>
                                </h6>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($evolucao['registrado_em'])) ?>
                                </small>
                            </div>
                            <p class="card-text text-break">
                                <?= nl2br(htmlspecialchars($evolucao['texto_evolucao'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3 class="mb-3 mt-4">Imagens Anexadas</h3>

    <?php if (empty($anexos)): ?>
        <div class="alert alert-info">
            Nenhuma imagem anexada.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($anexos as $anexo): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?= e($anexo['view_url'] ?? '') ?>" class="card-img-top" alt="<?= e($anexo['nome_original']) ?>" style="max-height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title text-truncate"><?= e($anexo['nome_original']) ?></h6>
                            <p class="card-text mb-1"><strong>Medico:</strong> Dr(a). <?= e($anexo['medico_nome']) ?></p>
                            <p class="card-text text-muted mb-0"><?= date('d/m/Y H:i', strtotime($anexo['registrado_em'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
