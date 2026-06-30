<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Usuario;
use App\Models\Funcionario;
use App\Models\Medico;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class AuthController extends Controller
{
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCK_MINUTES = 15;

    private function currentLoginAttempt(string $email): ?array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM login_tentativas WHERE email = ?");
        $stmt->execute([mb_strtolower(trim($email))]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function isLoginBlocked(string $email): bool
    {
        $attempt = $this->currentLoginAttempt($email);

        if (!$attempt || empty($attempt['bloqueado_ate'])) {
            return false;
        }

        return strtotime((string) $attempt['bloqueado_ate']) > time();
    }

    private function registerFailedLogin(string $email): void
    {
        $pdo = Database::getInstance()->getConnection();
        $normalizedEmail = mb_strtolower(trim($email));
        $attempt = $this->currentLoginAttempt($normalizedEmail);
        $count = ((int) ($attempt['tentativas'] ?? 0)) + 1;
        $blockedUntil = $count >= self::LOGIN_MAX_ATTEMPTS
            ? date('Y-m-d H:i:s', strtotime('+' . self::LOGIN_LOCK_MINUTES . ' minutes'))
            : null;

        $stmt = $pdo->prepare("
            INSERT INTO login_tentativas (email, tentativas, ultima_tentativa, bloqueado_ate)
            VALUES (?, ?, NOW(), ?)
            ON CONFLICT (email) DO UPDATE
            SET tentativas = EXCLUDED.tentativas,
                ultima_tentativa = EXCLUDED.ultima_tentativa,
                bloqueado_ate = EXCLUDED.bloqueado_ate
        ");
        $stmt->execute([$normalizedEmail, $count, $blockedUntil]);
    }

    private function clearFailedLogin(string $email): void
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM login_tentativas WHERE email = ?");
        $stmt->execute([mb_strtolower(trim($email))]);
    }

    private function buildDashboardSeries(\PDO $pdo, int $days = 14): array
    {
        $stmt = $pdo->prepare("
            SELECT
                to_char(day_ref, 'DD/MM') AS label,
                COALESCE((
                    SELECT COUNT(*)
                    FROM pacientes p
                    WHERE DATE(p.atualizado_em) = DATE(day_ref)
                ), 0) AS pacientes_alterados,
                COALESCE((
                    SELECT COUNT(*)
                    FROM pacientes p
                    WHERE DATE(p.criado_em) = DATE(day_ref)
                ), 0) AS novos_pacientes,
                COALESCE((
                    SELECT COUNT(*)
                    FROM prontuario_evolucoes pe
                    WHERE DATE(pe.registrado_em) = DATE(day_ref)
                ), 0) AS evolucoes
            FROM generate_series(CURRENT_DATE - INTERVAL '{$days} days', CURRENT_DATE, INTERVAL '1 day') AS day_ref
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return [
            'labels' => array_map(fn (array $row): string => $row['label'], $rows),
            'datasets' => [
                [
                    'label' => 'Pacientes alterados',
                    'color' => '#0f766e',
                    'values' => array_map(fn (array $row): int => (int) $row['pacientes_alterados'], $rows),
                ],
                [
                    'label' => 'Novos pacientes',
                    'color' => '#d4af37',
                    'values' => array_map(fn (array $row): int => (int) $row['novos_pacientes'], $rows),
                ],
                [
                    'label' => 'Evoluções',
                    'color' => '#1d4ed8',
                    'values' => array_map(fn (array $row): int => (int) $row['evolucoes'], $rows),
                ],
            ],
        ];
    }

    public function login()
    {
        (new \App\Middleware\GuestMiddleware())->handle();
        $this->view('auth/login', [], 'base');
    }

    public function doLogin()
    {
        (new \App\Middleware\GuestMiddleware())->handle();
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $validator = new Validator($data);
        $validator->required('email')->email('email')->required('senha');

        if ($validator->fails()) {
            Redirect::to('/login')->withErrors($validator->getErrors())->withInput();
        }

        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));

        if ($this->isLoginBlocked($email)) {
            Redirect::to('/login')->with('error', 'Acesso temporariamente bloqueado. Tente novamente em alguns minutos.')->withInput();
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findBy('email', $email);

        if (!$usuario || !$usuario['ativo']) {
            $this->registerFailedLogin($email);
            Redirect::to('/login')->with('error', 'E-mail ou senha inválidos.')->withInput();
        }

        if (!Security::verifyPassword($data['senha'], $usuario['senha_hash'])) {
            $this->registerFailedLogin($email);
            Redirect::to('/login')->with('error', 'E-mail ou senha inválidos.')->withInput();
        }

        Session::regenerate();
        Session::set('usuario_id', $usuario['id']);
        Session::set('usuario_papel', $usuario['papel']);
        Session::set('usuario_email', $usuario['email']);
        Session::touchActivity();
        $this->clearFailedLogin($email);

        if ($usuario['papel'] === 'medico') {
            $medicoModel = new Medico();
            $medico = $medicoModel->findByUsuarioId($usuario['id']);
            Session::set('usuario_nome', $medico['nome_completo']);
        } else {
            $funcionarioModel = new Funcionario();
            $funcionario = $funcionarioModel->findByUsuarioId($usuario['id']);
            Session::set('usuario_nome', $funcionario['nome_completo']);
        }

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);

        Logger::log('login', 'usuarios', $usuario['id'], 'Usuário logou no sistema');

        Redirect::to('/dashboard');
    }

    public function sessionPing()
    {
        (new \App\Middleware\AuthMiddleware())->handle();

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
            http_response_code(400);
            exit;
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'ok' => true,
            'timeout' => Session::getTimeoutInSeconds(),
            'timestamp' => time(),
        ]);
        exit;
    }

    public function logout()
    {
        Logger::log('logout', 'usuarios', Session::get('usuario_id'), 'Usuário deslogou do sistema');
        Session::destroy();
        Redirect::to('/login');
    }

    public function dashboard()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        $pdo = Database::getInstance()->getConnection();
        $papel = Session::get('usuario_papel');

        $stats = [
            'pacientes_total' => (int) $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn(),
            'pacientes_30_dias' => (int) $pdo->query("SELECT COUNT(*) FROM pacientes WHERE criado_em >= NOW() - INTERVAL '30 days'")->fetchColumn(),
            'evolucoes_30_dias' => (int) $pdo->query("SELECT COUNT(*) FROM prontuario_evolucoes WHERE registrado_em >= NOW() - INTERVAL '30 days'")->fetchColumn(),
            'funcionarios_total' => (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE papel IN ('administrador', 'funcionario', 'consultador', 'chefe_equipe')")->fetchColumn(),
            'medicos_total' => (int) $pdo->query("SELECT COUNT(*) FROM medicos")->fetchColumn(),
            'meu_total' => 0,
            'meu_rotulo' => 'Minha utilização',
        ];

        if ($papel === 'medico') {
            $medico = (new Medico())->findByUsuarioId(Session::get('usuario_id'));
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM prontuario_evolucoes WHERE medico_id = ? AND registrado_em >= NOW() - INTERVAL '30 days'");
            $stmt->execute([$medico['id'] ?? 0]);
            $stats['meu_total'] = (int) $stmt->fetchColumn();
        } else {
            $funcionario = (new Funcionario())->findByUsuarioId(Session::get('usuario_id'));
            if ($funcionario) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE cadastrado_por = ? AND criado_em >= NOW() - INTERVAL '30 days'");
                $stmt->execute([$funcionario['id']]);
                $stats['meu_total'] = (int) $stmt->fetchColumn();
                $stats['meu_rotulo'] = 'Cadastros nos ultimos 30 dias';
            }
        }

        $chart = $this->buildDashboardSeries($pdo);

        $this->view('dashboard/index', [
            'usuario_nome' => Session::get('usuario_nome'),
            'usuario_papel' => $papel,
            'stats' => $stats,
            'chart' => $chart,
        ]);
    }
}
