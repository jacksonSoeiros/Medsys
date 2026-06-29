<?php
$medicos = $medicos ?? [];
$search = $search ?? '';
?>
<div class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Médicos</h1>
        <a href="<?= url('medicos/create') ?>" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Novo Médico
        </a>
    </div>

    <?php if (\App\Helpers\Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= \App\Helpers\Session::flash('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (\App\Helpers\Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= \App\Helpers\Session::flash('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>CRM</th>
                            <th>Especialidade</th>
                            <th>Telefone</th>
                            <th class="text-end">Ações</th>
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
                                <td class="text-end">
                                    <a href="<?= url("medicos/{$medico['id']}/edit") ?>" class="btn btn-sm btn-primary me-1">Editar</a>
                                    <a href="<?= url("medicos/{$medico['id']}/delete") ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

