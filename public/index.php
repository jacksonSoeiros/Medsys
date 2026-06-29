<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/config/app.php';

use App\Core\Router;

// Obtém o baseUri da variável APP_URL
$appUrl = $_ENV['APP_URL'] ?? '';
$baseUri = parse_url($appUrl, PHP_URL_PATH) ?: '/';

$router = new Router($baseUri);

require_once dirname(__DIR__) . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);

?>