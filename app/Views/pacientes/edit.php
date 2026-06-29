<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0">Editar Paciente</h1>
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

                    <form method="POST" action="<?= url("pacientes/{$paciente['id']}") ?>">
                        <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome_completo" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= $paciente['nome_completo'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $paciente['cpf'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $paciente['data_nascimento'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $paciente['telefone'] ?>">
                            </div>

                            <div class="col-md-12">
                                <hr>
                                <h6>Endereço</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="endereco_logradouro" class="form-label">Logradouro</label>
                                <input type="text" class="form-control" id="endereco_logradouro" name="endereco_logradouro" value="<?= $paciente['endereco_logradouro'] ?>">
                            </div>

                            <div class="col-md-3">
                                <label for="endereco_numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="endereco_numero" name="endereco_numero" value="<?= $paciente['endereco_numero'] ?>">
                            </div>

                            <div class="col-md-3">
                                <label for="endereco_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="endereco_complemento" name="endereco_complemento" value="<?= $paciente['endereco_complemento'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="endereco_bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="endereco_bairro" name="endereco_bairro" value="<?= $paciente['endereco_bairro'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="endereco_cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="endereco_cidade" name="endereco_cidade" value="<?= $paciente['endereco_cidade'] ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="endereco_uf" class="form-label">UF</label>
                                <input type="text" class="form-control" id="endereco_uf" name="endereco_uf" maxlength="2" value="<?= $paciente['endereco_uf'] ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="endereco_cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="endereco_cep" name="endereco_cep" value="<?= $paciente['endereco_cep'] ?>">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

