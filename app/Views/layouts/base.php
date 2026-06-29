<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MedCare' ?></title>
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
</head>

<body>
    <?= $content ?? '' ?>
    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>

