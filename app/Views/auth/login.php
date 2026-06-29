<?php
use App\Helpers\ViewHelper;
use App\Helpers\Security;
use App\Helpers\Session;
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h1 class="display-6">🏥 MedCare</h1>
                <p class="text-muted">Sistema de Gerenciamento</p>
            </div>

            <?php
            if (\App\Helpers\Session::hasFlash('error')):
                ?>
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
            

            <form method="POST" action="<?= ViewHelper::url('login') ?>">
                <input type="hidden" name="_token" value="<?= Security::generateCsrfToken() ?>">

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= ViewHelper::old('email') ?>" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
            </form>
        </div>
    </div>
</div>

