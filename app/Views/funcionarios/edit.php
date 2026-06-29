<h1>Editar Funcionário</h1>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('errors')): ?>
    <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="<?= url("funcionarios/{$funcionario['id']}") ?>">
    <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
    
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" id="nome_completo" name="nome_completo" value="<?= $funcionario['nome_completo'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?= $funcionario['cpf'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="cargo">Cargo</label>
        <input type="text" id="cargo" name="cargo" value="<?= $funcionario['cargo'] ?>">
    </div>
    
    <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?= $funcionario['telefone'] ?>">
    </div>
    
    <div class="form-group">
        <label for="senha">Nova Senha (deixe em branco para manter a atual)</label>
        <input type="password" id="senha" name="senha">
    </div>
    
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="<?= url('funcionarios') ?>" class="btn btn-secondary">Voltar</a>
</form>

