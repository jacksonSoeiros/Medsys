<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .error { color: #dc3545; font-size: 0.9em; }
        nav { background-color: #343a40; padding: 15px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; }
        nav a:hover { text-decoration: underline; }
        .nav-right { float: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f9fa; }
        .search-box { margin-bottom: 20px; }
        .search-box input { width: 300px; padding: 10px; }
    </style>
</head>
<body>
    <nav>
        <a href="/dashboard">Início</a>
        <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador'])): ?>
            <a href="/funcionarios">Funcionários</a>
            <a href="/medicos">Médicos</a>
        <?php endif; ?>
        <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['administrador', 'funcionario'])): ?>
            <a href="/pacientes">Pacientes</a>
        <?php endif; ?>
        <?php if (in_array(\App\Helpers\Session::get('usuario_papel'), ['medico'])): ?>
            <a href="/pacientes">Pacientes</a>
        <?php endif; ?>
        <div class="nav-right">
            <span style="color: white; margin-right: 20px;"><?= \App\Helpers\Session::get('usuario_nome') ?></span>
            <a href="/logout">Sair</a>
        </div>
    </nav>
    <div class="container">
        <?= $content ?>
    </div>
</body>
</html>

