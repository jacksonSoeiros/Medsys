<h1>Funcionários</h1>

<?php if (\App\Helpers\Session::hasFlash('success')): ?>
    <div class="alert alert-success"><?= \App\Helpers\Session::flash('success') ?></div>
<?php endif; ?>

<?php if (\App\Helpers\Session::hasFlash('error')): ?>
    <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
<?php endif; ?>

<a href="/funcionarios/create" class="btn btn-success">Novo Funcionário</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Cargo</th>
            <th>Telefone</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($funcionarios as $funcionario): ?>
            <tr>
                <td><?= $funcionario['id'] ?></td>
                <td><?= $funcionario['nome_completo'] ?></td>
                <td><?= $funcionario['cpf'] ?></td>
                <td><?= $funcionario['email'] ?></td>
                <td><?= $funcionario['cargo'] ?></td>
                <td><?= $funcionario['telefone'] ?></td>
                <td>
                    <a href="/funcionarios/<?= $funcionario['id'] ?>/edit" class="btn btn-primary">Editar</a>
                    <a href="/funcionarios/<?= $funcionario['id'] ?>/delete" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

