<?php
use App\Helpers\ViewHelper;
use App\Helpers\Security;
use App\Helpers\Session;
?>

<div class="container">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="auth-aside d-flex flex-column justify-content-between">
                    <div>
                        <img src="<?= asset('assets/img/prefeitura-paragominas-2025.png') ?>" alt="Prefeitura de Paragominas" class="auth-logo">
                        <p class="mt-3 mb-0 auth-aside-caption">
                            Um ambiente clínico institucional.
                        </p>

                        <div class="auth-bullets">
                            <span>Prontuários e cadastros com leitura mais limpa</span>
                            <span>Navegação mais refinada e profissional</span>
                            <span>Experiência visual mais consistente em todo o sistema</span>
                        </div>
                    </div>

                    <small class="text-white-50">Acesso seguro para equipes administrativas e médicas.</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="auth-panel h-100 d-flex align-items-center">
                    <div class="w-100">
                        <div class="mb-4">
                            <div class="page-eyebrow">Acesso ao sistema</div>
                            <div class="login-title">Entrar na plataforma</div>
                            <p class="login-subtitle mb-0">Use suas credenciais para acessar o painel clínico.</p>
                        </div>

                        <?php if (Session::hasFlash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= Session::flash('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (Session::hasFlash('errors')): ?>
                            <?php foreach (Session::flash('errors') as $error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= $error ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <form method="POST" action="<?= ViewHelper::url('login') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCsrfToken() ?>">

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= ViewHelper::old('email') ?>" required autofocus placeholder="voce@empresa.com">
                            </div>

                            <div class="mb-4">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required placeholder="Digite sua senha">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                        </form>

                        <div class="section-divider mt-4">Ambiente profissional</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
