<?php
$pacientes = $pacientes ?? [];
$search = $search ?? '';
?>

<div class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Pacientes</h1>
        <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
            <a href="<?= url('pacientes/create') ?>" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Novo Paciente
            </a>
        <?php endif; ?>
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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('pacientes') ?>" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nome, CPF ou cidade..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                        <?php if (!empty($search)): ?>
                            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Limpar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Data de Nascimento</th>
                            <th>Cidade</th>
                            <th class="text-end">Ações</th>
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
                                <td class="text-end">
                                    <a href="<?= url("pacientes/{$paciente['id']}") ?>" class="btn btn-sm btn-primary me-1">Visualizar</a>
                                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
                                        <a href="<?= url("pacientes/{$paciente['id']}/edit") ?>" class="btn btn-sm btn-primary me-1">Editar</a>
                                        <a href="<?= url("pacientes/{$paciente['id']}/delete") ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                                    <?php endif; ?>
                                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
                                        <a href="<?= url("prontuarios/{$paciente['id']}") ?>" class="btn btn-sm btn-success">Prontuário</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

