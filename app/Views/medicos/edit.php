<?php
$medico = $medico ?? [];
$search = $search ?? '';
?>

<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0">Editar Médico</h1>
                </div>
                <div class="card-body">
                    <?php if (\App\Helpers\Session::hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= \App\Helpers\Session::flash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (\App\Helpers\Session::hasFlash('errors')): ?>
                        <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST" action="<?= url("medicos/{$medico['id']}") ?>">
                        <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome_completo" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= $medico['nome_completo'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $medico['cpf'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="crm" class="form-label">CRM</label>
                                <input type="text" class="form-control" id="crm" name="crm" value="<?= $medico['crm'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $medico['telefone'] ?>">
                            </div>

                            <div class="col-md-12">
                                <label for="especialidade" class="form-label">Especialidade</label>
                                <input type="text" class="form-control" id="especialidade" name="especialidade" value="<?= $medico['especialidade'] ?>">
                            </div>

                            <div class="col-md-12">
                                <label for="senha" class="form-label">Nova Senha <span class="text-muted">(deixe em branco para manter a atual)</span></label>
                                <input type="password" class="form-control" id="senha" name="senha">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <a href="<?= url('medicos') ?>" class="btn btn-secondary">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>