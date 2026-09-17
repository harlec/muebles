<?php

declare(strict_types=1);

// Solo para `php -S` (servidor embebido de desarrollo): sirve archivos
// estáticos reales tal cual. En Plesk/Apache esto ya lo resuelve el .htaccess.
if (PHP_SAPI === 'cli-server') {
    $path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($path !== '/' && is_file(__DIR__ . $path)) {
        return false;
    }
}

$config = require dirname(__DIR__) . '/config/config.php';
date_default_timezone_set($config['app']['timezone']);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\Api\SalesController;

$router = new Router();

$router->get('/', [DashboardController::class, 'index']);

$router->get('/api/today', [SalesController::class, 'today']);
$router->get('/api/month', [SalesController::class, 'month']);
$router->get('/api/payments', [SalesController::class, 'payments']);
$router->get('/api/rotation', [SalesController::class, 'rotation']);
$router->get('/api/feed', [SalesController::class, 'feed']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
