<h1>Prontuário de <?= $paciente['nome_completo'] ?></h1>

<?php if (\App\Helpers\Session::hasFlash('success')): ?>
    <div class="alert alert-success"><?= \App\Helpers\Session::flash('success') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('errors')): ?>
    <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="card" style="margin-bottom: 30px;">
    <h3>Nova Evolução</h3>
    <form method="POST" action="/prontuarios/<?= $paciente['id'] ?>/evolucoes">
        <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
        
        <div class="form-group">
            <label for="texto_evolucao">Descrição da Evolução</label>
            <textarea id="texto_evolucao" name="texto_evolucao" rows="5" required style="width: 100%; padding: 10px;"></textarea>
        </div>
        
        <button type="submit" class="btn btn-success">Adicionar Evolução</button>
    </form>
</div>

<h3>Histórico de Evoluções</h3>

<?php if (empty($evolucoes)): ?>
    <p>Nenhuma evolução registrada.</p>
<?php else: ?>
    <?php foreach ($evolucoes as $evolucao): ?>
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong>Dr(a). <?= $evolucao['medico_nome'] ?></strong>
                <span style="color: #666;"><?= date('d/m/Y H:i', strtotime($evolucao['registrado_em'])) ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($evolucao['texto_evolucao'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="/pacientes" class="btn btn-secondary">Voltar à Lista de Pacientes</a>
    <a href="/pacientes/<?= $paciente['id'] ?>" class="btn btn-primary">Dados do Paciente</a>
</div>

