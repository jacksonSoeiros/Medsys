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

        // First check for exact matches
        if (isset($this->routes[$method][$uri])) {
            [$controller, $action] = $this->routes[$method][$uri];
            $this->callAction($controller, $action, []);
            return;
        }

        // Then check for dynamic routes
        foreach ($this->routes[$method] as $routeUri => $routeAction) {
            $params = $this->matchRoute($routeUri, $uri);
            if ($params !== false) {
                [$controller, $action] = $routeAction;
                $this->callAction($controller, $action, $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Página não encontrada";
        exit;
    }

    private function matchRoute(string $routeUri, string $requestUri): array|false
    {
        $routeParts = explode('/', trim($routeUri, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^\{(\w+)\}$/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $requestParts[$i];
            } else if ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    private function callAction(string $controller, string $action, array $params): void
    {
        if (!class_exists($controller)) {
            die("Controller {$controller} não encontrado.");
        }

        $instance = new $controller();

        if (!method_exists($instance, $action)) {
            die("Método {$action} não encontrado no controller {$controller}.");
        }

        call_user_func_array([$instance, $action], [$params]);
    }
}
