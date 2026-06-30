<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('medcare-theme');
            const theme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/medcare-elegant.css') ?>" rel="stylesheet">
</head>

<body class="auth-shell">
    <button type="button" class="theme-switch-floating" data-theme-toggle aria-label="Alternar tema">◐</button>
    <?= $content ?? '' ?>
    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');
            const applyTheme = function (theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('medcare-theme', theme);
                themeToggleButtons.forEach(function (button) {
                    button.textContent = theme === 'dark' ? '☀' : '◐';
                    button.setAttribute('aria-label', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
                });
            };

            applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');

            themeToggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    applyTheme(nextTheme);
                });
            });
        });
    </script>
</body>
</html>
