<?php

namespace App\Core;

use App\Core\Database;
use App\Helpers\Session;
use PDO;
use PDOException;

class Installer
{
    private static function appDatabaseConfig(): array
    {
        return require dirname(__DIR__, 2) . '/config/database.php';
    }

    private static function setupDatabaseConfig(): array
    {
        $config = self::appDatabaseConfig();

        return [
            'host' => $_ENV['DB_SETUP_HOST'] ?? $config['host'],
            'port' => $_ENV['DB_SETUP_PORT'] ?? $config['port'],
            'database' => $_ENV['DB_SETUP_DATABASE'] ?? 'postgres',
            'username' => $_ENV['DB_SETUP_USERNAME'] ?? $config['username'],
            'password' => $_ENV['DB_SETUP_PASSWORD'] ?? $config['password'],
        ];
    }

    private static function connectAppDatabase(): PDO
    {
        $config = self::appDatabaseConfig();

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['host'],
            $config['port'],
            $config['database']
        );

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    private static function tableExists(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare("
        SELECT EXISTS (
            SELECT 1
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_name = ?
        )
    ");

        $stmt->execute([$table]);
        return $stmt->fetchColumn();
    }

    private static function createDatabase(PDO $pdo): void
    {
        $config = self::appDatabaseConfig();
        $dbName = preg_replace('/[^a-zA-Z0-9_]+/', '', (string) $config['database']);

        // verifica se existe
        $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
        $stmt->execute([$dbName]);

        if ($stmt->fetch()) {
            return;
        }

        // cria banco
        $pdo->exec('CREATE DATABASE "' . $dbName . '"');
    }

    private static function ensureDatabaseStructure(PDO $pdo): void
    {
        $stmt = $pdo->query("
        SELECT EXISTS (
            SELECT 1 FROM information_schema.tables
            WHERE table_name = 'usuarios'
        )
    ");

        if ($stmt->fetchColumn()) {
            return;
        }

        $schema = file_get_contents(
            dirname(__DIR__, 2) . '/database/schema.sql'
        );

        $pdo->exec($schema);
    }

    private static function ensureSchemaUpgrades(PDO $pdo): void
    {
        if (self::tableExists($pdo, 'usuarios')) {
            $pdo->exec("ALTER TABLE usuarios DROP CONSTRAINT IF EXISTS usuarios_papel_check");
            $pdo->exec("
                ALTER TABLE usuarios
                ADD CONSTRAINT usuarios_papel_check
                CHECK (papel IN ('administrador','funcionario','consultador','chefe_equipe','medico'))
            ");
        }

        if (self::tableExists($pdo, 'pacientes')) {
            $pdo->exec("ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS codigo_paciente BIGINT");
            $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uq_pacientes_codigo ON pacientes(codigo_paciente)");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_paciente_codigo ON pacientes(codigo_paciente)");
            $pdo->exec("UPDATE pacientes SET codigo_paciente = id WHERE codigo_paciente IS NULL");
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS prontuario_anexos (
                id BIGSERIAL PRIMARY KEY,
                prontuario_id BIGINT NOT NULL,
                medico_id BIGINT NOT NULL,
                nome_original VARCHAR(255) NOT NULL,
                caminho_arquivo VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                registrado_em TIMESTAMP NOT NULL DEFAULT NOW(),
                CONSTRAINT fk_anexo_prontuario
                    FOREIGN KEY (prontuario_id)
                    REFERENCES prontuarios(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT fk_anexo_medico
                    FOREIGN KEY (medico_id)
                    REFERENCES medicos(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS login_tentativas (
                id BIGSERIAL PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                tentativas INTEGER NOT NULL DEFAULT 0,
                ultima_tentativa TIMESTAMP,
                bloqueado_ate TIMESTAMP
            )
        ");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_anexo_prontuario ON prontuario_anexos(prontuario_id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_anexo_data ON prontuario_anexos(registrado_em DESC)");

        $privateUploadPath = dirname(__DIR__, 2) . '/storage/private/prontuarios';
        if (!is_dir($privateUploadPath)) {
            mkdir($privateUploadPath, 0750, true);
        }

        $legacyUploadPath = dirname(__DIR__, 2) . '/public/uploads/prontuarios';
        if (is_dir($legacyUploadPath)) {
            foreach (glob($legacyUploadPath . '/*') ?: [] as $legacyFile) {
                $targetFile = $privateUploadPath . '/' . basename($legacyFile);
                if (is_file($legacyFile) && !is_file($targetFile)) {
                    @rename($legacyFile, $targetFile);
                }
            }
        }
    }

    private static function ensureAdminUser(PDO $pdo): void
    {
        $stmt = $pdo->query("
        SELECT COUNT(*) FROM usuarios WHERE papel = 'administrador'
    ");

        if ($stmt->fetchColumn() > 0) {
            return;
        }

        $stmt = $pdo->prepare("
        INSERT INTO usuarios (email, senha_hash, papel, ativo)
        VALUES (?, ?, 'administrador', true)
    ");

        $stmt->execute([
            $_ENV['APP_ADMIN_EMAIL'] ?? 'admin@medcare.local',
            password_hash($_ENV['APP_ADMIN_PASSWORD'] ?? 'Admin@123', PASSWORD_BCRYPT)
        ]);
    }

    private static function showError(string $message): void
    {
        echo '<h1>Erro na inicialização do sistema</h1>';
        echo "<pre>{$message}</pre>";
    }

    private static function testConnection(): PDO
    {
        $config = self::setupDatabaseConfig();

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['host'],
            $config['port'],
            $config['database']
        );

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public static function check(): void
    {
        try {
            $pdo = self::testConnection();

            self::createDatabase($pdo);

            // reconecta no banco do sistema
            $pdo = self::connectAppDatabase();

            self::ensureDatabaseStructure($pdo);
            self::ensureSchemaUpgrades($pdo);
            self::ensureAdminUser($pdo);
        } catch (\Throwable $e) {
            self::showError($e->getMessage());
            exit;
        }
    }
}
