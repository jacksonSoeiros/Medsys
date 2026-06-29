<h1>Editar Paciente</h1>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('errors')): ?>
    <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="<?= url("pacientes/{$paciente['id']}") ?>">
    <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
    
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" id="nome_completo" name="nome_completo" value="<?= $paciente['nome_completo'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?= $paciente['cpf'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="data_nascimento">Data de Nascimento</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $paciente['data_nascimento'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?= $paciente['telefone'] ?>">
    </div>
    
    <h3>Endereço</h3>
    
    <div class="form-group">
        <label for="endereco_logradouro">Logradouro</label>
        <input type="text" id="endereco_logradouro" name="endereco_logradouro" value="<?= $paciente['endereco_logradouro'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_numero">Número</label>
        <input type="text" id="endereco_numero" name="endereco_numero" value="<?= $paciente['endereco_numero'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_complemento">Complemento</label>
        <input type="text" id="endereco_complemento" name="endereco_complemento" value="<?= $paciente['endereco_complemento'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_bairro">Bairro</label>
        <input type="text" id="endereco_bairro" name="endereco_bairro" value="<?= $paciente['endereco_bairro'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_cidade">Cidade</label>
        <input type="text" id="endereco_cidade" name="endereco_cidade" value="<?= $paciente['endereco_cidade'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_uf">UF</label>
        <input type="text" id="endereco_uf" name="endereco_uf" maxlength="2" value="<?= $paciente['endereco_uf'] ?>">
    </div>
    
    <div class="form-group">
        <label for="endereco_cep">CEP</label>
        <input type="text" id="endereco_cep" name="endereco_cep" value="<?= $paciente['endereco_cep'] ?>">
    </div>
    
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar</a>
</form>

