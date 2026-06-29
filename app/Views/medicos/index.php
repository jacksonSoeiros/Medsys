<h1>Médicos</h1>

<?php if (\App\Helpers\Session::hasFlash('success')): ?>
    <div class="alert alert-success"><?= \App\Helpers\Session::flash('success') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<a href="<?= url('medicos/create') ?>" class="btn btn-success">Novo Médico</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>CRM</th>
            <th>Especialidade</th>
            <th>Telefone</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicos as $medico): ?>
            <tr>
                <td><?= $medico['id'] ?></td>
                <td><?= $medico['nome_completo'] ?></td>
                <td><?= $medico['cpf'] ?></td>
                <td><?= $medico['email'] ?></td>
                <td><?= $medico['crm'] ?></td>
                <td><?= $medico['especialidade'] ?></td>
                <td><?= $medico['telefone'] ?></td>
                <td>
                    <a href="<?= url("medicos/{$medico['id']}/edit") ?>" class="btn btn-primary">Editar</a>
                    <a href="<?= url("medicos/{$medico['id']}/delete") ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

