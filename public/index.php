<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/config/app.php';

use App\Core\Router;

$router = new Router();

require_once dirname(__DIR__) . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);

?>