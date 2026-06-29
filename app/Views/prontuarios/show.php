<div class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Prontuário de <?= $paciente['nome_completo'] ?></h1>
        <div class="d-flex gap-2">
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar à Lista de Pacientes</a>
            <a href="<?= url("pacientes/{$paciente['id']}") ?>" class="btn btn-primary">Dados do Paciente</a>
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
                            <p class="card-text">
                                <?= nl2br(htmlspecialchars($evolucao['texto_evolucao'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

