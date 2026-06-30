<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class RelatorioController extends Controller
{
    private function resolvePeriod(): array
    {
        $end = $_GET['fim'] ?? date('Y-m-d');
        $start = $_GET['inicio'] ?? date('Y-m-d', strtotime('-29 days'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $start = date('Y-m-d', strtotime('-29 days'));
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            $end = date('Y-m-d');
        }

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }

    private function buildReportData(): array
    {
        [$start, $end] = $this->resolvePeriod();
        $pdo = Database::getInstance()->getConnection();
        $periodEndExclusive = date('Y-m-d', strtotime($end . ' +1 day'));

        $summary = [
            'pacientes_novos' => 0,
            'pacientes_atualizados' => 0,
            'evolucoes' => 0,
            'anexos' => 0,
            'usuarios_ativos' => 0,
        ];

        $summaryQueries = [
            'pacientes_novos' => "SELECT COUNT(*) FROM pacientes WHERE criado_em >= ? AND criado_em < ?",
            'pacientes_atualizados' => "SELECT COUNT(*) FROM pacientes WHERE atualizado_em >= ? AND atualizado_em < ?",
            'evolucoes' => "SELECT COUNT(*) FROM prontuario_evolucoes WHERE registrado_em >= ? AND registrado_em < ?",
            'anexos' => "SELECT COUNT(*) FROM prontuario_anexos WHERE registrado_em >= ? AND registrado_em < ?",
        ];

        foreach ($summaryQueries as $key => $sql) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$start, $periodEndExclusive]);
            $summary[$key] = (int) $stmt->fetchColumn();
        }

        $summary['usuarios_ativos'] = (int) $pdo
            ->query("SELECT COUNT(*) FROM usuarios WHERE ativo = TRUE")
            ->fetchColumn();

        $dailyStmt = $pdo->prepare("
            SELECT
                to_char(day_ref, 'DD/MM') AS label,
                COALESCE((SELECT COUNT(*) FROM pacientes p WHERE DATE(p.criado_em) = DATE(day_ref)), 0) AS pacientes_novos,
                COALESCE((SELECT COUNT(*) FROM pacientes p WHERE DATE(p.atualizado_em) = DATE(day_ref)), 0) AS pacientes_atualizados,
                COALESCE((SELECT COUNT(*) FROM prontuario_evolucoes pe WHERE DATE(pe.registrado_em) = DATE(day_ref)), 0) AS evolucoes
            FROM generate_series(?::date, ?::date, INTERVAL '1 day') AS day_ref
            ORDER BY day_ref
        ");
        $dailyStmt->execute([$start, $end]);
        $daily = $dailyStmt->fetchAll();

        $recentPatientsStmt = $pdo->prepare("
            SELECT codigo_paciente, nome_completo, cpf, endereco_cidade, atualizado_em
            FROM pacientes
            WHERE atualizado_em >= ? AND atualizado_em < ?
            ORDER BY atualizado_em DESC, id DESC
            LIMIT 10
        ");
        $recentPatientsStmt->execute([$start, $periodEndExclusive]);

        $recentEvolutionsStmt = $pdo->prepare("
            SELECT p.nome_completo AS paciente_nome, m.nome_completo AS medico_nome, e.texto_evolucao, e.registrado_em
            FROM prontuario_evolucoes e
            JOIN prontuarios pr ON pr.id = e.prontuario_id
            JOIN pacientes p ON p.id = pr.paciente_id
            JOIN medicos m ON m.id = e.medico_id
            WHERE e.registrado_em >= ? AND e.registrado_em < ?
            ORDER BY e.registrado_em DESC, e.id DESC
            LIMIT 10
        ");
        $recentEvolutionsStmt->execute([$start, $periodEndExclusive]);

        return [
            'periodo' => [
                'inicio' => $start,
                'fim' => $end,
            ],
            'summary' => $summary,
            'daily' => $daily,
            'recentPatients' => $recentPatientsStmt->fetchAll(),
            'recentEvolutions' => $recentEvolutionsStmt->fetchAll(),
        ];
    }

    public function index()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'chefe_equipe']);

        $this->view('relatorios/index', $this->buildReportData());
    }

    public function print()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'chefe_equipe']);

        $this->view('relatorios/print', $this->buildReportData(), 'print');
    }
}
