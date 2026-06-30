<div class="py-5">
    <section class="page-hero mb-4">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <div class="page-eyebrow">Operação interna</div>
                <h1 class="page-title">Funcionários</h1>
                <p class="page-subtitle">Controle a equipe com visual mais sóbrio, melhor espaçamento e ações mais evidentes.</p>
            </div>
            <a href="<?= url('funcionarios/create') ?>" class="btn btn-success px-4">Novo funcionário</a>
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
                            <th>Perfil</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Cargo</th>
                            <th>Telefone</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?= $funcionario['id'] ?></td>
                                <td><?= $funcionario['nome_completo'] ?></td>
                                <td><?= roleLabel($funcionario['papel']) ?></td>
                                <td><?= formatCpf($funcionario['cpf']) ?></td>
                                <td><?= $funcionario['email'] ?></td>
                                <td><?= $funcionario['cargo'] ?></td>
                                <td><?= formatPhone($funcionario['telefone']) ?></td>
                                <td class="text-end">
                                    <div class="table-actions">
                                        <a href="<?= url("funcionarios/{$funcionario['id']}/edit") ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <form method="POST" action="<?= url("funcionarios/{$funcionario['id']}/delete") ?>" onsubmit="return confirm('Tem certeza?')">
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
