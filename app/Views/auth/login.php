<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <h1 style="text-align: center; margin-bottom: 20px;">MedCare</h1>
        <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>
        
        <?php if (\App\Helpers\Session::hasFlash('error')): ?>
            <div class="alert alert-error"><?= \App\Helpers\Session::flash('error') ?></div>
        <?php endif; ?>
        
        <?php if (\App\Helpers\Session::hasFlash('errors')): ?>
            <?php foreach (\App\Helpers\Session::flash('errors') as $error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="<?= \App\Helpers\Security::generateCsrfToken() ?>">
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
    </div>
</div>

