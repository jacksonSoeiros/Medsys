<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->routes[$method][$uri])) {

            http_response_code(404);

            echo "404 - Página não encontrada";

            exit;

        }

        [$controller, $action] = $this->routes[$method][$uri];

        $controller = "App\\Controllers\\{$controller}";

        $instance = new $controller();

        call_user_func([$instance, $action]);
    }
}

?>