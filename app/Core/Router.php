<?php

namespace App\Core;

class Router
{
    /** @var array<string, array<string, callable|array>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Ruta no encontrada', 'path' => $path]);
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action();
            return;
        }

        $handler();
    }
}
