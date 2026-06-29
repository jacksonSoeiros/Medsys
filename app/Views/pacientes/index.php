<h1>Pacientes</h1>

<?php if (\App\Helpers\Session::hasFlash('success')): ?>
    <div class="alert alert-success"><?= \App\Helpers\Session::flash('success') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
    <a href="<?= url('pacientes/create') ?>" class="btn btn-success">Novo Paciente</a>
<?php endif; ?>

<div class="search-box" style="margin-top: 20px;">
    <form method="GET" action="<?= url('pacientes') ?>">
        <input type="text" name="search" placeholder="Buscar por nome, CPF ou cidade..." value="<?= htmlspecialchars($search) ?>" style="width: 400px; padding: 10px;">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($search)): ?>
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Data de Nascimento</th>
            <th>Cidade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pacientes as $paciente): ?>
            <tr>
                <td><?= $paciente['id'] ?></td>
                <td><?= $paciente['nome_completo'] ?></td>
                <td><?= $paciente['cpf'] ?></td>
                <td><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></td>
                <td><?= $paciente['endereco_cidade'] ?></td>
                <td>
                    <a href="<?= url("pacientes/{$paciente['id']}") ?>" class="btn btn-primary">Visualizar</a>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
                        <a href="<?= url("pacientes/{$paciente['id']}/edit") ?>" class="btn btn-primary">Editar</a>
                        <a href="<?= url("pacientes/{$paciente['id']}/delete") ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
                        <a href="<?= url("prontuarios/{$paciente['id']}") ?>" class="btn btn-success">Prontuário</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

