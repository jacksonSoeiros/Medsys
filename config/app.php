<?php

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

date_default_timezone_set('America/Sao_Paulo');

// Define o caminho das sessões
$sessionPath = __DIR__ . '/../storage/Sessions';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_save_path($sessionPath);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.gc_maxlifetime', (string) ((int) ($_ENV['SESSION_TIMEOUT'] ?? 600)));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
