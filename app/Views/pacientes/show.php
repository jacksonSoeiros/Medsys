<h1><?= $paciente['nome_completo'] ?></h1>

<div class="card">
    <h3>Dados Pessoais</h3>
    <p><strong>CPF:</strong> <?= $paciente['cpf'] ?></p>
    <p><strong>Data de Nascimento:</strong> <?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></p>
    <p><strong>Telefone:</strong> <?= $paciente['telefone'] ?></p>
    
    <h3>Endereço</h3>
    <p><strong>Logradouro:</strong> <?= $paciente['endereco_logradouro'] ?></p>
    <p><strong>Número:</strong> <?= $paciente['endereco_numero'] ?></p>
    <p><strong>Complemento:</strong> <?= $paciente['endereco_complemento'] ?></p>
    <p><strong>Bairro:</strong> <?= $paciente['endereco_bairro'] ?></p>
    <p><strong>Cidade:</strong> <?= $paciente['endereco_cidade'] ?></p>
    <p><strong>UF:</strong> <?= $paciente['endereco_uf'] ?></p>
    <p><strong>CEP:</strong> <?= $paciente['endereco_cep'] ?></p>
</div>

<div style="margin-top: 20px;">
    <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar</a>
    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
        <a href="<?= url("pacientes/{$paciente['id']}/edit") ?>" class="btn btn-primary">Editar</a>
    <?php endif; ?>
    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
        <a href="<?= url("prontuarios/{$paciente['id']}") ?>" class="btn btn-success">Prontuário</a>
    <?php endif; ?>
</div>

