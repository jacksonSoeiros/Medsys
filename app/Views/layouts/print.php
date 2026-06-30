<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Impressão Med-Sys' ?></title>
    <style>
        :root {
            --print-green: #0f6b39;
            --print-gold: #d4af37;
            --print-blue: #1e4f87;
            --print-border: #d9e1ea;
            --print-muted: #667085;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            color: #101828;
        }

        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid var(--print-border);
        }

        .print-toolbar button,
        .print-toolbar a {
            padding: 0.8rem 1.1rem;
            border: 0;
            border-radius: 10px;
            background: var(--print-green);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
        }

        .print-toolbar a {
            background: #475467;
        }

        .print-page {
            width: 210mm;
            min-height: 297mm;
            margin: 1rem auto 2rem;
            padding: 16mm 14mm;
            background: #fff;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.12);
        }

        .document-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .document-brand {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .document-brand img {
            width: 170px;
            height: auto;
        }

        .document-title h1 {
            margin: 0;
            font-size: 1.6rem;
        }

        .document-title p {
            margin: 0.35rem 0 0;
            color: var(--print-muted);
            font-size: 0.95rem;
        }

        .document-meta {
            text-align: right;
            font-size: 0.88rem;
            color: var(--print-muted);
        }

        .document-section {
            margin-top: 1.4rem;
        }

        .document-section h2 {
            margin: 0 0 0.9rem;
            padding-bottom: 0.45rem;
            border-bottom: 2px solid var(--print-green);
            font-size: 1.1rem;
        }

        .document-card,
        .document-grid-item {
            border: 1px solid var(--print-border);
            border-radius: 12px;
            padding: 1rem;
            background: #fff;
        }

        .document-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .document-list {
            display: grid;
            gap: 0.9rem;
        }

        .document-line {
            margin: 0 0 0.55rem;
            line-height: 1.5;
        }

        .document-line strong {
            display: inline-block;
            min-width: 130px;
        }

        .document-paragraph {
            margin: 0.8rem 0 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .document-photo-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .document-photo-grid img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--print-border);
        }

        .print-hint {
            margin-top: 1rem;
            color: var(--print-muted);
            font-size: 0.84rem;
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
            }

            .print-toolbar,
            .print-hint {
                display: none !important;
            }

            .print-page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <button type="button" onclick="window.print()">Confirmar e imprimir</button>
        <a href="javascript:window.close()">Fechar</a>
    </div>
    <div class="print-page">
        <?= $content ?? '' ?>
    </div>
    <div class="print-hint">Ao confirmar, o navegador abre a seleção da impressora conectada para impressão direta.</div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
