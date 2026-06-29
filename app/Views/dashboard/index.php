<h1>Bem-vindo, <?= $usuario_nome ?>!</h1>
<p>Papel: <?= ucfirst($usuario_papel) ?></p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
    <?php if (in_array($usuario_papel, ['administrador', 'funcionario'])): ?>
        <div class="card">
            <h3>Pacientes</h3>
            <p>Gerencie os pacientes do sistema</p>
            <a href="<?= url('pacientes') ?>" class="btn btn-primary">Acessar</a>
        </div>
    <?php endif; ?>
    
    <?php if (in_array($usuario_papel, ['administrador'])): ?>
        <div class="card">
            <h3>Funcionários</h3>
            <p>Gerencie os funcionários do sistema</p>
            <a href="<?= url('funcionarios') ?>" class="btn btn-primary">Acessar</a>
        </div>
        
        <div class="card">
            <h3>Médicos</h3>
            <p>Gerencie os médicos do sistema</p>
            <a href="<?= url('medicos') ?>" class="btn btn-primary">Acessar</a>
        </div>
    <?php endif; ?>
    
    <?php if (in_array($usuario_papel, ['medico'])): ?>
        <div class="card">
            <h3>Pacientes</h3>
            <p>Visualize os pacientes e seus prontuários</p>
            <a href="<?= url('pacientes') ?>" class="btn btn-primary">Acessar</a>
        </div>
    <?php endif; ?>
</div>

