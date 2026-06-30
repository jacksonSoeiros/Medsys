<?php
$medicos = $medicos ?? [];
$search = $search ?? '';
?>
<div class="py-5">
    <section class="page-hero mb-4">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <div class="page-eyebrow">Equipe médica</div>
                <h1 class="page-title">Médicos</h1>
                <p class="page-subtitle">Gerencie profissionais e especialidades com uma leitura mais limpa e institucional.</p>
            </div>
            <a href="<?= url('medicos/create') ?>" class="btn btn-success px-4">Novo médico</a>
        </div>
    </section>

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

    <div class="card content-card table-panel">
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
                                <td><?= formatCpf($medico['cpf']) ?></td>
                                <td><?= $medico['email'] ?></td>
                                <td><?= $medico['crm'] ?></td>
                                <td><?= $medico['especialidade'] ?></td>
                                <td><?= formatPhone($medico['telefone']) ?></td>
                                <td class="text-end">
                                    <div class="table-actions">
                                        <a href="<?= url("medicos/{$medico['id']}/edit") ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <form method="POST" action="<?= url("medicos/{$medico['id']}/delete") ?>" onsubmit="return confirm('Tem certeza?')">
                                            <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
