<?php

namespace App\Core;

use App\Core\Database;
use App\Helpers\Session;
use PDO;
use PDOException;

class Installer
{
    private static function connectAppDatabase(): PDO
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';

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
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dbName = $config['database'];

        // verifica se existe
        $stmt = $pdo->query("
        SELECT 1 FROM pg_database WHERE datname = '{$dbName}'
    ");

        if ($stmt->fetch()) {
            return;
        }

        // cria banco
        $pdo->exec("CREATE DATABASE {$dbName}");
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
            'admin@medcare.local',
            password_hash('Admin@123', PASSWORD_BCRYPT)
        ]);
    }

    private static function showError(string $message): void
    {
        echo '<h1>Erro na inicialização do sistema</h1>';
        echo "<pre>{$message}</pre>";
    }

    private static function testConnection(): PDO
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=postgres',
            $config['host'],
            $config['port']
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
            self::ensureAdminUser($pdo);
        } catch (\Throwable $e) {
            self::showError($e->getMessage());
            exit;
        }
    }
}
