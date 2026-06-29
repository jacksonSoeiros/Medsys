<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
        .card { border-radius: 0.5rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('dashboard') ?>">🏥 MedCare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('dashboard') ?>">Início</a>
                    </li>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('funcionarios') ?>">Funcionários</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('medicos') ?>">Médicos</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario', 'medico'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('pacientes') ?>">Pacientes</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">Olá, <?= \App\Helpers\Session::get('usuario_nome') ?></span>
                    <a href="<?= url('logout') ?>" class="btn btn-outline-light btn-sm">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <?= $content ?>
    </main>

    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>

