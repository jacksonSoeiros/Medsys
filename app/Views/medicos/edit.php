<h1>Editar Médico</h1>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('errors')): ?>
    <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="/medicos/<?= $medico['id'] ?>">
    <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
    
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" id="nome_completo" name="nome_completo" value="<?= $medico['nome_completo'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?= $medico['cpf'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="crm">CRM</label>
        <input type="text" id="crm" name="crm" value="<?= $medico['crm'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="especialidade">Especialidade</label>
        <input type="text" id="especialidade" name="especialidade" value="<?= $medico['especialidade'] ?>">
    </div>
    
    <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?= $medico['telefone'] ?>">
    </div>
    
    <div class="form-group">
        <label for="senha">Nova Senha (deixe em branco para manter a atual)</label>
        <input type="password" id="senha" name="senha">
    </div>
    
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="/medicos" class="btn btn-secondary">Voltar</a>
</form>

