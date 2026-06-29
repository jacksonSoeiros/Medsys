<div class="py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="display-5 fw-bold">Bem-vindo, <?= $usuario_nome ?>!</h1>
            <p class="lead text-muted">Papel: <span class="badge bg-secondary"><?= ucfirst($usuario_papel) ?></span></p>
        </div>
    </div>

    <div class="row g-4">
        <?php if (in_array($usuario_papel, ['administrador', 'funcionario'])): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <span class="display-4">👤</span>
                        </div>
                        <h5 class="card-title">Pacientes</h5>
                        <p class="card-text text-muted mb-3">Gerencie os pacientes do sistema</p>
                        <a href="<?= url('pacientes') ?>" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array($usuario_papel, ['administrador'])): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <span class="display-4">👥</span>
                        </div>
                        <h5 class="card-title">Funcionários</h5>
                        <p class="card-text text-muted mb-3">Gerencie os funcionários do sistema</p>
                        <a href="<?= url('funcionarios') ?>" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <span class="display-4">👨‍⚕️</span>
                        </div>
                        <h5 class="card-title">Médicos</h5>
                        <p class="card-text text-muted mb-3">Gerencie os médicos do sistema</p>
                        <a href="<?= url('medicos') ?>" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array($usuario_papel, ['medico'])): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <span class="display-4">👤</span>
                        </div>
                        <h5 class="card-title">Pacientes</h5>
                        <p class="card-text text-muted mb-3">Visualize os pacientes e seus prontuários</p>
                        <a href="<?= url('pacientes') ?>" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

