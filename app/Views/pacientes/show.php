<?php
$paciente = $paciente ?? [];
?>
<div class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= $paciente['nome_completo'] ?></h1>
        <div class="d-flex gap-2">
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar</a>
            <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
                <a href="<?= url("pacientes/{$paciente['id']}/edit") ?>" class="btn btn-primary">Editar</a>
            <?php endif; ?>
            <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
                <a href="<?= url("prontuarios/{$paciente['id']}") ?>" class="btn btn-success">Prontuário</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Dados Pessoais</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">CPF</dt>
                        <dd class="col-sm-8"><?= $paciente['cpf'] ?></dd>
                        
                        <dt class="col-sm-4">Data de Nascimento</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></dd>
                        
                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8"><?= $paciente['telefone'] ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Endereço</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Logradouro</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_logradouro'] ?></dd>
                        
                        <dt class="col-sm-4">Número</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_numero'] ?></dd>
                        
                        <dt class="col-sm-4">Complemento</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_complemento'] ?></dd>
                        
                        <dt class="col-sm-4">Bairro</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_bairro'] ?></dd>
                        
                        <dt class="col-sm-4">Cidade</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_cidade'] ?></dd>
                        
                        <dt class="col-sm-4">UF</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_uf'] ?></dd>
                        
                        <dt class="col-sm-4">CEP</dt>
                        <dd class="col-sm-8"><?= $paciente['endereco_cep'] ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

