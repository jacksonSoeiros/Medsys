<?php
$paciente = $paciente ?? [];
?>
<div class="page-shell">
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">Paciente</div>
            <h1 class="page-title"><?= e($paciente['nome_completo']) ?></h1>
            <p class="page-subtitle">Código para pesquisa: <strong><?= e(formatPatientCode($paciente['codigo_paciente'] ?? null)) ?></strong></p>
        </div>
        <div class="page-actions">
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar</a>
            <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario', 'consultador', 'chefe_equipe'])): ?>
                <a href="<?= url("pacientes/{$paciente['id']}/edit") ?>" class="btn btn-primary">Editar</a>
            <?php endif; ?>
            <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
                <a href="<?= url("prontuarios/{$paciente['id']}") ?>" class="btn btn-success">Prontuário</a>
            <?php endif; ?>
            <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario', 'consultador', 'chefe_equipe', 'medico'])): ?>
                <a href="<?= url("prontuarios/{$paciente['id']}/imprimir") ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">Imprimir prontuário</a>
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
                        <dd class="col-sm-8"><?= formatCpf($paciente['cpf']) ?></dd>

                        <dt class="col-sm-4">Código</dt>
                        <dd class="col-sm-8"><?= e(formatPatientCode($paciente['codigo_paciente'] ?? null)) ?></dd>
                        
                        <dt class="col-sm-4">Data de Nascimento</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></dd>
                        
                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8"><?= formatPhone($paciente['telefone']) ?></dd>
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
                        <dd class="col-sm-8"><?= formatCep($paciente['endereco_cep']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

